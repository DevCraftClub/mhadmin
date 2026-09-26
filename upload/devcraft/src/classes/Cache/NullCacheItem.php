<?php

declare(strict_types=1);

namespace DevCraft\Core\Cache;

/**
 * Элемент пустого кэша: всегда miss. Без PSR (запасной путь без vendor).
 */
final class NullCacheItem {

	private mixed $value = NULL;

	public function __construct(
		private readonly string $key,
	) {}

	public function getKey(): string {
		return $this->key;
	}

	public function get(): mixed {
		return NULL;
	}

	public function isHit(): bool {
		return false;
	}

	public function set(mixed $value): static {
		$this->value = $value;

		return $this;
	}

	public function expiresAt(mixed $expiration): static {
		return $this;
	}

	public function expiresAfter(mixed $time): static {
		return $this;
	}

}
