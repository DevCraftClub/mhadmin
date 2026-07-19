<?php
//===============================================================
// Файл: NewModuleHandler.php                                   =
// Путь: devcraft/src/modules/Admin/Ajax/NewModuleHandler.php   =
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

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Modules\Admin\Services\ModuleGeneratorInput;
use DevCraft\Modules\Admin\Services\ModuleGeneratorService;

/**
 * AJAX-обработчик генерации каркаса нового DevCraft-модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class NewModuleHandler implements AjaxHandlerInterface {

	/**
	 * Создаёт модуль по данным формы и возвращает отчёт о результате.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полями формы генератора.
	 *
	 * @return JsonResponse JSON-ответ с отчётом, ошибками валидации или успехом.
	 *
	 * @example
	 *     $response = (new NewModuleHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$input  = ModuleGeneratorInput::fromArray($request->data);
		$report = (new ModuleGeneratorService())->generate($input);

		if(!empty($report['invalid_fields'])) {
			$errorMessage = match ((string) ($report['error'] ?? '')) {
				'Нужные данные не были заполнены' => __('Нужные данные не были заполнены'),
				default                           => (string) ($report['error'] ?? __('Нужные данные не были заполнены')),
			};

			return JsonResponse::fail(
				__('Ошибка'),
				$errorMessage,
				'validation',
				400,
				['fields' => $report['invalid_fields']],
			);
		}

		if(!$report['success']) {
			$message = match (true) {
				$report['files']['fails'] !== [] || $report['plugin']['fails'] !== [] => __('Создание завершено с ошибками'),
				default                                                               => match ((string) ($report['error'] ?? '')) {
					'Не удалось создать модуль' => __('Не удалось создать модуль'),
					default                     => (string) ($report['error'] ?? __('Не удалось создать модуль')),
				},
			};

			return JsonResponse::notify(
				__('Ошибка'),
				$message,
				JsonResponse::TYPE_ERROR,
				['report' => $report],
				409,
				false,
				[
					'code'    => 'scaffold_failed',
					'message' => $message,
					'title'   => __('Ошибка'),
					'detail'  => $report,
				],
			);
		}

		return JsonResponse::ok(
			['report' => $report],
			__('Модуль «{name}» создан', ['{name}' => $input->name]),
		);
	}

}
