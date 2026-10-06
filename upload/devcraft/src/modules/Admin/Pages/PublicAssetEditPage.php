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
 * Страница правки публичного CSS/JS (зависимости и разделы показа).
 */
final class PublicAssetEditPage extends AbstractPage {

	public function handle(): array {
		$kind = (string) ($_GET['kind'] ?? '');
		$id   = (int) ($_GET['id'] ?? 0);

		if($kind !== 'css' && $kind !== 'js') {
			return $this->errorPage(__('Некорректный тип ресурса'));
		}

		$listAction = $kind === 'css' ? 'public_styles' : 'public_scripts';
		$listTitle  = $kind === 'css' ? __('Публичные стили') : __('Публичные скрипты');

		$this->addBreadcrumb($listTitle, '?mod=' . AdminIdentity::mod() . '&action=' . $listAction);
		$this->addBreadcrumb(__('Правка записи'));

		/** @var PublicAssetEntryRepository $repo */
		$repo  = Application::instance()->database()->repository(PublicAssetEntry::class);
		$entry = $repo->findByPK($id);

		if(!$entry instanceof PublicAssetEntry || $entry->kind !== $kind) {
			return $this->errorPage(__('Запись не найдена'), $listAction);
		}

		return [
			'view' => 'admin/public_asset_edit/index.twig',
			'data' => [
				'page_title'  => __('Правка записи'),
				'kind'        => $kind,
				'entry_id'    => $entry->id(),
				'origin'      => $entry->origin,
				'list_url'    => '?mod=' . AdminIdentity::mod() . '&action=' . $listAction,
				'form'        => (new PublicAssetAdminFormService())->assetEditForm($entry),
				'mod'         => AdminIdentity::mod(),
			],
		];
	}

	/**
	 * @return array{view: string, data: array<string, mixed>}
	 */
	private function errorPage(string $message, string $backAction = 'public_styles'): array {
		return [
			'view' => 'admin/public_asset_edit/index.twig',
			'data' => [
				'page_title' => __('Правка записи'),
				'error'      => $message,
				'list_url'   => '?mod=' . AdminIdentity::mod() . '&action=' . $backAction,
			],
		];
	}

}
