<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\ModuleAssets;

/**
 * Fluent-строитель секции `assets` манифеста.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class ModuleAssetsBuilder {

	/** @var list<string> */
	private array $js = [];

	/** @var list<string> */
	private array $css = [];

	public static function create(): self {
		return new self();
	}

	/**
	 * @param   list<string>|string  $files
	 */
	public function js(array|string $files): self {
		foreach((array) $files as $file) {
			if(is_string($file) && $file !== '') {
				$this->js[] = $file;
			}
		}

		return $this;
	}

	/**
	 * @param   list<string>|string  $files
	 */
	public function css(array|string $files): self {
		foreach((array) $files as $file) {
			if(is_string($file) && $file !== '') {
				$this->css[] = $file;
			}
		}

		return $this;
	}

	public function build(): ModuleAssets {
		return new ModuleAssets(js: $this->js, css: $this->css);
	}

}
