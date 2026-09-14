<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\PublicAssetWriteService;
use Throwable;

/** DnD-порядок публичных CSS/JS. */
final class PublicAssetReorderHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$kind = (string) ($request->data['kind'] ?? '');
			$ids  = $request->data['ids'] ?? [];
			if(!is_array($ids)) {
				$ids = [];
			}

			(new PublicAssetWriteService())->reorder($kind, $ids);

			return JsonResponse::toast(__('Порядок сохранён'));
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
