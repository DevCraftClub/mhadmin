<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Modules\Admin\Services\PublicAssetAdminFormService;
use DevCraft\Modules\Admin\Services\PublicAssetListPageService;

/**
 * Страница управления публичными meta-заголовками.
 */
final class PublicHeadersPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные заголовки'));

		return [
			'view' => 'admin/public_headers/index.twig',
			'data' => array_merge(
				(new PublicAssetListPageService())->headersPayload('public_headers'),
				[
					'page_title'       => __('Публичные заголовки'),
					'page_description' => __('Meta name/content для head. Автозаписи только выключаются. Порядок — перетаскиванием.'),
					'form'             => (new PublicAssetAdminFormService())->headerAddForm(),
				],
			),
		];
	}

}
