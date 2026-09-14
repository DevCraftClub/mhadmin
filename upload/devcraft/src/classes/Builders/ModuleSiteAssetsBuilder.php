<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\ModuleSiteAssets;

/**
 * Fluent-строитель секции `siteAssets` манифеста (оболочка сайта).
 */
final class ModuleSiteAssetsBuilder {

	/** @var list<string> */
	private array $js = [];

	/** @var list<string> */
	private array $css = [];

	/** @var list<array{name: string, content: string}> */
	private array $meta = [];

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

	public function meta(string $name, string $content): self {
		$name = trim($name);

		if($name !== '' && $content !== '') {
			$this->meta[] = ['name' => $name, 'content' => $content];
		}

		return $this;
	}

	public function build(): ModuleSiteAssets {
		return new ModuleSiteAssets(js: $this->js, css: $this->css, meta: $this->meta);
	}

}
