<?php
//===============================================================
// Файл: CheckUpdateHandler.php                                 =
// Путь: devcraft/src/modules/Admin/Ajax/CheckUpdateHandler.php =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Core\Application;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;

/**
 * AJAX-обработчик проверки обновлений плагина на devcraft.club.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class CheckUpdateHandler implements AjaxHandlerInterface {

	/**
	 * URL API ресурсов devcraft.club.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private const API_URL = 'https://devcraft.club/api/resources/';

	/**
	 * Гостевой ключ API devcraft.club.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private const API_KEY = '8uO1gW7Ge47co5Y0tTOEzZ1V0lclAvXy';

	/**
	 * Запрашивает удалённую версию ресурса и сравнивает с локальной.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с `resource_id` и `local_version`.
	 *
	 * @return JsonResponse JSON-ответ с данными обновления или ошибкой.
	 *
	 * @example
	 *     $response = (new CheckUpdateHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$plugin     = Application::instance()->registry()->forMod('devcraft');
		$meta       = $plugin?->meta() ?? [];
		$resourceId = (int) ($request->data['resource_id'] ?? 0);

		if($resourceId <= 0) {
			$resourceId = (int) ($meta['siteId'] ?? 0);
		}

		if($resourceId <= 0) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Требуется идентификатор ресурса'),
				'validation'
			);
		}

		$localVersion = (string) ($request->data['local_version'] ?? '');

		if($localVersion === '') {
			$localVersion = (string) ($meta['version'] ?? '');
		}

		$context = stream_context_create([
			'http' => [
				'header' => 'XF-Api-Key: ' . self::API_KEY . "\r\n",
			],
		]);

		$response = @file_get_contents(self::API_URL . $resourceId . '/', false, $context);

		if($response === false || $response === '') {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Проверка обновления не удалась'),
				'remote_error',
				502,
			);
		}

		/** @var array<string, mixed> $decoded */
		$decoded = json_decode($response, true);

		if(!is_array($decoded) || isset($decoded['errors'])) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Некорректный ответ API'),
				'remote_error',
				502,
				['detail' => $decoded ?? $response],
			);
		}

		$resource        = $decoded['resource'] ?? [];
		$remoteVersion   = (string) ($resource['version'] ?? '');
		$updateAvailable = $localVersion !== ''
		                   && $remoteVersion !== ''
		                   && version_compare($localVersion, $remoteVersion, '<');

		$data = [
			'download_link'    => ($resource['view_url'] ?? '') . 'download/',
			'download_count'   => $resource['download_count'] ?? 0,
			'last_update'      => $resource['last_update'] ?? NULL,
			'title'            => $resource['title'] ?? '',
			'update_count'     => $resource['update_count'] ?? 0,
			'site_link'        => $resource['view_url'] ?? '',
			'version'          => $remoteVersion,
			'remote_version'   => $remoteVersion,
			'local_version'    => $localVersion,
			'update_available' => $updateAvailable,
		];

		if(!$updateAvailable) {
			return JsonResponse::toast(__('Обновлений нет'), $data);
		}

		return JsonResponse::notify(
			__('Обновление'),
			__('Доступна версия {version}', ['{version}' => $remoteVersion]),
			JsonResponse::TYPE_WARNING,
			$data,
		);
	}

}
