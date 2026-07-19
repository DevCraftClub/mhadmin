<?php
//===============================================================
// Файл: SyncAssetsHandler.php                                  =
// Путь: devcraft/src/modules/Admin/Ajax/SyncAssetsHandler.php  =
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
 * AJAX-обработчик массовой синхронизации ресурсов DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class SyncAssetsHandler implements AjaxHandlerInterface {

	/**
	 * Загружает отсутствующие или все ресурсы в зависимости от режима `mode`.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полем `mode` (`changed` или `all`).
	 *
	 * @return JsonResponse JSON-ответ о количестве загруженных файлов или ошибке.
	 *
	 * @example
	 *     $response = (new SyncAssetsHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$mode    = (string) ($request->data['mode'] ?? 'changed');
		$checker = Application::instance()->assetsChecker();

		if($mode === 'all') {
			$downloaded = $checker->downloadAll();
		} else {
			$report     = $checker->compareReport();
			$downloaded = $checker->downloadMissing([
				'missing'  => $report['missing'],
				'outdated' => $report['outdated'],
			]);
		}

		if($downloaded <= 0) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Не удалось синхронизировать ресурсы'),
				'download_failed',
				500,
				['downloaded' => $downloaded],
			);
		}

		return JsonResponse::toast(__('Синхронизация завершена'), [
			'downloaded' => $downloaded,
			'report'     => $checker->compareReport(),
		]);
	}

}
