<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * DTO результата действия Composer для единого JSON-контракта.
 *
 * Используется адаптером {@see ComposerRuntimeAdapter} и политикой {@see PackagePolicyService}.
 *
 * @example
 *     $result = ComposerActionResult::ok('Пакет установлен');
 *     $payload = $result->toArray();
 */
final readonly class ComposerActionResult {

	public function __construct(
		public string $status,
		public string $message,
		public array $details = [],
	) {}

	public static function ok(string $message, array $details = []): self {
		return new self('ok', $message, $details);
	}

	public static function error(string $message, array $details = []): self {
		return new self('error', $message, $details);
	}

	public static function requiresDecision(string $message, array $details = []): self {
		return new self('requires_decision', $message, $details);
	}

	/**
	 * @return array{status: string, message: string, details: array<string, mixed>}
	 */
	public function toArray(): array {
		return [
			'status'  => $this->status,
			'message' => $this->message,
			'details' => $this->details,
		];
	}
}
