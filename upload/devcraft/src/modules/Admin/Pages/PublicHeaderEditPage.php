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
 * Страница правки публичного meta-заголовка.
 */
final class PublicHeaderEditPage extends AbstractPage {

	public function handle(): array {
		$id = (int) ($_GET['id'] ?? 0);

		$this->addBreadcrumb(__('Публичные заголовки'), '?mod=' . AdminIdentity::mod() . '&action=public_headers');
		$this->addBreadcrumb(__('Правка заголовка'));

		/** @var PublicHeaderEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicHeaderEntry) {
			return [
				'view' => 'admin/public_header_edit/index.twig',
				'data' => [
					'page_title' => __('Правка заголовка'),
					'error'      => __('Запись не найдена'),
					'list_url'   => '?mod=' . AdminIdentity::mod() . '&action=public_headers',
				],
			];
		}

		return [
			'view' => 'admin/public_header_edit/index.twig',
			'data' => [
				'page_title' => __('Правка заголовка'),
				'entry_id'   => $entry->id(),
				'origin'     => $entry->origin,
				'list_url'   => '?mod=' . AdminIdentity::mod() . '&action=public_headers',
				'form'       => (new PublicAssetAdminFormService())->headerEditForm($entry),
				'mod'        => AdminIdentity::mod(),
			],
		];
	}

}
