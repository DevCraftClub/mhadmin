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
		private readonly PublicAssetDependencyService     $deps       = new PublicAssetDependencyService(),
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
			$entry             = new PublicAssetEntry();
			$entry->origin     = 'manual';
			$entry->kind       = $kind;
			$entry->sort_order = $repo->maxSortOrder($kind) + 1;
		} elseif($entry->origin === 'auto' && isset($data['local_path'])) {
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
		$entry->active      = $this->resolveActiveFlag($data, $entry->active);
		$entry->origin      = $entry->origin === 'auto' ? 'auto' : 'manual';

		/** @var PublicAssetEntry $saved */
		$saved = $repo->saveEntity($entry);
		(new PublicAssetBundleCacheService())->invalidateAllSections($saved->kind);

		return $saved;
	}

	/**
	 * @param   array<string, mixed>  $data
	 */
	public function saveEdit(array $data): PublicAssetEntry {
		$kind = (string) ($data['kind'] ?? '');
		$id   = (int) ($data['id'] ?? 0);

		if($kind !== 'css' && $kind !== 'js') {
			throw new RuntimeException(__('Некорректный тип ресурса'));
		}

		if($id <= 0) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicAssetEntry || $entry->kind !== $kind) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry->origin === 'manual') {
			$sourceUrl = trim((string) ($data['source_url'] ?? ''));
			$localPath = trim((string) ($data['local_path'] ?? $entry->local_path));

			if($sourceUrl !== '') {
				$localPath = $this->downloader->download($sourceUrl, $kind);
				$entry->source_url = $sourceUrl;
			}

			if($localPath !== '' && !str_contains($localPath, '..')) {
				$entry->local_path = $localPath;
			}

			$entry->label = trim((string) ($data['label'] ?? $entry->label)) ?: null;
		}

		$entry->active = $this->resolveActiveFlag($data, $entry->active);

		return $this->persistDepsFields($entry, $data, true);
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

	/**
	 * @return array{deleted: bool, probe?: array<string, mixed>}
	 */
	public function delete(int $id, ?string $mode = null): array {
		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicAssetEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry->origin === 'auto') {
			throw new RuntimeException(__('Автозапись нельзя удалить — только выключить'));
		}

		$dependents = $repo->findDependents($id, $entry->kind);

		if($dependents !== [] && ($mode === null || $mode === 'probe')) {
			return [
				'deleted' => false,
				'probe'   => $this->dependentsProbePayload($dependents),
			];
		}

		if($dependents !== [] && $mode === 'unlink') {
			$this->unlinkFromDependents($id, $dependents);
		} elseif($dependents !== [] && $mode === 'cascade') {
			$this->cascadeDeleteDependents($id, $dependents);
		} elseif($dependents !== [] && $mode !== null) {
			throw new RuntimeException(__('Неизвестный режим удаления'));
		}

		$kind = $entry->kind;
		$repo->deleteEntity($entry);
		(new PublicAssetBundleCacheService())->invalidateAllSections($kind);

		return ['deleted' => true];
	}

	/**
	 * @param   list<int|string>  $ids
	 *
	 * @return array{reordered: bool, ids: list<int>}
	 */
	public function reorder(string $kind, array $ids): array {
		if($kind !== 'css' && $kind !== 'js') {
			throw new RuntimeException(__('Некорректный тип ресурса'));
		}

		/** @var PublicAssetEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entries = $repo->listByKind($kind);
		$depsMap = $this->deps->buildDepsMap($entries);
		$ordered = array_values(array_map('intval', $ids));
		$adjusted = false;

		if($this->deps->orderViolatesDeps($ordered, $depsMap)) {
			$ordered  = $this->deps->topologicalSort($ordered, $depsMap);
			$adjusted = true;
		}

		$sortById = [];

		foreach($ordered as $index => $entryId) {
			$sortById[$entryId] = $index;
		}

		$repo->bulkUpdateSortOrder($sortById);
		$this->markManuallyOrdered($kind);
		(new PublicAssetBundleCacheService())->invalidateAllSections($kind);

		return [
			'reordered' => $adjusted,
			'ids'       => $ordered,
		];
	}

	/**
	 * @param   array<string, mixed>  $data
	 */
	private function persistDepsFields(PublicAssetEntry $entry, array $data, bool $recalcSort): PublicAssetEntry {
		/** @var PublicAssetEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicAssetEntry::class);
		$all     = $repo->listByKind($entry->kind);
		$valid   = array_map(static fn(PublicAssetEntry $row): int => $row->id(), $all);
		$depends = $this->deps->sanitizeDependsOn(
			$this->normalizeIntList($data['depends_on'] ?? []),
			$entry->id() ?? 0,
			$valid,
		);

		$entry->setDependsOnIds($depends);
		[$available, $notAvailable] = $this->normalizeSectionLists(
			$data['available'] ?? [],
			$data['not_available'] ?? [],
		);
		$entry->setAvailableKeys($available);
		$entry->setNotAvailableKeys($notAvailable);

		/** @var PublicAssetEntry $saved */
		$saved = $repo->saveEntity($entry);

		if($recalcSort) {
			$this->recalculateKindSort($entry->kind);
		}

		(new PublicAssetBundleCacheService())->invalidateAllSections($entry->kind);

		return $saved;
	}

	public function recalculateKindSort(string $kind): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entries = $repo->listByKind($kind);
		$depsMap = $this->deps->buildDepsMap($entries);
		$this->deps->assertAcyclic($depsMap);

		$ordered = $this->deps->topologicalSort(
			array_map(static fn(PublicAssetEntry $e): int => $e->id(), $entries),
			$depsMap,
		);

		$sortById = [];

		foreach($ordered as $index => $entryId) {
			$sortById[$entryId] = $index;
		}

		$repo->bulkUpdateSortOrder($sortById);
	}

	/**
	 * @param   list<PublicAssetEntry>  $dependents
	 *
	 * @return array<string, mixed>
	 */
	private function dependentsProbePayload(array $dependents): array {
		$rows = [];

		foreach($dependents as $entry) {
			$rows[] = [
				'id'     => $entry->id(),
				'label'  => $entry->label ?: $entry->local_path,
				'origin' => $entry->origin,
			];
		}

		return [
			'dependents_count' => count($rows),
			'dependents'       => $rows,
			'auto_note'        => __('Автозаписи при удалении вместе не удаляются — только отвязываются'),
		];
	}

	/**
	 * @param   list<PublicAssetEntry>  $dependents
	 */
	private function unlinkFromDependents(int $targetId, array $dependents): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);

		foreach($dependents as $entry) {
			$deps = array_values(array_filter(
				$entry->dependsOnIds(),
				static fn(int $depId): bool => $depId !== $targetId,
			));
			$entry->setDependsOnIds($deps);
			$repo->saveEntity($entry);
		}
	}

	/**
	 * @param   list<PublicAssetEntry>  $dependents
	 */
	private function cascadeDeleteDependents(int $targetId, array $dependents): void {
		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);

		foreach($dependents as $entry) {
			if($entry->origin === 'auto') {
				$deps = array_values(array_filter(
					$entry->dependsOnIds(),
					static fn(int $depId): bool => $depId !== $targetId,
				));
				$entry->setDependsOnIds($deps);
				$repo->saveEntity($entry);
				continue;
			}

			$repo->deleteEntity($entry);
		}
	}

	private function markManuallyOrdered(string $kind): void {
		$code   = AdminIdentity::code();
		$config = DataManager::getConfig($code);
		$key    = 'public_assets_list_manually_ordered_' . $kind;
		$config[$key] = true;
		DataManager::saveConfig($code, $config);
	}

	/**
	 * @return list<int>
	 */
	private function normalizeIntList(mixed $raw): array {
		if(!is_array($raw)) {
			return [];
		}

		return array_values(array_map('intval', $raw));
	}

	/**
	 * @return list<string>
	 */
	private function normalizeStringList(mixed $raw): array {
		if(!is_array($raw)) {
			return [];
		}

		$keys = [];

		foreach($raw as $item) {
			$key = strtolower(trim((string) $item));
			if($key !== '') {
				$keys[] = $key;
			}
		}

		return array_values(array_unique($keys));
	}

	/**
	 * Whitelist + blacklist; при пересечении ключ остаётся только в исключениях.
	 *
	 * @return array{0: list<string>, 1: list<string>}
	 */
	private function normalizeSectionLists(mixed $availableRaw, mixed $notAvailableRaw): array {
		$available    = $this->normalizeStringList($availableRaw);
		$notAvailable = $this->normalizeStringList($notAvailableRaw);

		if($notAvailable === []) {
			return [$available, $notAvailable];
		}

		$denied = array_flip($notAvailable);
		$available = array_values(array_filter(
			$available,
			static fn(string $key): bool => !isset($denied[$key]),
		));

		return [$available, $notAvailable];
	}

	/**
	 * Разбирает флаг «Активен» из формы/AJAX (bool, 0/1, on/off).
	 */
	private function resolveActiveFlag(array $data, bool $fallback): bool {
		if(!array_key_exists('active', $data)) {
			return $fallback;
		}

		$raw = $data['active'];

		if(is_bool($raw)) {
			return $raw;
		}

		if(is_int($raw) || is_float($raw)) {
			return (int) $raw !== 0;
		}

		if(is_string($raw)) {
			$normalized = strtolower(trim($raw));

			if(in_array($normalized, ['0', 'false', 'off', 'no', ''], true)) {
				return false;
			}

			if(in_array($normalized, ['1', 'true', 'on', 'yes'], true)) {
				return true;
			}
		}

		return (bool) $raw;
	}

}
