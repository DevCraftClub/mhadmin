<?php
//===============================================================
// Файл: ModuleMigrationsGenerator.php                          =
// Путь: devcraft/src/classes/Database/ModuleMigrationsGenerator=
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

namespace DevCraft\Core\Database;

use Cycle\Migrations\Atomizer\TableSorter;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Schema\Generator\Migrations\NameBasedOnChangesGenerator;
use Cycle\Schema\Generator\Migrations\Strategy\MultipleFilesStrategy;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;

/**
 * Как `GenerateMigrations` Cycle, но файл каждой таблицы пишет в `Migrations` модуля-владельца модели.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Core.Database
 */
final class ModuleMigrationsGenerator implements GeneratorInterface {

	/** @var list<string> */
	private array $written = [];

	/**
	 * @since 200.4.2
	 *
	 * @param   EntityPathResolver  $paths  Резолвер каталогов миграций.
	 */
	public function __construct(
		private readonly EntityPathResolver $paths,
	) {}

	/**
	 * Создаёт файлы миграций для таблиц с изменениями.
	 *
	 * @since 200.4.2
	 *
	 * @param   Registry  $registry  Реестр схемы Cycle.
	 *
	 * @return Registry Тот же реестр.
	 */
	public function run(Registry $registry): Registry {
		$databases = [];
		$targets   = [];

		foreach($registry as $entity) {
			if(!$registry->hasTable($entity) || $entity->getOptions()->has(SyncTables::READONLY_SCHEMA)) {
				continue;
			}

			$table = $registry->getTableSchema($entity);
			$class = $entity->getClass();
			$file  = $class !== null && class_exists($class)? (new \ReflectionClass($class))->getFileName() : false;

			$databases[$registry->getDatabase($entity)][] = $table;
			$targets[spl_object_id($table)]               = is_string($file)
				? $this->paths->migrationsDirectoryForModel($file)
				: $this->paths->migrationsDirectory();
		}

		$sorter = new TableSorter();
		/** @var array<string, array{FileRepository, MultipleFilesStrategy}> $writers */
		$writers = [];

		foreach($databases as $database => $tables) {
			// Общий порядок по внешним ключам, затем каждая таблица — в свой каталог.
			// ponytail: номер шага в имени файла считается отдельно в каждом каталоге — внешний ключ
			// между модулями, созданными в одну секунду, может накатиться раньше своей таблицы; тогда
			// нужен общий счётчик шагов.
			foreach($sorter->sort($tables) as $table) {
				$directory = $targets[spl_object_id($table)];
				$writers[$directory] ??= $this->writer($directory);
				[$repository, $strategy] = $writers[$directory];

				foreach($strategy->generate($database, [$table]) as $image) {
					$this->written[] = $repository->registerMigration(
						substr($image->buildFileName(), 0, 128),
						$image->getClass()->getName(),
						(string) $image->getFile(),
					);
				}
			}
		}

		return $registry;
	}

	/**
	 * Пути файлов, созданных последним запуском.
	 *
	 * @since 200.4.2
	 *
	 * @return list<string>
	 */
	public function written(): array {
		return $this->written;
	}

	/**
	 * @return array{FileRepository, MultipleFilesStrategy}
	 */
	private function writer(string $directory): array {
		$config = new MigrationConfig([
			'directory' => $directory,
			'table'     => 'devcraft_migrations',
			'safe'      => true,
		]);

		return [
			new FileRepository($config),
			new MultipleFilesStrategy($config, new NameBasedOnChangesGenerator()),
		];
	}

}
