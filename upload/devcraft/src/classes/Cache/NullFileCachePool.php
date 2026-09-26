<?php

declare(strict_types=1);

namespace DevCraft\Core\Cache;

/**
 * Пустой пул кэша, пока нет `devcraftclub/dev-tools` (FileCachePool).
 * Без PSR-интерфейсов: на свежей установке `vendor` может быть неполным.
 */
final class NullFileCachePool {

	public function __construct(
		private readonly string $baseDir,
	) {}

	public function getBaseDir(): string {
		return $this->baseDir;
	}

	public function getItem(string $key): NullCacheItem {
		return new NullCacheItem($key);
	}

	public function clear(): bool {
		return true;
	}

	public function clearNamespace(string $prefix): bool {
		return true;
	}

	public function save(object $item): bool {
		return true;
	}

}
