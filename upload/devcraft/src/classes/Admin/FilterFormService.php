<?php
//===============================================================
// Файл: FilterFormService.php                                  =
// Путь: devcraft/src/classes/Admin/FilterFormService.php       =
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

namespace DevCraft\Core\Admin;

use DateTimeImmutable;
use DevCraft\Types\FormField;
use DevCraft\Core\Config\Paths;
use DevCraft\Types\FilterSchema;
use Cycle\ORM\RepositoryInterface;
use Cycle\Database\Query\SelectQuery;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Interfaces\FilterableRepositoryInterface;

/**
 * Сервис фильтрации списковых страниц админки (наследник логики TwigFilter).
 *
 * @package    DevCraft
 * @since      173.3.4
 * @subpackage Core.Admin
 */
final class FilterFormService {

	/**
	 * Кэш view-model каталога фильтров по ключу схемы и репозитория.
	 *
	 * @since 200.4.0
	 * @var array<string, mixed>
	 */
	private array $catalogCache = [];

	/**
	 * Возвращает карту стандартных PHP-фильтров для query-параметров админки.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string|int, int|string|null>  $additionalFilters  Дополнительные фильтры id => filter_var.
	 *
	 * @return array<string, int> Объединённые фильтры без null-значений.
	 *
	 * @example
	 *     $filters = FilterFormService::getDefaultFilters(['status' => FILTER_VALIDATE_INT]);
	 */
	public static function getDefaultFilters(array $additionalFilters = []): array {
		$defaultFilters = [
			'page'   => FILTER_VALIDATE_INT,
			'mod'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS|FILTER_NULL_ON_FAILURE,
			'action' => FILTER_SANITIZE_FULL_SPECIAL_CHARS|FILTER_NULL_ON_FAILURE,
			'sites'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS|FILTER_NULL_ON_FAILURE,
			'order'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS|FILTER_NULL_ON_FAILURE,
			'sort'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS|FILTER_FLAG_STRIP_LOW|FILTER_NULL_ON_FAILURE,
		];

		foreach($additionalFilters as $key => $value) {
			if(is_int($key)) {
				[$key, $value] = [$value, NULL];
			}

			$defaultFilters[(string) $key] = $value ?? FILTER_SANITIZE_FULL_SPECIAL_CHARS;
		}

		return array_filter(
			$defaultFilters,
			static fn(mixed $filter): bool => $filter !== NULL,
		);
	}

	/**
	 * Преобразует строку направления сортировки в константу SelectQuery.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $sort  Строка asc/desc.
	 *
	 * @return string SelectQuery::SORT_ASC или SelectQuery::SORT_DESC.
	 *
	 * @example
	 *     $direction = FilterFormService::getSort('asc');
	 */
	public static function getSort(string $sort): string {
		return match (strtolower($sort)) {
			'asc' => SelectQuery::SORT_ASC,
			default      => SelectQuery::SORT_DESC,
		};
	}

	/**
	 * Определяет размер страницы списка из настроек DevCraft или DLE.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed> $config Глобальная конфигурация DLE.
	 *
	 * @return int Число записей на странице.
	 *
	 * @example
	 *     $count = FilterFormService::resolveListCount();
	 */
	public static function resolveListCount(): int {
		global $config;

		$settings = DevCraftConfig::all();
		$count    = (int) ($settings['list_count'] ?? 0);

		if($count > 0) {
			return $count;
		}

		return (int) ($config['news_number'] ?? 10);
	}

	/**
	 * Нормализует имя колонки сортировки по допустимым ключам схемы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string        $order   Запрошенная колонка.
	 * @param   FilterSchema  $schema  Схема фильтра.
	 *
	 * @return string Валидная колонка или исходное значение.
	 *
	 * @example
	 *     $order = FilterFormService::normalizeOrder('time', $schema);
	 */
	public static function normalizeOrder(string $order, FilterSchema $schema): string {
		$columns = $schema->sortColumnKeys();

		if($columns === []) {
			return $order;
		}

		return in_array($order, $columns, true)? $order : $schema->defaultOrder;
	}

	/**
	 * Формирует URL Ajax-запроса таблицы с параметрами фильтра.
	 *
	 * @param   array<string, mixed>  $query     Текущие query-параметры.
	 * @param   string                $userHash  CSRF-хеш пользователя DLE.
	 * @param   string                $order     Колонка сортировки.
	 * @param   string                $sort      Направление сортировки.
	 * @param   string                $method    Ajax-метод (logs_table, composer_table, …).
	 *
	 * @return string Полный URL ajax-контроллера.
	 */
	public function buildTableAjaxUrl(
		array $query,
		string $userHash,
		string $order,
		string $sort,
		string $method,
	): string {
		$params = [
			'controller' => 'admin',
			'method'     => $method,
			'user_hash'  => $userHash,
			'order'      => $order,
			'sort'       => $sort,
		];

		if(isset($query['filter_rules']) && is_array($query['filter_rules'])) {
			$params['filter_rules'] = $query['filter_rules'];
		}

		return Paths::ajaxBase() . '?' . http_build_query($params);
	}

	/**
	 * Формирует URL Ajax-запроса таблицы журнала с параметрами фильтра.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $query     Текущие query-параметры.
	 * @param   string                $userHash  CSRF-хеш пользователя DLE.
	 * @param   string                $order     Колонка сортировки.
	 * @param   string                $sort      Направление сортировки.
	 *
	 * @return string Полный URL ajax-контроллера.
	 *
	 * @example
	 *     $url = $service->buildLogsTableAjaxUrl($query, $hash, 'time', 'DESC');
	 */
	public function buildLogsTableAjaxUrl(array $query, string $userHash, string $order, string $sort): string {
		return $this->buildTableAjaxUrl($query, $userHash, $order, $sort, 'logs_table');
	}

	/**
	 * Читает и санитизирует query-параметры текущего GET-запроса.
	 *
	 * @since 173.3.4
	 *
	 * @return array<string, mixed> Нормализованные параметры запроса.
	 *
	 * @example
	 *     $query = (new FilterFormService())->parseRequestQuery();
	 */
	public function parseRequestQuery(): array {
		$inputFilters = self::getDefaultFilters();
		/** @var array<string, mixed> $query */
		$query = filter_input_array(INPUT_GET, $inputFilters)? : [];

		if(isset($_GET['filter_rules'])) {
			$query['filter_rules'] = DataManager::sanitizeArrayInput(
				$_GET['filter_rules'],
				[FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			);
		}

		if(isset($query['sort']) && is_string($query['sort'])) {
			$query['sort'] = strtoupper($query['sort']);
		}

		return $query;
	}

	/**
	 * Разбирает массив filter_rules в список нормализованных правил.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $query  Query с ключом filter_rules.
	 *
	 * @return list<array<string, mixed>> Завершённые правила фильтрации.
	 *
	 * @example
	 *     $rules = $service->parseRules($query);
	 */
	public function parseRules(array $query): array {
		$raw = $query['filter_rules'] ?? NULL;

		if(!is_array($raw)) {
			return [];
		}

		$rules = [];

		foreach($raw as $rule) {
			if(!is_array($rule)) {
				continue;
			}

			$field = (string) ($rule['field'] ?? '');
			$type  = (string) ($rule['type'] ?? '');

			if($field === '' || $type === '') {
				continue;
			}

			$normalized = [
				'field' => $field,
				'type'  => $type,
			];

			if(isset($rule['value'])) {
				$normalized['value'] = $rule['value'];
			}

			if(isset($rule['value_from'])) {
				$normalized['value_from'] = (string) $rule['value_from'];
			}

			if(isset($rule['value_to'])) {
				$normalized['value_to'] = (string) $rule['value_to'];
			}

			if($this->isRuleComplete($normalized)) {
				$rules[] = $normalized;
			} elseif(LogGenerator::isDebugEnabled()) {
				LogGenerator::for('FilterFormService')->debug(__('parseRules: {rule} не могло быть обработано', ['{rule}' => $normalized]),
					['rule' => $normalized]);
			}
		}

		return $rules;
	}

	/**
	 * Строит view-model каталога полей фильтра с опциями из репозитория.
	 *
	 * @since 200.4.0
	 *
	 * @param   FilterSchema              $schema  Схема фильтра.
	 * @param   RepositoryInterface|null  $repo    Репозиторий для динамических choices.
	 *
	 * @return array{sections: list<array{title: string, fields: list<array<string, mixed>>}>} Каталог секций.
	 *
	 * @example
	 *     $catalog = $service->buildCatalogViewModel($schema, $repo);
	 */
	public function buildCatalogViewModel(FilterSchema $schema, ?RepositoryInterface $repo = NULL): array {
		$cacheKey = spl_object_hash($schema) . ':' . ($repo === NULL? '0' : spl_object_hash($repo));

		if(isset($this->catalogCache[$cacheKey])) {
			/** @var array{sections: list<array{title: string, fields: list<array<string, mixed>>}>} */
			return $this->catalogCache[$cacheKey];
		}

		$sections = [];

		foreach($schema->sections as $section) {
			$fields = [];

			foreach($section->fields as $field) {
				$fields[] = $this->enrichCatalogField($field, $repo, $schema);
			}

			$sections[] = [
				'title'  => $section->title,
				'fields' => $fields,
			];
		}

		$viewModel                     = ['sections' => $sections];
		$this->catalogCache[$cacheKey] = $viewModel;

		return $viewModel;
	}

	/**
	 * Формирует view-model чипов активных правил фильтра.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array<string, mixed>>  $rules   Активные правила.
	 * @param   FilterSchema                $schema  Схема фильтра.
	 *
	 * @return list<array{index: int, field: string, label: string, summary: string}> Чипы для UI.
	 *
	 * @example
	 *     $chips = $service->buildChipViewModel($rules, $schema);
	 */
	public function buildChipViewModel(array $rules, FilterSchema $schema): array {
		$labels = [];

		foreach($schema->allFields() as $field) {
			$labels[$field->id] = $field->label;
		}

		$chips = [];

		foreach($rules as $index => $rule) {
			$fieldId = (string) ($rule['field'] ?? '');
			$type    = (string) ($rule['type'] ?? '');

			$chips[] = [
				'index'   => $index,
				'field'   => $fieldId,
				'label'   => $labels[$fieldId] ?? $fieldId,
				'summary' => $this->formatRuleSummary($rule, $labels[$fieldId] ?? $fieldId),
				'type'    => $type,
			];
		}

		return $chips;
	}


	/**
	 * Создаёт список значений для multi-фильтра по distinct-колонке репозитория.
	 *
	 * @since 200.4.0
	 *
	 * @param   RepositoryInterface  $repo    Репозиторий с FilterableRepositoryInterface.
	 * @param   string               $name    Id поля (legacy-параметр).
	 * @param   string               $column  Имя SQL-колонки.
	 * @param   FilterSchema         $schema  Схема фильтра.
	 *
	 * @return array<string, string> Карта value => label.
	 *
	 * @example
	 *     $choices = $service->createFilterChoices($repo, 'level', 'level', $schema);
	 */
	public function createFilterChoices(
		RepositoryInterface $repo,
		string              $name,
		string              $column,
		FilterSchema        $schema,
	): array {
		if(!$repo instanceof FilterableRepositoryInterface) {
			return [];
		}

		if(!in_array($column, $schema->filterDbColumns(), true)) {
			return [];
		}

		$choices = ['' => 'Все'];
		$values  = $repo->distinctColumnValues($column);

		foreach($values as $value) {
			$choices[$value] = $value;
		}

		return $choices;
	}

	/**
	 * Создаёт конфигурацию range-фильтра по границам колонки.
	 *
	 * @since 200.4.0
	 *
	 * @param   RepositoryInterface  $repo    Репозиторий данных.
	 * @param   string               $column  Имя SQL-колонки.
	 * @param   string               $label   Подпись фильтра.
	 * @param   FilterSchema         $schema  Схема фильтра.
	 *
	 * @return array<string, array<string, mixed>> Конфигурация по ключу колонки.
	 *
	 * @example
	 *     $filter = $service->createRangeFilter($repo, 'rating', __('Рейтинг'), $schema);
	 */
	public function createRangeFilter(
		RepositoryInterface $repo,
		string              $column,
		string              $label,
		FilterSchema        $schema,
	): array {
		if(!$repo instanceof FilterableRepositoryInterface) {
			return [];
		}

		if(!in_array($column, $schema->filterDbColumns(), true)) {
			return [];
		}

		$bounds = $repo->columnBounds($column);
		$from   = $bounds['min'] ?? 0;
		$to     = $bounds['max'] ?? 10;

		return [
			$column => [
				'type'   => 'range',
				'label'  => $label,
				'from'   => $from,
				'to'     => $to,
				'values' => range((int) $from, (int) $to),
			],
		];
	}

	/**
	 * Создаёт конфигурацию daterange-фильтра по границам дат колонки.
	 *
	 * @since 200.4.0
	 *
	 * @param   RepositoryInterface  $repo    Репозиторий данных.
	 * @param   string               $column  Имя SQL-колонки.
	 * @param   string               $label   Подпись фильтра.
	 * @param   FilterSchema         $schema  Схема фильтра.
	 *
	 * @return array<string, array<string, mixed>> Конфигурация по ключу колонки.
	 *
	 * @example
	 *     $filter = $service->createDateRangeFilter($repo, 'time', __('Дата'), $schema);
	 */
	public function createDateRangeFilter(
		RepositoryInterface $repo,
		string              $column,
		string              $label,
		FilterSchema        $schema,
	): array {
		if(!$repo instanceof FilterableRepositoryInterface) {
			return [];
		}

		if(!in_array($column, $schema->filterDbColumns(), true)) {
			return [];
		}

		$bounds = $repo->columnBounds($column);
		$now    = (new DateTimeImmutable())->format('Y-m-d H:i:s');
		$first  = (string) ($bounds['min'] ?? $now);
		$last   = (string) ($bounds['max'] ?? $now);

		return [
			$column => [
				'type'  => 'daterange',
				'label' => $label,
				'min'   => $first,
				'max'   => $last,
			],
		];
	}

	/**
	 * Преобразует UI-правила в критерии запроса репозитория.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array<string, mixed>>  $rules   Активные правила.
	 * @param   FilterSchema                $schema  Схема фильтра.
	 *
	 * @return list<array{column: string, op: string, value: mixed}> Критерии where.
	 *
	 * @example
	 *     $criteria = $service->rulesToCriteria($rules, $schema);
	 */
	public function rulesToCriteria(array $rules, FilterSchema $schema): array {
		$criteria = [];

		foreach($rules as $rule) {
			$field = $this->findField($schema, (string) ($rule['field'] ?? ''));

			if($field === NULL) {
				continue;
			}

			$column = (string) ($field->metro['db_column'] ?? $field->id);
			$type   = (string) ($rule['type'] ?? $field->type);

			match ($type) {
				'multi'     => $this->appendMultiCriterion($criteria, $column, $rule),
				'text'      => $this->appendTextCriterion($criteria, $column, $rule),
				'daterange' => $this->appendBetweenCriterion($criteria, $column, $rule, 'daterange'),
				'range'     => $this->appendBetweenCriterion($criteria, $column, $rule, 'range'),
				default     => NULL,
			};
		}

		return $criteria;
	}


	/**
	 * Проверяет, содержит ли черновик правила все обязательные значения.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $rule  Нормализованное правило.
	 *
	 * @return bool true, если правило можно применить.
	 */
	private function isRuleComplete(array $rule): bool {
		$type = (string) ($rule['type'] ?? '');

		return match ($type) {
			'multi'              => is_array($rule['value'] ?? NULL) && $rule['value'] !== [],
			'text'               => trim((string) ($rule['value'] ?? '')) !== '',
			'daterange', 'range' => trim((string) ($rule['value_from'] ?? '')) !== ''
			                        && trim((string) ($rule['value_to'] ?? '')) !== '',
			default              => false,
		};
	}

	/**
	 * Обогащает поле каталога данными choices или границ диапазона.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormField                 $field   Поле схемы.
	 * @param   RepositoryInterface|null  $repo    Репозиторий или null.
	 * @param   FilterSchema              $schema  Схема фильтра.
	 *
	 * @return array<string, mixed> Данные поля для Twig.
	 */
	private function enrichCatalogField(FormField $field, ?RepositoryInterface $repo, FilterSchema $schema): array {
		$column = (string) ($field->metro['db_column'] ?? $field->id);
		$data   = [
			'id'    => $field->id,
			'type'  => $field->type,
			'label' => $field->label,
			'metro' => $field->metro,
		];

		if($repo === NULL) {
			return $data;
		}

		return match ($field->type) {
			'multi'     => array_merge($data, [
				'options' => $this->createFilterChoices($repo, $field->id, $column, $schema),
			]),
			'range'     => array_merge($data, $this->createRangeFilter($repo, $column, $field->label, $schema)[$column] ?? []),
			'daterange' => array_merge($data, $this->createDateRangeFilter($repo, $column, $field->label, $schema)[$column] ?? []),
			default     => $data,
		};
	}

	/**
	 * Находит поле схемы по id.
	 *
	 * @since 200.4.0
	 *
	 * @param   FilterSchema  $schema   Схема фильтра.
	 * @param   string        $fieldId  Id поля.
	 *
	 * @return FormField|null Поле или null.
	 */
	private function findField(FilterSchema $schema, string $fieldId): ?FormField {
		foreach($schema->allFields() as $field) {
			if($field->id === $fieldId) {
				return $field;
			}
		}

		return NULL;
	}

	/**
	 * Формирует краткую текстовую сводку правила для чипа.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $rule   Правило фильтра.
	 * @param   string                $label  Подпись поля.
	 *
	 * @return string Текстовая сводка.
	 */
	private function formatRuleSummary(array $rule, string $label): string {
		$type = (string) ($rule['type'] ?? '');

		return match ($type) {
			'multi'              => $label . ': ' . implode(', ', array_map('strval', (array) ($rule['value'] ?? []))),
			'text'               => $label . ': «' . ($rule['value'] ?? '') . '»',
			'daterange', 'range' => $label . ': ' . ($rule['value_from'] ?? '')
			                        . ' — ' . ($rule['value_to'] ?? ''),
			default              => $label,
		};
	}

	/**
	 * Добавляет критерий IN для multi-правила.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria  Массив критериев (по ссылке).
	 * @param   string                                                 $column    SQL-колонка.
	 * @param   array<string, mixed>                                   $rule      Правило фильтра.
	 */
	private function appendMultiCriterion(array &$criteria, string $column, array $rule): void {
		$values = $rule['value'] ?? [];

		if(!is_array($values)) {
			$values = [(string) $values];
		}

		$values = array_values(array_filter(
			array_map(static fn(mixed $value): string => trim((string) $value), $values),
			static fn(string $value): bool => $value !== '',
		));

		if($values === []) {
			return;
		}

		$criteria[] = [
			'column' => $column,
			'op'     => 'in',
			'value'  => $values,
		];
	}

	/**
	 * Добавляет критерий LIKE для text-правила.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria  Массив критериев (по ссылке).
	 * @param   string                                                 $column    SQL-колонка.
	 * @param   array<string, mixed>                                   $rule      Правило фильтра.
	 */
	private function appendTextCriterion(array &$criteria, string $column, array $rule): void {
		$value = trim((string) ($rule['value'] ?? ''));

		if($value === '') {
			return;
		}

		$criteria[] = [
			'column' => $column,
			'op'     => 'like',
			'value'  => $value,
		];
	}

	/**
	 * Добавляет критерий BETWEEN для range/daterange-правила.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria  Массив критериев (по ссылке).
	 * @param   string                                                 $column    SQL-колонка.
	 * @param   array<string, mixed>                                   $rule      Правило фильтра.
	 * @param   string                                                 $type      Тип диапазона (range|daterange).
	 */
	private function appendBetweenCriterion(array &$criteria, string $column, array $rule, string $type): void {
		$from = trim((string) ($rule['value_from'] ?? ''));
		$to   = trim((string) ($rule['value_to'] ?? ''));

		if($from === '' || $to === '') {
			if($type === 'daterange' && LogGenerator::isDebugEnabled()) {
				LogGenerator::for('FilterFormService')->debug(__('appendBetweenCriterion: пустые значения для диапазона от/до'), ['rule' => $rule]);
			}

			return;
		}

		if($type === 'daterange' && preg_match('/^\d{4}-\d{2}-\d{2} 00:00:00$/', $to) === 1) {
			$to = str_replace('00:00:00', '23:59:59', $to);
		}

		if(LogGenerator::isDebugEnabled()) {
			LogGenerator::for('FilterFormService')->debug('appendBetweenCriterion', [
				'column' => $column,
				'type'   => $type,
				'from'   => $from,
				'to'     => $to,
				'rule'   => $rule,
			]);
		}

		$criteria[] = [
			'column' => $column,
			'op'     => 'between',
			'value'  => [
				'from' => $from,
				'to'   => $to,
				'type' => $type,
			],
		];
	}

}
