<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use RuntimeException;

/**
 * Граф зависимостей публичных ресурсов одной категории.
 *
 * Отвечает за: очистку списка `depends_on`, поиск циклов, топологический
 * порядок, замыкание кандидатов для оболочки (FR-017: принудительный
 * вывод зависимостей даже при несовпадении разделов или неактивности).
 */
final class PublicAssetDependencyService {

	/**
	 * Оставляет только валидные id той же категории; убирает self и дубликаты.
	 *
	 * @param   list<int>        $rawIds
	 * @param   list<int|string> $validIds
	 *
	 * @return list<int>
	 */
	public function sanitizeDependsOn(array $rawIds, int $selfId, array $validIds): array {
		$valid = [];

		foreach($validIds as $raw) {
			$id = (int) $raw;
			if($id > 0) {
				$valid[$id] = true;
			}
		}

		$result = [];

		foreach($rawIds as $raw) {
			$id = (int) $raw;
			if($id <= 0 || $id === $selfId || !isset($valid[$id])) {
				continue;
			}

			$result[] = $id;
		}

		return array_values(array_unique($result));
	}

	/**
	 * Бросает исключение, если в графе есть цикл.
	 *
	 * @param   array<int, list<int>>  $depsMap  id → список id зависимостей
	 *
	 * @throws RuntimeException
	 */
	public function assertAcyclic(array $depsMap): void {
		$visited   = [];
		$inStack   = [];

		foreach(array_keys($depsMap) as $node) {
			if($this->dfsCycle((int) $node, $depsMap, $visited, $inStack)) {
				throw new RuntimeException(__('Обнаружен цикл зависимостей — сохранение отменено'));
			}
		}
	}

	/**
	 * Замыкание кандидатов по `depends_on` (рекурсивно), без порядка.
	 *
	 * Включает зависимости даже если они не были в кандидатном наборе
	 * (неактивны или не подошли по разделам) — FR-017.
	 *
	 * @param   list<int>              $candidateIds
	 * @param   array<int, list<int>>  $depsMap
	 *
	 * @return list<int>
	 */
	public function closureForOutput(array $candidateIds, array $depsMap): array {
		$closure = [];
		$stack   = [];

		foreach($candidateIds as $raw) {
			$id = (int) $raw;
			if($id <= 0) {
				continue;
			}

			$closure[$id] = true;
			$stack[]      = $id;
		}

		while($stack !== []) {
			$id = array_pop($stack);

			foreach($depsMap[$id] ?? [] as $depId) {
				if(!isset($closure[$depId])) {
					$closure[$depId] = true;
					$stack[]         = $depId;
				}
			}
		}

		return array_map('intval', array_keys($closure));
	}

	/**
	 * Замыкание кандидатов с порядком «сначала зависимости» (topo).
	 *
	 * @param   list<int>              $candidateIds
	 * @param   array<int, list<int>>  $depsMap
	 *
	 * @return list<int>
	 */
	public function orderedClosureForOutput(array $candidateIds, array $depsMap): array {
		return $this->topologicalSort(
			$this->closureForOutput($candidateIds, $depsMap),
			$depsMap,
		);
	}

	/**
	 * Проверяет, нарушен ли порядок «зависимость раньше зависимой».
	 *
	 * @param   list<int>              $orderedIds
	 * @param   array<int, list<int>>  $depsMap
	 */
	public function orderViolatesDeps(array $orderedIds, array $depsMap): bool {
		$position = [];

		foreach(array_values($orderedIds) as $index => $id) {
			$position[(int) $id] = $index;
		}

		foreach($orderedIds as $rawId) {
			$id = (int) $rawId;

			foreach($depsMap[$id] ?? [] as $depId) {
				if(!isset($position[$depId])) {
					continue;
				}

				if($position[$depId] > $position[$id]) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Стабильный топологический порядок с учётом желаемого порядка пользователя.
	 *
	 * @param   list<int>              $orderedIds   текущий желаемый порядок
	 * @param   array<int, list<int>>  $depsMap
	 *
	 * @return list<int>
	 *
	 * @throws RuntimeException при цикле
	 */
	public function topologicalSort(array $orderedIds, array $depsMap): array {
		$nodes = array_values(array_unique(array_map('intval', $orderedIds)));
		$index = [];

		foreach($nodes as $pos => $id) {
			$index[$id] = $pos;
		}

		foreach(array_keys($depsMap) as $id) {
			if(!isset($index[$id])) {
				$index[$id] = 100000 + (int) $id;
				$nodes[]    = (int) $id;
			}
		}

		$inDegree = [];

		foreach($nodes as $id) {
			$inDegree[$id] = 0;
		}

		foreach($depsMap as $id => $deps) {
			if(!isset($inDegree[$id])) {
				$inDegree[$id] = 0;
			}

			foreach($deps as $depId) {
				if(!isset($inDegree[$depId])) {
					$inDegree[$depId] = 0;
				}
			}
		}

		foreach($depsMap as $id => $deps) {
			foreach($deps as $depId) {
				if(!isset($inDegree[$id])) {
					continue;
				}

				$inDegree[$id]++;
			}
		}

		$queue = [];

		foreach($inDegree as $id => $degree) {
			if($degree === 0) {
				$queue[] = $id;
			}
		}

		usort($queue, static fn(int $a, int $b): int => ($index[$a] ?? 0) <=> ($index[$b] ?? 0));

		$result = [];

		while($queue !== []) {
			$id = array_shift($queue);
			$result[] = $id;

			foreach($depsMap as $node => $deps) {
				if(!in_array($id, $deps, true)) {
					continue;
				}

				$inDegree[$node]--;

				if($inDegree[$node] === 0) {
					$queue[] = $node;
				}
			}

			usort($queue, static fn(int $a, int $b): int => ($index[$a] ?? 0) <=> ($index[$b] ?? 0));
		}

		if(count($result) !== count($inDegree)) {
			throw new RuntimeException(__('Обнаружен цикл зависимостей'));
		}

		return $result;
	}

	/**
	 * Строит карту id → depends_on (только существующие id той же выборки).
	 *
	 * @param   list<PublicAssetEntry|PublicHeaderEntry>  $entries
	 *
	 * @return array<int, list<int>>
	 */
	public function buildDepsMap(array $entries): array {
		$valid = [];

		foreach($entries as $entry) {
			$valid[$entry->id()] = true;
		}

		$map = [];

		foreach($entries as $entry) {
			$id   = $entry->id();
			$deps = [];

			foreach($entry->dependsOnIds() as $depId) {
				if(isset($valid[$depId])) {
					$deps[] = $depId;
				}
			}

			$map[$id] = $deps;
		}

		return $map;
	}

	/**
	 * @param   list<int>  $stack
	 * @param   array<int, bool>  $visited
	 * @param   array<int, bool>  $inStack
	 */
	private function dfsCycle(int $node, array $depsMap, array &$visited, array &$inStack): bool {
		if(isset($inStack[$node])) {
			return true;
		}

		if(isset($visited[$node])) {
			return false;
		}

		$visited[$node] = true;
		$inStack[$node] = true;

		foreach($depsMap[$node] ?? [] as $dep) {
			if($this->dfsCycle($dep, $depsMap, $visited, $inStack)) {
				return true;
			}
		}

		unset($inStack[$node]);

		return false;
	}

}
