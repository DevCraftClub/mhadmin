<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use RuntimeException;

/**
 * CRUD правил для публичных CSS/JS (manual delete only; auto — toggle).
 */
final class PublicAssetWriteService {

	public function __construct(
		private readonly PublicAssetRemoteDownloadService $downloader = new PublicAssetRemoteDownloadService(),
	) {}

	/**
	 * @param   array<string, mixed>  $data
	 */
	public function save(array $data): PublicAssetEntry {
		$kind   = (string) ($data['kind'] ?? '');
		$id     = (int) ($data['id'] ?? 0);

		if($kind !== 'css' && $kind !== 'js') {
			throw new RuntimeException(__('Некорректный тип ресурса'));
		}

		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $id > 0 ? $repo->findByPK($id) : null;

		if($id > 0 && !$entry instanceof PublicAssetEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry === null) {
			$entry         = new PublicAssetEntry();
			$entry->origin = 'manual';
			$entry->kind   = $kind;
			$entry->sort_order = $repo->maxSortOrder($kind) + 1;
		} elseif($entry->origin === 'auto' && isset($data['local_path'])) {
			// auto: разрешаем только label/active через toggle; путь не меняем вручную
			throw new RuntimeException(__('Автозапись нельзя редактировать как ручную'));
		}

		$sourceUrl = trim((string) ($data['source_url'] ?? ''));
		$localPath = trim((string) ($data['local_path'] ?? $entry->local_path));

		if($sourceUrl !== '') {
			$localPath = $this->downloader->download($sourceUrl, $kind);
			$entry->source_url = $sourceUrl;
		}

		if($localPath === '') {
			throw new RuntimeException(__('Укажите локальный путь или внешний URL'));
		}

		if(str_contains($localPath, '..')) {
			throw new RuntimeException(__('Путь не должен содержать ..'));
		}

		$dup = $repo->findByKindAndLocalPath($kind, $localPath);
		if($dup !== null && $dup->active && !($id > 0 && $dup->id() === $id)) {
			throw new RuntimeException(__('Активная запись с таким путём уже существует'));
		}

		$entry->kind        = $kind;
		$entry->label       = trim((string) ($data['label'] ?? $entry->label)) ?: null;
		$entry->source_path = trim((string) ($data['source_path'] ?? $entry->source_path)) ?: null;
		$entry->local_path  = $localPath;
		$entry->active      = array_key_exists('active', $data)
			? (bool) $data['active']
			: $entry->active;
		$entry->origin      = $entry->origin === 'auto' ? 'auto' : 'manual';

		/** @var PublicAssetEntry */
		return $repo->saveEntity($entry);
	}

	public function toggle(int $id, bool $active): PublicAssetEntry {
		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicAssetEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		$entry->active = $active;

		/** @var PublicAssetEntry */
		return $repo->saveEntity($entry);
	}

	public function delete(int $id): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicAssetEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry->origin === 'auto') {
			throw new RuntimeException(__('Автозапись нельзя удалить — только выключить'));
		}

		$repo->deleteEntity($entry);
	}

	/**
	 * @param   list<int|string>  $ids
	 */
	public function reorder(string $kind, array $ids): void {
		if($kind !== 'css' && $kind !== 'js') {
			throw new RuntimeException(__('Некорректный тип ресурса'));
		}

		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$order = 0;

		foreach($ids as $rawId) {
			$id    = (int) $rawId;
			$entry = $repo->findByPK($id);

			if(!$entry instanceof PublicAssetEntry || $entry->kind !== $kind) {
				continue;
			}

			$entry->sort_order = $order++;
			$repo->saveEntity($entry);
		}

		$this->markManuallyOrdered($kind);
		(new PublicAssetBundleCacheService())->invalidate($kind);
	}

	private function markManuallyOrdered(string $kind): void {
		$code   = AdminIdentity::code();
		$config = DataManager::getConfig($code);
		$key    = 'public_assets_list_manually_ordered_' . $kind;
		$config[$key] = true;
		DataManager::saveConfig($code, $config);
	}

}
