<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Composer\ComposerDbSyncService;

final class ComposerSyncHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		(new ComposerDbSyncService())->syncFromRuntimeSnapshot();

		return JsonResponse::ok(['synced' => true], __('Синхронизация с composer.lock завершена'));
	}
}
