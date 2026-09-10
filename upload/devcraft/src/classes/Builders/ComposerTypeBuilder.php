<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Core\Types\ComposerType;

/**
 * Fluent-строитель правила composer_required.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class ComposerTypeBuilder {

	private string $version = '*';

	private bool $requires = false;

	private function __construct(
		private string $package,
	) {}

	public static function create(string $package): self {
		return new self($package);
	}

	public function version(string $version): self {
		$this->version = $version !== ''? $version : '*';

		return $this;
	}

	/**
	 * Alias для minVersion из legacy-массива манифеста.
	 */
	public function minVersion(string $version): self {
		return $this->version($version);
	}

	public function requires(bool $requires = true): self {
		$this->requires = $requires;

		return $this;
	}

	/**
	 * Alias hardRequired.
	 */
	public function hardRequired(bool $hard = true): self {
		return $this->requires($hard);
	}

	public function build(): ComposerType {
		return new ComposerType(
			package : $this->package,
			version : $this->version,
			requires: $this->requires,
		);
	}

}
