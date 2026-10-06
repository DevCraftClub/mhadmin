<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Services\PublicHeaderWriteService;
use Throwable;

/** Сохранение meta-заголовка. */
final class PublicHeaderSaveHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$service = new PublicHeaderWriteService();
			$isEdit  = !empty($request->data['edit'])
				|| array_key_exists('depends_on', $request->data)
				|| array_key_exists('available', $request->data)
				|| array_key_exists('not_available', $request->data);

			$entry = $isEdit
				? $service->saveEdit($request->data)
				: $service->save($request->data);

			return JsonResponse::toast(__('Сохранено'), [
				'id'       => $entry->id(),
				'redirect' => $isEdit
					? '?mod=' . AdminIdentity::mod() . '&action=public_headers'
					: null,
			]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
