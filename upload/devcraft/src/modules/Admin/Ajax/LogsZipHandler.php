<?php
//===============================================================
// Файл: LogsZipHandler.php                                     =
// Путь: devcraft/src/modules/Admin/Ajax/LogsZipHandler.php     =
// Последнее изменение: 2026-10-28 12:00:00                     =
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
use DevCraft\Core\Http\FileResponse;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Models\LogRecord;
use DevCraft\Modules\Admin\Repositories\LogRecordRepository;
use DevCraft\Modules\Admin\Services\LogsZipExportService;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Modules\Admin\AdminIdentity;
use Throwable;

/**
 * Скачивание отмеченных записей журнала одним ZIP.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class LogsZipHandler implements AjaxHandlerInterface {

	private const MAX_IDS = 10000;

	/**
	 * @since 200.4.2
	 *
	 * @param   AjaxRequest  $request  Запрос с массивом `ids`.
	 */
	public function handle(AjaxRequest $request): ResponseInterface {
		$raw = $request->data['ids'] ?? [];
		$ids = is_array($raw)? array_values(array_unique(array_filter(array_map('intval', $raw), static fn(int $id): bool => $id > 0))) : [];

		if($ids === []) {
			return JsonResponse::fail(__('Ошибка'), __('Отметьте хотя бы одну строку'), 'validation');
		}

		if(count($ids) > self::MAX_IDS) {
			return JsonResponse::fail(__('Ошибка'), __('Слишком много строк: не больше {max}', ['{max}' => self::MAX_IDS]), 'validation');
		}

		try {
			/** @var LogRecordRepository $repository */
			$repository = Application::instance()->database()->repository(LogRecord::class);
			$path       = (new LogsZipExportService($repository))->export($ids);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'logs_zip', 500);
		}

		LogGenerator::for(AdminIdentity::name())->log(
			__('Скачан ZIP журнала: {count} записей', ['{count}' => count($ids)]),
			'info',
		);

		return new FileResponse($path, 'devcraft_logs_' . date('Ymd_His') . '.zip', 'application/zip');
	}

}
