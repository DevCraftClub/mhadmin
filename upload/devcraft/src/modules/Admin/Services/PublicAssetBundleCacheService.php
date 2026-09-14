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
		private readonly PublicAssetMinify $minify = new PublicAssetMinify(),
	) {}

	public function ensure(string $kind): ?string {
		if($kind !== 'css' && $kind !== 'js') {
			return null;
		}

		$dir = Paths::cache() . '/public_assets';
		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return null;
		}

		$entries = $this->activeEntries($kind);
		$hash    = $this->compositionHash($entries);
		$metaPath = $dir . '/bundle.' . $kind . '.meta.json';
		$outPath  = $dir . '/bundle.' . $kind;

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
			'kind'              => $kind,
			'generated_at'      => $generatedAt,
			'sources'           => $sources,
			'composition_hash'  => $hash,
			'output_file'       => str_replace(ROOT_DIR, '', $outPath),
		], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

		return $outPath;
	}

	public function invalidate(string $kind): void {
		$dir = Paths::cache() . '/public_assets';
		@unlink($dir . '/bundle.' . $kind);
		@unlink($dir . '/bundle.' . $kind . '.meta.json');
	}

	/**
	 * @return list<PublicAssetEntry>
	 */
	private function activeEntries(string $kind): array {
		try {
			/** @var PublicAssetEntryRepository $repo */
			$repo = Application::instance()->database()->repository(PublicAssetEntry::class);

			return $repo->listByKindOrdered($kind, true);
		} catch(\Throwable) {
			return [];
		}
	}

	/**
	 * @param   list<PublicAssetEntry>  $entries
	 */
	private function compositionHash(array $entries): string {
		$parts = [];

		foreach($entries as $entry) {
			$parts[] = $entry->id() . '|' . $entry->local_path . '|' . $entry->sort_order;
		}

		return hash('sha256', implode(';', $parts));
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

}
