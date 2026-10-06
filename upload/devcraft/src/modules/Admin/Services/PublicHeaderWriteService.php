<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use RuntimeException;

/**
 * CRUD и граф зависимостей для публичных meta-заголовков.
 */
final class PublicHeaderWriteService {

	public function __construct(
		private readonly PublicAssetDependencyService $deps = new PublicAssetDependencyService(),
	) {}

	/**
	 * @param   array<string, mixed>  $data
	 */
	public function save(array $data): PublicHeaderEntry {
		$name    = trim((string) ($data['name'] ?? ''));
		$content = trim((string) ($data['content'] ?? ''));
		$id      = (int) ($data['id'] ?? 0);

		if($name === '' || $content === '') {
			throw new RuntimeException(__('Имя и содержимое обязательны'));
		}

		/** @var PublicHeaderEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entry = $id > 0 ? $repo->findByPK($id) : null;

		if($id > 0 && !$entry instanceof PublicHeaderEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry === null) {
			$entry             = new PublicHeaderEntry();
			$entry->origin     = 'manual';
			$entry->sort_order = $repo->maxSortOrder() + 1;
		}

		$entry->name    = $name;
		$entry->content = $content;
		$entry->active  = $this->resolveActiveFlag($data, $entry->active);

		/** @var PublicHeaderEntry $saved */
		$saved = $this->persistDepsFields($entry, $data, false);

		return $saved;
	}

	/**
	 * @param   array<string, mixed>  $data
	 */
	public function saveEdit(array $data): PublicHeaderEntry {
		$id = (int) ($data['id'] ?? 0);

		if($id <= 0) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		/** @var PublicHeaderEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicHeaderEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry->origin !== 'auto') {
			$name    = trim((string) ($data['name'] ?? $entry->name));
			$content = trim((string) ($data['content'] ?? $entry->content));

			if($name === '' || $content === '') {
				throw new RuntimeException(__('Имя и содержимое обязательны'));
			}

			$entry->name    = $name;
			$entry->content = $content;
		}

		$entry->active = $this->resolveActiveFlag($data, $entry->active);

		return $this->persistDepsFields($entry, $data, true);
	}

	/**
	 * @return array{deleted: bool, probe?: array<string, mixed>}
	 */
	public function delete(int $id, ?string $mode = null): array {
		/** @var PublicHeaderEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicHeaderEntry) {
			throw new RuntimeException(__('Запись не найдена'));
		}

		if($entry->origin === 'auto') {
			throw new RuntimeException(__('Автозапись нельзя удалить — только выключить'));
		}

		$dependents = $repo->findDependents($id);

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

		$repo->deleteEntity($entry);
		(new PublicAssetBundleCacheService())->invalidateAllSections('meta');

		return ['deleted' => true];
	}

	/**
	 * @param   list<int|string>  $ids
	 *
	 * @return array{reordered: bool}
	 */
	/**
	 * @param   list<int|string>  $ids
	 *
	 * @return array{reordered: bool, ids: list<int>}
	 */
	public function reorder(array $ids): array {
		/** @var PublicHeaderEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entries = $repo->listAllOrdered();
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
		$this->markManuallyOrdered();

		return [
			'reordered' => $adjusted,
			'ids'       => $ordered,
		];
	}

	/**
	 * @param   array<string, mixed>  $data
	 */
	private function persistDepsFields(PublicHeaderEntry $entry, array $data, bool $recalcSort): PublicHeaderEntry {
		/** @var PublicHeaderEntryRepository $repo */
		$repo      = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$all       = $repo->listAllOrdered();
		$valid     = array_map(static fn(PublicHeaderEntry $row): int => $row->id(), $all);
		$depends   = $this->deps->sanitizeDependsOn(
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

		/** @var PublicHeaderEntry $saved */
		$saved = $repo->saveEntity($entry);

		if($recalcSort) {
			$this->recalculateCategorySortOrder();
		}

		(new PublicAssetBundleCacheService())->invalidateAllSections('meta');

		return $saved;
	}

	private function recalculateCategorySortOrder(): void {
		/** @var PublicHeaderEntryRepository $repo */
		$repo    = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entries = $repo->listAllOrdered();
		$depsMap = $this->deps->buildDepsMap($entries);
		$this->deps->assertAcyclic($depsMap);

		$ordered = $this->deps->topologicalSort(
			array_map(static fn(PublicHeaderEntry $e): int => $e->id(), $entries),
			$depsMap,
		);

		$sortById = [];

		foreach($ordered as $index => $entryId) {
			$sortById[$entryId] = $index;
		}

		$repo->bulkUpdateSortOrder($sortById);
	}

	/**
	 * @param   list<PublicHeaderEntry>  $dependents
	 *
	 * @return array<string, mixed>
	 */
	private function dependentsProbePayload(array $dependents): array {
		$rows = [];

		foreach($dependents as $entry) {
			$rows[] = [
				'id'     => $entry->id(),
				'label'  => $entry->name,
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
	 * @param   list<PublicHeaderEntry>  $dependents
	 */
	private function unlinkFromDependents(int $targetId, array $dependents): void {
		/** @var PublicHeaderEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicHeaderEntry::class);

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
	 * @param   list<PublicHeaderEntry>  $dependents
	 */
	private function cascadeDeleteDependents(int $targetId, array $dependents): void {
		/** @var PublicHeaderEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicHeaderEntry::class);

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

	private function markManuallyOrdered(): void {
		$config = DataManager::getConfig(AdminIdentity::code());
		$config['public_assets_list_manually_ordered_meta'] = true;
		DataManager::saveConfig(AdminIdentity::code(), $config);
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
