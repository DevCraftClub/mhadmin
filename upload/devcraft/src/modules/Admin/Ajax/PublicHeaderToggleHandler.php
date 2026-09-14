<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Application;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use RuntimeException;
use Throwable;

/**
 * Включение/выключение публичного meta-заголовка.
 */
final class PublicHeaderToggleHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$id     = (int) ($request->data['id'] ?? 0);
			$active = !empty($request->data['active']);

			/** @var PublicHeaderEntryRepository $repo */
			$repo  = Application::instance()->database()->repository(PublicHeaderEntry::class);
			$entry = $repo->findByPK($id);

			if(!$entry instanceof PublicHeaderEntry) {
				throw new RuntimeException(__('Запись не найдена'));
			}

			$entry->active = $active;
			$repo->saveEntity($entry);

			return JsonResponse::toast(__('Обновлено'), ['active' => $entry->active]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
