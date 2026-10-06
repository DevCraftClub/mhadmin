<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Modules\Admin\Services\PublicAssetAdminFormService;
use DevCraft\Modules\Admin\Services\PublicAssetListPageService;

/**
 * Страница управления публичными стилями оболочки сайта.
 */
final class PublicStylesPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные стили'));

		return [
			'view' => 'admin/public_styles/index.twig',
			'data' => array_merge(
				(new PublicAssetListPageService())->assetsPayload('css', 'public_styles'),
				[
					'page_title'       => __('Публичные стили'),
					'page_description' => __('CSS оболочки сайта: авто из манифестов модулей и ручные записи. Порядок — перетаскиванием.'),
					'kind'             => 'css',
					'form'             => (new PublicAssetAdminFormService())->assetAddForm('css'),
				],
			),
		];
	}

}
