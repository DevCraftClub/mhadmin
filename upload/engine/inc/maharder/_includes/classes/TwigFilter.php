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
	public function createFilter(string $column_name, #[ExpectedValues(values: ['select', 'text', 'tags', 'checkbox'])]
	string                              $type, string $label, ?string $select_value = null, ?array $choices = null): array {
		$filter = [
			$column_name => [
				'type'  => $type,
				'label' => $label
			]
		];

		if (in_array($type, ['select', 'tags'])) {
			$select_value                    = $select_value ?? $column_name;
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
	 * @throws Throwable
	 * @see translate Вызванный косвенно через функцию __.
	 * @see getRepository Используется для получения данных из базы через методы репозитория.
	 * @see __ Используется для локализации первой строки массива фильтров.
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
			default      => SelectQuery::SORT_DESC
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
	 * Если значение пользовательского фильтра равно `null`, используется стандартный фильтр
	 * `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
	 *
	 * @param array $additionalFilters  Ассоциативный массив дополнительных фильтров, где ключи — названия,
	 *                                  а значения — их типы.
	 *                                  Если значение `null`, применяется стандартный
	 *                                  `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
	 *
	 * @return array Ассоциативный массив, содержащий объединенные стандартные и дополнительные фильтры.
	 */
	public static function getDefaultFilters(array $additionalFilters = []): array {
		$defaultFilters = [
			'page'   => FILTER_VALIDATE_INT,
			'mod'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'action' => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'sites'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'order'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS | FILTER_NULL_ON_FAILURE,
			'sort'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS | CASE_UPPER | FILTER_NULL_ON_FAILURE
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

	/**
	 * Создает фильтр диапазона на основе указанных данных столбца.
	 *
	 * @param string               $column_name Название столбца, данные которого используются для определения
	 *                                          диапазона.
	 * @param string               $label       Метка для фильтра диапазона.
	 *
	 * @return array Возвращает массив конфигурации фильтра диапазона, включающий название столбца, метку, начальную и
	 *               конечную точки диапазона.
	 *
	 * @global RepositoryInterface $repository  Репозиторий, использующийся для выполнения запросов.
	 *
	 * @see getRepository() Метод получения текущего репозитория.
	 */
	public function createRangeFilter(string $column_name, string $label): array {
		$data  = (array)$this->getRepository()->select()->columns([$column_name])->orderBy($column_name)->sortBy(
			SelectQuery::SORT_ASC
		)->fetchAll();
		$first = $data[0][$column_name];
		$last  = $data[count($data) - 1][$column_name];

		return [
			$column_name => [
				'type'  => 'range',
				'label' => $label,
				'from'  => $first,
				'to'    => $last
			]
		];
	}

	/**
	 * Создает фильтр диапазона дат для указанного столбца.
	 *
	 * Фильтр включает информацию о диапазоне дат, включающую минимальное и максимальное значение
	 * на основе данных, извлеченных из репозитория.
	 *
	 * @param string               $column_name Название столбца, по которому создается фильтр.
	 * @param string               $label       Метка, которая будет отображаться для фильтра.
	 *
	 * @return array Ассоциативный массив с описанием фильтра, содержащий тип, метку, минимальное и максимальное
	 *               значения.
	 *
	 * @see \TwigFilter::getRepository Используется для получения репозитория данных.
	 * @see \TwigFilter::setRepository Используется для установки репозитория данных.
	 * @global RepositoryInterface $repository  Репозиторий, с которым связан класс.
	 */
	public function createDateRangeFilter(string $column_name, string $label): array {
		$data  = (array)$this->getRepository()->select()->columns([$column_name])->orderBy($column_name)->sortBy(
			SelectQuery::SORT_ASC
		)->fetchAll();
		$first = $data[0][$column_name];
		$last  = $data[count($data) - 1][$column_name];

		return [
			$column_name => [
				'type'  => 'daterange',
				'label' => $label,
				'min'   => $first,
				'max'   => $last
			]
		];
	}

}