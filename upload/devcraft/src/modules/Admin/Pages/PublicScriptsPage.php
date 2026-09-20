<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Modules\Admin\Services\PublicAssetAdminFormService;
use DevCraft\Modules\Admin\Services\PublicAssetListPageService;

/**
 * Страница управления публичными скриптами оболочки сайта.
 */
final class PublicScriptsPage extends AbstractPage {

	public function handle(): array {
		$this->addBreadcrumb(__('Публичные скрипты'));

		return [
			'view' => 'admin/public_scripts/index.twig',
			'data' => array_merge(
				(new PublicAssetListPageService())->assetsPayload('js', 'public_scripts'),
				[
					'page_title'       => __('Публичные скрипты'),
					'page_description' => __('JS оболочки сайта: авто из манифестов модулей и ручные записи. Порядок — перетаскиванием.'),
					'kind'             => 'js',
					'form'             => (new PublicAssetAdminFormService())->assetAddForm('js'),
				],
			),
		];
	}

}
