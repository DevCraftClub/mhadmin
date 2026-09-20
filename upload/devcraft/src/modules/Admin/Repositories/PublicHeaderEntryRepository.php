<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Repositories;

use DevCraft\Core\Abstracts\AbstractRepository;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;

/**
 * Репозиторий публичных meta-заголовков (`dc_public_headers`).
 */
class PublicHeaderEntryRepository extends AbstractRepository {

	/**
	 * @return list<PublicHeaderEntry>
	 */
	public function listOrdered(bool $activeOnly = false): array {
		$select = $this->select()->orderBy('sort_order', 'ASC');

		if($activeOnly) {
			$select->where('active', true);
		}

		/** @var list<PublicHeaderEntry> */
		return $select->fetchAll();
	}

	public function findByNameAndModule(?string $moduleCode, string $name): ?PublicHeaderEntry {
		$select = $this->select()->where('name', $name);

		if($moduleCode === null || $moduleCode === '') {
			$select->where('module_code', null);
		} else {
			$select->where('module_code', $moduleCode);
		}

		/** @var PublicHeaderEntry|null */
		return $select->fetchOne();
	}

	public function deactivateByModule(string $moduleCode): int {
		$items = $this->select()
			->where('origin', 'auto')
			->where('module_code', $moduleCode)
			->fetchAll();
		$count = 0;

		foreach($items as $item) {
			if(!$item instanceof PublicHeaderEntry || !$item->active) {
				continue;
			}

			$item->active = false;
			$this->saveEntity($item);
			$count++;
		}

		return $count;
	}

	public function maxSortOrder(): int {
		$row = $this->select()->orderBy('sort_order', 'DESC')->limit(1)->fetchOne();

		return $row instanceof PublicHeaderEntry ? $row->sort_order : 0;
	}

	/**
	 * @return list<PublicHeaderEntry>
	 */
	public function listAllOrdered(): array {
		/** @var list<PublicHeaderEntry> */
		return $this->select()->orderBy('sort_order', 'ASC')->fetchAll();
	}

	/**
	 * @return list<PublicHeaderEntry>
	 */
	public function findDependents(int $id): array {
		$result = [];

		foreach($this->listAllOrdered() as $entry) {
			if(in_array($id, $entry->dependsOnIds(), true)) {
				$result[] = $entry;
			}
		}

		return $result;
	}

	/**
	 * @param   array<int, int>  $sortById
	 */
	public function bulkUpdateSortOrder(array $sortById): void {
		foreach($sortById as $id => $order) {
			$entry = $this->findByPK((int) $id);
			if(!$entry instanceof PublicHeaderEntry) {
				continue;
			}

			$entry->sort_order = (int) $order;
			$this->saveEntity($entry);
		}
	}

}
