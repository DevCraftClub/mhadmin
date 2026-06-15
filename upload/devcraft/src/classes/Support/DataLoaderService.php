<?php
//===============================================================
// Файл: DataLoaderService.php                                  =
// Путь: devcraft/src/classes/Support/DataLoaderService.php     =
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

namespace DevCraft\Core\Support;

use Throwable;
use Cycle\Database\Query\SelectQuery;
use DevCraft\Core\Cache\CacheControl;
use Cycle\Database\DatabaseInterface;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Database\DatabaseGateway;

/**
 * Загружает строки из таблиц DLE через Cycle SelectQuery с кешированием.
 *
 * Порт логики трейта DataLoader (load_data).
 *
 * @package    DevCraft
 * @since      173.3.0
 * @subpackage Core.Support
 */
final class DataLoaderService {

	/**
	 * Имя типа кеша для результатов загрузки.
	 *
	 * @since 173.3.0
	 * @var string
	 */
	private const CACHE_TYPE = 'dataloader';

	/**
	 * Таблицы DLE с префиксом USERPREFIX.
	 *
	 * @since 173.3.0
	 * @var list<string>
	 */
	private const USER_TABLES = ['users', 'users_delete', 'usergroups'];

	/**
	 * Создаёт сервис загрузки данных.
	 *
	 * @since 173.3.0
	 *
	 * @param   DatabaseGateway  $db          Шлюз базы данных.
	 * @param   int              $cacheTimer  Время жизни кеша в секундах.
	 */
	public function __construct(private readonly DatabaseGateway $db, private readonly int $cacheTimer = 3600) {}

	/**
	 * Загружает данные таблицы DLE с кешированием (legacy load_data).
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $args  Аргументы запроса: table, selects, where, order, limit, offset.
	 *
	 * @return array<int, array<string, mixed>> Строки результата или пустой массив при ошибке.
	 *
	 * @example
	 *     $rows = $loader->loadData([
	 *         'table'   => 'users',
	 *         'selects' => ['user_id', 'name'],
	 *         'where'   => ['user_group' => 1],
	 *     ]);
	 */
	public function loadData(array $args): array {
		$normalized = $this->normalizeArgs($args);
		$cacheKey   = 'dataloader_' . md5(serialize($normalized));
		$cached     = $this->readCache($cacheKey);

		if($cached !== NULL) {
			return $cached;
		}

		try {
			$rows = $this->executeQuery($normalized);
			$this->writeCache($cacheKey, $rows);

			return $rows;
		} catch(Throwable $exception) {
			LogGenerator::for('DataLoaderService')->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Очищает кеш загрузчика целиком или по ключу.
	 *
	 * @since 173.3.0
	 *
	 * @param   string|null  $key  Ключ записи кеша или null для полной очистки типа.
	 *
	 * @example
	 *     $loader->clearCache();
	 */
	public function clearCache(?string $key = NULL): void {
		if($key === NULL) {
			CacheControl::clearCache(self::CACHE_TYPE);

			return;
		}

		CacheControl::clearCache($key);
	}

	/**
	 * Сортирует ключи аргументов для стабильного ключа кеша.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $args  Исходные аргументы.
	 *
	 * @return array<string, mixed> Аргументы с ksort по ключам.
	 */
	private function normalizeArgs(array $args): array {
		ksort($args);

		return $args;
	}

	/**
	 * Выполняет SELECT к таблице DLE через Cycle SelectQuery.
	 *
	 * Префикс физической таблицы (`PREFIX` / `USERPREFIX`) определяется в {@see resolvePrefix()}
	 * и применяется через {@see resolveDatabaseForTable()}. Параметры запроса передаются
	 * структурированно — без конкатенации SQL-строк.
	 *
	 * @since 173.3.0
	 *
	 * @see   resolveDatabaseForTable()
	 * @see   applyWhere()
	 * @see   applyOrder()
	 *
	 * @param   array<string, mixed>  $args  Нормализованные аргументы (см. {@see loadData()}), ключи:
	 *                                       - `table`   (string): Имя таблицы без префикса.
	 *                                       - `selects` (list<string>|отсутствует): Столбцы выборки.
	 *                                       - `where`   (array): Условия `['поле' => значение]`.
	 *                                       - `order`   (array): Сортировка `['поле' => 'ASC'|'DESC']`.
	 *                                       - `limit`   (int|null): LIMIT.
	 *                                       - `offset`  (int|null): OFFSET.
	 *
	 * @return array<int, array<string, mixed>> Строки результата fetchAll().
	 *
	 */
	private function executeQuery(array $args): array {
		$table = (string) ($args['table'] ?? '');

		if($table === '') {
			return [];
		}

		$db     = $this->resolveDatabaseForTable($table);
		$select = $db->table($table)->select();

		$selects = $args['selects'] ?? '*';

		if(is_array($selects) && $selects !== []) {
			$select->columns($selects);
		}

		$where = $args['where'] ?? [];

		if(is_array($where)) {
			$this->applyWhere($select, $where);
		}

		$order = $args['order'] ?? [];

		if(is_array($order)) {
			$this->applyOrder($select, $order);
		}

		if(isset($args['limit']) && is_int($args['limit'])) {
			$select->limit($args['limit']);
		}

		if(isset($args['offset']) && is_int($args['offset'])) {
			$select->offset($args['offset']);
		}

		return $select->fetchAll();
	}

	/**
	 * Применяет условия where к SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   SelectQuery           $select  Объект запроса.
	 * @param   array<string, mixed>  $where   Условия фильтрации.
	 */
	private function applyWhere(SelectQuery $select, array $where): void {
		foreach($where as $column => $value) {
			if(!is_string($column) || $column === '') {
				continue;
			}

			if(is_array($value) && isset($value['op'], $value['value'])) {
				$select->where($column, (string) $value['op'], $value['value']);

				continue;
			}

			$select->where($column, $value);
		}
	}

	/**
	 * Применяет сортировку orderBy к SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   SelectQuery            $select  Объект запроса.
	 * @param   array<string, string>  $order   Карта колонка => направление.
	 */
	private function applyOrder(SelectQuery $select, array $order): void {
		foreach($order as $column => $direction) {
			if(!is_string($column) || $column === '') {
				continue;
			}

			$select->orderBy($column, $this->normalizeSortDirection($direction));
		}
	}

	/**
	 * Нормализует направление сортировки в константу SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $direction  Строка ASC/DESC.
	 *
	 * @return string SelectQuery::SORT_ASC или SelectQuery::SORT_DESC.
	 */
	private function normalizeSortDirection(string $direction): string {
		return strtoupper($direction) === 'DESC'
			? SelectQuery::SORT_DESC
			: SelectQuery::SORT_ASC;
	}

	/**
	 * Возвращает Cycle Database с корректным префиксом таблицы.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $table  Имя таблицы без префикса.
	 *
	 * @return DatabaseInterface Подключение с нужным prefix.
	 */
	private function resolveDatabaseForTable(string $table): DatabaseInterface {
		$prefix = $this->resolvePrefix($table) . '_';
		$db     = $this->db->connection();

		if($prefix !== $db->getPrefix()) {
			return $db->withPrefix($prefix, false);
		}

		return $db;
	}

	/**
	 * Определяет PREFIX или USERPREFIX для таблицы DLE.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $table  Имя таблицы.
	 *
	 * @return string Префикс без завершающего подчёркивания.
	 */
	private function resolvePrefix(string $table): string {
		if(in_array(strtolower($table), self::USER_TABLES, true)) {
			return DataManager::getUserPrefix();
		}

		return DataManager::getPrefix();
	}

	/**
	 * Читает закешированные строки, если TTL не истёк.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $cacheKey  Ключ записи кеша.
	 *
	 * @return array<int, array<string, mixed>>|null Строки или null при промахе/истечении.
	 */
	private function readCache(string $cacheKey): ?array {
		$cached = CacheControl::getCache(self::CACHE_TYPE, $cacheKey);

		if($cached === false || !is_array($cached)) {
			return NULL;
		}

		$storedAt = (int) ($cached['_stored_at'] ?? 0);
		$rows     = $cached['rows'] ?? NULL;

		if(!is_array($rows)) {
			return NULL;
		}

		if($storedAt > 0 && (time() - $storedAt) >= $this->cacheTimer) {
			return NULL;
		}

		return $rows;
	}

	/**
	 * Записывает строки результата в кеш.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                            $cacheKey  Ключ записи.
	 * @param   array<int, array<string, mixed>>  $rows      Строки результата.
	 */
	private function writeCache(string $cacheKey, array $rows): void {
		try {
			CacheControl::setCache(self::CACHE_TYPE, $cacheKey, [
				'_stored_at' => time(),
				'rows'       => $rows,
			]);
		} catch(Throwable $exception) {
			LogGenerator::for('DataLoaderService')->log($exception->getMessage());
		}
	}

}
