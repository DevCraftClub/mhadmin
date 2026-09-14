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

/** Удаление ручной записи публичного CSS/JS. */
final class PublicAssetDeleteHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$id   = (int) ($request->data['id'] ?? 0);
			$kind = (string) ($request->data['kind'] ?? 'js');
			(new PublicAssetWriteService())->delete($id);
			(new PublicAssetBundleCacheService())->invalidate($kind);

			return JsonResponse::toast(__('Удалено'));
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
