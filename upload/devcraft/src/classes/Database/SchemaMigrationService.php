<?php
//===============================================================
// Файл: SchemaMigrationService.php                             =
// Путь: devcraft/src/classes/Database/SchemaMigrationService.php
// Последнее изменение: 2026-11-18 14:40:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Database;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry as SchemaRegistry;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Ловит отличия моделей от базы: пишет их файлами миграций (история изменений) и накатывает.
 *
 * Таблица совпадает с моделью — ни файла, ни запроса на изменение. В отличие от `SyncTables` Cycle,
 * база меняется только через файл миграции, поэтому каждое изменение видно в разделе «Миграции».
 *
 * Блокировку схемы держит вызывающий `SchemaSyncService` (`ensureSchemaReady`, `generateMigrations`).
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Core.Database
 */
final class SchemaMigrationService {

	/**
	 * @since 200.4.2
	 *
	 * @param   SchemaSyncService   $sync     Сборка схемы и накат миграций.
	 * @param   DatabaseGateway     $gateway  Шлюз БД.
	 * @param   EntityPathResolver  $paths    Каталоги моделей и миграций.
	 */
	public function __construct(
		private readonly SchemaSyncService  $sync,
		private readonly DatabaseGateway    $gateway,
		private readonly EntityPathResolver $paths,
	) {}

	/**
	 * Сравнивает модели с базой и пишет файл миграции на каждую таблицу с отличиями. Базу не меняет.
	 *
	 * @since 200.4.2
	 *
	 * @return array{schema: array<string, mixed>, written: list<string>, changed: list<string>, unchanged: list<string>, applied: int}
	 *     Схема ORM, пути новых файлов, таблицы с отличиями и без них, число накатанных миграций (здесь 0).
	 */
	public function capture(): array {
		$registry = new SchemaRegistry($this->gateway->databaseManager());
		$schema   = $this->sync->compileSchema(
			$registry,
			$this->paths->entityModelDirectories(),
			$this->paths,
			TRUE,
			FALSE,
		);
		$written = $this->sync->lastWritten();
		$changed = $unchanged = [];

		foreach ($registry as $entity) {
			if (!$registry->hasTable($entity) || $entity->getOptions()->has(SyncTables::READONLY_SCHEMA)) {
				continue;
			}

			$table = $registry->getTableSchema($entity);

			// Ключ — имя таблицы: несколько моделей могут делить одну таблицу.
			if ($table->getComparator()->hasChanges()) {
				$changed[$table->getFullName()] = TRUE;
			} else {
				$unchanged[$table->getFullName()] = TRUE;
			}
		}

		if ($written !== []) {
			LogGenerator::for('SchemaMigrationService')->log([
				'info'    => __('Модели отличаются от базы, записаны миграции: {tables}', ['{tables}' => implode(', ', array_keys($changed))]),
				'written' => $written,
			], 'info');
		}

		return [
			'schema'    => $schema,
			'written'   => $written,
			'changed'   => array_keys($changed),
			'unchanged' => array_keys($unchanged),
			'applied'   => 0,
		];
	}

	/**
	 * Пишет миграции по отличиям и накатывает ожидающие. Отличий и ожидающих нет — базу не трогает.
	 *
	 * @since 200.4.2
	 *
	 * @return array{schema: array<string, mixed>, written: list<string>, changed: list<string>, unchanged: list<string>, applied: int}
	 *     То же, что `capture()`, плюс число накатанных миграций.
	 */
	public function captureAndApply(): array {
		$report   = $this->capture();
		$migrator = $this->sync->migrator($this->paths);
		$pending  = $this->sync->pendingCount($migrator);

		if ($pending > 0) {
			$this->sync->runPending($migrator);
			$report['applied'] = $pending - $this->sync->pendingCount($migrator);
		}

		return $report;
	}

}
