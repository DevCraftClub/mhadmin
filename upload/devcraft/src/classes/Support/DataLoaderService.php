<?php
//===============================================================
// Файл: DataLoaderService.php                                  =
// Путь: devcraft/src/classes/Support/DataLoaderService.php     =
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
use DevCraft\Core\Application;
use DevCraft\Builders\QueryBuilder;
use Cycle\Database\Query\SelectQuery;
use Cycle\Database\Query\UpdateQuery;
use Cycle\Database\Query\DeleteQuery;
use DevCraft\Core\Cache\CacheControl;
use Cycle\Database\DatabaseInterface;
use DevCraft\Core\Enums\SortDirection;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Загружает и изменяет строки таблиц DLE через Cycle Database с кешированием SELECT.
 *
 * Static-фасад: БД берётся из {@see Application::database()}.
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
	 * Запрет инстанцирования (только static API).
	 *
	 * @since 200.4.0
	 */
	private function __construct() {}

	/**
	 * Загружает данные таблицы DLE с кешированием (legacy load_data).
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>|QueryBuilder  $args  Аргументы запроса или fluent-билдер.
	 *
	 * @return array<int, array<string, mixed>> Строки результата или пустой массив при ошибке.
	 *
	 * @example
	 *     $rows = DataLoaderService::loadData([
	 *         'table'      => 'users',
	 *         'columns'    => ['user_id', 'name'],
	 *         'conditions' => ['user_group' => 1],
	 *     ]);
	 *     $rows = DataLoaderService::loadData(
	 *         QueryBuilder::create('users')->withConditionsItem('user_group', 1)
	 *     );
	 */
	public static function loadData(array|QueryBuilder $args): array {
		if($args instanceof QueryBuilder) {
			$args = $args->build();
		}

		$normalized = self::normalizeArgs($args);
		$cacheKey   = 'dataloader_' . md5(serialize($normalized));
		$cached     = self::readCache($cacheKey);

		if($cached !== NULL) {
			return $cached;
		}

		try {
			$rows = self::executeQuery($normalized);
			self::writeCache($cacheKey, $rows);

			return $rows;
		} catch(Throwable $exception) {
			LogGenerator::for('DataLoaderService')->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Возвращает первую строку результата или пустой массив.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>|QueryBuilder  $args  Аргументы запроса или fluent-билдер.
	 *
	 * @return array<string, mixed> Первая строка либо [].
	 *
	 * @example
	 *     $user = DataLoaderService::loadOne(
	 *         QueryBuilder::create('users')->withConditionsItem('user_id', 1)->withLimit(1)
	 *     );
	 */
	public static function loadOne(array|QueryBuilder $args): array {
		return self::loadData($args)[0] ?? [];
	}

	/**
	 * Вставляет строку и возвращает созданную запись (reload по PK).
	 *
	 * @since 200.4.0
	 *
	 * @param   QueryBuilder  $query  Билдер с table + values (+ primaryKey).
	 *
	 * @return array<string, mixed> Созданная строка либо [].
	 *
	 * @example
	 *     $row = DataLoaderService::insert(
	 *         QueryBuilder::create('pm')->withValues(['subj' => 'Hi', 'user' => 1])
	 *     );
	 */
	public static function insert(QueryBuilder $query): array {
		try {
			$table  = $query->getTable();
			$values = $query->getValues();

			if($table === '' || $values === []) {
				return [];
			}

			$db = self::resolveDatabaseForTable($table);
			$id = $db->insert($table)->values($values)->run();

			$pk = $query->getPrimaryKey();
			if($pk === '' || $id === NULL || $id === false || $id === '') {
				return [];
			}

			self::clearCache();

			return QueryBuilder::create($table)
			                   ->withConditionsItem($pk, $id)
			                   ->withLimit(1)
			                   ->first();
		} catch(Throwable $exception) {
			LogGenerator::for(self::class)->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Обновляет строки по conditions и возвращает все совпавшие записи.
	 *
	 * @since 200.4.0
	 *
	 * @param   QueryBuilder  $query  Билдер с table + values + conditions.
	 *
	 * @return array<int, array<string, mixed>> Строки после UPDATE либо [].
	 *
	 * @example
	 *     $rows = DataLoaderService::update(
	 *         QueryBuilder::create('users')
	 *             ->withValues(['banned' => 1])
	 *             ->withConditionsItem('user_id', 1)
	 *     );
	 */
	public static function update(QueryBuilder $query): array {
		try {
			$table      = $query->getTable();
			$values     = $query->getValues();
			$conditions = $query->getConditions();

			if($table === '' || $values === []) {
				return [];
			}

			$db     = self::resolveDatabaseForTable($table);
			$update = $db->update($table)->values($values);
			self::applyConditions($update, $conditions);
			$update->run();

			self::clearCache();

			return QueryBuilder::create($table)
			                   ->withConditions($conditions)
			                   ->load();
		} catch(Throwable $exception) {
			LogGenerator::for(self::class)->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Удаляет строки по conditions.
	 *
	 * Без conditions DELETE не выполняется (защита от полной очистки таблицы).
	 *
	 * @since 200.4.0
	 *
	 * @param   QueryBuilder  $query  Билдер с table + conditions.
	 *
	 * @return bool true при успехе, false при ошибке или пустых conditions.
	 *
	 * @example
	 *     $ok = DataLoaderService::delete(
	 *         QueryBuilder::create('tags')
	 *             ->withConditionsItem('news_id', $newsId)
	 *             ->withConditionsItem('tag', $tag)
	 *     );
	 */
	public static function delete(QueryBuilder $query): bool {
		try {
			$table      = $query->getTable();
			$conditions = $query->getConditions();

			if($table === '' || $conditions === []) {
				return false;
			}

			$db     = self::resolveDatabaseForTable($table);
			$delete = $db->delete($table);
			self::applyConditions($delete, $conditions);
			$delete->run();

			self::clearCache();

			return true;
		} catch(Throwable $exception) {
			LogGenerator::for(self::class)->log($exception->getMessage());

			return false;
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
	 *     DataLoaderService::clearCache();
	 */
	public static function clearCache(?string $key = NULL): void {
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
	private static function normalizeArgs(array $args): array {
		ksort($args);

		return $args;
	}

	/**
	 * Выполняет SELECT к таблице DLE через Cycle SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $args  Нормализованные аргументы (см. {@see loadData()}).
	 *
	 * @return array<int, array<string, mixed>> Строки результата fetchAll().
	 */
	private static function executeQuery(array $args): array {
		$table = (string) ($args['table'] ?? '');

		if($table === '') {
			return [];
		}

		$db     = self::resolveDatabaseForTable($table);
		$select = $db->table($table)->select();

		$columns = $args['columns'] ?? '*';

		if(is_array($columns) && $columns !== []) {
			$select->columns($columns);
		}

		$conditions = $args['conditions'] ?? [];

		if(is_array($conditions)) {
			self::applyConditions($select, $conditions);
		}

		$order = $args['order'] ?? [];

		if(is_array($order)) {
			self::applyOrder($select, $order);
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
	 * Применяет условия фильтрации к SelectQuery, UpdateQuery или DeleteQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   SelectQuery|UpdateQuery|DeleteQuery  $query       Объект запроса.
	 * @param   array<string, mixed>                 $conditions  Условия фильтрации.
	 */
	private static function applyConditions(SelectQuery|UpdateQuery|DeleteQuery $query, array $conditions): void {
		foreach($conditions as $column => $value) {
			if(!is_string($column) || $column === '') {
				continue;
			}

			if(is_array($value) && isset($value['op'], $value['value'])) {
				$query->where($column, (string) $value['op'], $value['value']);

				continue;
			}

			$query->where($column, $value);
		}
	}

	/**
	 * Применяет сортировку orderBy к SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   SelectQuery                          $select  Объект запроса.
	 * @param   array<string, SortDirection|string>  $order   Карта колонка => направление.
	 */
	private static function applyOrder(SelectQuery $select, array $order): void {
		foreach($order as $column => $direction) {
			if(!is_string($column) || $column === '') {
				continue;
			}

			$select->orderBy($column, self::normalizeSortDirection($direction));
		}
	}

	/**
	 * Нормализует направление сортировки в константу SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   SortDirection|string  $direction  Enum или строка ASC/DESC.
	 *
	 * @return string SelectQuery::SORT_ASC или SelectQuery::SORT_DESC.
	 */
	private static function normalizeSortDirection(SortDirection|string $direction): string {
		if($direction instanceof SortDirection) {
			return $direction->toCycle();
		}

		return SortDirection::fromString($direction)->toCycle();
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
	private static function resolveDatabaseForTable(string $table): DatabaseInterface {
		$prefix = self::resolvePrefix($table) . '_';
		$db     = Application::instance()->database()->connection();

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
	private static function resolvePrefix(string $table): string {
		if(in_array(strtolower($table), self::USER_TABLES, true)) {
			return DataManager::getUserPrefix();
		}

		return DataManager::getPrefix();
	}

	/**
	 * Читает закешированные строки.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $cacheKey  Ключ записи кеша.
	 *
	 * @return array<int, array<string, mixed>>|null Строки или null при промахе.
	 */
	private static function readCache(string $cacheKey): ?array {
		$cached = CacheControl::getCache(self::CACHE_TYPE, $cacheKey);

		if($cached === false) {
			return NULL;
		}

		if(is_array($cached) && isset($cached['rows']) && is_array($cached['rows'])) {
			return $cached['rows'];
		}

		if(!is_array($cached)) {
			return NULL;
		}

		return $cached;
	}

	/**
	 * Записывает строки результата в кеш.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                            $cacheKey  Ключ записи.
	 * @param   array<int, array<string, mixed>>  $rows      Строки результата.
	 */
	private static function writeCache(string $cacheKey, array $rows): void {
		try {
			CacheControl::setCache(self::CACHE_TYPE, $cacheKey, $rows);
		} catch(Throwable $exception) {
			LogGenerator::for('DataLoaderService')->log($exception->getMessage());
		}
	}

}
