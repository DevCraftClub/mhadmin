<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\DebugToolbar;

/**
 * Накопитель запросов отладки на один запрос.
 */
final class QueryRegistry
{
	/**
	 * @var list<array{source: string, sql: string, time_ms: float, file?: string}>
	 */
	private static array $records = [];

	public static function clear(): void
	{
		self::$records = [];
	}

	public static function record(string $source, string $sql, float|int $timeMs = 0.0, ?string $file = null): void
	{
		if(!ToolbarService::instance()->isBooted()) {
			return;
		}

		$sql = trim($sql);

		if($sql === '') {
			return;
		}

		$entry = [
			'source'  => self::normalizeSource($source),
			'sql'     => $sql,
			'time_ms' => round((float) $timeMs, 3),
		];

		if($file === null && $entry['source'] !== 'dle') {
			$file = self::resolveFile();
		}

		if(is_string($file) && $file !== '') {
			$entry['file'] = $file;
		}

		self::$records[] = $entry;
	}

	/**
	 * @return list<array{source: string, sql: string, time_ms: float, file?: string}>
	 */
	public static function all(): array
	{
		return self::$records;
	}

	/**
	 * @return array{all: list<array{source: string, sql: string, time_ms: float, file?: string}>, dle: list<array{source: string, sql: string, time_ms: float, file?: string}>, cycle: list<array{source: string, sql: string, time_ms: float, file?: string}>, other: list<array{source: string, sql: string, time_ms: float, file?: string}>}
	 */
	public static function grouped(): array
	{
		return [
			'all'   => self::$records,
			'dle'   => self::bySource('dle'),
			'cycle' => self::bySource('cycle'),
			'other' => self::bySource('other'),
		];
	}

	/**
	 * @return list<array{source: string, sql: string, time_ms: float, file?: string}>
	 */
	private static function bySource(string $source): array
	{
		return array_values(array_filter(
			self::$records,
			static fn(array $record): bool => ($record['source'] ?? '') === $source,
		));
	}

	private static function normalizeSource(string $source): string
	{
		return in_array($source, ['dle', 'cycle', 'other'], true)? $source : 'other';
	}

	private static function resolveFile(): ?string
	{
		$skipClasses = [
			self::class,
			CycleQueryLogger::class,
			ToolbarService::class,
			\DevCraft\Core\Database\DatabaseGateway::class,
		];

		foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 24) as $frame) {
			$class = (string) ($frame['class'] ?? '');
			$file  = (string) ($frame['file'] ?? '');

			if($file === '') {
				continue;
			}

			if($class !== '' && in_array($class, $skipClasses, true)) {
				continue;
			}

			// Внутри Cycle DBAL / ORM — ищем вызывающий код приложения.
			if(str_starts_with($class, 'Cycle\\') || str_contains($file, '/vendor/cycle/')) {
				continue;
			}

			return self::relativize($file);
		}

		return null;
	}

	private static function relativize(string $path): string
	{
		$path = str_replace('\\', '/', $path);

		if(defined('ROOT_DIR')) {
			$path = str_replace(str_replace('\\', '/', ROOT_DIR), '', $path);
		}

		return ltrim($path, '/');
	}
}
