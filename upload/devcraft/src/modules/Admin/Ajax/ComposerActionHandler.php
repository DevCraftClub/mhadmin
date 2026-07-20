<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Composer\PackagePolicyService;
use DevCraft\Core\Composer\ComposerDbSyncService;
use DevCraft\Core\Composer\ComposerRuntimeAdapter;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;

final class ComposerActionHandler implements AjaxHandlerInterface {

	public function handle(AjaxRequest $request): JsonResponse {
		$actionType = (string) ($request->data['actionType'] ?? '');
		$package    = (string) ($request->data['packageName'] ?? '');
		$version    = (string) ($request->data['version'] ?? '');

		if($actionType === '' || $package === '') {
			return JsonResponse::fail(__('Ошибка'), __('Не переданы обязательные параметры'), 'validation', 422);
		}

		$policyService = new PackagePolicyService();
		$policyError   = $policyService->validateAction($actionType, $package, $version !== ''? $version : NULL);
		if($policyError !== NULL) {
			$status = $policyError->toArray();

			return new JsonResponse([
				'success' => false,
				'data'    => [],
				'error'   => [
					'code'    => 'policy_violation',
					'message' => $status['message'],
					'title'   => __('Политика пакетов'),
					'detail'  => $status['details'],
				],
				'notice'  => [
					'channel' => 'notify',
					'title'   => __('Политика пакетов'),
					'message' => $status['message'],
					'type'    => 'error',
				],
			], 409);
		}

		$runtime = new ComposerRuntimeAdapter();
		$result  = match ($actionType) {
			'install' => $runtime->install($package, $version !== ''? $version : NULL),
			'update'  => $runtime->update($package, $version !== ''? $version : NULL),
			'remove'  => $runtime->remove($package),
			default   => NULL,
		};

		if($result === NULL) {
			return JsonResponse::fail(__('Ошибка'), __('Неизвестный тип действия'), 'validation', 422);
		}

		$data = $result->toArray();
		if($data['status'] !== 'ok') {
			return new JsonResponse([
				'success' => false,
				'data'    => [],
				'error'   => [
					'code'    => 'composer_action_failed',
					'title'   => __('Ошибка Composer'),
					'message' => $data['message'],
					'detail'  => $data['details'],
				],
				'notice'  => [
					'channel' => 'notify',
					'title'   => __('Ошибка Composer'),
					'message' => $data['message'],
					'type'    => 'error',
				],
			], 500);
		}

		(new ComposerDbSyncService())->applySuccessfulAction(
			$actionType,
			$package,
			$version !== ''? $version : NULL,
		);

		return JsonResponse::ok($data, __('Операция Composer выполнена'));
	}

}
