<?php
//===============================================================
// Файл: SchemaSyncService.php                                  =
// Путь: devcraft/src/classes/Database/SchemaSyncService.php    =
// Последнее изменение: 2026-09-14 16:20:00                     =
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

use Cycle\Annotated;
use Cycle\Migrations;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\State;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Generator\Migrations\NameBasedOnChangesGenerator;
use Cycle\Schema\Generator\Migrations\Strategy\SingleFileStrategy;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry as SchemaRegistry;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Module\Registry;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;

/**
 * Готовит схему Cycle ORM: ожидающие файлы, затем debug → SyncTables иначе файлы+накат.
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Database
 */
final class SchemaSyncService {

	/**
	 * @since 200.4.1
	 *
	 * @param   Registry          $registry  Реестр модулей для путей Models.
	 * @param   DatabaseGateway   $gateway   Шлюз: менеджер БД и SQL без ORM.
	 */
	public function __construct(
		private readonly Registry        $registry,
		private readonly DatabaseGateway $gateway,
	) {}

	/**
	 * Приводит схему к актуальному виду и возвращает скомпилированный массив схемы.
	 *
	 * @since 200.4.1
	 *
	 * @return array<string, mixed> Схема ORM.
	 *
	 * @throws \RuntimeException Если синхронизация схемы упала неустранимо.
	 */
	public function ensureSchemaReady(): array {
		Paths::register();

		$lock = $this->acquireLock();

		try {
			return $this->ensureSchemaReadyLocked();
		} finally {
			$this->releaseLock($lock);
		}
	}

	/**
	 * @return array<string, mixed>
	 */
	private function ensureSchemaReadyLocked(): array {
		$this->gateway->databaseManager();

		$migrationsDir = Paths::src() . '/database/migrations';
		$fingerprint   = $this->quickModelsSignature();
		$bootstrap     = $this->ormSchemaBootstrapNeeded();
		$needPending   = $this->migrationFilesNeedApply($migrationsDir);
		$cached        = $bootstrap ? null : $this->loadCachedSchema($fingerprint);

		if($cached !== null && !$needPending && !$bootstrap && $this->schemaTablesExist($cached)) {
			return $cached;
		}

		$migrator = $this->createMigratorForDirectory($migrationsDir);
		$this->runPending($migrator);

		$fingerprint = $this->quickModelsSignature();
		$bootstrap   = $this->ormSchemaBootstrapNeeded();
		$cached      = $bootstrap ? null : $this->loadCachedSchema($fingerprint);

		$debug = (bool) DevCraftConfig::get('debug', false);

		if($cached !== null && !$bootstrap && !$debug && $this->schemaTablesExist($cached)) {
			return $cached;
		}

		$path_resolver     = new EntityPathResolver($this->registry);
		$model_directories = $path_resolver->entityModelDirectories();
		$registry          = new SchemaRegistry($this->gateway->databaseManager());

		if($debug) {
			$schema_array = $this->compileSchema($registry, $model_directories, $migrator, false, true);
			$this->storeCachedSchema($fingerprint, $schema_array);

			return $schema_array;
		}

		$before       = $this->migrationPhpFiles($migrationsDir);
		$schema_array = $this->compileSchema($registry, $model_directories, $migrator, true, false);
		$after        = $this->migrationPhpFiles($migrationsDir);
		$wroteNew     = array_diff($after, $before) !== [];

		if($wroteNew) {
			$this->runPending($migrator);
		}

		// Таблиц нет, а create уже «выполнен» или файл не появился — только миграции, не SyncTables.
		if(!$this->schemaTablesExist($schema_array)) {
			$this->unmarkCreateMigrationsWithAllTablesMissing($migrator);
			$registry     = new SchemaRegistry($this->gateway->databaseManager());
			$schema_array = $this->compileSchema($registry, $model_directories, $migrator, true, false);
			$this->runPending($migrator);
		}

		$this->storeCachedSchema($fingerprint, $schema_array);

		return $schema_array;
	}

	/**
	 * @return resource|null
	 */
	private function acquireLock() {
		$dir = rtrim(Paths::cache(), '/\\');

		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return null;
		}

		$path   = $dir . '/cycle_orm_schema.lock';
		$handle = @fopen($path, 'c');

		if($handle === false) {
			return null;
		}

		flock($handle, LOCK_EX);

		return $handle;
	}

	/**
	 * @param   resource|null  $handle
	 */
	private function releaseLock(mixed $handle): void {
		if(!is_resource($handle)) {
			return;
		}

		flock($handle, LOCK_UN);
		fclose($handle);
	}

	private function runPending(Migrations\Migrator $migrator): void {
		$capsule = new Capsule($this->gateway->databaseManager()->database());
		$this->skipCreateMigrationsForExistingTables($migrator);

		try {
			while($migrator->run($capsule) !== null) {
				$this->skipCreateMigrationsForExistingTables($migrator);
			}
		} catch(\Throwable) {
			$this->skipCreateMigrationsForExistingTables($migrator);

			try {
				while($migrator->run($capsule) !== null) {
					$this->skipCreateMigrationsForExistingTables($migrator);
				}
			} catch(\Throwable) {
				// Таблица уже есть / дубликат create — схема всё равно собирается дальше.
			}
		}
	}

	private function ormSchemaBootstrapNeeded(): bool {
		foreach(['devcraft_migrations', 'devcraft_logs', 'devcraft_composer_data'] as $suffix) {
			if(!$this->tableExists(PREFIX . '_' . $suffix)) {
				return true;
			}
		}

		return false;
	}

	private function quickModelsSignature(): string {
		$parts   = [];
		$modules = Paths::src() . '/modules';

		foreach(glob($modules . '/*/Models') ?: [] as $dir) {
			$parts[] = $dir . ':' . (string) (@filemtime($dir) ?: 0);

			foreach(glob($dir . '/*.php') ?: [] as $file) {
				$parts[] = $file . ':' . (string) (@filemtime($file) ?: 0);
			}
		}

		$composerModels = Paths::src() . '/classes/Composer/Models';

		if(is_dir($composerModels)) {
			$parts[] = $composerModels . ':' . (string) (@filemtime($composerModels) ?: 0);

			foreach(glob($composerModels . '/*.php') ?: [] as $file) {
				$parts[] = $file . ':' . (string) (@filemtime($file) ?: 0);
			}
		}

		sort($parts);

		return hash('sha256', implode('|', $parts));
	}

	private function migrationFilesNeedApply(string $migrationsDir): bool {
		$fileCount = count($this->migrationPhpFiles($migrationsDir));

		if($fileCount === 0) {
			return false;
		}

		$migrationTable = PREFIX . '_devcraft_migrations';

		if(!$this->tableExists($migrationTable)) {
			return true;
		}

		$result   = $this->gateway->query("SELECT COUNT(*) AS total FROM `{$migrationTable}`")->fetchAll();
		$executed = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

		return $fileCount > $executed;
	}

	/**
	 * @return list<string>
	 */
	private function migrationPhpFiles(string $migrationsDir): array {
		$files = glob($migrationsDir . '/*.php') ?: [];
		sort($files);

		return array_values($files);
	}

	/**
	 * @return array<string, mixed>|null
	 */
	private function loadCachedSchema(string $fingerprint): ?array {
		$path = rtrim(Paths::cache(), '/\\') . '/cycle_orm_schema.ser';

		if(!is_file($path)) {
			return null;
		}

		$raw = @file_get_contents($path);

		if($raw === false || $raw === '') {
			return null;
		}

		/** @var mixed $payload */
		$payload = @unserialize($raw);

		if(!is_array($payload)
			|| ($payload['fingerprint'] ?? null) !== $fingerprint
			|| !is_array($payload['schema'] ?? null)
		) {
			return null;
		}

		/** @var array<string, mixed> $schema */
		$schema = $payload['schema'];

		return $schema;
	}

	/**
	 * @param array<string, mixed> $schema
	 */
	private function storeCachedSchema(string $fingerprint, array $schema): void {
		$dir = rtrim(Paths::cache(), '/\\');

		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return;
		}

		$path = $dir . '/cycle_orm_schema.ser';
		@file_put_contents($path, serialize([
			'fingerprint' => $fingerprint,
			'schema'      => $schema,
		]), LOCK_EX);
	}

	private function createMigratorForDirectory(string $directory): Migrations\Migrator {
		if(!is_dir($directory)) {
			@mkdir($directory, 0775, true);
		}

		$migrator_config = new MigrationConfig(
			[
				'directory' => $directory,
				'table'     => 'devcraft_migrations',
				'safe'      => true,
			],
		);

		$migrator = new Migrations\Migrator(
			$migrator_config,
			$this->gateway->databaseManager(),
			new Migrations\FileRepository($migrator_config),
		);
		$migrator->configure();

		return $migrator;
	}

	/**
	 * @param   list<string>  $model_directories
	 *
	 * @return array<string, mixed>
	 */
	private function compileSchema(
		SchemaRegistry $registry,
		array $model_directories,
		Migrations\Migrator $migrator,
		bool $generateMigrations,
		bool $syncTables,
	): array {
		if($model_directories === []) {
			$class_locator = new ClassLocator(new \ArrayIterator([]));
		} else {
			$finder        = new Finder();
			$files         = $finder->files()->in($model_directories);
			$class_locator = new ClassLocator($files);
		}

		$generators = [
			new Generator\ResetTables(),
			new Annotated\Embeddings(new TokenizerEmbeddingLocator($class_locator)),
			new Annotated\Entities(new TokenizerEntityLocator($class_locator)),
			new Annotated\TableInheritance(),
			new Annotated\MergeColumns(),
			new Generator\GenerateRelations(),
			new Generator\GenerateModifiers(),
			new Generator\ValidateEntities(),
			new Generator\RenderTables(),
			new Generator\RenderRelations(),
			new Generator\RenderModifiers(),
			new Generator\ForeignKeys(),
			new Annotated\MergeIndexes(),
		];

		if($generateMigrations) {
			$generators[] = new GenerateMigrations(
				$migrator->getRepository(),
				$migrator->getConfig(),
				new SingleFileStrategy(
					$migrator->getConfig(),
					new NameBasedOnChangesGenerator(),
				),
			);
		}

		if($syncTables) {
			$generators[] = new SyncTables();
		}

		$generators[] = new Generator\GenerateTypecast();

		$compiler = new Compiler();

		return $compiler->compile($registry, $generators);
	}

	private function skipCreateMigrationsForExistingTables(Migrations\Migrator $migrator): void {
		$migrationTable = PREFIX . '_devcraft_migrations';

		if(!$this->tableExists($migrationTable)) {
			return;
		}

		foreach($migrator->getMigrations() as $migration) {
			$state = $migration->getState();

			if($state->getStatus() === State::STATUS_EXECUTED) {
				continue;
			}

			$migrationName = $state->getName();

			if(str_contains($migrationName, '_add_fk_') || str_contains($migrationName, '_add_foreign_')) {
				// Cycle пытается вешать FK INT UNSIGNED → BIGINT (id AbstractEntity) — MySQL errno 150.
				$this->markMigrationAsExecuted($migrationTable, $migrationName);
				continue;
			}

			$createTables = $this->createTableNamesFromMigration($migrationName);

			if($createTables !== []) {
				$allPresent = true;

				foreach($createTables as $tableName) {
					if(!$this->tableExists($tableName)) {
						$allPresent = false;
						break;
					}
				}

				if($allPresent) {
					$this->markMigrationAsExecuted($migrationTable, $migrationName);
				}

				continue;
			}

			if(preg_match('/_change_dle_(.+?)_add_(.+)$/', $migrationName, $change) === 1) {
				$tableName = PREFIX . '_' . (string) $change[1];
				$ops       = 'add_' . $change[2];

				if($this->tableExists($tableName) && $this->changeMigrationFirstOpExists($tableName, $ops)) {
					$this->markMigrationAsExecuted($migrationTable, $migrationName);
				}
			}
		}
	}

	/**
	 * Имена таблиц с префиксом из сегментов `_create_` в имени миграции.
	 *
	 * @return list<string>
	 */
	private function createTableNamesFromMigration(string $migrationName): array {
		if(preg_match_all('/_create_(?:dle_)?([a-z0-9_]+?)(?=_create_|_change_|$)/', $migrationName, $matches) < 1) {
			return [];
		}

		$tables = [];

		foreach($matches[1] as $key) {
			$key = (string) $key;

			if($key === '') {
				continue;
			}

			$tables[] = PREFIX . '_' . $key;
		}

		return array_values(array_unique($tables));
	}

	/**
	 * Все таблицы из скомпилированной схемы Cycle существуют в БД.
	 *
	 * @param   array<string, mixed>  $schema
	 */
	private function schemaTablesExist(array $schema): bool {
		foreach($schema as $def) {
			if(!is_array($def)) {
				continue;
			}

			$table = $def[\Cycle\ORM\SchemaInterface::TABLE] ?? null;

			if(!is_string($table) || $table === '') {
				continue;
			}

			$full = str_starts_with($table, PREFIX . '_')? $table : PREFIX . '_' . $table;

			if(!$this->tableExists($full)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Снимает отметку «выполнена» с create-миграции, если в БД нет ни одной её таблицы
	 * (безопасный повторный накат). Частично созданные multi-create не трогаем — допишет GenerateMigrations.
	 */
	private function unmarkCreateMigrationsWithAllTablesMissing(Migrations\Migrator $migrator): void {
		$migrationTable = PREFIX . '_devcraft_migrations';

		if(!$this->tableExists($migrationTable)) {
			return;
		}

		foreach($migrator->getMigrations() as $migration) {
			$state = $migration->getState();

			if($state->getStatus() !== State::STATUS_EXECUTED) {
				continue;
			}

			$tables = $this->createTableNamesFromMigration($state->getName());

			if($tables === []) {
				continue;
			}

			$anyPresent = false;

			foreach($tables as $tableName) {
				if($this->tableExists($tableName)) {
					$anyPresent = true;
					break;
				}
			}

			if(!$anyPresent) {
				$this->unmarkMigrationAsExecuted($migrationTable, $state->getName());
			}
		}
	}

	private function changeMigrationFirstOpExists(string $tableName, string $ops): bool {
		if(preg_match('/^add_index_([a-z0-9_]+?)(?=_add_|$)/', $ops, $m) === 1) {
			return $this->indexExists($tableName, $m[1]);
		}

		if(preg_match('/^add_([a-z0-9_]+?)(?=_add_|$)/', $ops, $m) === 1) {
			return $this->columnExists($tableName, $m[1]);
		}

		return false;
	}

	private function indexExists(string $tableName, string $indexName): bool {
		$result = $this->gateway->query(
			'SELECT COUNT(*) AS total FROM information_schema.statistics WHERE table_schema = :schema AND table_name = :table AND index_name = :idx',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
				'idx'    => $indexName,
			],
		)->fetchAll();

		return isset($result[0]['total']) && (int) $result[0]['total'] > 0;
	}

	private function columnExists(string $tableName, string $columnName): bool {
		$result = $this->gateway->query(
			'SELECT COUNT(*) AS total FROM information_schema.columns WHERE table_schema = :schema AND table_name = :table AND column_name = :col',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
				'col'    => $columnName,
			],
		)->fetchAll();

		return isset($result[0]['total']) && (int) $result[0]['total'] > 0;
	}

	private function tableExists(string $tableName): bool {
		$result = $this->gateway->query(
			'SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = :schema AND table_name = :table',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
			],
		)->fetchAll();

		$total = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

		return $total > 0;
	}

	private function markMigrationAsExecuted(string $migrationTable, string $migrationName): void {
		$existsResult = $this->gateway->query(
			"SELECT COUNT(*) AS total FROM `{$migrationTable}` WHERE `migration` = :migration",
			['migration' => $migrationName],
		)->fetchAll();

		$exists = isset($existsResult[0]['total']) ? (int) $existsResult[0]['total'] : 0;

		if($exists > 0) {
			return;
		}

		$this->gateway->query(
			"INSERT INTO `{$migrationTable}` (`migration`, `time_executed`, `created_at`) VALUES (:migration, NOW(), NOW())",
			['migration' => $migrationName],
		);
	}

	private function unmarkMigrationAsExecuted(string $migrationTable, string $migrationName): void {
		$this->gateway->query(
			"DELETE FROM `{$migrationTable}` WHERE `migration` = :migration",
			['migration' => $migrationName],
		);
	}

}
