<?php

declare(strict_types=1);

namespace DevCraft\Dle\Fluent;

use DevCraft\Builders\QueryBuilder;
use DevCraft\Core\Enums\SortDirection;
use DevCraft\Dle\Schema\SchemaRegistry;
use DevCraft\Dle\Schema\TableSchemaInterface;
use DevCraft\Dle\Sdk\SdkException;

/**
 * Универсальный SELECT с фильтрами по схеме и виртуальным FK (RelationMap).
 */
final class TableQuery {
	private TableSchemaInterface $schema;

	/** @var list<array{sql: string, params: list<mixed>}> */
	private array $clauses = [];

	/**
	 * Условия-равенства для моста в {@see QueryBuilder}.
	 *
	 * @var array<string, string>
	 */
	private array $equalities = [];

	/**
	 * Причины, по которым запрос нельзя выразить через {@see QueryBuilder}.
	 *
	 * @var list<string>
	 */
	private array $bridgeBlockers = [];

	/** @var list<string> */
	private array $selectColumns = [];

	/** @var list<array{0: string, 1: string}> */
	private array $orderClauses = [];

	private int $limit = 20;

	private int $offset = 0;

	public function __construct(private string $table, ?TableSchemaInterface $schema = null) {
		$this->schema = $schema ?? SchemaRegistry::get($table);
		if($schema !== null) {
			$this->table = $schema->table();
		}
	}

	public static function of(string $table): self {
		return new self($table);
	}

	public static function ofSchema(TableSchemaInterface $schema): self {
		return new self($schema->table(), $schema);
	}

	public function table(): string {
		return $this->table;
	}

	public function schema(): TableSchemaInterface {
		return $this->schema;
	}

	/**
	 * @param list<string> $columns
	 */
	public function select(array $columns): self {
		$allowed = $this->schema->columns();
		$this->selectColumns = [];
		foreach($columns as $col) {
			$col = (string) $col;
			if(in_array($col, $allowed, true)) {
				$this->selectColumns[] = $col;
			}
		}

		return $this;
	}

	/**
	 * Фильтр по колонке схемы. Префиксы значения: ! (не), % (LIKE).
	 */
	public function where(string $column, string $value): self {
		if(!in_array($column, $this->schema->columns(), true)) {
			throw new \InvalidArgumentException("Неизвестная колонка «{$column}» для таблицы {$this->table}");
		}
		[$mode, $val] = self::parseMode($value);
		$edge = RelationMap::edge($this->table, $column);
		if($mode === 'like') {
			$this->bridgeBlockers[] = 'LIKE ' . $column;
			$this->clauses[]        = [
				'sql'    => '`' . $column . '` LIKE ?',
				'params' => ['%' . $val . '%'],
			];

			return $this;
		}
		if($edge !== null) {
			$this->bridgeBlockers[] = 'RelationMap ' . $column;
			$this->addRelationClause($column, $edge['kind'], $mode, $val);

			return $this;
		}
		if($mode === 'neq') {
			$this->bridgeBlockers[] = '!' . $column;
			$this->clauses[]        = [
				'sql'    => '`' . $column . '` <> ?',
				'params' => [$val],
			];

			return $this;
		}
		$this->addEquality($column, $val);

		return $this;
	}

	public function whereXfield(string $name, string $value): self {
		if(!in_array('xfields', $this->schema->columns(), true)) {
			throw new \InvalidArgumentException("Таблица {$this->table} не имеет колонки xfields");
		}
		if($name === '' || !preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
			throw new \InvalidArgumentException('Некорректное имя доп. поля');
		}
		$this->bridgeBlockers[] = 'xfield ' . $name;
		$this->clauses[] = [
			'sql'    => "CONCAT('||', `xfields`, '||') LIKE CONCAT('%||', ?, '|', ?, '||%')",
			'params' => [$name, $value],
		];

		return $this;
	}

	public function orderBy(string $column, string $sort = 'DESC'): self {
		if($column !== '' && in_array($column, $this->schema->columns(), true)) {
			$dir = strtoupper($sort) === 'ASC' ? 'ASC' : 'DESC';
			$this->orderClauses[] = [$column, $dir];
		}

		return $this;
	}

	/**
	 * @param array<string, string> $order колонка => ASC|DESC
	 */
	public function orderMap(array $order): self {
		foreach($order as $column => $sort) {
			$this->orderBy((string) $column, (string) $sort);
		}

		return $this;
	}

	public function limit(int $limit): self {
		$this->limit = max(1, min(200, $limit));

		return $this;
	}

	public function offset(int $offset): self {
		$this->offset = max(0, $offset);

		return $this;
	}

	/**
	 * @return list<array<string, mixed>>
	 */
	public function fetchAll(): array {
		$compiled = $this->compileSelect();
		$cacheKey = null;
		if(class_exists(\DevCraft\Core\Cache\CacheControl::class)) {
			$cacheKey = hash('sha256', $this->table . '|' . $compiled['sql'] . '|' . json_encode($compiled['params']));
			try {
				\DevCraft\Core\Cache\CacheControl::init();
				$cached = \DevCraft\Core\Cache\CacheControl::getCache('dle_api_query', $cacheKey);
				if(is_array($cached)) {
					return $cached;
				}
			} catch(\Throwable) {
			}
		}

		$rows = dle_api_db()->query($compiled['sql'], $compiled['params'])->fetchAll();
		$rows = is_array($rows) ? $rows : [];

		if($cacheKey !== null && class_exists(\DevCraft\Core\Cache\CacheControl::class)) {
			try {
				\DevCraft\Core\Cache\CacheControl::setCache('dle_api_query', $cacheKey, $rows);
			} catch(\Throwable) {
			}
		}

		return $rows;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function find(int|string $id): ?array {
		$cacheKey = null;
		if(class_exists(\DevCraft\Core\Cache\CacheControl::class)) {
			$cacheKey = hash('sha256', $this->table . '|find|' . $id);
			try {
				\DevCraft\Core\Cache\CacheControl::init();
				$cached = \DevCraft\Core\Cache\CacheControl::getCache('dle_api_query', $cacheKey);
				if(is_array($cached) || $cached === null) {
					return $cached;
				}
			} catch(\Throwable) {
			}
		}

		$row = dle_api_find($this->table, $id);

		if($cacheKey !== null && class_exists(\DevCraft\Core\Cache\CacheControl::class) && $row !== null) {
			try {
				\DevCraft\Core\Cache\CacheControl::setCache('dle_api_query', $cacheKey, $row);
			} catch(\Throwable) {
			}
		}

		return $row;
	}

	/**
	 * @return array{sql: string, params: list<mixed>}
	 */
	public function compileSelect(): array {
		$phys   = dle_api_table($this->table);
		$where  = ['1=1'];
		$params = [];
		foreach($this->clauses as $c) {
			$where[] = $c['sql'];
			foreach($c['params'] as $p) {
				$params[] = $p;
			}
		}
		$pk = is_string($this->schema->primaryKey())
			? $this->schema->primaryKey()
			: ($this->schema->columns()[0] ?? 'id');

		if($this->selectColumns !== []) {
			$select = implode(', ', array_map(static fn(string $c) => '`' . $c . '`', $this->selectColumns));
		} else {
			$select = '*';
		}

		$orderSql = [];
		foreach($this->orderClauses as [$col, $dir]) {
			$orderSql[] = '`' . $col . '` ' . $dir;
		}
		if($orderSql === []) {
			$orderSql[] = '`' . $pk . '` DESC';
		}

		$sql = 'SELECT ' . $select . ' FROM ' . $phys
			. ' WHERE ' . implode(' AND ', $where)
			. ' ORDER BY ' . implode(', ', $orderSql)
			. ' LIMIT ' . $this->limit . ' OFFSET ' . $this->offset;

		return ['sql' => $sql, 'params' => $params];
	}

	/**
	 * @return array{sql: string, params: list<mixed>}
	 */
	public function compileWhere(): array {
		$parts  = [];
		$params = [];
		foreach($this->clauses as $c) {
			$parts[] = $c['sql'];
			foreach($c['params'] as $p) {
				$params[] = $p;
			}
		}

		return [
			'sql'    => $parts === [] ? '1=1' : implode(' AND ', $parts),
			'params' => $params,
		];
	}

	/**
	 * Переносит equality-запрос в generic {@see QueryBuilder} (DataLoaderService).
	 *
	 * Поддерживаются только `columns` / равенства / `order` / `limit` / `offset`.
	 * LIKE (`%`), отрицание (`!`), RelationMap и `whereXfield()` через QueryBuilder
	 * не выражаются — в этом случае бросается исключение.
	 *
	 * @since 200.4.1
	 *
	 * @return QueryBuilder Билдер с теми же таблицей, колонками, условиями и сортировкой.
	 *
	 * @throws SdkException Если запрос содержит несовместимые условия.
	 *
	 * @example
	 *     $rows = DcApi::query('post')->where('approve', '1')->limit(10)->toQueryBuilder()->load();
	 */
	public function toQueryBuilder(): QueryBuilder {
		if($this->bridgeBlockers !== []) {
			throw new SdkException(
				'query_bridge_unsupported',
				__('QueryBuilder поддерживает только равенства; несовместимые условия: {list}', [
					'{list}' => implode(', ', $this->bridgeBlockers),
				]),
				422,
			);
		}

		$builder = QueryBuilder::create($this->table)
			->withLimit($this->limit)
			->withOffset($this->offset);

		$pk = $this->schema->primaryKey();
		if(is_string($pk)) {
			$builder = $builder->withPrimaryKey($pk);
		}
		if($this->selectColumns !== []) {
			$builder = $builder->withColumns($this->selectColumns);
		}
		foreach($this->equalities as $column => $value) {
			$builder = $builder->withConditionsItem($column, $value);
		}
		foreach($this->orderClauses as [$column, $dir]) {
			$builder = $builder->withOrderItem($column, SortDirection::fromString($dir));
		}

		return $builder;
	}

	/**
	 * Собирает Schema-aware {@see TableQuery} из generic {@see QueryBuilder}.
	 *
	 * Таблица обязана быть в {@see SchemaRegistry} (ядро DLE): таблицы модулей
	 * (`api_*` / `dc_*`) остаются за Cycle ORM. Условия вида
	 * `['op' => ..., 'value' => ...]` не поддерживаются.
	 *
	 * @since 200.4.1
	 *
	 * @param   QueryBuilder  $builder  Билдер с equality-условиями.
	 *
	 * @return self Запрос с валидированными по схеме колонками.
	 *
	 * @throws SdkException              Если условие не является равенством.
	 * @throws \InvalidArgumentException Если таблицы нет в SchemaRegistry.
	 *
	 * @example
	 *     $rows = TableQuery::fromQueryBuilder(QueryBuilder::create('users')->withLimit(5))->fetchAll();
	 */
	public static function fromQueryBuilder(QueryBuilder $builder): self {
		$query   = self::of($builder->getTable());
		$columns = $builder->getColumns();
		if($columns !== []) {
			$query->select($columns);
		}
		foreach($builder->getConditions() as $column => $value) {
			$column = (string) $column;
			if(is_array($value) || is_object($value)) {
				throw new SdkException(
					'query_bridge_unsupported',
					__('TableQuery принимает только равенства; условие «{column}» составное', ['{column}' => $column]),
					422,
				);
			}
			if(!in_array($column, $query->schema->columns(), true)) {
				throw SdkException::unknownColumn($query->table, $column);
			}
			$query->addEquality($column, $value === null ? '' : (string) $value);
		}
		foreach($builder->getOrder() as $column => $dir) {
			$query->orderBy((string) $column, $dir instanceof SortDirection ? $dir->value : (string) $dir);
		}
		$limit = $builder->getLimit();
		if($limit !== NULL) {
			$query->limit($limit);
		}
		$offset = $builder->getOffset();
		if($offset !== NULL) {
			$query->offset($offset);
		}

		return $query;
	}

	/**
	 * Добавляет условие-равенство без разбора префиксов `!` / `%`.
	 */
	private function addEquality(string $column, string $value): void {
		$this->clauses[]           = [
			'sql'    => '`' . $column . '` = ?',
			'params' => [$value],
		];
		$this->equalities[$column] = $value;
	}

	private function addRelationClause(string $column, string $kind, string $mode, string $val): void {
		$neg = $mode === 'neq';
		if($kind === RelationMap::KIND_CSV || $kind === RelationMap::KIND_CSV_OR_ALL) {
			if($kind === RelationMap::KIND_CSV_OR_ALL && $val === 'all') {
				$sql    = "(FIND_IN_SET(?, `{$column}`) OR `{$column}` = ?)";
				$params = ['all', 'all'];
			} else {
				$sql    = "FIND_IN_SET(?, `{$column}`)";
				$params = [$val];
			}
			if($this->table === 'post' && $column === 'category') {
				$pk   = is_string($this->schema->primaryKey()) ? $this->schema->primaryKey() : 'id';
				$cats = dle_api_table('post_extras_cats');
				$sql  = '(' . $sql . " OR EXISTS (SELECT 1 FROM {$cats} pec WHERE pec.news_id = `{$pk}` AND pec.cat_id = ?))";
				$params[] = (int) $val > 0 ? (int) $val : $val;
			}
			if($neg) {
				$sql = 'NOT (' . $sql . ')';
			}
			$this->clauses[] = ['sql' => $sql, 'params' => $params];

			return;
		}
		$op = $neg ? '<>' : '=';
		$this->clauses[] = [
			'sql'    => '`' . $column . '` ' . $op . ' ?',
			'params' => [$val],
		];
	}

	/**
	 * @return array{0: 'eq'|'neq'|'like', 1: string}
	 */
	private static function parseMode(string $raw): array {
		if($raw !== '' && $raw[0] === '!') {
			return ['neq', substr($raw, 1)];
		}
		if($raw !== '' && $raw[0] === '%') {
			return ['like', substr($raw, 1)];
		}

		return ['eq', $raw];
	}
}
