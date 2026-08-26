<?php
//===============================================================
// Файл: QueryBuilder.php                                       =
// Путь: devcraft/src/classes/Builders/QueryBuilder.php         =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Builders;

use Lombok\Getter;
use Devcraft\Attributes\With;
use Devcraft\Attributes\WithItem;
use Devcraft\Abstracts\AbstractWith;
use DevCraft\Core\Enums\SortDirection;
use DevCraft\Core\Support\DataLoaderService;

/**
 * Fluent-аргументы для {@see DataLoaderService} (SELECT / INSERT / UPDATE / DELETE).
 *
 * Свойства мутируются через `#[With]` / `#[WithItem]` (`withConditionsItem`, `withColumnsItem` и т. д.).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 *
 * @method self withTable(string $table)
 * @method self withColumns(list<string> $columns)
 * @method self withColumnsItem(string $column)
 * @method self withConditions(array<string, mixed> $conditions)
 * @method self withConditionsItem(string $column, mixed $value)
 * @method self withValues(array<string, mixed> $values)
 * @method self withValuesItem(string $column, mixed $value)
 * @method self withPrimaryKey(string $primaryKey)
 * @method self withOrder(array<string, SortDirection|string> $order)
 * @method self withOrderItem(string $column, SortDirection|string $direction)
 * @method self withLimit(?int $limit)
 * @method self withOffset(?int $offset)
 * @method string getTable()
 * @method list<string> getColumns()
 * @method array<string, mixed> getConditions()
 * @method array<string, mixed> getValues()
 * @method string getPrimaryKey()
 * @method array<string, SortDirection|string> getOrder()
 * @method int|null getLimit()
 * @method int|null getOffset()
 */
#[Getter]
final class QueryBuilder extends AbstractWith {

	#[With]
	private string $table = '';

	/** @var list<string> */
	#[With, WithItem('string')]
	private array $columns = [];

	/**
	 * Условия: равенство или `['op' => string, 'value' => mixed]`.
	 *
	 * @var array<string, mixed>
	 */
	#[With, WithItem('string', 'mixed')]
	private array $conditions = [];

	/**
	 * Значения для INSERT/UPDATE (колонка => значение или Fragment).
	 *
	 * @var array<string, mixed>
	 */
	#[With, WithItem('string', 'mixed')]
	private array $values = [];

	#[With]
	private string $primaryKey = 'id';

	/** @var array<string, SortDirection|string> */
	#[With, WithItem('string', [SortDirection::class, 'string'])]
	private array $order = [];

	#[With]
	private ?int $limit = NULL;

	#[With]
	private ?int $offset = NULL;

	/**
	 * Создаёт билдер для таблицы DLE (без префикса).
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $table  Имя таблицы без PREFIX/USERPREFIX.
	 *
	 * @return self Новый экземпляр с заданной таблицей.
	 *
	 * @example
	 *     $q = QueryBuilder::create('users')->withConditionsItem('user_id', 1);
	 */
	public static function create(string $table): self {
		return (new self())->withTable($table);
	}

	/**
	 * Собирает массив аргументов для {@see DataLoaderService::loadData()}.
	 *
	 * @since 200.4.0
	 *
	 * @return array{
	 *     table: string,
	 *     columns?: list<string>,
	 *     conditions?: array<string, mixed>,
	 *     order?: array<string, string>,
	 *     limit?: int,
	 *     offset?: int
	 * }
	 *
	 * @example
	 *     $args = QueryBuilder::create('users')->withLimit(1)->build();
	 */
	public function build(): array {
		$args = ['table' => $this->table];

		if($this->columns !== []) {
			$args['columns'] = $this->columns;
		}

		if($this->conditions !== []) {
			$args['conditions'] = $this->conditions;
		}

		if($this->order !== []) {
			$normalized = [];
			foreach($this->order as $column => $direction) {
				$normalized[$column] = $direction instanceof SortDirection
					? $direction->value
					: SortDirection::fromString((string) $direction)->value;
			}
			$args['order'] = $normalized;
		}

		if($this->limit !== NULL) {
			$args['limit'] = $this->limit;
		}

		if($this->offset !== NULL) {
			$args['offset'] = $this->offset;
		}

		return $args;
	}

	/**
	 * Выполняет загрузку через DataLoaderService.
	 *
	 * @since 200.4.0
	 *
	 * @return array<int, array<string, mixed>> Строки результата.
	 *
	 * @example
	 *     $rows = QueryBuilder::create('users')->withLimit(10)->load();
	 */
	public function load(): array {
		return DataLoaderService::loadData($this);
	}

	/**
	 * Возвращает первую строку или пустой массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Первая строка либо [].
	 *
	 * @example
	 *     $user = QueryBuilder::create('users')
	 *         ->withConditionsItem('user_id', 1)
	 *         ->withLimit(1)
	 *         ->first();
	 */
	public function first(): array {
		return DataLoaderService::loadOne($this);
	}

	/**
	 * Вставляет строку по {@see $values} и возвращает созданную запись.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Созданная строка либо [].
	 *
	 * @example
	 *     $row = QueryBuilder::create('pm')
	 *         ->withValues(['subj' => 'Hi', 'user' => 1])
	 *         ->insert();
	 */
	public function insert(): array {
		return DataLoaderService::insert($this);
	}

	/**
	 * Обновляет строки по {@see $conditions} и возвращает все совпавшие записи.
	 *
	 * @since 200.4.0
	 *
	 * @return array<int, array<string, mixed>> Строки после обновления либо [].
	 *
	 * @example
	 *     $rows = QueryBuilder::create('users')
	 *         ->withValues(['fullname' => 1])
	 *         ->withConditionsItem('user_id', 1)
	 *         ->update();
	 */
	public function update(): array {
		return DataLoaderService::update($this);
	}

	/**
	 * Удаляет строки по {@see $conditions}.
	 *
	 * Без conditions DELETE не выполняется.
	 *
	 * @since 200.4.0
	 *
	 * @return bool true при успехе, false при ошибке или пустых conditions.
	 *
	 * @example
	 *     $ok = QueryBuilder::create('tags')
	 *         ->withConditionsItem('news_id', $newsId)
	 *         ->delete();
	 */
	public function delete(): bool {
		return DataLoaderService::delete($this);
	}

}
