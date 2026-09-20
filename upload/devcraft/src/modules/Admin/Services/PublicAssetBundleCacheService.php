<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;

/**
 * Объединённый кэш публичных CSS/JS + sidecar meta.
 */
final class PublicAssetBundleCacheService {

	public function __construct(
		private readonly PublicAssetMinify            $minify = new PublicAssetMinify(),
		private readonly PublicAssetDependencyService $deps   = new PublicAssetDependencyService(),
	) {}

	/**
	 * Каталог собранных файлов и `.htaccess` с доступом (нужен из‑за закрытого `devcraft/.htaccess`).
	 */
	public function ensureCacheDir(): ?string {
		$dir = Paths::cache() . '/public_assets';

		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return null;
		}

		$this->ensurePublicHtaccess($dir);

		return $dir;
	}

	public function ensure(string $kind, ?string $sectionKey = null): ?string {
		if($kind !== 'css' && $kind !== 'js') {
			return null;
		}

		$sectionKey ??= DleSiteSectionRegistry::instance()->currentKey();
		$dir = $this->ensureCacheDir();

		if($dir === null) {
			return null;
		}

		$entries  = $this->activeEntriesForSection($kind, $sectionKey);
		$hash     = $this->compositionHash($entries, $sectionKey);
		$metaPath = $dir . '/bundle.' . $kind . '.' . $this->sectionSlug($sectionKey) . '.meta.json';
		$outPath  = $dir . '/bundle.' . $kind . '.' . $this->sectionSlug($sectionKey);

		if(is_file($metaPath) && is_file($outPath)) {
			$meta = json_decode((string) file_get_contents($metaPath), true);
			if(is_array($meta)
				&& ($meta['composition_hash'] ?? '') === $hash
				&& !$this->sourcesNewer($meta['sources'] ?? [], (int) ($meta['generated_at'] ?? 0))) {
				return $outPath;
			}
		}

		$chunks  = [];
		$sources = [];

		foreach($entries as $entry) {
			$abs = $this->absolutePath($entry->local_path);
			if($abs === null) {
				continue;
			}

			$body = (string) file_get_contents($abs);
			$chunks[] = $kind === 'css' ? $this->minify->css($body) : $this->minify->js($body);
			$sources[] = [
				'path'  => $entry->local_path,
				'mtime' => filemtime($abs) ?: 0,
			];
		}

		$content = implode($kind === 'css' ? '' : "\n", $chunks);
		if(@file_put_contents($outPath, $content) === false) {
			return null;
		}

		$generatedAt = time();
		@file_put_contents($metaPath, json_encode([
			'kind'             => $kind,
			'section_key'      => $sectionKey,
			'generated_at'     => $generatedAt,
			'sources'          => $sources,
			'composition_hash' => $hash,
			'output_file'      => str_replace(ROOT_DIR, '', $outPath),
		], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

		return $outPath;
	}

	public function invalidate(string $kind): void {
		$this->invalidateAllSections($kind);
	}

	public function invalidateAllSections(string $kind): void {
		$dir = Paths::cache() . '/public_assets';
		if(!is_dir($dir)) {
			return;
		}

		if($kind === 'meta') {
			return;
		}

		foreach(glob($dir . '/bundle.' . $kind . '.*') ?: [] as $file) {
			if(str_ends_with($file, '.meta.json')) {
				@unlink($file);
				continue;
			}

			@unlink($file);
		}
	}

	/**
	 * @return list<PublicAssetEntry>
	 */
	private function activeEntriesForSection(string $kind, string $sectionKey): array {
		try {
			/** @var PublicAssetEntryRepository $repo */
			$repo    = Application::instance()->database()->repository(PublicAssetEntry::class);
			$entries = $repo->listByKind($kind);
			$depsMap = $this->deps->buildDepsMap($entries);
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
				if(!isset($byId[$id])) {
					continue;
				}

				$result[] = $byId[$id];
			}

			return $result;
		} catch(\Throwable) {
			return [];
		}
	}

	/**
	 * @param   list<PublicAssetEntry>  $entries
	 */
	private function compositionHash(array $entries, string $sectionKey): string {
		$parts = [$sectionKey];

		foreach($entries as $entry) {
			$parts[] = $entry->id() . '|' . $entry->local_path . '|' . $entry->sort_order;
		}

		return hash('sha256', implode(';', $parts));
	}

	private function sectionSlug(string $sectionKey): string {
		$slug = preg_replace('/[^a-z0-9_-]+/', '_', strtolower($sectionKey)) ?? 'main';

		return $slug === '' ? 'main' : $slug;
	}

	/**
	 * @param   list<array{path?: string, mtime?: int}>  $sources
	 */
	private function sourcesNewer(array $sources, int $generatedAt): bool {
		foreach($sources as $source) {
			$path = (string) ($source['path'] ?? '');
			$abs  = $this->absolutePath($path);
			if($abs === null) {
				return true;
			}

			if((filemtime($abs) ?: 0) > $generatedAt) {
				return true;
			}
		}

		return false;
	}

	private function absolutePath(string $localPath): ?string {
		$path = trim($localPath);
		if($path === '' || str_contains($path, '..')) {
			return null;
		}

		$abs = str_starts_with($path, '/')
			? $path
			: rtrim(ROOT_DIR, '/') . '/' . ltrim($path, '/');

		return is_file($abs) ? $abs : null;
	}

	/**
	 * Разрешает HTTP-доступ к собранным файлам: родительский `devcraft/.htaccess` закрывает весь каталог.
	 */
	private function ensurePublicHtaccess(string $dir): void {
		$file = $dir . '/.htaccess';

		if(is_file($file)) {
			return;
		}

		$content = <<<'HTACCESS'
<IfModule mod_authz_core.c>
    Require all granted
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Allow from all
</IfModule>

HTACCESS;

		@file_put_contents($file, $content);
	}

}
