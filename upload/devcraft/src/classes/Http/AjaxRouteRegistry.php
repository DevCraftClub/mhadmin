<?php
//===============================================================
// Файл: AjaxRouteRegistry.php                                  =
// Путь: devcraft/src/classes/Http/AjaxRouteRegistry.php        =
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

namespace DevCraft\Core\Http;

use DevCraft\Core\Module\PluginContext;

/**
 * Реестр AJAX-маршрутов controller/method → класс-обработчик.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Http
 */
final class AjaxRouteRegistry {

	/**
	 * Карта маршрутов: controller → method → FQCN обработчика.
	 *
	 * @since 200.4.0
	 *
	 * @var array<string, array<string, class-string>>
	 */
	private array $routes = [];

	/**
	 * Регистрирует обработчик для пары controller/method.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $controller    Имя контроллера.
	 * @param   string  $method        Имя метода.
	 * @param   string  $handlerClass  FQCN класса-обработчика.
	 *
	 * @example
	 *     $registry->register('admin', 'saveSettings', SaveSettingsHandler::class);
	 */
	public function register(string $controller, string $method, string $handlerClass): void {
		$this->routes[$controller][$method] = $handlerClass;
	}

	/**
	 * Возвращает FQCN обработчика для пары controller/method.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $controller  Имя контроллера.
	 * @param   string  $method      Имя метода.
	 *
	 * @return class-string|null FQCN обработчика или null, если маршрут не найден.
	 *
	 * @example
	 *     $class = $registry->resolve('admin', 'saveSettings');
	 */
	public function resolve(string $controller, string $method): ?string {
		return $this->routes[$controller][$method] ?? NULL;
	}

	/**
	 * Загружает маршруты из manifest плагина.
	 *
	 * @since 200.4.0
	 *
	 * @param   PluginContext  $context  Контекст плагина с AJAX-методами.
	 *
	 * @example
	 *     $registry->loadFromManifest($pluginContext);
	 */
	public function loadFromManifest(PluginContext $context): void {
		$controller = $context->ajaxController();

		foreach($context->ajaxMethods() as $method => $handler) {
			$this->register($controller, $method, $handler);
		}
	}

}
