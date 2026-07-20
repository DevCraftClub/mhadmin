<?php
//===============================================================
// Файл: CheckAssetsHandler.php                                 =
// Путь: devcraft/src/modules/Admin/Ajax/CheckAssetsHandler.php =
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
 * AJAX-обработчик сравнения локальных и удалённых ресурсов DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class CheckAssetsHandler implements AjaxHandlerInterface {

	/**
	 * Возвращает отчёт о расхождениях файлов ресурсов.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  Входящий AJAX-запрос (данные не используются).
	 *
	 * @return JsonResponse JSON-ответ с отчётом сравнения ресурсов.
	 *
	 * @example
	 *     $response = (new CheckAssetsHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$checker = Application::instance()->assetsChecker();

		return JsonResponse::ok($checker->compareReport());
	}

}
