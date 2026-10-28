<?php
//===============================================================
// Файл: MigrationSqlHandler.php                                =
// Путь: devcraft/src/modules/Admin/Ajax/MigrationSqlHandler.php=
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

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\FileResponse;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\MigrationWorkbenchService;
use DevCraft\Modules\Admin\Services\TempDownload;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Modules\Admin\AdminIdentity;
use Throwable;

/**
 * Скачивание SQL миграции: `up`, `down` или `both`.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class MigrationSqlHandler implements AjaxHandlerInterface {

	/**
	 * @since 200.4.2
	 *
	 * @param   AjaxRequest  $request  Запрос с полями `id` и `direction`.
	 */
	public function handle(AjaxRequest $request): ResponseInterface {
		$id        = (string) ($request->data['id'] ?? '');
		$direction = (string) ($request->data['direction'] ?? 'up');

		if($id === '' || !in_array($direction, ['up', 'down', 'both'], true)) {
			return JsonResponse::fail(__('Ошибка'), __('Не указана миграция или направление'), 'validation');
		}

		try {
			$export = MigrationWorkbenchService::create()->sql($id, $direction);
		} catch(Throwable $e) {
			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'migrations', 500);
		}

		LogGenerator::for(AdminIdentity::name())->log(
			__('Скачан SQL миграции {id} ({direction})', [
				'{id}'        => $id,
				'{direction}' => $direction,
			]),
			'info',
		);

		return new FileResponse(TempDownload::write($export['sql'], 'sql'), $export['name'], 'application/sql; charset=utf-8');
	}

}
