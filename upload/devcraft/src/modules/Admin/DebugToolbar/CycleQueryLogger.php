<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\DebugToolbar;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Перехватывает SQL драйвера Cycle (в т.ч. ORM Select) в QueryRegistry.
 */
final class CycleQueryLogger extends AbstractLogger
{
	/**
	 * @param   array<string, mixed>  $context
	 */
	public function log($level, string|Stringable $message, array $context = []): void
	{
		if(!isset($context['elapsed'])) {
			return;
		}

		$sql = trim((string) $message);

		if($sql === '') {
			return;
		}

		QueryRegistry::record(
			'cycle',
			$sql,
			((float) $context['elapsed']) * 1000.0,
		);
	}
}
