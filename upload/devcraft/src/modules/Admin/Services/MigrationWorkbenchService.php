<?php
//===============================================================
// Файл: MigrationWorkbenchService.php                          =
// Путь: devcraft/src/modules/Admin/Services/MigrationWorkbench…=
// Последнее изменение: 2026-10-28 12:00:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use Cycle\Database\Database;
use Cycle\Database\Driver\Driver;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\State;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Database\DatabaseGateway;
use DevCraft\Core\Database\SchemaSyncService;
use DevCraft\Core\Database\SqlCaptureDriver;

/**
 * Действия страницы миграций: список, создание, накат, откат шага, отметка без SQL, выгрузка SQL.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class MigrationWorkbenchService {

	public const STATUS_PENDING  = 'pending';
	public const STATUS_EXECUTED = 'executed';
	public const STATUS_ORPHAN   = 'orphan';

	/**
	 * @since 200.4.2
	 *
	 * @param   SchemaSyncService  $sync     Синхронизация схемы и Migrator.
	 * @param   DatabaseGateway    $gateway  Шлюз БД.
	 */
	public function __construct(
		private readonly SchemaSyncService $sync,
		private readonly DatabaseGateway   $gateway,
	) {}

	/**
	 * Сервис на объектах текущего приложения.
	 *
	 * @since 200.4.2
	 */
	public static function create(): self {
		$app = Application::instance();

		return new self(new SchemaSyncService($app->registry(), $app->database()), $app->database());
	}

	/**
	 * Строки таблицы миграций: файлы всех модулей и записи учёта без файла.
	 *
	 * @since 200.4.2
	 *
	 * @return list<array{id: string, name: string, module: string, file: string, created: string, executed: string,
	 *     status: string, has_down: bool}>
	 */
	public function rows(): array {
		$migrator = $this->sync->migrator();
		$rows     = [];
		$known    = [];
		$root     = rtrim(Paths::src(), '/\\') . '/';

		foreach($migrator->getMigrations() as $migration) {
			$state = $migration->getState();
			$file  = (string) (new \ReflectionClass($migration))->getFileName();

			$known[$state->getName()] = true;

			$rows[] = [
				'id'       => self::key($state),
				'name'     => $state->getName(),
				'module'   => basename(dirname($file, 2)),
				'file'     => str_starts_with($file, $root)? substr($file, strlen($root)) : $file,
				'created'  => $state->getTimeCreated()->format('Y-m-d H:i:s'),
				'executed' => $state->getTimeExecuted()?->format('Y-m-d H:i:s') ?? '',
				'status'   => $state->getStatus() === State::STATUS_EXECUTED? self::STATUS_EXECUTED : self::STATUS_PENDING,
				'has_down' => $this->methodBody($migration, 'down') !== '',
			];
		}

		$table = PREFIX . '_devcraft_migrations';

		foreach($this->gateway->query("SELECT `migration`, `time_executed`, `created_at` FROM `{$table}`")->fetchAll() as $record) {
			if(isset($known[(string) $record['migration']])) {
				continue;
			}

			$rows[] = [
				'id'       => '',
				'name'     => (string) $record['migration'],
				'module'   => '',
				'file'     => '',
				'created'  => substr((string) $record['created_at'], 0, 19),
				'executed' => substr((string) $record['time_executed'], 0, 19),
				'status'   => self::STATUS_ORPHAN,
				'has_down' => false,
			];
		}

		return $rows;
	}

	/**
	 * Создаёт файлы миграций по отличиям моделей от базы.
	 *
	 * @since 200.4.2
	 *
	 * @return int Сколько файлов создано.
	 */
	public function generate(): int {
		return count($this->sync->generateMigrations());
	}

	/**
	 * Накатывает ожидающие миграции.
	 *
	 * @since 200.4.2
	 *
	 * @return int Сколько стало выполненными.
	 */
	public function migrate(): int {
		return $this->sync->applyPending();
	}

	/**
	 * Откатывает последнюю выполненную миграцию.
	 *
	 * @since 200.4.2
	 *
	 * @return string|null Имя отменённой миграции или null, если откатывать нечего.
	 */
	public function rollback(): ?string {
		$migrator = $this->sync->migrator();
		$migrated = $migrator->rollback(new Capsule($this->gateway->databaseManager()->database()));
		$this->sync->dropCachedSchema();

		return $migrated?->getState()->getName();
	}

	/**
	 * Помечает миграцию выполненной без запуска SQL.
	 *
	 * @since 200.4.2
	 *
	 * @param   string  $id  Ключ миграции из `rows()`.
	 *
	 * @throws \RuntimeException Если миграция не найдена.
	 */
	public function fake(string $id): void {
		$this->sync->markExecuted($this->find($id)->getState());
	}

	/**
	 * Удаляет записи учёта без файлов.
	 *
	 * @since 200.4.2
	 *
	 * @return int Сколько записей удалено.
	 */
	public function prune(): int {
		return $this->sync->pruneRecordsWithoutFiles($this->sync->migrator());
	}

	/**
	 * Собирает SQL миграции без изменения базы.
	 *
	 * Если SQL собрать нельзя (состояние базы не совпадает с шагом), в текст идёт исходник метода
	 * с пометкой.
	 *
	 * @since 200.4.2
	 *
	 * @param   string  $id         Ключ миграции из `rows()`.
	 * @param   string  $direction  `up`, `down` или `both`.
	 *
	 * @return array{name: string, sql: string} Имя файла для скачивания и текст.
	 *
	 * @throws \RuntimeException Если миграция не найдена.
	 */
	public function sql(string $id, string $direction): array {
		$migration = $this->find($id);
		$state     = $migration->getState();
		$main      = $this->gateway->databaseManager()->driver('mysql');

		if(!$main instanceof Driver) {
			throw new \RuntimeException(__('Драйвер базы не поддерживает выгрузку SQL'));
		}

		$driver  = SqlCaptureDriver::fromDriver($main);
		$capsule = new Capsule(new Database('default', PREFIX . '_', $driver));

		// Шаги идут по очереди на одном снимке схемы: для выполненной сначала down, иначе up.
		$order    = $state->getStatus() === State::STATUS_EXECUTED? ['down', 'up'] : ['up', 'down'];
		$captured = [];

		foreach($order as $step) {
			try {
				$migration->withCapsule($capsule)->{$step}();
				$captured[$step] = implode(";\n", $driver->takeStatements()) . ';';
			} catch(\Throwable $e) {
				$driver->takeStatements();
				$captured[$step] = '-- ' . __('SQL собрать не удалось: {error}', ['{error}' => $e->getMessage()]) . "\n"
				                   . '-- ' . __('Ниже исходник метода {method} из файла миграции.', ['{method}' => $step]) . "\n"
				                   . '/*' . "\n" . $this->methodBody($migration, $step) . "\n" . '*/';
			}
		}

		$driver->disconnect();

		$steps = $direction === 'both'? ['up', 'down'] : [$direction === 'down'? 'down' : 'up'];
		$text  = '-- ' . __('Миграция: {name}', ['{name}' => $state->getName()]) . "\n";

		foreach($steps as $step) {
			$text .= "\n-- ---------- {$step} ----------\n" . $captured[$step] . "\n";
		}

		return [
			'name' => $state->getName() . '.' . implode('_', $steps) . '.sql',
			'sql'  => $text,
		];
	}

	private static function key(State $state): string {
		return $state->getName() . '@' . $state->getTimeCreated()->format('Ymd.His');
	}

	private function find(string $id): MigrationInterface {
		foreach($this->sync->migrator()->getMigrations() as $migration) {
			if(self::key($migration->getState()) === $id) {
				return $migration;
			}
		}

		throw new \RuntimeException(__('Миграция не найдена'));
	}

	/**
	 * Тело метода миграции без фигурных скобок (пустая строка, если метод пуст).
	 */
	private function methodBody(MigrationInterface $migration, string $method): string {
		$reflection = new \ReflectionMethod($migration, $method);
		$file       = $reflection->getFileName();

		if($file === false) {
			return '';
		}

		$lines  = array_slice(file($file) ?: [], $reflection->getStartLine() - 1, $reflection->getEndLine() - $reflection->getStartLine() + 1);
		$source = implode('', $lines);
		$open   = strpos($source, '{');
		$close  = strrpos($source, '}');

		return $open === false || $close === false? '' : trim(substr($source, $open + 1, $close - $open - 1));
	}

}
