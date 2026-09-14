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
 * Страница управления публичными скриптами оболочки сайта.
 */
final class PublicScriptsPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные скрипты'));

		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);
		$rows = [];

		foreach($repo->listByKindOrdered('js') as $entry) {
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
			'view' => 'admin/public_scripts/index.twig',
			'data' => [
				'page_title'       => __('Публичные скрипты'),
				'page_description' => __('JS оболочки сайта: авто из манифестов модулей и ручные записи. Порядок — перетаскиванием.'),
				'kind'             => 'js',
				'rows'             => $rows,
				'form'             => (new PublicAssetAdminFormService())->assetAddForm('js'),
				'user_hash'        => $dle_login_hash ?? '',
				'mod'              => AdminIdentity::mod(),
			],
		];
	}

}
