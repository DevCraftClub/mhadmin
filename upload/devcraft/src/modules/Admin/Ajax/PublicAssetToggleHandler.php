<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\PublicAssetBundleCacheService;
use DevCraft\Modules\Admin\Services\PublicAssetWriteService;
use Throwable;

/** Включение/выключение записи публичного CSS/JS. */
final class PublicAssetToggleHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$id     = (int) ($request->data['id'] ?? 0);
			$active = !empty($request->data['active']);
			$entry  = (new PublicAssetWriteService())->toggle($id, $active);
			(new PublicAssetBundleCacheService())->invalidate($entry->kind);

			return JsonResponse::toast(__('Обновлено'), ['active' => $entry->active]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
