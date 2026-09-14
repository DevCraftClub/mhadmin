<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use DevCraft\Types\ModuleManifest;
use DevCraft\Types\ModuleSiteAssets;

/**
 * Синхронизация siteAssets манифеста со списками Admin.
 */
final class PublicAssetManifestSyncService {

	public function syncModule(ModuleManifest $manifest, bool $isFirstInstall = false): void {
		$code = $manifest->code ?: $manifest->id;
		$site = $manifest->siteAssets;

		if($site->isEmpty()) {
			return;
		}

		$this->syncFiles($manifest, $site, $code, 'js', $site->js, $isFirstInstall);
		$this->syncFiles($manifest, $site, $code, 'css', $site->css, $isFirstInstall);
		$this->syncMeta($manifest, $site, $code, $isFirstInstall);
	}

	public function deactivateModule(string $moduleCode): void {
		/** @var PublicAssetEntryRepository $assets */
		$assets = Application::instance()->database()->repository(PublicAssetEntry::class);
		/** @var PublicHeaderEntryRepository $headers */
		$headers = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$assets->deactivateByModule($moduleCode);
		$headers->deactivateByModule($moduleCode);
	}

	/**
	 * @param   list<string>  $paths
	 */
	private function syncFiles(
		ModuleManifest $manifest,
		ModuleSiteAssets $site,
		string $code,
		string $kind,
		array $paths,
		bool $isFirstInstall,
	): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo     = Application::instance()->database()->repository(PublicAssetEntry::class);
		$ordered  = $this->isManuallyOrdered($kind);
		$append   = !$isFirstInstall || $ordered;

		foreach($paths as $path) {
			$local = $this->resolvePath($manifest, $path);
			if($local === null) {
				continue;
			}

			$existing = $repo->findByKindAndLocalPath($kind, $local);
			if($existing !== null) {
				continue;
			}

			$entry              = new PublicAssetEntry();
			$entry->kind        = $kind;
			$entry->origin      = 'auto';
			$entry->module_code = $code;
			$entry->label       = basename($path);
			$entry->source_path = $path;
			$entry->local_path  = $local;
			$entry->active      = true;
			$entry->sort_order  = $append
				? $repo->maxSortOrder($kind) + 1
				: $this->initialSortHint($code, $kind);
			$repo->saveEntity($entry);
		}
	}

	private function syncMeta(
		ModuleManifest $manifest,
		ModuleSiteAssets $site,
		string $code,
		bool $isFirstInstall,
	): void {
		/** @var PublicHeaderEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$ordered = $this->isManuallyOrdered('meta');
		$append  = !$isFirstInstall || $ordered;

		foreach($site->meta as $meta) {
			$name = $meta['name'];
			$existing = $repo->findByNameAndModule($code, $name);
			if($existing !== null) {
				continue;
			}

			$entry              = new PublicHeaderEntry();
			$entry->origin      = 'auto';
			$entry->module_code = $code;
			$entry->name        = $name;
			$entry->content     = $meta['content'];
			$entry->active      = true;
			$entry->sort_order  = $append ? $repo->maxSortOrder() + 1 : 0;
			$repo->saveEntity($entry);
		}
	}

	private function resolvePath(ModuleManifest $manifest, string $path): ?string {
		$path = trim($path);
		if($path === '' || str_contains($path, '..')) {
			return null;
		}

		if(str_starts_with($path, 'devcraft/') || str_starts_with($path, '/')) {
			$rel = ltrim($path, '/');
			$abs = rtrim(ROOT_DIR, '/') . '/' . $rel;

			return is_file($abs) ? $rel : null;
		}

		$abs = rtrim($manifest->path, '/\\') . '/Public/' . ltrim($path, '/');
		if(!is_file($abs)) {
			return null;
		}

		return ltrim(str_replace(ROOT_DIR, '', $abs), '/\\');
	}

	private function isManuallyOrdered(string $type): bool {
		$config = DataManager::getConfig(AdminIdentity::code());

		return !empty($config['public_assets_list_manually_ordered_' . $type]);
	}

	private function initialSortHint(string $moduleCode, string $kind): int {
		if($moduleCode === AdminIdentity::code()) {
			return 0;
		}

		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);

		return $repo->maxSortOrder($kind) + 1;
	}

}
