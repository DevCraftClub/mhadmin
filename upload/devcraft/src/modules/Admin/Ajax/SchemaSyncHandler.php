<?php
//===============================================================
// Файл: SchemaSyncHandler.php                                  =
// Путь: devcraft/src/modules/Admin/Ajax/SchemaSyncHandler.php  =
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
use DevCraft\Core\Database\SchemaSyncService;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use Throwable;

/**
 * Ручной запуск синхронизации таблиц Cycle с моделями.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class SchemaSyncHandler implements AjaxHandlerInterface {

	/**
	 * Сбрасывает запас копии схемы и создаёт недостающие таблицы.
	 *
	 * @since 200.4.2
	 *
	 * @param   AjaxRequest  $request  Запрос без обязательных полей (хеш проверяет контроллер).
	 */
	public function handle(AjaxRequest $request): ResponseInterface {
		unset($request);

		try {
			$app     = Application::instance();
			$service = new SchemaSyncService($app->registry(), $app->database());
			$report  = $service->forceEnsure();
		} catch(Throwable $e) {
			return JsonResponse::fail(
				__('Ошибка'),
				$e->getMessage() !== '' ? $e->getMessage() : __('Не удалось обновить таблицы'),
				'schema_sync',
				500,
			);
		}

		if($report['missing'] !== []) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Не удалось создать таблицы: {list}', [
					'{list}' => implode(', ', $report['missing']),
				]),
				'schema_sync',
				500,
			);
		}

		return JsonResponse::ok(
			['tables' => $report['present']],
			__('Таблицы базы в актуальном состоянии'),
		);
	}

}
