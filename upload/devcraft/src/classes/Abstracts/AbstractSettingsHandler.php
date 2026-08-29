<?php
//===============================================================
// Файл: AbstractSettingsHandler.php                            =
// Путь: devcraft/src/classes/Abstracts/AbstractSettingsHandler.php
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Abstracts;

use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Admin\SettingsFormService;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Types\FormSchema;

/**
 * Базовый AJAX-обработчик частичного сохранения настроек модуля по схеме.
 *
 * Модули переопределяют хуки: {@see configName()}, {@see prepareConfig()}, {@see afterSave()},
 * {@see partialErrorResponse()}.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractSettingsHandler implements AjaxHandlerInterface {

	/**
	 * Валидирует и сохраняет настройки модуля, допуская частичное обновление.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  AJAX-запрос с полями формы настроек.
	 *
	 * @return ResponseInterface JSON-ответ об успехе, предупреждении или ошибке валидации.
	 */
	final public function handle(AjaxRequest $request): ResponseInterface {
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
			$existing = DataManager::getConfig($schema->codename, NULL, $this->configName());
			$prepared = $this->prepareConfig(
				is_array($existing) ? $existing : [],
				$result['valid'],
				$schema,
			);

			if($prepared instanceof JsonResponse) {
				return $prepared;
			}

			if(!DataManager::saveConfig($schema->codename, $prepared)) {
				return JsonResponse::fail(
					__('Ошибка'),
					__('Не удалось сохранить настройки'),
					'save_failed',
					500,
				);
			}

			DevCraftConfig::resetCache();
			$this->afterSave($prepared, $schema);
		}

		if(function_exists('clear_cache')) {
			clear_cache();
		}

		if($result['errors'] !== []) {
			return $this->partialErrorResponse($result['errors']);
		}

		return JsonResponse::toast(__('Сохранено'), ['saved' => true]);
	}

	/**
	 * Третий аргумент {@see DataManager::getConfig()} (legacy confName), если нужен.
	 *
	 * @since 200.4.0
	 */
	protected function configName(): ?string {
		return NULL;
	}

	/**
	 * Готовит итоговый конфиг перед записью (merge, нормализация, пост-валидация).
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $existing  Уже сохранённые настройки.
	 * @param   array<string, mixed>  $valid     Успешно провалидированные поля запроса.
	 * @param   FormSchema            $schema    Схема формы модуля.
	 *
	 * @return array<string, mixed>|JsonResponse Конфиг для записи или ошибка.
	 */
	protected function prepareConfig(array $existing, array $valid, FormSchema $schema): array|JsonResponse {
		return array_merge($existing, $valid);
	}

	/**
	 * Хук после успешной записи конфига (сброс кэшей модуля, синхронизация и т.п.).
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $saved   Записанный конфиг.
	 * @param   FormSchema            $schema  Схема формы модуля.
	 */
	protected function afterSave(array $saved, FormSchema $schema): void {
	}

	/**
	 * Ответ при частичном сохранении с ошибками в отдельных полях.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, string>  $fields  Карта поле → сообщение об ошибке.
	 */
	protected function partialErrorResponse(array $fields): JsonResponse {
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
				'fields'  => $fields,
			],
		);
	}

}
