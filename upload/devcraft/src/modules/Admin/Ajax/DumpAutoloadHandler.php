<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Composer\ComposerRuntimeAdapter;

final class DumpAutoloadHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		$result = (new ComposerRuntimeAdapter())->dumpAutoload()->toArray();
		if($result['status'] !== 'ok') {
			return JsonResponse::fail(__('Ошибка Composer'), $result['message'], 'composer_dump_failed', 500, [
				'detail' => $result['details'],
			]);
		}

		return JsonResponse::ok($result, __('Autoload успешно обновлён'));
	}

}
