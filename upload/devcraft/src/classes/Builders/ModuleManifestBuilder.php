<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use Closure;
use InvalidArgumentException;
use DevCraft\Core\Types\ComposerType;
use DevCraft\Types\AdminLink;
use DevCraft\Types\Author;
use DevCraft\Types\Changelog;
use DevCraft\Types\ModuleAjaxConfig;
use DevCraft\Types\ModuleAssets;
use DevCraft\Types\ModuleManifest;

/**
 * Fluent-строитель манифеста модуля DevCraft.
 *
 * Собирает форму, совместимую с ModuleManifest::fromManifest(), либо готовый ModuleManifest.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class ModuleManifestBuilder {

	private string $mod = '';

	private ?string $code = NULL;

	private ?string $crowdinName = NULL;

	private ?string $crowdinStatId = NULL;

	/** @var array<string, mixed> */
	private array $meta = [];

	/** @var AdminLink[] */
	private array $menu = [];

	private ?ModuleAjaxConfig $ajax = NULL;

	private ?ModuleAssets $assets = NULL;

	/** @var Changelog[] */
	private array $changelog = [];

	/** @var ComposerType[] */
	private array $composerRequired = [];

	private ?Author $author = NULL;

	public static function create(): self {
		return new self();
	}

	public function mod(string $mod): self {
		$this->mod = $mod;

		return $this;
	}

	public function code(string $code): self {
		$this->code = $code;

		return $this;
	}

	public function crowdinName(string $name): self {
		$this->crowdinName = $name;

		return $this;
	}

	public function crowdinStatId(string $id): self {
		$this->crowdinStatId = $id;

		return $this;
	}

	/**
	 * @param   array<string, mixed>  $meta
	 */
	public function meta(array $meta): self {
		$this->meta = array_merge($this->meta, $meta);

		return $this;
	}

	public function name(string $name): self {
		$this->meta['name'] = $name;

		return $this;
	}

	public function version(string $version): self {
		$this->meta['version'] = $version;

		return $this;
	}

	public function description(string $description): self {
		$this->meta['description'] = $description;

		return $this;
	}

	public function icon(string $icon): self {
		$this->meta['icon'] = $icon;

		return $this;
	}

	public function docsLink(string $url): self {
		$this->meta['docsLink'] = $url;

		return $this;
	}

	public function siteLink(string $url): self {
		$this->meta['siteLink'] = $url;

		return $this;
	}

	public function siteId(int $siteId): self {
		$this->meta['siteId'] = $siteId;

		return $this;
	}

	public function author(Author|AuthorBuilder|null $author): self {
		if($author instanceof AuthorBuilder) {
			$author = $author->build();
		}

		$this->author = $author;

		return $this;
	}

	/**
	 * @param   list<AdminLink>  $links
	 */
	public function menu(array $links): self {
		foreach($links as $link) {
			if($link instanceof AdminLink) {
				$this->menu[] = $link;
			}
		}

		return $this;
	}

	public function addMenu(AdminLink $link): self {
		$this->menu[] = $link;

		return $this;
	}

	/**
	 * @param   ModuleAjaxConfig|ModuleAjaxConfigBuilder|Closure(ModuleAjaxConfigBuilder): mixed|array<string, mixed>  $ajax
	 */
	public function ajax(ModuleAjaxConfig|ModuleAjaxConfigBuilder|Closure|array $ajax): self {
		if($ajax instanceof Closure) {
			$builder = ModuleAjaxConfigBuilder::create();
			$ajax($builder);
			$this->ajax = $builder->build();
		} elseif($ajax instanceof ModuleAjaxConfigBuilder) {
			$this->ajax = $ajax->build();
		} elseif($ajax instanceof ModuleAjaxConfig) {
			$this->ajax = $ajax;
		} else {
			$this->ajax = ModuleAjaxConfig::fromArray($ajax);
		}

		return $this;
	}

	/**
	 * @param   ModuleAssets|ModuleAssetsBuilder|array<string, mixed>  $assets
	 */
	public function assets(ModuleAssets|ModuleAssetsBuilder|array $assets): self {
		if($assets instanceof ModuleAssetsBuilder) {
			$this->assets = $assets->build();
		} elseif($assets instanceof ModuleAssets) {
			$this->assets = $assets;
		} else {
			$this->assets = ModuleAssets::fromArray($assets);
		}

		return $this;
	}

	/**
	 * @param   list<Changelog|array<string, mixed>>|Changelog  $entries
	 */
	public function changelog(array|Changelog $entries): self {
		if($entries instanceof Changelog) {
			$this->changelog[] = $entries;

			return $this;
		}

		foreach($entries as $entry) {
			if($entry instanceof Changelog) {
				$this->changelog[] = $entry;
			} elseif($entry instanceof ChangelogBuilder) {
				$this->changelog[] = $entry->build();
			} elseif(is_array($entry)) {
				$this->changelog[] = Changelog::fromArray($entry);
			}
		}

		return $this;
	}

	/**
	 * @param   list<ComposerType|array<string, mixed>>  $rules
	 */
	public function composerRequired(array $rules): self {
		foreach($rules as $rule) {
			if($rule instanceof ComposerType) {
				$this->composerRequired[] = $rule;
			} elseif($rule instanceof ComposerTypeBuilder) {
				$this->composerRequired[] = $rule->build();
			} elseif(is_array($rule)) {
				$this->composerRequired[] = ComposerType::fromArray($rule);
			}
		}

		return $this;
	}

	/**
	 * Собирает ModuleManifest (path/namespace из каталога модуля).
	 */
	public function build(string $modulePath): ModuleManifest {
		if($this->mod === '') {
			throw new InvalidArgumentException(__('mod манифеста не может быть пустым'));
		}

		return ModuleManifest::fromManifest($this->mod, $this->toManifestArray(), $modulePath);
	}

	/**
	 * Форма массива, совместимая с legacy manifest.php / fromManifest.
	 *
	 * @return array<string, mixed>
	 */
	public function toManifestArray(): array {
		$meta = $this->meta;

		if($this->author !== NULL) {
			$meta['author'] = $this->author->toArray();
		}

		$data = [
			'mod'  => $this->mod,
			'meta' => $meta,
			'menu' => $this->menu,
		];

		if($this->code !== NULL) {
			$data['code'] = $this->code;
		}

		if($this->crowdinName !== NULL) {
			$data['crowdinName'] = $this->crowdinName;
		}

		if($this->crowdinStatId !== NULL) {
			$data['crowdinStatId'] = $this->crowdinStatId;
		}

		if($this->ajax !== NULL) {
			$data['ajax'] = [
				'controller' => $this->ajax->controller,
				'methods'    => $this->ajax->methods,
				'public'     => $this->ajax->public,
			];
		}

		if($this->assets !== NULL) {
			$data['assets'] = $this->assets->toArray();
		}

		if($this->changelog !== []) {
			$data['changelog'] = $this->changelog;
		}

		if($this->composerRequired !== []) {
			$data['composer_required'] = array_map(
				static function (ComposerType $rule): array {
					return [
						'name'         => $rule->package,
						'minVersion'   => $rule->version,
						'hardRequired' => $rule->requires,
					];
				},
				$this->composerRequired,
			);
		}

		return $data;
	}

}
