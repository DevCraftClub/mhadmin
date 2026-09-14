<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Application;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use DevCraft\Modules\Admin\Services\PublicAssetAdminFormService;

/**
 * Страница управления публичными meta-заголовками.
 */
final class PublicHeadersPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные заголовки'));

		/** @var PublicHeaderEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$rows = [];

		foreach($repo->listOrdered() as $entry) {
			$rows[] = [
				'id'          => $entry->id(),
				'name'        => $entry->name,
				'content'     => $entry->content,
				'origin'      => $entry->origin,
				'active'      => $entry->active,
				'module_code' => $entry->module_code,
			];
		}

		global $dle_login_hash;

		return [
			'view' => 'admin/public_headers/index.twig',
			'data' => [
				'page_title'       => __('Публичные заголовки'),
				'page_description' => __('Meta name/content для head. Автозаписи только выключаются. Порядок — перетаскиванием.'),
				'rows'             => $rows,
				'form'             => (new PublicAssetAdminFormService())->headerAddForm(),
				'user_hash'        => $dle_login_hash ?? '',
				'mod'              => AdminIdentity::mod(),
			],
		];
	}

}
