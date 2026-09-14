<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Application;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Core\Support\DataManager;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use Throwable;

/** DnD-порядок meta-заголовков. */
final class PublicHeaderReorderHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$ids = $request->data['ids'] ?? [];
			if(!is_array($ids)) {
				$ids = [];
			}

			/** @var PublicHeaderEntryRepository $repo */
			$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
			$order = 0;

			foreach($ids as $rawId) {
				$entry = $repo->findByPK((int) $rawId);
				if(!$entry instanceof PublicHeaderEntry) {
					continue;
				}

				$entry->sort_order = $order++;
				$repo->saveEntity($entry);
			}

			$config = DataManager::getConfig(AdminIdentity::code());
			$config['public_assets_list_manually_ordered_meta'] = true;
			DataManager::saveConfig(AdminIdentity::code(), $config);

			return JsonResponse::toast(__('Порядок сохранён'));
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
