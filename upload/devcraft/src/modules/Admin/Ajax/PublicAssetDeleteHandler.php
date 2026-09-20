<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\PublicAssetWriteService;
use Throwable;

/** Удаление ручной записи публичного CSS/JS. */
final class PublicAssetDeleteHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$id   = (int) ($request->data['id'] ?? 0);
			$kind = (string) ($request->data['kind'] ?? 'js');
			$mode = isset($request->data['mode']) ? (string) $request->data['mode'] : null;
			if($mode === '') {
				$mode = null;
			}

			$result = (new PublicAssetWriteService())->delete($id, $mode);

			if(!$result['deleted']) {
				return JsonResponse::ok(array_merge($result['probe'] ?? [], [
					'needs_confirm' => true,
				]));
			}

			return JsonResponse::toast(__('Удалено'), ['kind' => $kind]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
