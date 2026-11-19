<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\DebugToolbar;

use DevCraft\Core\Application;
use DevCraft\Core\Config\DevCraftConfig;

/**
 * Сборщик и рендер отладочной панели.
 */
final class ToolbarService
{
	private static ?self $instance = null;

	/** @var array<string, float> */
	private static array $marks = [];

	private bool $booted = false;
	private bool $rendered = false;
	private bool $dleAdminHooked = false;
	private ?float $requestStartedAt = null;
	private ?array $viewModel = null;
	private bool $dleCollected = false;
	private string $surface = 'admin';

	public static function instance(): self
	{
		return self::$instance ??= new self();
	}

	/**
	 * Метка времени для таймлайна «куда ушло».
	 */
	public static function mark(string $name): void
	{
		self::$marks[$name] = microtime(true);
	}

	public function isBooted(): bool
	{
		return $this->booted;
	}

	/**
	 * Можно ли показывать панель (сбор + группа пользователя).
	 *
	 * @param   string  $surface  `admin` или `public`.
	 */
	public function isActive(string $surface): bool
	{
		return $this->canCollect($surface) && $this->currentUserAllowed();
	}

	/**
	 * Запуск на любом `admin.php` (настройки DLE, чужие модули) — не только DevCraft.
	 * Рендер в конце запроса; повтор на страницах DevCraft отсекается флагом rendered.
	 */
	public function hookDleAdmin(): void
	{
		if($this->dleAdminHooked || !$this->isDleAdminScript()) {
			return;
		}

		$this->dleAdminHooked = true;
		// Ранний сбор SQL. HTML вставляет echofooter() до </body> (см. install.xml).
		$this->boot('admin');
	}

	public function boot(string $surface = 'admin'): void
	{
		// Группы проверяем при показе: на раннем init ещё нет $member_id.
		if($this->booted || !$this->canCollect($surface)) {
			return;
		}

		$this->booted           = true;
		$this->surface          = $surface;
		$this->requestStartedAt = microtime(true);
		$this->viewModel        = null;
		$this->dleCollected     = false;
		$this->rendered         = false;

		if(!isset(self::$marks['request'])) {
			self::$marks['request'] = isset($_SERVER['REQUEST_TIME_FLOAT'])
				? (float) $_SERVER['REQUEST_TIME_FLOAT']
				: $this->requestStartedAt;
		}

		self::mark('toolbar_boot');
		QueryRegistry::clear();
	}

	public function renderHtml(): string
	{
		if($this->rendered || !$this->booted || !$this->isActive($this->surface)) {
			return '';
		}

		try {
			$html = Application::instance()->twig()->render('core/includes/debug_toolbar.twig', [
				'toolbar' => $this->viewModel(),
			]);
			$this->rendered = ($html !== '');

			return $html;
		} catch(\Throwable) {
			return '';
		}
	}

	public function injectIntoHtml(string $html): string
	{
		if(!$this->booted || !$this->isActive($this->surface) || $html === '') {
			return $html;
		}

		self::mark('footer');

		try {
			$toolbar = $this->renderHtml();

			if($toolbar === '' || str_contains($html, 'data-devcraft-debug-toolbar')) {
				return $html;
			}

			$position = strripos($html, '</body>');

			if($position === false) {
				return $html . $toolbar;
			}

			return substr($html, 0, $position) . $toolbar . substr($html, $position);
		} catch(\Throwable) {
			return $html;
		}
	}

	/**
	 * @return array{
	 *     request_ms: float,
	 *     request_ms_label: string,
	 *     sql_ms: float,
	 *     sql_ms_label: string,
	 *     speed: string,
	 *     total_count: int,
	 *     sections: array{
	 *         all: array{label: string, count: int, items: list<array<string, mixed>>},
	 *         dle: array{label: string, count: int, items: list<array<string, mixed>>},
	 *         cycle: array{label: string, count: int, items: list<array<string, mixed>>},
	 *         other: array{label: string, count: int, items: list<array<string, mixed>>}
	 *     }
	 * }|array{}
	 */
	public function viewModel(): array
	{
		if(!$this->booted || !$this->isActive($this->surface)) {
			return [];
		}

		if($this->viewModel === null) {
			$this->collectDleQueries();

			$enabled = $this->enabledPanels();
			$groups  = QueryRegistry::grouped();
			$dle     = $this->decorateRecords(in_array('dle', $enabled, true)? $groups['dle'] : []);
			$cycle   = $this->decorateRecords(in_array('cycle', $enabled, true)? $groups['cycle'] : []);
			$other   = $this->decorateRecords(in_array('other', $enabled, true)? $groups['other'] : []);
			$all     = $this->sortByTimeDesc(array_merge($dle, $cycle, $other));

			$vars = $this->buildRequestVars();

			$this->viewModel = [
				'total_count' => count($all),
				'vars'        => $vars,
				'sections'    => [
					'time'  => [
						'label' => 'Время',
						'count' => 0,
						'kind'  => 'timing',
					],
					'vars'  => [
						'label' => 'Переменные',
						'count' => $vars['count'],
						'kind'  => 'vars',
					],
					'all'   => [
						'label' => 'All',
						'count' => count($all),
						'kind'  => 'sql',
						'items' => $all,
					],
					'dle'   => [
						'label' => '$db',
						'count' => count($dle),
						'kind'  => 'sql',
						'items' => $dle,
					],
					'cycle' => [
						'label' => 'Cycle',
						'count' => count($cycle),
						'kind'  => 'sql',
						'items' => $cycle,
					],
					'other' => [
						'label' => 'Other',
						'count' => count($other),
						'kind'  => 'sql',
						'items' => $other,
					],
				],
			];
		}

		// Время всегда свежее: иначе кэш ловит момент до конца страницы.
		return $this->withFreshTiming($this->viewModel);
	}

	/**
	 * Снимок GET/POST/REQUEST/SESSION/COOKIE для вкладки «Переменные».
	 *
	 * @return array{
	 *     count: int,
	 *     bags: list<array{key: string, label: string, count: int, items: list<array{name: string, value: string, redacted: bool}>}>
	 * }
	 */
	private function buildRequestVars(): array
	{
		$session = [];

		if(session_status() === PHP_SESSION_ACTIVE && isset($_SESSION) && is_array($_SESSION)) {
			$session = $_SESSION;
		}

		$bags = [
			$this->exportBag('get', 'GET', is_array($_GET ?? null)? $_GET : []),
			$this->exportBag('post', 'POST', is_array($_POST ?? null)? $_POST : []),
			$this->exportBag('request', 'REQUEST', is_array($_REQUEST ?? null)? $_REQUEST : []),
			$this->exportBag('session', 'SESSION', $session),
			$this->exportBag('cookie', 'COOKIE', is_array($_COOKIE ?? null)? $_COOKIE : []),
		];

		$count = 0;

		foreach($bags as $bag) {
			$count += $bag['count'];
		}

		return [
			'count' => $count,
			'bags'  => $bags,
			'note'  => 'Секреты (пароль, токен и т.п.) скрыты. localStorage и sessionStorage подставляет браузер.',
		];
	}

	/**
	 * @param   array<array-key, mixed>  $data
	 *
	 * @return array{key: string, label: string, count: int, items: list<array{name: string, value: string, redacted: bool}>}
	 */
	private function exportBag(string $key, string $label, array $data): array
	{
		$items = [];
		$names = array_keys($data);
		sort($names, SORT_STRING);

		foreach($names as $name) {
			$nameStr  = (string) $name;
			$redacted = $this->isSensitiveKey($nameStr);
			$value    = $redacted
				? '••••••••'
				: $this->stringifyVar($data[$name]);

			$items[] = [
				'name'     => $nameStr,
				'value'    => $value,
				'redacted' => $redacted,
			];

			if(count($items) >= 200) {
				$items[] = [
					'name'     => '…',
					'value'    => 'Показаны первые 200 ключей.',
					'redacted' => false,
				];
				break;
			}
		}

		return [
			'key'   => $key,
			'label' => $label,
			'count' => count($data),
			'items' => $items,
		];
	}

	private function isSensitiveKey(string $key): bool
	{
		$normalized = strtolower($key);

		foreach([
			'password', 'passwd', 'pass', 'secret', 'token', 'api_key', 'apikey',
			'authorization', 'auth', 'csrf', 'hash', 'salt', 'private', 'session_id',
		] as $needle) {
			if(str_contains($normalized, $needle)) {
				return true;
			}
		}

		return false;
	}

	private function stringifyVar(mixed $value, int $depth = 0): string
	{
		if($value === null) {
			return 'null';
		}

		if(is_bool($value)) {
			return $value? 'true' : 'false';
		}

		if(is_int($value) || is_float($value)) {
			return (string) $value;
		}

		if(is_string($value)) {
			if(strlen($value) > 500) {
				return substr($value, 0, 500) . '…';
			}

			return $value;
		}

		if($depth >= 3) {
			return is_array($value)? '[…]' : '{…}';
		}

		if(is_array($value)) {
			$encoded = json_encode(
				$value,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR,
			);

			if(!is_string($encoded)) {
				return '[array]';
			}

			if(strlen($encoded) > 800) {
				return substr($encoded, 0, 800) . '…';
			}

			return $encoded;
		}

		if(is_object($value)) {
			return $value::class;
		}

		return get_debug_type($value);
	}

	/**
	 * @param   list<array{source: string, sql: string, time_ms: float, file?: string}>  $records
	 *
	 * @return list<array{source: string, source_label: string, sql: string, time_ms: float, time_label: string, file?: string}>
	 */
	private function decorateRecords(array $records): array
	{
		$out = [];

		foreach($records as $record) {
			$item = [
				'source'       => $record['source'],
				'source_label' => $this->sourceLabel($record['source']),
				'sql'          => $record['sql'],
				'time_ms'      => $record['time_ms'],
				'time_label'   => $this->formatMs($record['time_ms']),
			];

			if(isset($record['file'])) {
				$item['file'] = $record['file'];
			}

			$out[] = $item;
		}

		return $this->sortByTimeDesc($out);
	}

	/**
	 * @param   list<array{time_ms?: float}>  $records
	 *
	 * @return list<array{time_ms?: float}>
	 */
	private function sortByTimeDesc(array $records): array
	{
		usort(
			$records,
			static fn(array $a, array $b): int => ((float) ($b['time_ms'] ?? 0)) <=> ((float) ($a['time_ms'] ?? 0)),
		);

		return $records;
	}

	private function collectDleQueries(): void
	{
		if($this->dleCollected) {
			return;
		}

		$this->dleCollected = true;

		if(!in_array('dle', $this->enabledPanels(), true)) {
			return;
		}

		global $db;

		if(!is_object($db) || !isset($db->query_list) || !is_iterable($db->query_list)) {
			return;
		}

		foreach($db->query_list as $entry) {
			if(!is_array($entry)) {
				continue;
			}

			$sql = (string) ($entry['query'] ?? '');

			if($sql === '') {
				continue;
			}

			$time = (float) ($entry['time'] ?? 0);
			QueryRegistry::record('dle', $sql, $time * 1000);
		}
	}

	/**
	 * @param   array<string, mixed>  $model
	 *
	 * @return array<string, mixed>
	 */
	private function withFreshTiming(array $model): array
	{
		$requestMs = $this->requestMs();
		$allItems  = is_array($model['sections']['all']['items'] ?? null)
			? $model['sections']['all']['items']
			: [];
		$sqlMs     = 0.0;

		foreach($allItems as $item) {
			if(is_array($item)) {
				$sqlMs += (float) ($item['time_ms'] ?? 0);
			}
		}

		$sqlMs  = round($sqlMs, 3);
		$phpMs  = round(max(0.0, $requestMs - $sqlMs), 3);
		$sqlPct = $requestMs > 0.0? round(($sqlMs / $requestMs) * 100, 1) : 0.0;
		$phpPct = $requestMs > 0.0? round(($phpMs / $requestMs) * 100, 1) : 0.0;
		$memory = round(memory_get_peak_usage(true) / 1048576, 2);

		$bySource = [];

		foreach(['dle' => '$db', 'cycle' => 'Cycle', 'other' => 'Other'] as $key => $label) {
			$items = is_array($model['sections'][$key]['items'] ?? null)
				? $model['sections'][$key]['items']
				: [];
			$ms    = 0.0;

			foreach($items as $item) {
				if(is_array($item)) {
					$ms += (float) ($item['time_ms'] ?? 0);
				}
			}

			$ms         = round($ms, 3);
			$bySource[] = [
				'key'         => $key,
				'label'       => $label,
				'count'       => count($items),
				'ms'          => $ms,
				'ms_label'    => $this->formatMs($ms),
				'pct'         => $requestMs > 0.0? round(($ms / $requestMs) * 100, 1) : 0.0,
				'sql_pct'     => $sqlMs > 0.0? round(($ms / $sqlMs) * 100, 1) : 0.0,
			];
		}

		$slowest = array_slice($allItems, 0, 10);

		$model['request_ms']       = $requestMs;
		$model['request_ms_label'] = $this->formatMs($requestMs);
		$model['sql_ms']           = $sqlMs;
		$model['sql_ms_label']     = $this->formatMs($sqlMs);
		$model['php_ms']           = $phpMs;
		$model['php_ms_label']     = $this->formatMs($phpMs);
		$model['sql_pct']          = $sqlPct;
		$model['php_pct']          = $phpPct;
		$model['memory_mb']        = $memory;
		$model['memory_label']     = rtrim(rtrim(sprintf('%.2f', $memory), '0'), '.');
		$model['speed']            = $this->speedClass($requestMs);
		$stages  = $this->buildStages();
		$verdict = $sqlPct < 20.0
			? 'Почти всё время ушло не на SQL, а на PHP и генерацию страницы. Смотрите этапы ниже.'
			: 'Заметная доля времени — SQL. Смотрите источники и самые долгие запросы.';

		$model['timing'] = [
			'request_ms'       => $requestMs,
			'request_ms_label' => $this->formatMs($requestMs),
			'sql_ms'           => $sqlMs,
			'sql_ms_label'     => $this->formatMs($sqlMs),
			'sql_pct'          => $sqlPct,
			'php_ms'           => $phpMs,
			'php_ms_label'     => $this->formatMs($phpMs),
			'php_pct'          => $phpPct,
			'memory_mb'        => $memory,
			'memory_label'     => $model['memory_label'],
			'by_source'        => $bySource,
			'slowest'          => $slowest,
			'stages'           => $stages,
			'verdict'          => $verdict,
			'note'             => 'SQL и PHP — оценка: сумма длительностей запросов; остаток — PHP и прочее. «Генерация страницы» — всё между готовностью DevCraft и показом панели, не время самой панели.',
		];

		if(isset($model['sections']['time']) && is_array($model['sections']['time'])) {
			$model['sections']['time']['count'] = (int) round($requestMs);
		}

		// Переменные на момент показа (сессия могла дополниться за запрос).
		$vars         = $this->buildRequestVars();
		$model['vars'] = $vars;

		if(isset($model['sections']['vars']) && is_array($model['sections']['vars'])) {
			$model['sections']['vars']['count'] = $vars['count'];
		}

		return $model;
	}

	/**
	 * @return list<array{id: string, label: string, ms: float, ms_label: string, pct: float}>
	 */
	private function buildStages(): array
	{
		self::mark('render');

		$marks = self::$marks;

		if(!isset($marks['request'])) {
			$marks['request'] = isset($_SERVER['REQUEST_TIME_FLOAT'])
				? (float) $_SERVER['REQUEST_TIME_FLOAT']
				: ($this->requestStartedAt ?? microtime(true));
		}

		asort($marks, SORT_NUMERIC);

		$labels = [
			'request'             => 'Старт PHP-запроса',
			'devcraft_boot_start' => 'Старт загрузки DevCraft',
			'devcraft_psr4'       => 'Манифесты / PSR-4 готовы',
			'devcraft_boot_end'   => 'DevCraft готов',
			'toolbar_boot'        => 'Старт панели отладки',
			'run_admin_start'     => 'Старт модуля админки',
			'page_handle_start'   => 'Старт страницы модуля',
			'page_handle_end'     => 'Страница модуля готова',
			'layout_start'        => 'Сборка оболочки',
			'footer'              => 'Подвал / вставка панели',
			'render'              => 'Рендер панели',
		];

		// Большие «дыры» между boot и показом — это работа страницы, не панель.
		$pageWorkTo = ['toolbar_boot', 'footer', 'render', 'layout_start', 'page_handle_start', 'run_admin_start'];

		$ids = array_keys($marks);
		$first = reset($marks);
		$last  = end($marks);
		// Доли считаем от реального размаха меток — иначе $Timer короче и получается 700%+.
		$total = max($this->requestMs(), round(($last - $first) * 1000, 3));
		$out   = [];

		for($i = 0, $n = count($ids); $i < $n - 1; $i++) {
			$from = $ids[$i];
			$to   = $ids[$i + 1];
			$ms   = round(($marks[$to] - $marks[$from]) * 1000, 3);

			if($ms < 0.05) {
				continue;
			}

			$isPageWork = ($from === 'devcraft_boot_end' || $from === 'toolbar_boot')
				&& in_array($to, $pageWorkTo, true)
				&& $ms >= 5.0;

			$label = $isPageWork
				? 'Генерация страницы'
				: (($labels[$from] ?? $from) . ' → ' . ($labels[$to] ?? $to));

			$out[] = [
				'id'       => $from . ':' . $to,
				'label'    => $label,
				'ms'       => $ms,
				'ms_label' => $this->formatMs($ms),
				'pct'      => $total > 0.0? round(min(100.0, ($ms / $total) * 100), 1) : 0.0,
			];
		}

		usort(
			$out,
			static fn(array $a, array $b): int => ((float) $b['ms']) <=> ((float) $a['ms']),
		);

		return $out;
	}

	private function requestMs(): float
	{
		// Метки панели — wall-clock от REQUEST_TIME_FLOAT; $Timer DLE часто короче
		// (стартует позже) и давал доли этапов >100%.
		if(isset($_SERVER['REQUEST_TIME_FLOAT'])) {
			return round((microtime(true) - (float) $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 3);
		}

		if($this->requestStartedAt === null) {
			return 0.0;
		}

		return round((microtime(true) - $this->requestStartedAt) * 1000, 3);
	}

	private function speedClass(float $ms): string
	{
		if($ms < 200.0) {
			return 'success';
		}

		if($ms < 1000.0) {
			return 'warn';
		}

		return 'error';
	}

	private function formatMs(float|int $value): string
	{
		$formatted = rtrim(rtrim(sprintf('%.3f', (float) $value), '0'), '.');

		return $formatted !== ''? $formatted : '0';
	}

	private function sourceLabel(string $source): string
	{
		return match ($source) {
			'dle'   => '$db',
			'cycle' => 'Cycle',
			'other' => 'Other',
			default => $source,
		};
	}

	/**
	 * Сбор метрик без проверки группы (ранний init админки DLE).
	 */
	private function canCollect(string $surface): bool
	{
		// Не зависит от debug / ?dc_debug=1 — только от настроек панели.
		if(!(bool) DevCraftConfig::get('debug_toolbar', true)) {
			return false;
		}

		if($surface === 'admin' && !(bool) DevCraftConfig::get('debug_toolbar_admin', true)) {
			return false;
		}

		if($surface === 'public' && !(bool) DevCraftConfig::get('debug_toolbar_public', true)) {
			return false;
		}

		return true;
	}

	private function currentUserAllowed(): bool
	{
		global $member_id;

		$groupId = (int) ($member_id['user_group'] ?? 0);
		$allowed = $this->parseMulti(DevCraftConfig::get('debug_toolbar_groups', '1'));

		if($allowed === []) {
			return false;
		}

		return in_array((string) $groupId, $allowed, true);
	}

	private function isDleAdminScript(): bool
	{
		$script = (string) ($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');

		return str_ends_with($script, '/admin.php') || basename($script) === 'admin.php';
	}

	/**
	 * @return list<string>
	 */
	private function enabledPanels(): array
	{
		$known  = ['dle', 'cycle', 'other'];
		$panels = $this->parseMulti(DevCraftConfig::get('debug_toolbar_panels', 'dle cycle other'));

		return array_values(array_intersect($known, $panels));
	}

	/**
	 * @return list<string>
	 */
	private function parseMulti(mixed $value): array
	{
		if(is_array($value)) {
			return array_values(array_filter(
				array_map(static fn(mixed $item): string => (string) $item, $value),
				static fn(string $item): bool => $item !== '',
			));
		}

		if(!is_string($value) || trim($value) === '') {
			return [];
		}

		return array_values(array_filter(
			preg_split('/[\s,]+/', trim($value)) ?: [],
			static fn(string $item): bool => $item !== '',
		));
	}
}
