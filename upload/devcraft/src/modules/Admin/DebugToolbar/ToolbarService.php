<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\DebugToolbar;

use DevCraft\Core\Application;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Сборщик и рендер отладочной панели.
 */
final class ToolbarService
{
	private static ?self $instance = null;

	private bool $booted = false;
	private ?float $requestStartedAt = null;
	private ?array $viewModel = null;
	private bool $dleCollected = false;

	public static function instance(): self
	{
		return self::$instance ??= new self();
	}

	public function boot(): void
	{
		if($this->booted || !LogGenerator::isDebugEnabled()) {
			return;
		}

		$this->booted           = true;
		$this->requestStartedAt = microtime(true);
		$this->viewModel        = null;
		$this->dleCollected     = false;

		QueryRegistry::clear();
	}

	public function renderHtml(): string
	{
		if(!LogGenerator::isDebugEnabled()) {
			return '';
		}

		try {
			return Application::instance()->twig()->render('core/includes/debug_toolbar.twig', [
				'toolbar' => $this->viewModel(),
			]);
		} catch(\Throwable) {
			return '';
		}
	}

	public function injectIntoHtml(string $html): string
	{
		if(!LogGenerator::isDebugEnabled() || $html === '') {
			return $html;
		}

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
		if(!LogGenerator::isDebugEnabled()) {
			return [];
		}

		$this->boot();

		if($this->viewModel !== null) {
			return $this->viewModel;
		}

		$this->collectDleQueries();

		$groups = QueryRegistry::grouped();
		$all    = $this->decorateRecords($groups['all']);
		$dle    = $this->decorateRecords($groups['dle']);
		$cycle  = $this->decorateRecords($groups['cycle']);
		$other  = $this->decorateRecords($groups['other']);

		$requestMs = $this->requestMs();

		$this->viewModel = [
			'request_ms'        => $requestMs,
			'request_ms_label'  => $this->formatMs($requestMs),
			'total_count'       => count($all),
			'sections'          => [
				'all'   => [
					'label' => 'All',
					'count' => count($all),
					'items' => $all,
				],
				'dle'   => [
					'label' => '$db',
					'count' => count($dle),
					'items' => $dle,
				],
				'cycle' => [
					'label' => 'Cycle',
					'count' => count($cycle),
					'items' => $cycle,
				],
				'other' => [
					'label' => 'Other',
					'count' => count($other),
					'items' => $other,
				],
			],
		];

		return $this->viewModel;
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

		return $out;
	}

	private function collectDleQueries(): void
	{
		if($this->dleCollected) {
			return;
		}

		$this->dleCollected = true;

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

	private function requestMs(): float
	{
		if($this->requestStartedAt === null) {
			return 0.0;
		}

		return round((microtime(true) - $this->requestStartedAt) * 1000, 3);
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
}
