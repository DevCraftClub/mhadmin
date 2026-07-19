<?php

namespace DevCraft\Core\Types;

use DevCraft\Core\Abstracts\AbstractType;

class ComposerType extends AbstractType {

	public function __construct(public string $package, public string $version = '*', public bool $requires = false) {}

	/**
	 * @inheritDoc
	 */
	public static function fromArray(array $data): static {
		return new self(
			package : (string) ($data['name'] ?? $data['package'] ?? ''),
			version : (string) ($data['minVersion'] ?? $data['version'] ?? '*'),
			requires: (bool) ($data['hardRequired'] ?? $data['requires'] ?? false),
		);
	}

	public function version(string $version): self {
		$this->version = $version;

		return $this;
	}

	public function requires(bool $requires): self {
		$this->requires = $requires;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(): array {
		return [
			'package'  => $this->package,
			'version'  => $this->version,
			'requires' => $this->requires,
		];
	}

}