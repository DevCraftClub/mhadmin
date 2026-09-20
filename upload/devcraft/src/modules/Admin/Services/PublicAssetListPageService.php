<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Core\Application;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use DevCraft\Types\FilterSchema;
use DLEPlugins;

/**
 * Общие данные списков публичных CSS/JS/meta: фильтр + строки таблицы.
 */
final class PublicAssetListPageService {

	/**
	 * @return array<string, mixed>
	 */
	public function assetsPayload(string $kind, string $pageAction): array {
		$filter = $this->filterPayload(
			'Admin/Filter/public_assets.filter.schema.php',
			$pageAction,
		);

		/** @var PublicAssetEntryRepository $repo */
		$repo   = Application::instance()->database()->repository(PublicAssetEntry::class);
		$result = $repo->findFilteredForKind(
			$kind,
			$filter['criteria'],
			1,
			500,
			$filter['order'],
			$filter['sort'],
			$filter['schema']->sortColumnKeys(),
			$filter['schema']->defaultOrder,
		);

		$rows = [];

		foreach($result['items'] as $entry) {
			if(!$entry instanceof PublicAssetEntry) {
				continue;
			}

			$rows[] = [
				'id'          => $entry->id(),
				'label'       => $entry->label ?: $entry->local_path,
				'local_path'  => $entry->local_path,
				'source_url'  => $entry->source_url,
				'origin'      => $entry->origin,
				'active'      => $entry->active,
				'module_code' => $entry->module_code,
			];
		}

		return $this->mergeList($filter, $rows, $result['total'], $pageAction);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function headersPayload(string $pageAction): array {
		$filter = $this->filterPayload(
			'Admin/Filter/public_headers.filter.schema.php',
			$pageAction,
		);

		/** @var PublicHeaderEntryRepository $repo */
		$repo   = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$result = $repo->findFiltered(
			$filter['criteria'],
			1,
			500,
			$filter['order'],
			$filter['sort'],
			$filter['schema']->sortColumnKeys(),
			$filter['schema']->defaultOrder,
		);

		$rows = [];

		foreach($result['items'] as $entry) {
			if(!$entry instanceof PublicHeaderEntry) {
				continue;
			}

			$rows[] = [
				'id'          => $entry->id(),
				'name'        => $entry->name,
				'content'     => $entry->content,
				'origin'      => $entry->origin,
				'active'      => $entry->active,
				'module_code' => $entry->module_code,
			];
		}

		return $this->mergeList($filter, $rows, $result['total'], $pageAction);
	}

	/**
	 * @return array{
	 *     schema: FilterSchema,
	 *     criteria: list<array{column: string, op: string, value: mixed}>,
	 *     order: string,
	 *     sort: string,
	 *     filter_rules: list<array<string, mixed>>,
	 *     filter_chips: list<array<string, mixed>>,
	 *     filter_catalog: array<string, mixed>
	 * }
	 */
	private function filterPayload(string $schemaRel, string $pageAction): array {
		$filterService = new FilterFormService();
		$query         = $filterService->parseRequestQuery();
		$schema        = $this->loadSchema($schemaRel);
		$order         = FilterFormService::normalizeOrder(
			(string) ($query['order'] ?? $schema->defaultOrder),
			$schema,
		);
		$sort  = strtoupper((string) ($query['sort'] ?? 'ASC'));
		$rules = $filterService->parseRules($query);

		/** @var PublicAssetEntryRepository|PublicHeaderEntryRepository $catalogRepo */
		$catalogRepo = str_contains($schemaRel, 'header')
			? Application::instance()->database()->repository(PublicHeaderEntry::class)
			: Application::instance()->database()->repository(PublicAssetEntry::class);

		return [
			'schema'         => $schema,
			'criteria'       => $filterService->rulesToCriteria($rules, $schema),
			'order'          => $order,
			'sort'           => $sort,
			'filter_rules'   => $rules,
			'filter_chips'   => $filterService->buildChipViewModel($rules, $schema),
			'filter_catalog' => $filterService->buildCatalogViewModel($schema, $catalogRepo),
			'page_action'    => $pageAction,
		];
	}

	/**
	 * @param   array<string, mixed>     $filter
	 * @param   list<array<string, mixed>> $rows
	 *
	 * @return array<string, mixed>
	 */
	private function mergeList(array $filter, array $rows, int $total, string $pageAction): array {
		global $dle_login_hash;

		return [
			'rows'           => $rows,
			'total'          => $total,
			'order'          => $filter['order'],
			'sort'           => $filter['sort'],
			'filter_rules'   => $filter['filter_rules'],
			'filter_chips'   => $filter['filter_chips'],
			'filter_catalog' => $filter['filter_catalog'],
			'user_hash'      => $dle_login_hash ?? '',
			'mod'            => AdminIdentity::mod(),
			'action'         => $pageAction,
			'dnd_enabled'    => $filter['filter_rules'] === [],
		];
	}

	private function loadSchema(string $rel): FilterSchema {
		$loaded = require DLEPlugins::Check(DEVCRAFT_MODULES . '/' . $rel);

		if($loaded instanceof FilterSchema) {
			return $loaded;
		}

		/** @var array<string, mixed> $raw */
		$raw = is_array($loaded) ? $loaded : [];

		return FilterSchema::fromArray($raw);
	}

}
