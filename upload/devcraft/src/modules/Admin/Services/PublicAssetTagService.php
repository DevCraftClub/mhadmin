<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\DebugToolbar\ToolbarService;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;

/**
 * Подстановка тегов `{devcraft}`, `{devcraft-header}`, `{devcraft-scripts}` в main.tpl.
 */
final class PublicAssetTagService {

	private static bool $debugToolbarBufferStarted = false;

	/** @var array<string, list<PublicAssetEntry>> */
	private static array $assetsByKind = [];

	/** @var list<PublicHeaderEntry>|null */
	private static ?array $headersCache = null;

	public function __construct(
		private readonly PublicAssetDependencyService $deps = new PublicAssetDependencyService(),
	) {}

	public function applyToTemplate(object $tpl): void {
		if(LogGenerator::isDebugEnabled()) {
			// Публичка не сидит списки: полный seed — Dashboard / Public Assets.
			// В debug — только sync, если изменились mtime манифестов.
			(new PublicAssetManifestSyncService())->syncIfChanged();
		}

		$toolbar = ToolbarService::instance();
		$toolbar->boot('public');

		// Не ob_start: у DLE свои буферы — callback мог отдавать пустую страницу.
		if($toolbar->isBooted() && !self::$debugToolbarBufferStarted) {
			self::$debugToolbarBufferStarted = true;
			register_shutdown_function(static function (): void {
				try {
					echo ToolbarService::instance()->renderHtml();
				} catch(\Throwable) {
					// Панель не должна ронять публичную страницу.
				}
			});
		}

		$copy = (string) ($tpl->copy_template ?? '');
		$hasAll     = str_contains($copy, '{devcraft}');
		$hasHeader  = str_contains($copy, '{devcraft-header}');
		$hasScripts = str_contains($copy, '{devcraft-scripts}');

		if(!$hasAll && !$hasHeader && !$hasScripts) {
			return;
		}

		// Считать только нужные части: раньше всегда вызывались bundle+header+scripts → ×2–×3 одних SQL.
		$conflict = $hasAll && ($hasHeader || $hasScripts);

		if($conflict || $hasAll) {
			$tpl->set('{devcraft}', $this->renderBundle());

			if($conflict) {
				$tpl->set('{devcraft-header}', '');
				$tpl->set('{devcraft-scripts}', '');
			}

			return;
		}

		if($hasHeader) {
			$tpl->set('{devcraft-header}', $this->renderHeader());
		}

		if($hasScripts) {
			$tpl->set('{devcraft-scripts}', $this->renderScripts());
		}
	}

	public function renderBundle(): string {
		return $this->joinParts([
			$this->renderStyles(),
			$this->renderMeta(),
			$this->renderScripts(),
		]);
	}

	public function renderHeader(): string {
		return $this->joinParts([
			$this->renderStyles(),
			$this->renderMeta(),
		]);
	}

	public function renderScripts(): string {
		if($this->compressEnabled()) {
			$bundle = $this->bundleUrl('js');
			if($bundle !== null) {
				return '<script src="' . $this->escapeAttr($bundle) . '"></script>';
			}
		}

		$parts = [];

		foreach($this->outputAssets('js') as $entry) {
			$url = $this->publicUrl($entry->local_path);
			if($url === null) {
				continue;
			}

			$parts[] = '<script src="' . $this->escapeAttr($url) . '"></script>';
		}

		return implode('', $parts);
	}

	public function renderStyles(): string {
		if($this->compressEnabled()) {
			$bundle = $this->bundleUrl('css');
			if($bundle !== null) {
				return '<link rel="stylesheet" href="' . $this->escapeAttr($bundle) . '">';
			}
		}

		$parts = [];

		foreach($this->outputAssets('css') as $entry) {
			$url = $this->publicUrl($entry->local_path);
			if($url === null) {
				continue;
			}

			$parts[] = '<link rel="stylesheet" href="' . $this->escapeAttr($url) . '">';
		}

		return implode('', $parts);
	}

	public function renderMeta(): string {
		$parts = [];

		foreach($this->outputHeaders() as $entry) {
			$parts[] = '<meta name="' . $this->escapeAttr($entry->name) . '" content="'
				. $this->escapeAttr($entry->content) . '">';
		}

		return implode('', $parts);
	}

	/**
	 * @return list<PublicAssetEntry>
	 */
	private function outputAssets(string $kind): array {
		$sectionKey = DleSiteSectionRegistry::instance()->currentKey();
		$cacheKey   = $kind . ':' . $sectionKey;

		if(isset(self::$assetsByKind[$cacheKey])) {
			return self::$assetsByKind[$cacheKey];
		}

		try {
			/** @var PublicAssetEntryRepository $repo */
			$repo       = Application::instance()->database()->repository(PublicAssetEntry::class);
			$entries    = $repo->listByKind($kind);
			$depsMap    = $this->deps->buildDepsMap($entries);
			$candidates = [];

			foreach($entries as $entry) {
				if(!$entry->active) {
					continue;
				}

				if(!$entry->matchesSection($sectionKey)) {
					continue;
				}

				$candidates[] = $entry->id();
			}

			$outputIds = $this->deps->orderedClosureForOutput($candidates, $depsMap);
			$byId      = [];

			foreach($entries as $entry) {
				$byId[$entry->id()] = $entry;
			}

			$result = [];

			foreach($outputIds as $id) {
				if(isset($byId[$id])) {
					$result[] = $byId[$id];
				}
			}

			return self::$assetsByKind[$cacheKey] = $result;
		} catch(\Throwable $e) {
			$this->swallowed('outputAssets', $e);

			return self::$assetsByKind[$cacheKey] = [];
		}
	}

	/**
	 * @return list<PublicHeaderEntry>
	 */
	private function outputHeaders(): array {
		if(self::$headersCache !== null) {
			return self::$headersCache;
		}

		try {
			/** @var PublicHeaderEntryRepository $repo */
			$repo       = Application::instance()->database()->repository(PublicHeaderEntry::class);
			$sectionKey = DleSiteSectionRegistry::instance()->currentKey();
			$entries    = $repo->listAllOrdered();
			$depsMap    = $this->deps->buildDepsMap($entries);
			$candidates = [];

			foreach($entries as $entry) {
				if(!$entry->active) {
					continue;
				}

				if(!$entry->matchesSection($sectionKey)) {
					continue;
				}

				$candidates[] = $entry->id();
			}

			$outputIds = $this->deps->orderedClosureForOutput($candidates, $depsMap);
			$byId      = [];

			foreach($entries as $entry) {
				$byId[$entry->id()] = $entry;
			}

			$result = [];

			foreach($outputIds as $id) {
				if(isset($byId[$id])) {
					$result[] = $byId[$id];
				}
			}

			return self::$headersCache = $result;
		} catch(\Throwable $e) {
			$this->swallowed('outputHeaders', $e);

			return self::$headersCache = [];
		}
	}

	private function compressEnabled(): bool {
		try {
			$config = DataManager::getConfig(AdminIdentity::code());

			return (bool) ($config['public_assets_compress'] ?? true);
		} catch(\Throwable $e) {
			$this->swallowed('compressEnabled', $e);

			return true;
		}
	}

	private function bundleUrl(string $kind): ?string {
		try {
			$sectionKey = DleSiteSectionRegistry::instance()->currentKey();
			$cache      = new PublicAssetBundleCacheService();
			$file       = $cache->ensure($kind, $sectionKey);
			if($file === null || !is_file($file)) {
				return null;
			}

			$slug     = preg_replace('/[^a-z0-9_-]+/', '_', strtolower($sectionKey)) ?: 'main';
			$metaFile = Paths::cache() . '/public_assets/bundle.' . $kind . '.' . $slug . '.meta.json';
			$v        = is_file($metaFile)
				? (string) ((json_decode((string) file_get_contents($metaFile), true)['generated_at'] ?? null) ?? filemtime($file))
				: (string) filemtime($file);

			$relative = str_replace(ROOT_DIR, '', $file);

			return $this->siteBase() . ltrim(str_replace('\\', '/', $relative), '/') . '?v=' . rawurlencode($v);
		} catch(\Throwable $e) {
			$this->swallowed('bundleUrl', $e);

			return null;
		}
	}

	private function publicUrl(string $localPath): ?string {
		$path = trim($localPath);
		if($path === '' || str_contains($path, '..')) {
			return null;
		}

		$abs = str_starts_with($path, '/')
			? $path
			: rtrim(ROOT_DIR, '/') . '/' . ltrim($path, '/');

		if(!is_file($abs)) {
			return null;
		}

		$relative = str_replace(ROOT_DIR, '', $abs);

		return $this->siteBase() . ltrim(str_replace('\\', '/', $relative), '/');
	}

	private function siteBase(): string {
		global $config;

		$base = (string) ($config['http_home_url'] ?? '/');

		return rtrim($base, '/') . '/';
	}

	/**
	 * @param   list<string>  $parts
	 */
	private function joinParts(array $parts): string {
		return implode('', array_filter($parts, static fn(string $p): bool => $p !== ''));
	}

	private function escapeAttr(string $value): string {
		return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	private function swallowed(string $where, \Throwable $e): void {
		LogGenerator::for(AdminIdentity::name())->debug($where, ['error' => $e->getMessage()]);
	}

}
