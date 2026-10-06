<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\PublicHeaderWriteService;
use Throwable;

/** DnD-порядок meta-заголовков. */
final class PublicHeaderReorderHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): ResponseInterface {
		try {
			$ids = $request->data['ids'] ?? [];
			if(!is_array($ids)) {
				$ids = [];
			}

			$result = (new PublicHeaderWriteService())->reorder($ids);
			$data   = [
				'ids'      => $result['ids'],
				'adjusted' => $result['reordered'],
			];

			if($result['reordered']) {
				return JsonResponse::notify(
					__('Порядок сохранён'),
					__('Желаемый порядок скорректирован из‑за зависимостей между записями'),
					JsonResponse::TYPE_WARNING,
					$data,
				);
			}

			return JsonResponse::toast(__('Порядок сохранён'), $data);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'error', 400);
		}
	}

}
