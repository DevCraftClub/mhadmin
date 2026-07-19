<?php
//===============================================================
// Файл: SettingsHandler.php                                    =
// Путь: devcraft/src/modules/Admin/Ajax/SettingsHandler.php    =
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
use DevCraft\Core\Config\Paths;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Admin\SettingsFormService;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;

/**
 * AJAX-обработчик частичного сохранения настроек модуля по схеме.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class SettingsHandler implements AjaxHandlerInterface {

	/**
	 * Валидирует и сохраняет настройки модуля, допуская частичное обновление.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полями формы настроек.
	 *
	 * @return JsonResponse JSON-ответ об успехе, предупреждении или ошибке валидации.
	 *
	 * @example
	 *     $response = (new SettingsHandler())->handle($request);
	 */
	public function handle(AjaxRequest $request): JsonResponse {
		$plugin = Application::instance()->registry()->forMod($request->mod);
		$schema = $plugin?->settingsSchema();

		if($schema === NULL) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Схема настроек недоступна'),
				'validation',
			);
		}

		$configDir  = Paths::config();
		$configFile = $configDir . '/' . $schema->codename . '.json';

		if(is_file($configFile) && !is_writable($configFile)) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Файл конфигурации недоступен для записи'),
				'validation',
				500,
			);
		}

		if(!is_dir($configDir) && !DataManager::createDir($configDir)) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Каталог конфигурации недоступен для записи'),
				'validation',
				500,
			);
		}

		$service = new SettingsFormService();
		$result  = $service->validatePartial($request->data, $schema);

		if($result['valid'] === [] && $result['errors'] !== []) {
			return JsonResponse::fail(
				__('Ошибка'),
				__('Все поля недействительны'),
				'validation',
				422,
				['fields' => $result['errors']],
			);
		}

		if($result['valid'] !== []) {
			$existing = DataManager::getConfig($schema->codename);
			$merged   = array_merge($existing, $result['valid']);
			unset($merged['debug_filter_daterange']);
			DataManager::saveConfig($schema->codename, $merged);
			DevCraftConfig::resetCache();
			Translation::reset();
		}

		if($result['errors'] !== []) {
			$partialMessage = __('Частичное сохранение завершено с ошибками в полях');

			return JsonResponse::notify(
				__('Внимание'),
				$partialMessage,
				JsonResponse::TYPE_WARNING,
				[],
				422,
				false,
				[
					'code'    => 'validation',
					'message' => $partialMessage,
					'title'   => __('Внимание'),
					'fields'  => $result['errors'],
				],
			);
		}

		if(function_exists('clear_cache')) {
			clear_cache();
		}

		return JsonResponse::toast(__('Сохранено'), ['saved' => true]);
	}

}
