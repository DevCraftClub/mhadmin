<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Composer\DefaultPackagePolicyStore;

final class ComposerPolicyHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		$store  = new DefaultPackagePolicyStore();
		$action = (string) ($request->data['policyAction'] ?? 'list');

		if($action === 'list') {
			return JsonResponse::ok(['policies' => array_values($store->all())]);
		}

		if($action === 'upsert') {
			$store->upsert([
				'name'              => (string) ($request->data['packageName'] ?? ''),
				'minAllowedVersion' => (string) ($request->data['minAllowedVersion'] ?? ''),
				'downgradeBlocked'  => (bool) ($request->data['downgradeBlocked'] ?? true),
				'removeBlocked'     => (bool) ($request->data['removeBlocked'] ?? true),
				'origin'            => 'manual',
			]);

			return JsonResponse::ok(['policies' => array_values($store->all())], __('Политика сохранена'));
		}

		if($action === 'delete') {
			$store->remove((string) ($request->data['packageName'] ?? ''));

			return JsonResponse::ok(['policies' => array_values($store->all())], __('Политика удалена'));
		}

		return JsonResponse::fail(__('Ошибка'), __('Неизвестная операция политики'), 'validation', 422);
	}

}
