<?php
//===============================================================
// Файл: FilterSchema.php                                       =
// Путь: devcraft/src/classes/Types/FilterSchema.php            =
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

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Декларативная схема фильтра списковых страниц админки.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property FormSection[]        $sections     Секции полей фильтра.
 * @property string               $defaultOrder Колонка сортировки по умолчанию.
 * @property array<string,string> $sortColumns  Явная карта колонок сортировки.
 */
final class FilterSchema extends AbstractType {

	/**
	 * Создаёт схему фильтра.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormSection[]         $sections      Секции полей фильтра.
	 * @param   string                $defaultOrder  Колонка сортировки по умолчанию.
	 * @param   array<string,string>  $sortColumns   Явная карта колонок сортировки.
	 *
	 * @example
	 *     $filter = new FilterSchema([$section], defaultOrder: 'created_at');
	 */
	public function __construct(
		public array  $sections,
		public string $defaultOrder = 'time',
		public array  $sortColumns = [],
	) {}

	/**
	 * Создаёт схему фильтра из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходная схема фильтра.
	 *
	 * @return static Новый экземпляр схемы.
	 *
	 * @example
	 *     $filter = FilterSchema::fromArray(require 'Filter/logs.filter.schema.php');
	 */
	public static function fromArray(array $data): static {
		$sections = [];

		if(isset($data['sections']) && is_array($data['sections'])) {
			foreach($data['sections'] as $section) {
				if(is_array($section)) {
					$sections[] = FormSection::fromArray($section);
				}
			}
		}

		$defaultOrder = 'time';
		$sortColumns  = [];

		if(isset($data['sort']) && is_array($data['sort'])) {
			$defaultOrder = (string) ($data['sort']['default'] ?? $defaultOrder);

			if(isset($data['sort']['columns']) && is_array($data['sort']['columns'])) {
				foreach($data['sort']['columns'] as $key => $label) {
					$sortColumns[(string) $key] = (string) $label;
				}
			}
		}

		return new self(
			sections    : $sections,
			defaultOrder: $defaultOrder,
			sortColumns : $sortColumns,
		);
	}

	/**
	 * Преобразует схему фильтра в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Сериализованная схема.
	 *
	 * @example
	 *     $data = $filter->toArray();
	 */
	public function toArray(): array {
		$data = [
			'sections' => array_map(
				static fn(FormSection $section): array => $section->toArray(),
				$this->sections,
			),
		];

		if($this->sortColumns !== [] || $this->defaultOrder !== 'time') {
			$data['sort'] = [
				'default' => $this->defaultOrder,
				'columns' => $this->sortColumns,
			];
		}

		return $data;
	}

	/**
	 * Возвращает ключи колонок сортировки.
	 *
	 * @since 200.4.0
	 *
	 * @return list<string> Список имён колонок БД.
	 *
	 * @example
	 *     $keys = $filter->sortColumnKeys();
	 */
	public function sortColumnKeys(): array {
		return array_keys($this->resolvedSortColumns());
	}

	/**
	 * Возвращает карту колонок сортировки с учётом явных и выведенных значений.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, string> Карта `column => label`.
	 *
	 * @example
	 *     $columns = $filter->resolvedSortColumns();
	 */
	public function resolvedSortColumns(): array {
		if($this->sortColumns !== []) {
			return $this->sortColumns;
		}

		$columns = [];

		foreach($this->allFields() as $field) {
			$column           = (string) ($field->metro['db_column'] ?? $field->id);
			$columns[$column] = $field->label;
		}

		return $columns;
	}

	/**
	 * Возвращает плоский список всех полей фильтра.
	 *
	 * @since 200.4.0
	 *
	 * @return FormField[] Все поля всех секций.
	 *
	 * @example
	 *     $fields = $filter->allFields();
	 */
	public function allFields(): array {
		$fields = [];

		foreach($this->sections as $section) {
			foreach($section->fields as $field) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Возвращает имена колонок БД, участвующих в фильтрации.
	 *
	 * @since 200.4.0
	 *
	 * @return list<string> Список имён колонок.
	 *
	 * @example
	 *     $columns = $filter->filterDbColumns();
	 */
	public function filterDbColumns(): array {
		$columns = [];

		foreach($this->allFields() as $field) {
			$columns[] = (string) ($field->metro['db_column'] ?? $field->id);
		}

		return $columns;
	}

}
