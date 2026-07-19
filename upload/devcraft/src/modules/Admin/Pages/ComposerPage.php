<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DLEPlugins;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Core\Composer\Models\ComposerData;
use DevCraft\Modules\Admin\Services\DefaultPackageSeedService;

final class ComposerPage extends AbstractPage {

	public function handle(): array {
		(new DefaultPackageSeedService())->seedIfNeeded();

		$filterService      = new FilterFormService();
		$query              = $filterService->parseRequestQuery();
		$schema             = $this->loadFilterSchema();
		$order              = FilterFormService::normalizeOrder((string) ($query['order'] ?? $schema->defaultOrder), $schema);
		$allowedSortColumns = $schema->sortColumnKeys();
		if(!in_array($order, $allowedSortColumns, true)) {
			$order = in_array('package', $allowedSortColumns, true)? 'package' : $schema->defaultOrder;
		}
		$sort       = strtoupper((string) ($query['sort'] ?? 'ASC'));
		$rules      = $filterService->parseRules($query);
		$repository = Application::instance()->database()->repository(ComposerData::class);

		$this->addBreadcrumb(__('Composer'));
		global $dle_login_hash;

		return [
			'view' => 'admin/composer.twig',
			'data' => [
				'page_title'               => __('Composer'),
				'order'                    => $order,
				'sort'                     => $sort,
				'total'                    => 0,
				'per_page'                 => FilterFormService::resolveListCount(),
				'filter_rules'             => $rules,
				'filter_chips'             => $filterService->buildChipViewModel($rules, $schema),
				'filter_catalog'           => $filterService->buildCatalogViewModel($schema, $repository),
				'table_source_url'         => Paths::ajaxUrl('composer_table', 'admin', 'devcraft'),
				'table_initial_source_url' => $filterService->buildTableAjaxUrl(
					$query,
					(string) ($dle_login_hash ?? ''),
					$order,
					$sort,
					'composer_table',
				),
				'table_id'                 => 'dc-composer-table',
			],
		];
	}

	private function loadFilterSchema(): FilterSchema {
		$schemaFile = Paths::modules() . '/Admin/Filter/composer.filter.schema.php';
		/** @var array<string, mixed> $raw */
		$raw = require DLEPlugins::Check($schemaFile);

		return FilterSchema::fromArray($raw);
	}

}
