<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Services\PublicAssetWriteService;
use Throwable;

/** Сохранение записи публичного CSS/JS. */
final class PublicAssetSaveHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$isEdit = !empty($request->data['edit'])
				|| array_key_exists('depends_on', $request->data)
				|| array_key_exists('available', $request->data)
				|| array_key_exists('not_available', $request->data);

			$service = new PublicAssetWriteService();
			$entry   = $isEdit
				? $service->saveEdit($request->data)
				: $service->save($request->data);

			$kind       = $entry->kind;
			$listAction = $kind === 'css' ? 'public_styles' : 'public_scripts';
			$redirect   = '?mod=' . AdminIdentity::mod() . '&action=' . $listAction;

			return JsonResponse::toast(__('Сохранено'), [
				'id'       => $entry->id(),
				'redirect' => $isEdit ? $redirect : null,
			]);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
