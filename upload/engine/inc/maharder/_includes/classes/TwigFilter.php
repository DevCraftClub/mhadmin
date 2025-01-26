<?php

use Cycle\ORM\RepositoryInterface;
use Cycle\Database\Query\SelectQuery;
use JetBrains\PhpStorm\ExpectedValues;

class TwigFilter {
	private RepositoryInterface $repository;

	/**
	 * @param RepositoryInterface $repository
	 */
	public function __construct(RepositoryInterface $repository) { $this->setRepository($repository); }

	/**
	 * Создает фильтр для указанного столбца с заданным типом и ярлыком.
	 *
	 * @param string      $column_name   Название столбца, для которого создается фильтр.
	 * @param string      $type          Тип фильтра. Ожидаемые значения: 'select', 'text', 'tags', 'checkbox'.
	 * @param string      $label         Ярлык, отображаемый на фильтре.
	 * @param string|null $select_value  Поле, используемое для получения вариантов выбора в фильтре
	 *                                   (актуально для типов 'select' и 'tags').
	 *                                   Если не указано, берется название столбца.
	 * @param array|null  $choices       Пользовательские значения для фильтра. Если не указано, значения будут
	 *                                   рассчитаны с помощью метода {@see createFilterChoices()}.
	 *
	 * @return array Возвращает массив конфигурации фильтра.
	 *
	 * @throws InvalidArgumentException Если значение `$type` не соответствует ожидаемым.
	 *
	 * @see createFilterChoices() Используется для формирования списка значений для типов 'select' и 'tags'.
	 * @see getRepository() Используется внутри метода createFilterChoices для получения данных.
	 * @see __() Используется в createFilterChoices для перевода текста.
	 */
	public function createFilter(
		string $column_name,
		#[ExpectedValues(values: ['select', 'text', 'tags', 'checkbox'])]
		string $type,
		string $label,
		?string $select_value = null,
		?array $choices = null
	): array {
		$filter = [
			$column_name => [
				'type' => $type,
				'label' => $label
			]
		];

		if (in_array($type, ['select', 'tags'])) {
			$select_value = $select_value ?? $column_name;
			$filter[$column_name]['choices'] = $choices ?: $this->createFilterChoices($column_name, $select_value);
		}

		return $filter;
	}

	/**
	 * Создает массив вариантов для фильтра на основе переданных данных.
	 *
	 * @param string $name   Имя колонки, из которой берется значение фильтра.
	 * @param string $select Имя SQL-колонки, используемой в запросе для группировки данных.
	 *
	 * @return array Ассоциативный массив, где ключи — значения фильтров, а значения — отображаемые имена фильтров.
	 *
	 * @see __ Используется для локализации первой строки массива фильтров.
	 * @see translate Вызванный косвенно через функцию __.
	 * @see getRepository Используется для получения данных из базы через методы репозитория.
	 */
	public function createFilterChoices($name, $select): array {
		$filter = [
			'' => __('Все')
		];

		$filteredData = array_column(
			array_map(fn($item) => ['name' => $item->getColumnVal($name), 'value' => $item->getColumnVal($name)],
				$this->getRepository()->select()->columns([$select])->groupBy($select)->fetchAll()),
			'value',
			'name'
		);

		return array_merge($filter, $filteredData);
	}

	/**
	 * Определяет направление сортировки на основе переданной строки.
	 *
	 * Возвращает значение константы, соответствующее направлению сортировки:
	 * - Если передана строка 'asc' или 'ASC', возвращается SelectQuery::SORT_ASC.
	 * - В остальных случаях возвращается SelectQuery::SORT_DESC.
	 *
	 * @param string $sort Строка, определяющая направление сортировки (например, 'asc', 'desc').
	 *
	 * @return string Возвращаемое значение сортировки: SelectQuery::SORT_ASC или SelectQuery::SORT_DESC.
	 *
	 * @see SelectQuery
	 */
	public static function getSort(string $sort): string {
		return match ($sort) {
			'asc', 'ASC' => SelectQuery::SORT_ASC,
			default => SelectQuery::SORT_DESC
		};
	}

	/**
	 * Создает и возвращает массив стандартных фильтров для входящих данных.
	 *
	 * Стандартные фильтры включают:
	 * - 'page' — валидация целого числа.
	 * - 'mod', 'action', 'sites', 'order' — очистка данных с помощью `FILTER_SANITIZE_FULL_SPECIAL_CHARS`
	 *   и возврат `null`, если фильтрация не удалась.
	 * - 'sort' — очистка данных аналогично предыдущим, но с приведением к верхнему регистру.
	 *
	 * Метод предоставляет возможность добавлять пользовательские фильтры с помощью аргумента `$additionalFilters`.
	 * Если значение пользовательского фильтра равно `null`, используется стандартный фильтр `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
	 *
	 * @param array $additionalFilters Ассоциативный массив дополнительных фильтров, где ключи — названия,
	 *                                  а значения — их типы.
	 *                                  Если значение `null`, применяется стандартный `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
	 *
	 * @return array Ассоциативный массив, содержащий объединенные стандартные и дополнительные фильтры.
	 */
	public static function getDefaultFilters(array $additionalFilters = []): array {
		$defaultFilters = [
			'page'          => FILTER_VALIDATE_INT,
			'mod'           => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'action'        => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'sites'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'order'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'sort'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS | CASE_UPPER | FILTER_NULL_ON_FAILURE
		];

		foreach ($additionalFilters as $key => $value) {
			// Если значение пустое, используем дефолтную очистку FILTER_SANITIZE_FULL_SPECIAL_CHARS
			if (is_int($key)) [$key, $value] = [$value, null];
			$defaultFilters[$key] = $value ?? FILTER_SANITIZE_FULL_SPECIAL_CHARS;
		}

		return array_filter($defaultFilters, fn($filter) => $filter !== null);

	}

	/**
	 * Возвращает используемый репозиторий.
	 *
	 * @return RepositoryInterface Текущий экземпляр репозитория, связанный с данным объектом.
	 *
	 * @see TwigFilter::$repository
	 * @see TwigFilter::setRepository()
	 */
	public function getRepository(): RepositoryInterface {
		return $this->repository;
	}

	/**
	 * Устанавливает репозиторий для использования в текущем экземпляре класса.
	 *
	 * @param RepositoryInterface $repository Экземпляр репозитория, который будет использоваться в текущем объекте.
	 *
	 * @see TwigFilter::$repository
	 */
	public function setRepository(RepositoryInterface $repository): void {
		$this->repository = $repository;
	}



}