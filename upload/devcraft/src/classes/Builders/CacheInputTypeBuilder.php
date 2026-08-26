<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\CacheInputType;

/**
 * Fluent-строитель CacheInputType.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class CacheInputTypeBuilder {

	private mixed $cacheData = NULL;

	private int $storedAt = 0;

	public static function create(): self {
		return new self();
	}

	public function data(mixed $data): self {
		$this->cacheData = $data;

		return $this;
	}

	public function storedAt(int $unixTime): self {
		$this->storedAt = $unixTime;

		return $this;
	}

	public function now(): self {
		$this->storedAt = time();

		return $this;
	}

	public function build(): CacheInputType {
		return new CacheInputType($this->cacheData, $this->storedAt);
	}

}
