<?php
//===============================================================
// Файл: MigrationsActionHandler.php                            =
// Путь: devcraft/src/modules/Admin/Ajax/MigrationsActionHandle…=
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
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Modules\Admin\Services\MigrationWorkbenchService;
use DevCraft\Core\Logging\LogGenerator;
use Throwable;

/**
 * Действия над миграциями: `generate`, `migrate`, `rollback`, `fake`, `prune`.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class MigrationsActionHandler implements AjaxHandlerInterface {

	/**
	 * Выполняет действие из поля `op`.
	 *
	 * @since 200.4.2
	 *
	 * @param   AjaxRequest  $request  Запрос с полями `op` и (для `fake`) `id`.
	 */
	public function handle(AjaxRequest $request): ResponseInterface {
		$op      = (string) ($request->data['op'] ?? '');
		$id      = (string) ($request->data['id'] ?? '');
		$service = MigrationWorkbenchService::create();

		try {
			$message = match ($op) {
				'generate' => __('Создано файлов миграций: {count}', ['{count}' => $service->generate()]),
				'migrate'  => __('Накачено миграций: {count}', ['{count}' => $service->migrate()]),
				'rollback' => ($name = $service->rollback()) === null
					? __('Нет выполненных миграций для отката')
					: __('Откачена миграция {name}', ['{name}' => $name]),
				'fake'     => $this->fake($service, $id),
				'prune'    => __('Удалено записей учёта: {count}', ['{count}' => $service->prune()]),
				default    => null,
			};
		} catch(Throwable $e) {
			LogGenerator::for('Admin')->log($op . ': ' . $e->getMessage(), 'error');

			return JsonResponse::fail(__('Ошибка'), $e->getMessage(), 'migrations', 500);
		}

		if($message === null) {
			return JsonResponse::fail(__('Ошибка'), __('Неизвестное действие'), 'validation');
		}

		LogGenerator::for('Admin')->log($message . ($id !== ''? ' (' . $id . ')' : ''), 'info');

		return JsonResponse::ok(['reload' => true], $message);
	}

	private function fake(MigrationWorkbenchService $service, string $id): string {
		$service->fake($id);

		return __('Миграция помечена выполненной');
	}

}
