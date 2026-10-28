<?php
//===============================================================
// Файл: SchemaSyncService.php                                  =
// Путь: devcraft/src/classes/Database/SchemaSyncService.php    =
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

use Cycle\Annotated;
use Cycle\Migrations;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\State;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry as SchemaRegistry;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Module\ModulePsr4Registrar;
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

	/** @var list<string> Файлы миграций, созданные последней компиляцией схемы. */
	private array $lastWritten = [];

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
	 * Сбрасывает запас копии схемы и заново приводит таблицы к моделям.
	 *
	 * @since 200.4.2
	 *
	 * @return array{present: int, missing: list<string>} Сколько таблиц на месте и какие ещё отсутствуют.
	 */
	public function forceEnsure(): array {
		$this->dropCachedSchema();

		$schema  = $this->ensureSchemaReady();
		$missing = $this->missingSchemaTables($schema);

		return [
			'present' => count($schema) - count($missing),
			'missing' => $missing,
		];
	}

	/**
	 * Создаёт файлы миграций по отличиям моделей от базы (как `makemigrations`), ничего не накатывая.
	 *
	 * @since 200.4.2
	 *
	 * @return list<string> Пути созданных файлов.
	 *
	 * @throws \RuntimeException Если есть ожидающие миграции: сравнение с базой дало бы их копии.
	 */
	public function generateMigrations(): array {
		Paths::register();

		$lock = $this->acquireLock();

		try {
			$this->gateway->databaseManager();
			ModulePsr4Registrar::registerAll();

			$path_resolver = new EntityPathResolver($this->registry);

			foreach($this->migrator($path_resolver)->getMigrations() as $migration) {
				if($migration->getState()->getStatus() === State::STATUS_PENDING) {
					throw new \RuntimeException(__('Сначала накатите ожидающие миграции'));
				}
			}

			$this->compileSchema(
				new SchemaRegistry($this->gateway->databaseManager()),
				$path_resolver->entityModelDirectories(),
				$path_resolver,
				true,
				false,
			);
			$this->dropCachedSchema();

			return $this->lastWritten;
		} finally {
			$this->releaseLock($lock);
		}
	}

	/**
	 * Накатывает все ожидающие миграции (уже существующие таблицы помечаются выполненными).
	 *
	 * @since 200.4.2
	 *
	 * @return int Сколько миграций стало выполненными.
	 */
	public function applyPending(): int {
		Paths::register();

		$lock = $this->acquireLock();

		try {
			$migrator = $this->migrator();
			$before   = $this->pendingCount($migrator);
			$this->runPending($migrator);
			$this->dropCachedSchema();

			return $before - $this->pendingCount($migrator);
		} finally {
			$this->releaseLock($lock);
		}
	}

	/**
	 * Помечает миграцию выполненной без запуска SQL.
	 *
	 * @since 200.4.2
	 *
	 * @param   State  $state  Состояние миграции из Migrator.
	 */
	public function markExecuted(State $state): void {
		$this->markMigrationAsExecuted(PREFIX . '_devcraft_migrations', $state);
		$this->dropCachedSchema();
	}

	/**
	 * Удаляет из учёта записи, для которых нет файла ни в одном каталоге `Migrations`.
	 *
	 * Таблицы данных не трогает. Так убирается учёт удалённых легаси-файлов `database/migrations`.
	 *
	 * @since 200.4.2
	 *
	 * @param   Migrations\Migrator  $migrator  Migrator над текущими каталогами.
	 *
	 * @return int Сколько записей удалено.
	 */
	public function pruneRecordsWithoutFiles(Migrations\Migrator $migrator): int {
		$table = PREFIX . '_devcraft_migrations';

		if(!$this->tableExists($table)) {
			return 0;
		}

		$known = [];

		foreach($migrator->getMigrations() as $migration) {
			$known[$migration->getState()->getName()] = true;
		}

		$removed = 0;

		foreach($this->gateway->query("SELECT `id`, `migration` FROM `{$table}`")->fetchAll() as $row) {
			if(isset($known[(string) $row['migration']])) {
				continue;
			}

			$this->gateway->query("DELETE FROM `{$table}` WHERE `id` = :id", ['id' => (int) $row['id']]);
			$removed++;
		}

		return $removed;
	}

	/**
	 * Удаляет запас копии скомпилированной схемы.
	 *
	 * @since 200.4.2
	 */
	public function dropCachedSchema(): void {
		$path = rtrim(Paths::cache(), '/\\') . '/cycle_orm_schema.ser';

		if(is_file($path)) {
			@unlink($path);
		}
	}

	private function pendingCount(Migrations\Migrator $migrator): int {
		$count = 0;

		foreach($migrator->getMigrations() as $migration) {
			if($migration->getState()->getStatus() === State::STATUS_PENDING) {
				$count++;
			}
		}

		return $count;
	}

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
		ModulePsr4Registrar::registerAll();

		$path_resolver = new EntityPathResolver($this->registry);
		$migrationDirs = $path_resolver->migrationDirectories();
		$fingerprint   = $this->quickModelsSignature();
		$bootstrap     = $this->ormSchemaBootstrapNeeded();
		$needPending   = $this->migrationFilesNeedApply($migrationDirs);
		$cached        = $bootstrap ? null : $this->loadCachedSchema($fingerprint);

		if($cached !== null && !$needPending && !$bootstrap && $this->schemaTablesExist($cached)) {
			return $cached;
		}

		$migrator = $this->migrator($path_resolver);
		$this->pruneRecordsWithoutFiles($migrator);
		$this->runPending($migrator);

		$fingerprint = $this->quickModelsSignature();
		$bootstrap   = $this->ormSchemaBootstrapNeeded();
		$cached      = $bootstrap ? null : $this->loadCachedSchema($fingerprint);

		$debug = (bool) DevCraftConfig::get('debug', false);

		if($cached !== null && !$bootstrap && !$debug && $this->schemaTablesExist($cached)) {
			return $cached;
		}

		$model_directories = $path_resolver->entityModelDirectories();
		$registry          = new SchemaRegistry($this->gateway->databaseManager());

		if($debug) {
			$schema_array = $this->compileSchema($registry, $model_directories, $path_resolver, false, true);
			$this->storeCachedSchema($fingerprint, $schema_array);

			return $schema_array;
		}

		$schema_array = $this->compileSchema($registry, $model_directories, $path_resolver, true, false);

		if($this->lastWritten !== []) {
			$this->runPending($migrator);
		}

		// Таблиц нет, а create уже «выполнен» или файл не появился — только миграции, не SyncTables.
		if(!$this->schemaTablesExist($schema_array)) {
			$this->unmarkCreateMigrationsWithAllTablesMissing($migrator);
			$registry     = new SchemaRegistry($this->gateway->databaseManager());
			$schema_array = $this->compileSchema($registry, $model_directories, $path_resolver, true, false);
			$this->runPending($migrator);
		}

		// Файлы миграций не создали таблицы (чистая база или ложная отметка «выполнена»).
		// Один проход «на лету», не на каждый запрос.
		if(!$this->schemaTablesExist($schema_array)) {
			$registry     = new SchemaRegistry($this->gateway->databaseManager());
			$schema_array = $this->compileSchema($registry, $model_directories, $path_resolver, false, true);
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
		$migrator->configure();
		$this->skipCreateMigrationsForExistingTables($migrator);
		// Create недостающих таблиц — раньше старых change: иначе change падает и очередь стоит.
		$this->runMissingTableCreates($migrator, $capsule);

		$guard = 0;

		while($guard < 30) {
			$guard++;

			try {
				while($migrator->run($capsule) !== null) {
					$this->skipCreateMigrationsForExistingTables($migrator);
				}

				return;
			} catch(\Throwable $e) {
				$this->skipCreateMigrationsForExistingTables($migrator);

				if(!$this->markAlreadyAppliedMigrationExecuted($migrator, $e)) {
					return;
				}
			}
		}
	}

	/**
	 * Накатывает create, у которых в БД ещё нет ни одной таблицы, не дожидаясь более старых change.
	 */
	private function runMissingTableCreates(Migrations\Migrator $migrator, Capsule $capsule): void {
		foreach($migrator->getMigrations() as $migration) {
			if($migration->getState()->getStatus() !== State::STATUS_PENDING) {
				continue;
			}

			$tables = $this->createTableNamesFromMigration($migration->getState()->getName());

			if($tables === []) {
				continue;
			}

			foreach($tables as $tableName) {
				if($this->tableExists($tableName)) {
					continue 2;
				}
			}

			try {
				$capsule->getDatabase()->transaction(
					static function () use ($migration, $capsule): void {
						$migration->withCapsule($capsule)->up();
					},
				);
			} catch(\Throwable $e) {
				if(!$this->migrationFailureAlreadyApplied($e)) {
					continue;
				}
			}

			$this->markMigrationAsExecuted(
				PREFIX . '_devcraft_migrations',
				$migration->getState(),
			);
		}
	}

	/**
	 * Снимает с очереди миграцию, которая упала потому что таблица или колонка уже есть.
	 */
	private function markAlreadyAppliedMigrationExecuted(Migrations\Migrator $migrator, \Throwable $e): bool {
		if(!$this->migrationFailureAlreadyApplied($e)) {
			return false;
		}

		foreach($migrator->getMigrations() as $migration) {
			if($migration->getState()->getStatus() !== State::STATUS_PENDING) {
				continue;
			}

			$this->markMigrationAsExecuted(
				PREFIX . '_devcraft_migrations',
				$migration->getState(),
			);

			return true;
		}

		return false;
	}

	private function migrationFailureAlreadyApplied(\Throwable $e): bool {
		$current = $e;

		while($current !== null) {
			$message = $current->getMessage();

			if(
				str_contains($message, '42S01')
				|| str_contains($message, '42S21')
				|| str_contains($message, '1050')
				|| str_contains($message, '1060')
				|| str_contains($message, 'already exists')
			) {
				return true;
			}

			$current = $current->getPrevious();
		}

		return false;
	}

	private function ormSchemaBootstrapNeeded(): bool {
		foreach(['devcraft_migrations', 'devcraft_logs', 'devcraft_composer_data', 'dc_public_headers', 'dc_public_assets'] as $suffix) {
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
			$parts[] = $composerModels . ':' . (@filemtime($composerModels) ?: 0);

			foreach(glob($composerModels . '/*.php') ?: [] as $file) {
				$parts[] = $file . ':' . (@filemtime($file) ?: 0);
			}
		}

		sort($parts);

		return hash('sha256', implode('|', $parts));
	}

	/**
	 * @param   list<string>  $migrationDirs
	 */
	private function migrationFilesNeedApply(array $migrationDirs): bool {
		$fileCount = count($this->migrationPhpFiles($migrationDirs));

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
	 * @param   list<string>  $migrationDirs
	 *
	 * @return list<string>
	 */
	private function migrationPhpFiles(array $migrationDirs): array {
		$files = [];

		foreach($migrationDirs as $dir) {
			array_push($files, ...(glob($dir . '/*.php') ?: []));
		}

		sort($files);

		return $files;
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

		// version=2: старые запасы без моделей Admin отбрасываем один раз.
		if(!is_array($payload)
			|| (int) ($payload['version'] ?? 0) < 2
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
			'version'     => 2,
			'fingerprint' => $fingerprint,
			'schema'      => $schema,
		]), LOCK_EX);
	}

	/**
	 * Migrator Cycle над каталогами `Migrations` всех модулей (Admin — основной, остальные — vendorDirectories).
	 *
	 * @since 200.4.2
	 *
	 * @param   EntityPathResolver|null  $path_resolver  Резолвер путей; по умолчанию из реестра.
	 *
	 * @return Migrations\Migrator Настроенный Migrator.
	 */
	public function migrator(?EntityPathResolver $path_resolver = null): Migrations\Migrator {
		$directories = ($path_resolver ?? new EntityPathResolver($this->registry))->migrationDirectories();

		$migrator_config = new MigrationConfig(
			[
				'directory'         => array_shift($directories),
				'vendorDirectories' => $directories,
				'table'             => 'devcraft_migrations',
				'safe'              => true,
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
		EntityPathResolver $path_resolver,
		bool $generateMigrations,
		bool $syncTables,
	): array {
		$this->lastWritten = [];
		$model_directories = $this->normalizeModelDirectories($model_directories);
		ModulePsr4Registrar::registerAll();

		// ClassLocator молча пропускает класс, который не загрузился — подгружаем файлы заранее.
		foreach($model_directories as $directory) {
			foreach(glob(rtrim($directory, '/\\') . '/*.php') ?: [] as $file) {
				if(!is_file($file)) {
					continue;
				}

				try {
					require_once $file;
				} catch(\Throwable) {
					continue;
				}
			}
		}

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

		// По файлу на таблицу, в Migrations модуля-владельца: общий файл откатывает создание журнала
		// и заголовков, если соседняя таблица в том же шаге уже есть.
		$migrationsGenerator = $generateMigrations? new ModuleMigrationsGenerator($path_resolver) : null;

		if($migrationsGenerator !== null) {
			$generators[] = $migrationsGenerator;
		}

		if($syncTables) {
			$generators[] = new SyncTables();
		}

		$generators[] = new Generator\GenerateTypecast();

		$compiler = new Compiler();
		$schema   = $compiler->compile($registry, $generators);

		$this->lastWritten = $migrationsGenerator?->written() ?? [];

		return $schema;
	}

	/**
	 * Оставляет только существующие каталоги моделей и убирает дубликаты.
	 *
	 * @param   list<string>  $directories
	 *
	 * @return list<string>
	 */
	private function normalizeModelDirectories(array $directories): array {
		$normalized = [];

		foreach($directories as $directory) {
			if(!is_string($directory) || $directory === '') {
				continue;
			}

			$real = realpath($directory);

			if($real === false || !is_dir($real)) {
				continue;
			}

			$normalized[$real] = $real;
		}

		return array_values($normalized);
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
			$createTables  = $this->createTableNamesFromMigration($migrationName);
			$missing       = 0;

			foreach($createTables as $tableName) {
				if(!$this->tableExists($tableName)) {
					$missing++;
				}
			}

			if(
				$missing === 0
				&& (str_contains($migrationName, '_add_fk_') || str_contains($migrationName, '_add_foreign_'))
			) {
				$this->markMigrationAsExecuted($migrationTable, $state);
				continue;
			}

			if($createTables !== []) {
				if($missing < count($createTables)) {
					$this->markMigrationAsExecuted($migrationTable, $state);
				}

				continue;
			}

			if(preg_match('/_change_dle_(.+?)_add_(.+)$/', $migrationName, $change) === 1) {
				$tableName = PREFIX . '_' . (string) $change[1];
				$ops       = 'add_' . $change[2];

				if($this->tableExists($tableName) && $this->changeMigrationFirstOpExists($tableName, $ops)) {
					$this->markMigrationAsExecuted($migrationTable, $state);
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
	/**
	 * @param   array<string, mixed>  $schema
	 *
	 * @return list<string>
	 */
	private function missingSchemaTables(array $schema): array {
		$missing = [];

		foreach($schema as $def) {
			if(!is_array($def)) {
				continue;
			}

			$table = $def[SchemaInterface::TABLE] ?? null;

			if(!is_string($table) || $table === '') {
				continue;
			}

			$full = str_starts_with($table, PREFIX . '_') ? $table : PREFIX . '_' . $table;

			if(!$this->tableExists($full)) {
				$missing[] = $full;
			}
		}

		return $missing;
	}

	private function schemaTablesExist(array $schema): bool {
		foreach($schema as $def) {
			if(!is_array($def)) {
				continue;
			}

			$table = $def[SchemaInterface::TABLE] ?? null;

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
	 * (безопасный повторный накат). Если часть таблиц уже есть, файл не запускают снова.
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

	private function markMigrationAsExecuted(string $migrationTable, State $state): void {
		$migrationName = $state->getName();
		$createdAt     = $state->getTimeCreated()->format('Y-m-d H:i:s');
		$existsResult  = $this->gateway->query(
			"SELECT COUNT(*) AS total FROM `{$migrationTable}` WHERE `migration` = :migration AND `created_at` = :created_at",
			[
				'migration'  => $migrationName,
				'created_at' => $createdAt,
			],
		)->fetchAll();

		$exists = isset($existsResult[0]['total']) ? (int) $existsResult[0]['total'] : 0;

		if($exists > 0) {
			return;
		}

		$this->gateway->query(
			"INSERT INTO `{$migrationTable}` (`migration`, `time_executed`, `created_at`) VALUES (:migration, :executed, :created_at)",
			[
				'migration'  => $migrationName,
				'executed'   => date('Y-m-d H:i:s'),
				'created_at' => $createdAt,
			],
		);
	}

	private function unmarkMigrationAsExecuted(string $migrationTable, string $migrationName): void {
		$this->gateway->query(
			"DELETE FROM `{$migrationTable}` WHERE `migration` = :migration",
			['migration' => $migrationName],
		);
	}

}
