<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\LanguageData;

/**
 * Fluent-строитель LanguageData.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class LanguageDataBuilder {

	private string $englishName = '';

	private string $originalName = '';

	private string $iso2 = '';

	private string $tag = '';

	public static function create(): self {
		return new self();
	}

	public function englishName(string $name): self {
		$this->englishName = $name;

		return $this;
	}

	public function originalName(string $name): self {
		$this->originalName = $name;

		return $this;
	}

	public function iso2(string $iso2): self {
		$this->iso2 = $iso2;

		return $this;
	}

	public function tag(string $tag): self {
		$this->tag = $tag;

		return $this;
	}

	public function build(): LanguageData {
		return new LanguageData(
			$this->englishName,
			$this->originalName,
			$this->iso2,
			$this->tag,
		);
	}

}
