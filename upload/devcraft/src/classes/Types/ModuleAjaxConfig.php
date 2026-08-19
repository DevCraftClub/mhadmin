<?php
//===============================================================
// Файл: ModuleAjaxConfig.php                                   =
// Путь: devcraft/src/classes/Types/ModuleAjaxConfig.php        =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Конфигурация AJAX-эндпоинтов модуля из секции `ajax` manifest.php.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                      $controller Идентификатор AJAX-контроллера.
 * @property array<string, class-string> $methods    Карта method → FQCN обработчика.
 */
final class ModuleAjaxConfig extends AbstractType {

	/**
	 * Создаёт конфигурацию AJAX-эндпоинтов модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                       $controller  Идентификатор AJAX-контроллера.
	 * @param   array<string, class-string>  $methods     Карта method → FQCN обработчика.
	 *
	 * @example
	 *     $ajax = new ModuleAjaxConfig('admin', ['settings' => SettingsHandler::class]);
	 */
	public function __construct(
		public string $controller = 'admin',
		public array  $methods = [],
	) {}

	/**
	 * Создаёт конфигурацию AJAX из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Секция `ajax` из manifest.php.
	 *
	 * @return static Новый экземпляр конфигурации.
	 *
	 * @example
	 *     $ajax = ModuleAjaxConfig::fromArray($manifest['ajax']);
	 */
	public static function fromArray(array $data): static {
		$methods = [];

		if(isset($data['methods']) && is_array($data['methods'])) {
			foreach($data['methods'] as $method => $handler) {
				if(is_string($method) && is_string($handler) && $handler !== '') {
					$methods[$method] = $handler;
				}
			}
		}

		return new self(
			controller: (string) ($data['controller'] ?? 'admin'),
			methods   : $methods,
		);
	}

}
