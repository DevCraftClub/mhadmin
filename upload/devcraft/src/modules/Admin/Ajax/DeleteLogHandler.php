<?php
//===============================================================
// Файл: DeleteLogHandler.php                                   =
// Путь: devcraft/src/modules/Admin/Ajax/DeleteLogHandler.php   =
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
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;

/**
 * AJAX-обработчик удаления записи журнала по UUID.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class DeleteLogHandler implements AjaxHandlerInterface {

	/**
	 * Удаляет запись журнала по идентификатору UUID.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полем `id` или `uuid`.
	 *
	 * @return JsonResponse JSON-ответ об успехе или ошибке удаления.
	 *
	 * @example
	 *     $response = (new DeleteLogHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$uuid = (string) ($request->data['id'] ?? $request->data['uuid'] ?? '');

		if($uuid === '') {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Требуется идентификатор записи журнала'),
				'validation',
			);
		}

		/** @var LogRecordRepository $repository */
		$repository = Application::instance()->database()->repository(
			LogRecord::class,
		);

		$deleted = $repository->deleteByUuid($uuid);

		if(!$deleted) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Не удалось удалить запись журнала'),
				'not_found',
				404,
			);
		}

		return JsonResponse::toast(__('Запись удалена'), ['deleted' => true]);
	}

}
