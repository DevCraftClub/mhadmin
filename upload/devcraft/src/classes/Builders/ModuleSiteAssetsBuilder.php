<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\ModuleSiteAssets;

/**
 * Fluent-строитель секции `siteAssets` манифеста (оболочка сайта).
 */
final class ModuleSiteAssetsBuilder {

	/** @var list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}> */
	private array $js = [];

	/** @var list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}> */
	private array $css = [];

	/** @var list<array{name: string, content: string}> */
	private array $meta = [];

	public static function create(): self {
		return new self();
	}

	/**
	 * @param   list<string>|string  $files
	 * @param   list<string>         $dependsOn     пути файлов той же категории (Public/ или от корня сайта)
	 * @param   list<string>         $available     ключи разделов (`$do`); пусто — все, кроме исключений
	 * @param   list<string>         $notAvailable  ключи разделов, где файл не показывают
	 */
	public function js(
		array|string $files,
		array        $dependsOn = [],
		array        $available = [],
		array        $notAvailable = [],
		bool         $active = true,
	): self {
		$this->addFiles($this->js, $files, $dependsOn, $available, $notAvailable, $active);

		return $this;
	}

	/**
	 * @param   list<string>|string  $files
	 * @param   list<string>         $dependsOn
	 * @param   list<string>         $available
	 * @param   list<string>         $notAvailable
	 */
	public function css(
		array|string $files,
		array        $dependsOn = [],
		array        $available = [],
		array        $notAvailable = [],
		bool         $active = true,
	): self {
		$this->addFiles($this->css, $files, $dependsOn, $available, $notAvailable, $active);

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

	/**
	 * @param   list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}>  $bucket
	 * @param   list<string>|string                                                                                                   $files
	 * @param   list<string>                                                                                                          $dependsOn
	 * @param   list<string>                                                                                                          $available
	 * @param   list<string>                                                                                                          $notAvailable
	 */
	private function addFiles(
		array        &$bucket,
		array|string $files,
		array        $dependsOn,
		array        $available,
		array        $notAvailable,
		bool         $active,
	): void {
		foreach((array) $files as $file) {
			$item = ModuleSiteAssets::normalizeFile([
				'file'         => $file,
				'dependsOn'    => $dependsOn,
				'available'    => $available,
				'notAvailable' => $notAvailable,
				'active'       => $active,
			]);

			if($item !== null) {
				$bucket[] = $item;
			}
		}
	}

}
