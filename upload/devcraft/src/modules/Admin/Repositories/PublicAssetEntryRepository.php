<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Repositories;

use DevCraft\Core\Abstracts\AbstractRepository;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;

/**
 * Репозиторий записей публичных CSS/JS (`dc_public_assets`).
 */
class PublicAssetEntryRepository extends AbstractRepository {

	/**
	 * @return list<PublicAssetEntry>
	 */
	public function listByKindOrdered(string $kind, bool $activeOnly = false): array {
		$select = $this->select()->where('kind', $kind)->orderBy('sort_order', 'ASC');

		if($activeOnly) {
			$select->where('active', true);
		}

		/** @var list<PublicAssetEntry> */
		return $select->fetchAll();
	}

	public function findByKindAndLocalPath(string $kind, string $localPath): ?PublicAssetEntry {
		/** @var PublicAssetEntry|null */
		return $this->select()
			->where('kind', $kind)
			->where('local_path', $localPath)
			->fetchOne();
	}

	public function deactivateByModule(string $moduleCode): int {
		$items = $this->select()
			->where('origin', 'auto')
			->where('module_code', $moduleCode)
			->fetchAll();
		$count = 0;

		foreach($items as $item) {
			if(!$item instanceof PublicAssetEntry || !$item->active) {
				continue;
			}

			$item->active = false;
			$this->saveEntity($item);
			$count++;
		}

		return $count;
	}

	public function maxSortOrder(string $kind): int {
		$row = $this->select()
			->where('kind', $kind)
			->orderBy('sort_order', 'DESC')
			->limit(1)
			->fetchOne();

		return $row instanceof PublicAssetEntry ? $row->sort_order : 0;
	}

}
