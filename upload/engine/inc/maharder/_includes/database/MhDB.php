<?php
//===============================================================
// Файл: MhDB.php                                               =
// Путь: engine/inc/maharder/_includes/database/MhDB.php        =
// Дата создания: 2024-04-15 07:43:41                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

use Cycle\ORM;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\Schema\Registry;
use Cycle\Schema\Compiler;
use Cycle\Database\Config;
use Spiral\Pagination\Paginator;
use Spiral\Tokenizer\ClassLocator;
use Cycle\Database\DatabaseManager;
use Symfony\Component\Finder\Finder;
use Cycle\Database\StatementInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Symfony\Component\DependencyInjection\Container;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\Migrations;
use Cycle\Migrations\Capsule;

require_once(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php'));

/**
 * Класс для работы с базой данных
 *
 * @since 171.3.0
 */
class MhDB {
	/**
	 * ORM-класс для управления объектами и предоставления операций высокого уровня.
	 * Используется для создания экземпляра ORM, управления сущностями, получением менеджера базы данных и выполнения
	 * запросов.
	 */
	private ?ORM\ORM $orm = null;
	/**
	 * Менеджер базы данных, предоставляющий функционал работы с подключениями и конфигурациями БД.
	 * Генерируется с помощью метода `generateManager`.
	 */
	private ?DatabaseManager $orm_manager = null;
	/**
	 * Конфигурация базы данных для настройки параметров подключения, драйвера и других настроек.
	 * Генерируется с помощью метода `generateOrmConfig`.
	 */
	private ?Config\DatabaseConfig $orm_config = null;
	/**
	 * Менеджер сущностей ORM, обеспечивающий операции CRUD и управление состоянием сущностей.
	 * Инициализируется в методе `setManager`.
	 */
	private ?ORM\EntityManager $em = null;
	/**
	 * Флаг, определяющий подключение к базе данных пользователя (true) или основной базы данных (false).
	 * Используется при создании конфигурации базы данных.
	 */
	private bool $user_db = false;

	/**
	 * Конструктор класса MhDB.
	 * Настраивает базу данных, инициализирует ORM, а также управляет объектом EntityManager.
	 *
	 * @param bool $user_db Указывает, используется ли пользовательская база данных.
	 *                      Если `true`, будет применяться пользовательский префикс базы данных;
	 *                      если `false` — стандартный префикс.
	 *
	 * @throws \Cycle\ORM\Exception\SchemaException Если при генерации ORM произошла ошибка в схеме базы данных.
	 * @throws \Cycle\Migrations\Exception\MigrationException Если миграции не могут быть выполнены.
	 * @throws \RuntimeException Если не удается нормально настроить конфигурацию подключения к базе данных.
	 */

	public function __construct(bool $user_db = false) {
		$this->user_db = $user_db;
		$this->generateOrm();
		$this->setManager();
	}

	/**
	 * Создает и возвращает экземпляр ORM (Object-Relational Mapper), используя
	 * предварительно скомпилированную схему, фабрику ORM, менеджер базы данных,
	 * а также генератор команд с поддержкой событий. Если ORM уже инициализирован,
	 * то возвращает ранее созданный объект.
	 * Генерация ORM основывается на схеме, созданной методом {@see compileSchema()},
	 * которой, в свою очередь, необходим зарегистрированный реестр сущностей.
	 * Управление базой данных осуществляется через метод {@see generateManager()}.
	 *
	 * @return ORM\ORM Объект ORM (Object-Relational Mapper), предназначенный
	 *                 для взаимодействия с базой данных.
	 * @throws RuntimeException Если генерация схемы или конфигурации базы данных
	 *                          завершилась с ошибкой.
	 */
	private function generateOrm(): ORM\ORM {
		if (is_null($this->orm)) {
			$registry = new Registry($this->generateManager());
			[$schemaArray, $migrator] = $this->compileSchema($registry);

			$schema           = new Cycle\ORM\Schema($schemaArray);
			$factory          = new ORM\Factory($this->generateManager());
			$container        = new Container();
			$commandGenerator = new EventDrivenCommandGenerator($schema, $container);

			$this->orm = new ORM\ORM(
				factory         : $factory,
				schema          : $schema,
				commandGenerator: $commandGenerator
			);

			$migrator->run(new Capsule($this->generateManager()->database()));
		}
		return $this->orm;
	}

	/**
	 * Создает и возвращает экземпляр класса DatabaseManager.
	 * Если объект `orm_manager` еще не создан, метод инициирует новый объект
	 * `DatabaseManager` на основе конфигурации ORM, полученной из метода `generateOrmConfig`.
	 *
	 * @return DatabaseManager Экземпляр менеджера базы данных, конфигурированного с помощью ORM.
	 * @throws RuntimeException Генерируется, если не удается подключить необходимый файл конфигурации базы данных.
	 * @throws \ConfigException Генерируется, если конфигурация базы данных содержит ошибки.
	 * @see generateOrmConfig() Используется для получения необходимой ORM-конфигурации.
	 */
	private function generateManager(): DatabaseManager {
		return $this->orm_manager ??= new DatabaseManager($this->generateOrmConfig());
	}

	/**
	 * Генерирует и возвращает объект конфигурации базы данных ORM, если он ещё не создан.
	 * Функция проверяет, был ли ранее инициализирован объект конфигурации ORM.
	 * Если объект отсутствует, производится его создание на основании глобальных настроек.
	 * Подключается файл конфигурации базы данных и выполняется подготовка данных,
	 * таких как разбиение хоста и порта, а также выборка префикса таблиц в зависимости от типа базы данных.
	 *
	 * @global array $config Глобальный массив настроек приложения, содержащий данные конфигурации.
	 * @return Config\DatabaseConfig Объект конфигурации базы данных ORM.
	 */
	private function generateOrmConfig(): Config\DatabaseConfig {
		global $config;

		if ($this->orm_config !== null) {
			return $this->orm_config;
		}

		// Подключение файла конфигурации базы данных, если класс `db` не был найден
		if (!class_exists('db')) {
			require_once DLEPlugins::Check(ENGINE_DIR . '/data/dbconfig.php');
		}

		if (is_null($this->orm_config)) {
			if (!class_exists('db')) include_once(DLEPlugins::Check(ENGINE_DIR . '/data/dbconfig.php'));

			// Разделяем хост и порт
			[$host, $port] = explode(':', DBHOST);
			$port = $port ? (int)$port : 3306;

			// Выбор используемого префикса
			$prefix = $this->user_db ? DataManager::getUserPrefix() : DataManager::getPrefix();

			// Создание конфигурации ORM
			$this->orm_config = new Config\DatabaseConfig(
				[
					'databases'   => [
						'default' => [
							'driver' => 'mysql',
							'prefix' => $prefix . '_'
						]
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
							queryCache: true
						),
					],
				]
			);
		}
		return $this->orm_config;
	}

	/**
	 * Генерирует и компилирует схему на основе переданного реестра и генераторов схем.
	 * Этот метод обрабатывает пути моделей с использованием глобального объекта `$MIGRATOR`,
	 * находит классы и использует их для генерации комплексной схемы базы данных.
	 * **Примечание**: Метод использует глобальную переменную `$MIGRATOR`, поэтому убедитесь,
	 * что она правильно инициализирована до вызова метода.
	 *
	 * @global         $mh_models_paths
	 *
	 * @param Registry $registry Реестр, содержащий информацию о схемах, моделях и их взаимоотношениях.
	 *
	 * @return array Возвращает скомпилированную схему в виде массива.
	 * @throws LogicException Если при компиляции схемы возникли логические ошибки.
	 * @throws RuntimeException Если невозможно найти файлы моделей или извлечь классы.
	 */
	private function compileSchema(Registry $registry): array {
		global $mh_models_paths;

		$finder       = new Finder();
		$files        = $finder->files()->in($mh_models_paths);
		$classLocator = new ClassLocator($files);

		$migratorConfig = new MigrationConfig(
			[
				'directory' => DataManager::normalizePath(MH_ROOT . '/_migrations/'),
				'table'     => 'maharder_migrations',
				'safe'      => true
			]
		);

		$migrator = new Migrations\Migrator(
			$migratorConfig,
			$this->generateManager(),
			new Migrations\FileRepository($migratorConfig)
		);

		$migrator->configure();

		$compiler = new Compiler();
		$schemas  = $compiler->compile(
			$registry,
			[
				new Schema\Generator\ResetTables(),
				new Annotated\Embeddings(new TokenizerEmbeddingLocator($classLocator)),
				new Annotated\Entities(new TokenizerEntityLocator($classLocator)),
				new Annotated\TableInheritance(),
				new Annotated\MergeColumns(),
				new Schema\Generator\GenerateRelations(),
				new Schema\Generator\GenerateModifiers(),
				new Schema\Generator\ValidateEntities(),
				new Schema\Generator\RenderTables(),
				new Schema\Generator\RenderRelations(),
				new Schema\Generator\RenderModifiers(),
				new Schema\Generator\ForeignKeys(),
				new Annotated\MergeIndexes(),
				new Cycle\Schema\Generator\Migrations\GenerateMigrations(
					$migrator->getRepository(),
					$migrator->getConfig()
				),
				new Schema\Generator\GenerateTypecast(),
			]
		);

		return [$schemas, $migrator];
	}

	/**
	 * Устанавливает экземпляр EntityManager на основе текущего ORM.
	 * Метод инициализирует объект EntityManager (менеджера для управления сущностями)
	 * на основе существующего объекта ORM (Object-Relational Mapping).
	 * Если ORM не был предварительно сконфигурирован, это может привести к выбросу ошибок
	 * в процессе создания EntityManager.
	 *
	 * @return void
	 * @throws \RuntimeException Если объект ORM не был предварительно создан.
	 */
	public function setManager(): void {
		$this->em = new ORM\EntityManager($this->orm);
	}

	public function getManager(): ORM\EntityManager {
		return $this->em;
	}

	/**
	 * Удаляет сущность на основе переданного объекта и первичного ключа.
	 * Эта функция выполняет следующие шаги:
	 * 1. Извлекает объект сущности из базы данных с помощью метода `get`.
	 * 2. Передает этот объект менеджеру сущностей для удаления.
	 * 3. Выполняет транзакцию удаления.
	 *
	 * @param object|string $entity Объект сущности, подлежащей удалению.
	 * @param int           $pk     Первичный ключ сущности.
	 *
	 * @return ORM\Transaction\StateInterface Возвращает состояние завершенной транзакции удаления.
	 * @throws Throwable При любой ошибке в ORM (например, сбой во время удаления).
	 */
	public function delete(object|string $entity, int $pk): ORM\Transaction\StateInterface {
		$obj = $this->get($entity, $pk);
		return $this->getManager()->delete($obj)->run();
	}

	/**
	 * Получает сущность из хранилища данных по её первичному ключу.
	 *
	 * @param object|string $entity Объект сущности, для которого нужно выполнить поиск.
	 * @param int           $pk     Первичный ключ сущности.
	 *
	 * @return object|null Возвращает найденный объект сущности или null, если объект с заданным первичным ключом
	 *                     отсутствует.
	 */
	public function get(object|string $entity, int $pk): ?object {
		return $this->repository($entity)->findByPK($pk);
	}

	/**
	 * Получает все записи для указанной сущности из хранилища.
	 * Этот метод использует репозиторий, ассоциированный с указанной сущностью,
	 * для получения всех записей из базы данных.
	 *
	 * @param object|string $entity Объект сущности, для которой нужно получить записи.
	 *                              Сущность должна быть доступна в ORM.
	 *
	 * @return array Массив объектов сущности, извлеченных из базы данных.
	 *               Если данных в хранилище нет, возвращается пустой массив.
	 */
	public function getAll(object|string $entity): array {
		return $this->repository($entity)->findAll();
	}

	/**
	 * Возвращает репозиторий для указанной сущности или класса сущности.
	 * Эта функция принимает объект сущности или строку, представляющую имя класса сущности,
	 * определяет их имя класса и возвращает соответствующий репозиторий, используя ORM.
	 *
	 * @param object|string $entity Объект сущности или строка с именем класса сущности.
	 *                              Если передается объект, его класс будет определен через `$entity::class`.
	 *
	 * @return ORM\RepositoryInterface Интерфейс репозитория для работы с указанной сущностью.
	 * @throws ORM\Exception\RepositoryNotFoundException Если репозиторий для указанной сущности не найден.
	 */
	public function repository(object|string $entity): ORM\RepositoryInterface {
		$entityClass = is_string($entity) ? $entity : $entity::class;
		return $this->getOrm()->getRepository($entityClass);
	}

	/**
	 * Возвращает экземпляр ORM, создавая его при необходимости.
	 * Данный метод вызывает `generateOrm`, который инициализирует ORM,
	 * используя настраиваемые зависимости, такие как Schema, Factory, Container и CommandGenerator.
	 * Повторные вызовы возвращают уже созданный экземпляр ORM.
	 *
	 * @return ORM\ORM Экземпляр ORM.
	 * @throws \RuntimeException Если при генерации ORM возникли ошибки.
	 */
	public function getOrm(): ORM\ORM {
		return $this->generateOrm();
	}

	/**
	 * Выполняет сохранение сущности и возвращает состояние транзакции.
	 * Этот метод обрабатывает сохранение переданного объекта сущности с использованием
	 * связанного `EntityManager`. Завершает операцию методом `run()`, который выполняет
	 * транзакцию и возвращает её результирующее состояние.
	 *
	 * @param object $entity Объект сущности, который необходимо сохранить. Объект должен
	 *                       быть корректным и поддерживаться ORM.
	 *
	 * @return ORM\Transaction\StateInterface Результирующее состояние транзакции после
	 *                                         сохранения сущности.
	 * @throws \RuntimeException|Throwable Исключение выбрасывается, если `EntityManager` не был
	 *                           корректно инициализирован, что приводит к ошибке
	 *                           сохранения.
	 */
	public function run(object $entity): ORM\Transaction\StateInterface {
		return $this->getManager()->persist($entity)->run();
	}

	/**
	 * Обновляет предоставленную сущность в базе данных.
	 * Эта функция выполняет процесс обновления сущности посредством вызова метода `run`,
	 * который предполагает выполнение различных ORM-операций, таких как `persist`.
	 *
	 * @param object $entity Сущность, которая должна быть обновлена.
	 *
	 * @return ORM\Transaction\StateInterface Возвращает состояние ORM-транзакции после обновления сущности.
	 * @throws ORM\Exception\EntityNotManagedException Если сущность не управляется текущим менеджером сущностей.
	 * @throws ORM\Exception\TransactionException|Throwable Если транзакция не может быть завершена.
	 */
	public function create(object $entity): ORM\Transaction\StateInterface {
		return $this->run($entity);
	}

	/**
	 * Обновляет предоставленную сущность в базе данных.
	 * Эта функция выполняет процесс обновления сущности посредством вызова метода `run`,
	 * который предполагает выполнение различных ORM-операций, таких, как `persist`.
	 *
	 * @param object $entity Сущность, которая должна быть обновлена.
	 *
	 * @return ORM\Transaction\StateInterface Возвращает состояние ORM-транзакции после обновления сущности.
	 * @throws ORM\Exception\EntityNotManagedException Если сущность не управляется текущим менеджером сущностей.
	 * @throws ORM\Exception\TransactionException|Throwable Если транзакция не может быть завершена.
	 */
	public function update(object $entity): ORM\Transaction\StateInterface {
		return $this->run($entity);
	}

	/**
	 * Выполняет SQL-запрос с указанными параметрами и возвращает результат.
	 * Метод использует объект DatabaseManager для получения соединения с базой данных
	 * и дальнейшего выполнения SQL-запроса. Позволяет передавать параметры для безопасного
	 * выполнения подготовленных запросов.
	 *
	 * @param string $sql    SQL-запрос для выполнения. Обычно это строка запроса с плейсхолдерами.
	 * @param array  $params Параметры для привязки к плейсхолдерам в SQL-запросе. По умолчанию пустой массив.
	 *
	 * @return StatementInterface Результирующий объект, представляющий результат выполнения запроса.
	 * @throws \InvalidArgumentException Может бросить исключение при передаче некорректного SQL-запроса.
	 * @throws \RuntimeException Может бросить исключение, если соединение с базой данных невозможно.
	 */
	public function query(string $sql, array $params = []): StatementInterface {
		$dbal = $this->generateManager()->database();
		return $dbal->query($sql, $params);
	}

	/**
	 * Пагинирует данные из базы данных для указанной сущности.
	 * Этот метод извлекает данные для заданной сущности, применяет сортировку и пагинацию
	 * на основе переданных параметров и возвращает настроенный объект `ORM\Select`.
	 *
	 * @param object|string $entity  Экземпляр сущности, для которой извлекаются данные.
	 * @param string        $orderby Поле, по которому необходимо осуществить сортировку.
	 * @param string        $sortby  Направление сортировки, либо 'ASC', либо 'DESC'. По умолчанию — 'DESC'.
	 * @param int           $limit   Количество элементов на странице. По умолчанию — 10.
	 * @param int           $page    Номер страницы для извлечения данных. По умолчанию — 1.
	 *
	 * @return ORM\Select Настроенный объект Select, содержащий отсортированные и пагинированные данные.
	 */
	public function paginate(object|string $entity, string $orderby, string $sortby = 'DESC', int $limit = 10, int $page = 1): ORM\Select {
		$select    = $this->repository($entity)->select()->orderBy($orderby, $sortby);
		$paginator = new Paginator($limit);
		$paginator->withPage($page)->paginate($select);

		return $select;
	}

	/**
	 * Подсчитывает количество записей для указанной сущности.
	 * Эта функция использует репозиторий, связанный с сущностью, для выполнения запроса
	 * на подсчет количества записей.
	 *
	 * @param object|string $entity Объект сущности, для которой нужно подсчитать количество записей.
	 *
	 * @return int Количество записей в репозитории, связанных с данной сущностью.
	 */
	public function count(object|string $entity): int {
		return $this->repository($entity)->select()->count();
	}
}
