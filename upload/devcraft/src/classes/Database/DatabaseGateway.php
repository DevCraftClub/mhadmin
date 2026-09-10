<?php
//===============================================================
// Файл: DatabaseGateway.php                                    =
// Путь: devcraft/src/classes/Database/DatabaseGateway.php      =
// Последнее изменение: 2026-06-13 19:29:35                     =
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

use Cycle\ORM;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\Migrations;
use Cycle\Database\Config;
use Cycle\Schema\Compiler;
use Cycle\Migrations\State;
use Cycle\Schema\Generator;
use Cycle\ORM\EntityManager;
use Cycle\Migrations\Capsule;
use DevCraft\Core\Config\Paths;
use Spiral\Pagination\Paginator;
use Cycle\ORM\RepositoryInterface;
use DevCraft\Core\Module\Registry;
use Spiral\Tokenizer\ClassLocator;
use Cycle\Database\DatabaseManager;
use Symfony\Component\Finder\Finder;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\StatementInterface;
use Cycle\ORM\Transaction\StateInterface;
use Cycle\Schema\Registry as SchemaRegistry;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Symfony\Component\DependencyInjection\Container;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Schema\Generator\Migrations\Strategy\SingleFileStrategy;
use Cycle\Schema\Generator\Migrations\NameBasedOnChangesGenerator;

/**
 * Шлюз доступа к базе данных через Cycle ORM.
 *
 * @package    DevCraft
 * @since      171.3.0
 * @subpackage Core.Database
 */
final class DatabaseGateway {

	/**
	 * Экземпляр ORM, инициализируемый лениво.
	 *
	 * @since 171.3.0
	 *
	 * @var ORM\ORM|null
	 */
	private ?ORM\ORM $orm = NULL;

	/**
	 * Менеджер подключений Cycle Database.
	 *
	 * @since 171.3.0
	 *
	 * @var DatabaseManager|null
	 */
	private ?DatabaseManager $database_manager = NULL;

	/**
	 * Конфигурация подключения к базе данных.
	 *
	 * @since 171.3.0
	 *
	 * @var Config\DatabaseConfig|null
	 */
	private ?Config\DatabaseConfig $database_config = NULL;

	/**
	 * Менеджер сущностей ORM для persist/delete.
	 *
	 * @since 171.3.0
	 *
	 * @var EntityManager|null
	 */
	private ?EntityManager $entity_manager = NULL;

	/**
	 * Создаёт шлюз с реестром модулей для разрешения путей сущностей.
	 *
	 * @since 171.3.0
	 *
	 * @param   Registry  $registry  Реестр DevCraft-модулей.
	 *
	 * @example
	 *     $db = new DatabaseGateway(Application::instance()->registry());
	 */
	public function __construct(
		private readonly Registry $registry,
	) {}

	/**
	 * Возвращает экземпляр ORM, создавая его при необходимости.
	 *
	 * @since 171.3.0
	 *
	 * @return ORM\ORM Экземпляр ORM.
	 *
	 * @example
	 *     $orm = $db->getOrm();
	 */
	public function getOrm(): ORM\ORM {
		return $this->generateOrm();
	}

	/**
	 * Инициализирует менеджер сущностей на основе текущего ORM.
	 *
	 * @since 171.3.0
	 */
	public function setManager(): void {
		$this->entity_manager = new EntityManager($this->getOrm());
	}

	/**
	 * Возвращает менеджер сущностей, создавая его при первом обращении.
	 *
	 * @since 171.3.0
	 *
	 * @return EntityManager Менеджер сущностей ORM.
	 *
	 * @example
	 *     $state = $db->getManager()->persist($entity)->run();
	 */
	public function getManager(): EntityManager {
		if($this->entity_manager === NULL) {
			$this->setManager();
		}

		return $this->entity_manager;
	}

	/**
	 * Возвращает репозиторий для указанной сущности или класса.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity  Объект сущности или FQCN класса.
	 *
	 * @return RepositoryInterface Репозиторий Cycle ORM.
	 *
	 * @example
	 *     $repo = $db->repository(LogRecord::class);
	 */
	public function repository(object|string $entity): RepositoryInterface {
		$entity_class = is_string($entity)? $entity : $entity::class;

		return $this->getOrm()->getRepository($entity_class);
	}

	/**
	 * Получает сущность по первичному ключу.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity  Объект сущности или FQCN класса.
	 * @param   int            $pk      Первичный ключ записи.
	 *
	 * @return object|null Найденная сущность или null.
	 *
	 * @example
	 *     $record = $db->get(LogRecord::class, 42);
	 */
	public function get(object|string $entity, int $pk): ?object {
		return $this->repository($entity)->findByPK($pk);
	}

	/**
	 * Возвращает все записи указанной сущности.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity  Объект сущности или FQCN класса.
	 *
	 * @return array<int, object> Массив сущностей.
	 *
	 * @example
	 *     $rows = $db->getAll(LogRecord::class);
	 */
	public function getAll(object|string $entity): array {
		return $this->repository($entity)->findAll();
	}

	/**
	 * Удаляет сущность по первичному ключу.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity  Объект сущности или FQCN класса.
	 * @param   int            $pk      Первичный ключ записи.
	 *
	 * @return StateInterface Состояние завершённой транзакции.
	 *
	 * @throws \RuntimeException|\Throwable Если объект с указанным ключом не найден.
	 *
	 * @example
	 *     $db->delete(LogRecord::class, 42);
	 */
	public function delete(object|string $entity, int $pk): StateInterface {
		$object = $this->get($entity, $pk);

		if($object === NULL) {
			throw new \RuntimeException(__('Объект не найден: {object}', ['{object}' => $entity]));
		}

		return $this->getManager()->delete($object)->run();
	}

	/**
	 * Сохраняет сущность и возвращает состояние транзакции.
	 *
	 * @since 171.3.0
	 *
	 * @param   object  $entity  Сущность для сохранения.
	 *
	 * @return StateInterface Состояние завершённой транзакции.
	 *
	 * @example
	 *     $db->run($logRecord);
	 */
	public function run(object $entity): StateInterface {
		if(method_exists($entity, 'beforeSave')) {
			$entity->beforeSave();
		}

		return $this->getManager()->persist($entity)->run();
	}

	/**
	 * Создаёт новую запись сущности в базе данных.
	 *
	 * @since 171.3.0
	 *
	 * @param   object  $entity  Сущность для создания.
	 *
	 * @return StateInterface Состояние завершённой транзакции.
	 *
	 * @example
	 *     $db->create($logRecord);
	 */
	public function create(object $entity): StateInterface {
		return $this->run($entity);
	}

	/**
	 * Обновляет существующую запись сущности в базе данных.
	 *
	 * @since 171.3.0
	 *
	 * @param   object  $entity  Сущность для обновления.
	 *
	 * @return StateInterface Состояние завершённой транзакции.
	 *
	 * @example
	 *     $db->update($logRecord);
	 */
	public function update(object $entity): StateInterface {
		return $this->run($entity);
	}

	/**
	 * Создаёт или обновляет сущность в зависимости от наличия первичного ключа.
	 *
	 * @since 173.3.4
	 *
	 * @param   object  $entity  Сущность для сохранения.
	 *
	 * @return StateInterface Состояние завершённой транзакции.
	 *
	 * @example
	 *     $db->createOrUpdate($logRecord);
	 */
	public function createOrUpdate(object $entity): StateInterface {
		return $this->run($entity);
	}

	/**
	 * Возвращает интерфейс подключения Cycle Database.
	 *
	 * @since 171.3.0
	 *
	 * @return DatabaseInterface Активное подключение к БД.
	 *
	 * @example
	 *     $dbal = $db->connection();
	 */
	public function connection(): DatabaseInterface {
		return $this->generateManager()->database();
	}

	/**
	 * Выполняет SQL-запрос с параметрами.
	 *
	 * @since 171.3.0
	 *
	 * @param   string                $sql     SQL-запрос с плейсхолдерами.
	 * @param   array<string, mixed>  $params  Параметры для привязки.
	 *
	 * @return StatementInterface Результат выполнения запроса.
	 *
	 * @example
	 *     $stmt = $db->query('SELECT COUNT(*) FROM {table}', ['table' => 'users']);
	 */
	public function query(string $sql, array $params = []): StatementInterface {
		return $this->connection()->query($sql, $params);
	}

	/**
	 * Формирует пагинированную выборку для сущности.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity   Сущность или FQCN класса.
	 * @param   string         $orderby  Поле сортировки.
	 * @param   string         $sortby   Направление сортировки (`ASC` или `DESC`).
	 * @param   int            $limit    Количество записей на странице.
	 * @param   int            $page     Номер страницы.
	 *
	 * @return ORM\Select Настроенный объект выборки.
	 *
	 * @example
	 *     $select = $db->paginate(LogRecord::class, 'created_at', 'DESC', 20, 1);
	 */
	public function paginate(
		object|string $entity,
		string        $orderby,
		string        $sortby = 'DESC',
		int           $limit = 10,
		int           $page = 1,
	): ORM\Select {
		$select    = $this->repository($entity)->select()->orderBy($orderby, $sortby);
		$paginator = new Paginator($limit);
		$paginator->withPage($page)->paginate($select);

		return $select;
	}

	/**
	 * Подсчитывает количество записей указанной сущности.
	 *
	 * @since 171.3.0
	 *
	 * @param   object|string  $entity  Сущность или FQCN класса.
	 *
	 * @return int Число записей в таблице.
	 *
	 * @example
	 *     $total = $db->count(LogRecord::class);
	 */
	public function count(object|string $entity): int {
		return $this->repository($entity)->select()->count();
	}

	/**
	 * Лениво создаёт ORM. Схема кэшируется.
	 * GenerateMigrations: DEVCRAFT_GENERATE_MIGRATIONS или чистая установка (нет таблиц ядра).
	 *
	 * @since 171.3.0
	 *
	 * @return ORM\ORM Инициализированный ORM.
	 */
	private function generateOrm(): ORM\ORM {
		if($this->orm !== NULL) {
			return $this->orm;
		}

		Paths::register();

		$migrationsDir = Paths::src() . '/database/migrations';
		$fingerprint   = $this->quickModelsSignature();
		$bootstrap     = $this->ormSchemaBootstrapNeeded();
		$schema_array  = $bootstrap ? null : $this->loadCachedSchema($fingerprint);
		$needMigrate   = $bootstrap || $this->migrationFilesNeedApply($migrationsDir);
		$migrator      = null;
		$didGenerate   = false;

		if($schema_array === null || $needMigrate) {
			$migrator = $this->createMigratorForDirectory($migrationsDir);
		}

		if($schema_array === null) {
			$path_resolver      = new EntityPathResolver($this->registry);
			$model_directories  = $path_resolver->entityModelDirectories();
			$generateMigrations = $bootstrap
				|| (defined('DEVCRAFT_GENERATE_MIGRATIONS') && constant('DEVCRAFT_GENERATE_MIGRATIONS'));
			$didGenerate        = (bool) $generateMigrations;

			$registry     = new SchemaRegistry($this->generateManager());
			$schema_array = $this->compileSchema(
				$registry,
				$model_directories,
				$migrator ?? $this->createMigratorForDirectory($migrationsDir),
				$didGenerate,
			);
			$this->storeCachedSchema($fingerprint, $schema_array);
		}

		$schema            = new ORM\Schema($schema_array);
		$factory           = new ORM\Factory($this->generateManager());
		$container         = new Container();
		$command_generator = new ORM\Entity\Behavior\EventDrivenCommandGenerator($schema, $container);

		$this->orm = new ORM\ORM(
			factory         : $factory,
			schema          : $schema,
			commandGenerator: $command_generator,
		);

		// После GenerateMigrations файлы только что появились — всегда прогоняем migrator.
		if(($needMigrate || $didGenerate) && $migrator !== null) {
			$capsule = new Capsule($this->generateManager()->database());
			$this->skipCreateMigrationsForExistingTables($migrator);

			try {
				while($migrator->run($capsule) !== NULL) {
					$this->skipCreateMigrationsForExistingTables($migrator);
				}
			} catch(\Throwable) {
				$this->skipCreateMigrationsForExistingTables($migrator);

				try {
					while($migrator->run($capsule) !== NULL) {
						$this->skipCreateMigrationsForExistingTables($migrator);
					}
				} catch(\Throwable) {
					// Таблицы уже существуют / дубликат create — ORM-схема всё равно готова.
				}
			}
		}

		$this->setManager();

		return $this->orm;
	}

	/**
	 * Нет журнала миграций или таблиц ядра Admin — Cycle должен сам сгенерировать и применить схему.
	 */
	private function ormSchemaBootstrapNeeded(): bool {
		foreach(['devcraft_migrations', 'devcraft_logs', 'devcraft_composer_data'] as $suffix) {
			if(!$this->tableExists(PREFIX . '_' . $suffix)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Лёгкая подпись Models (один уровень, без RecursiveIterator).
	 */
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

	/**
	 * Есть ли файлы миграций, которых ещё нет в журнале (без загрузки классов).
	 */
	private function migrationFilesNeedApply(string $migrationsDir): bool {
		$fileCount = count(glob($migrationsDir . '/*.php') ?: []);

		if($fileCount === 0) {
			return false;
		}

		$migrationTable = PREFIX . '_devcraft_migrations';

		if(!$this->tableExists($migrationTable)) {
			return true;
		}

		$result   = $this->query("SELECT COUNT(*) AS total FROM `{$migrationTable}`")->fetchAll();
		$executed = isset($result[0]['total']) ? (int) $result[0]['total'] : 0;

		return $fileCount > $executed;
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
			$this->generateManager(),
			new Migrations\FileRepository($migrator_config),
		);
		$migrator->configure();

		return $migrator;
	}

	private function generateManager(): DatabaseManager {
		return $this->database_manager ??= new DatabaseManager($this->generateOrmConfig());
	}

	/**
	 * Формирует конфигурацию подключения из констант DLE.
	 *
	 * @since 171.3.0
	 *
	 * @global array<string, mixed> $config Глобальные настройки DLE.
	 *
	 * @return Config\DatabaseConfig Конфигурация Cycle Database.
	 */
	private function generateOrmConfig(): Config\DatabaseConfig {
		if($this->database_config !== NULL) {
			return $this->database_config;
		}

		$this->assertDatabaseConstants();
		$this->loadDatabaseConfigIfNeeded();

		global $config;

		$host_parts = explode(':', DBHOST, 2);
		$host       = $host_parts[0];
		$port       = isset($host_parts[1]) && $host_parts[1] !== ''? (int) $host_parts[1] : 3306;

		$prefix = PREFIX . '_';

		$this->database_config = new Config\DatabaseConfig(
			[
				'databases'   => [
					'default' => [
						'driver' => 'mysql',
						'prefix' => $prefix,
					],
				],
				'connections' => [
					'mysql' => new Config\MySQLDriverConfig(
						connection: new Config\MySQL\TcpConnectionConfig(
							database: DBNAME,
							host    : $host,
							port    : $port,
							user    : DBUSER,
							password: DBPASS,
						),
						reconnect : true,
						timezone  : $config['date_adjust'] ?? 'UTC',
						queryCache: true,
					),
				],
			],
		);

		return $this->database_config;
	}

	/**
	 * Компилирует схему ORM (без автогенерации миграций, если не запрошено явно).
	 *
	 * @since 171.3.0
	 *
	 * @param   SchemaRegistry         $registry            Реестр Cycle Schema.
	 * @param   list<string>           $model_directories   Каталоги моделей.
	 * @param   Migrations\Migrator    $migrator            Мигратор.
	 * @param   bool                   $generateMigrations  Писать новые файлы миграций (только CLI/флаг).
	 *
	 * @return array<string, mixed> Схема ORM.
	 */
	private function compileSchema(
		SchemaRegistry $registry,
		array $model_directories,
		Migrations\Migrator $migrator,
		bool $generateMigrations = false,
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
			$generators[] = new Schema\Generator\Migrations\GenerateMigrations(
				$migrator->getRepository(),
				$migrator->getConfig(),
				new SingleFileStrategy(
					$migrator->getConfig(),
					new NameBasedOnChangesGenerator(),
				),
			);
		}

		$generators[] = new Generator\GenerateTypecast();

		$compiler = new Compiler();

		return $compiler->compile($registry, $generators);
	}

	/**
	 * Помечает create/change-миграции выполненными, если объекты уже есть в БД.
	 *
	 * Create: таблица существует.
	 * Change: первый add_index / add_column из имени уже есть — весь up() уже применяли вне журнала.
	 *
	 * @since 200.4.0
	 */
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
			$tableKey      = '';

			if(preg_match('/_create_(?:dle_)?([a-z0-9_]+?)(?:_create_|_change_|$)/', $migrationName, $matches) === 1) {
				$tableKey = (string) ($matches[1] ?? '');
			}

			$alreadyThere = false;
			$tableName    = '';

			if($tableKey !== '') {
				$tableName    = PREFIX . '_' . $tableKey;
				$alreadyThere = $this->tableExists($tableName);
			} elseif(preg_match('/_change_dle_(.+?)_add_(.+)$/', $migrationName, $change) === 1) {
				$tableKey  = (string) $change[1];
				$tableName = PREFIX . '_' . $tableKey;
				$ops       = 'add_' . $change[2];
				$alreadyThere = $this->tableExists($tableName)
					&& $this->changeMigrationFirstOpExists($tableName, $ops);
			}

			if(!$alreadyThere) {
				continue;
			}

			$this->markMigrationAsExecuted($migrationTable, $migrationName);
		}
	}

	/**
	 * Проверяет, что первая операция change-миграции уже есть в таблице.
	 */
	private function changeMigrationFirstOpExists(string $tableName, string $ops): bool {
		if(preg_match('/^add_index_([a-z0-9_]+?)(?=_add_|$)/', $ops, $m) === 1) {
			return $this->indexExists($tableName, $m[1]);
		}

		if(preg_match('/^add_([a-z0-9_]+?)(?=_add_|$)/', $ops, $m) === 1) {
			return $this->columnExists($tableName, $m[1]);
		}

		return false;
	}

	/**
	 * Есть ли индекс у таблицы.
	 */
	private function indexExists(string $tableName, string $indexName): bool {
		$result = $this->query(
			'SELECT COUNT(*) AS total FROM information_schema.statistics WHERE table_schema = :schema AND table_name = :table AND index_name = :idx',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
				'idx'    => $indexName,
			],
		)->fetchAll();

		return isset($result[0]['total']) && (int) $result[0]['total'] > 0;
	}

	/**
	 * Есть ли колонка у таблицы.
	 */
	private function columnExists(string $tableName, string $columnName): bool {
		$result = $this->query(
			'SELECT COUNT(*) AS total FROM information_schema.columns WHERE table_schema = :schema AND table_name = :table AND column_name = :col',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
				'col'    => $columnName,
			],
		)->fetchAll();

		return isset($result[0]['total']) && (int) $result[0]['total'] > 0;
	}

	/**
	 * Проверяет существование таблицы в текущей БД.
	 */
	private function tableExists(string $tableName): bool {
		$result = $this->query(
			'SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = :schema AND table_name = :table',
			[
				'schema' => DBNAME,
				'table'  => $tableName,
			],
		)->fetchAll();

		$total = isset($result[0]['total'])? (int) $result[0]['total'] : 0;

		return $total > 0;
	}

	/**
	 * Регистрирует миграцию как выполненную, если её нет в журнале.
	 */
	private function markMigrationAsExecuted(string $migrationTable, string $migrationName): void {
		$existsResult = $this->query(
			"SELECT COUNT(*) AS total FROM `{$migrationTable}` WHERE `migration` = :migration",
			['migration' => $migrationName],
		)->fetchAll();

		$exists = isset($existsResult[0]['total'])? (int) $existsResult[0]['total'] : 0;

		if($exists > 0) {
			return;
		}

		$this->query(
			"INSERT INTO `{$migrationTable}` (`migration`, `time_executed`, `created_at`) VALUES (:migration, NOW(), NOW())",
			['migration' => $migrationName],
		);
	}

	/**
	 * Проверяет наличие обязательных констант подключения к БД.
	 *
	 * @since 171.3.0
	 *
	 * @throws \RuntimeException Если одна из констант не определена.
	 */
	private function assertDatabaseConstants(): void {
		foreach(['DBHOST', 'DBNAME', 'DBUSER', 'DBPASS', 'PREFIX'] as $constant) {
			if(!defined($constant)) {
				throw new \RuntimeException(__('Константа {const} не определена. Подключите файл dbconfig.php.', ['{const}' => $constant]));
			}
		}
	}

	/**
	 * Подключает dbconfig.php DLE, если константы БД ещё не определены.
	 *
	 * @since 171.3.0
	 *
	 * @throws \RuntimeException Если файл конфигурации не найден.
	 */
	private function loadDatabaseConfigIfNeeded(): void {
		if(defined('DBHOST')) {
			return;
		}

		if(!defined('ROOT_DIR')) {
			Paths::register();
		}

		$dbconfig = ROOT_DIR . '/engine/data/dbconfig.php';

		if(!is_file($dbconfig)) {
			throw new \RuntimeException(__('Файл dbconfig.php не найден по адресу {path}', ['{path}' => $dbconfig]));
		}

		/** Подключение файла конфигурации базы данных DLE. */
		require_once $dbconfig;
	}

}
