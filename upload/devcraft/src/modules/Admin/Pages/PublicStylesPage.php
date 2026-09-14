<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Application;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use DevCraft\Modules\Admin\Services\PublicAssetAdminFormService;

/**
 * Страница управления публичными стилями оболочки сайта.
 */
final class PublicStylesPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные стили'));

		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);
		$rows = [];

		foreach($repo->listByKindOrdered('css') as $entry) {
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

		global $dle_login_hash;

		return [
			'view' => 'admin/public_styles/index.twig',
			'data' => [
				'page_title'       => __('Публичные стили'),
				'page_description' => __('CSS оболочки сайта: авто из манифестов модулей и ручные записи. Порядок — перетаскиванием.'),
				'kind'             => 'css',
				'rows'             => $rows,
				'form'             => (new PublicAssetAdminFormService())->assetAddForm('css'),
				'user_hash'        => $dle_login_hash ?? '',
				'mod'              => AdminIdentity::mod(),
			],
		];
	}

}
