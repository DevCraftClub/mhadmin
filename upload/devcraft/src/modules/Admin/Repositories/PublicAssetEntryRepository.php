<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Repositories;

use DevCraft\Core\Abstracts\AbstractRepository;
use DevCraft\Core\Admin\FilterFormService;
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

	/**
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria
	 * @param   list<string>                                           $allowedOrderColumns
	 *
	 * @return array{items: object[], total: int}
	 */
	public function findFilteredForKind(
		string $kind,
		array  $criteria,
		int    $page,
		int    $perPage,
		string $order,
		string $sort,
		array  $allowedOrderColumns,
		string $defaultOrder = 'sort_order',
	): array {
		$select = $this->select()->where('kind', $kind);
		$this->applyCriteria($select, $criteria);

		$total = $select->count();
		$page  = max(1, $page);
		$order = in_array($order, $allowedOrderColumns, true) ? $order : $defaultOrder;

		/** @var object[] $items */
		$items = $select
			->orderBy($order, FilterFormService::getSort($sort))
			->limit($perPage)
			->offset(($page - 1) * $perPage)
			->fetchAll();

		return [
			'items' => $items,
			'total' => $total,
		];
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
