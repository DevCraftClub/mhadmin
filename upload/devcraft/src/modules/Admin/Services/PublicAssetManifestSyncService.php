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
			$this->clearModulePublicAssets($code);

			return;
		}

		$this->syncFiles($manifest, $code, 'js', $site->js, $isFirstInstall);
		$this->syncFiles($manifest, $code, 'css', $site->css, $isFirstInstall);
		$this->syncMeta($manifest, $site, $code, $isFirstInstall);
	}

	/**
	 * Снимает auto-записи siteAssets модуля, у которого в манифесте больше нет публичной оболочки.
	 */
	private function clearModulePublicAssets(string $moduleCode): void {
		$removed = $this->deactivateModule($moduleCode);

		if($removed <= 0) {
			return;
		}

		$cache = new PublicAssetBundleCacheService();
		$cache->invalidate('js');
		$cache->invalidate('css');
	}

	public function deactivateModule(string $moduleCode): int {
		/** @var PublicAssetEntryRepository $assets */
		$assets = Application::instance()->database()->repository(PublicAssetEntry::class);
		/** @var PublicHeaderEntryRepository $headers */
		$headers = Application::instance()->database()->repository(PublicHeaderEntry::class);

		return $assets->deactivateByModule($moduleCode) + $headers->deactivateByModule($moduleCode);
	}

	/**
	 * @param   list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}>  $specs
	 */
	private function syncFiles(
		ModuleManifest $manifest,
		string $code,
		string $kind,
		array $specs,
		bool $isFirstInstall,
	): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicAssetEntry::class);
		$ordered = $this->isManuallyOrdered($kind);
		$append  = !$isFirstInstall || $ordered;
		$keep    = [];
		$pending = [];

		foreach($specs as $spec) {
			$path  = $spec['file'];
			$local = $this->resolvePath($manifest, $path);
			if($local === null) {
				continue;
			}

			$keep[$local] = true;

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
			$entry->active      = $spec['active'];
			$entry->sort_order  = $append
				? $repo->maxSortOrder($kind) + 1
				: $this->initialSortHint($code, $kind);
			$repo->saveEntity($entry);
			$pending[] = [$entry, $spec];
		}

		$depsChanged = false;

		foreach($pending as [$entry, $spec]) {
			if($this->applyManifestPlacement($repo, $manifest, $kind, $entry, $spec)) {
				$depsChanged = true;
			}
		}

		if($depsChanged) {
			try {
				(new PublicAssetWriteService())->recalculateKindSort($kind);
			} catch(\RuntimeException) {
			}
		}

		$this->deactivateStaleAutoFiles($repo, $code, $kind, $keep);
	}

	/**
	 * Пишет разделы и зависимости из манифеста только для новой записи.
	 *
	 * @param   array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}  $spec
	 */
	private function applyManifestPlacement(
		PublicAssetEntryRepository $repo,
		ModuleManifest $manifest,
		string $kind,
		PublicAssetEntry $entry,
		array $spec,
	): bool {
		$ids = [];

		foreach($spec['dependsOn'] as $ref) {
			$dep = $this->findByKindAndRef($repo, $manifest, $kind, $ref);
			if($dep !== null && $dep->id() !== $entry->id()) {
				$ids[] = $dep->id();
			}
		}

		$ids = array_values(array_unique($ids));
		$entry->setDependsOnIds($ids);
		$entry->setAvailableKeys($spec['available']);
		$entry->setNotAvailableKeys($spec['notAvailable']);
		$repo->saveEntity($entry);

		return $ids !== [];
	}

	private function findByKindAndRef(
		PublicAssetEntryRepository $repo,
		ModuleManifest $manifest,
		string $kind,
		string $ref,
	): ?PublicAssetEntry {
		$local = $this->resolvePath($manifest, $ref);
		if($local !== null) {
			$found = $repo->findByKindAndLocalPath($kind, $local);
			if($found !== null) {
				return $found;
			}
		}

		$needle = trim($ref);
		if($needle === '') {
			return null;
		}

		$base = basename($needle);

		foreach($repo->listByKind($kind) as $row) {
			if(
				$row->local_path === $needle
				|| (string) $row->source_path === $needle
				|| basename($row->local_path) === $base
				|| basename((string) $row->source_path) === $base
			) {
				return $row;
			}
		}

		return null;
	}

	/**
	 * Выключает auto-записи модуля, которых больше нет в siteAssets (иначе в оболочку попадает лишнее).
	 *
	 * @param   array<string, true>  $keep
	 */
	private function deactivateStaleAutoFiles(
		PublicAssetEntryRepository $repo,
		string $code,
		string $kind,
		array $keep,
	): void {
		$changed = false;

		foreach($repo->listByKindOrdered($kind, false) as $entry) {
			if($entry->origin !== 'auto' || $entry->module_code !== $code || !$entry->active) {
				continue;
			}

			if(isset($keep[$entry->local_path])) {
				continue;
			}

			$entry->active = false;
			$repo->saveEntity($entry);
			$changed = true;
		}

		if($changed) {
			(new PublicAssetBundleCacheService())->invalidate($kind);
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
