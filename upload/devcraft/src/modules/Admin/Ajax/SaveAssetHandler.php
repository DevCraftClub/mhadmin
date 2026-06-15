<?php
//===============================================================
// Файл: SaveAssetHandler.php                                   =
// Путь: devcraft/src/modules/Admin/Ajax/SaveAssetHandler.php   =
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
 * AJAX-обработчик загрузки одного отсутствующего или устаревшего ресурса.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class SaveAssetHandler implements AjaxHandlerInterface {

	/**
	 * Загружает указанный файл ресурса с удалённого источника.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полем `file` — относительный путь ресурса.
	 *
	 * @return JsonResponse JSON-ответ об успешной загрузке или ошибке.
	 *
	 * @example
	 *     $response = (new SaveAssetHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$file = (string) ($request->data['file'] ?? '');

		if($file === '') {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Требуется путь к файлу ресурса'),
				'validation',
				400,
			);
		}

		$checker = Application::instance()->assetsChecker();
		$result  = $checker->downloadMissing([
			'missing'  => [$file],
			'outdated' => [$file],
		]);

		if($result <= 0) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Не удалось загрузить файл ресурса'),
				'download_failed',
				500,
				['detail' => ['file' => $file, 'downloaded' => $result]],
			);
		}

		return JsonResponse::toast(__('Файл загружен'), [
			'downloaded' => $result,
			'file'       => $file,
		]);
	}

}
