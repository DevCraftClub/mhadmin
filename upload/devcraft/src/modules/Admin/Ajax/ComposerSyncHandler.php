<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Composer\ComposerDbSyncService;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Modules\Admin\AdminIdentity;

final class ComposerSyncHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		(new ComposerDbSyncService())->syncFromRuntimeSnapshot();
		LogGenerator::for(AdminIdentity::name())->log(__('Синхронизация с composer.lock завершена'), 'info');

		return JsonResponse::ok(['synced' => true], __('Синхронизация с composer.lock завершена'));
	}

}
