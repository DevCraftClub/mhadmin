<?php
//===============================================================
// Файл: AjaxRouteRegistry.php                                  =
// Путь: devcraft/src/classes/Http/AjaxRouteRegistry.php        =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
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
	 * Флаги allow_guest для публичных методов: controller → method → bool.
	 *
	 * @var array<string, array<string, bool>>
	 */
	private array $guestFlags = [];

	/**
	 * Регистрирует обработчик для пары controller/method.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $controller    Имя контроллера.
	 * @param   string  $method        Имя метода.
	 * @param   string  $handlerClass  FQCN класса-обработчика.
	 * @param   bool    $allowGuest    Разрешить гостевой вызов (только public).
	 *
	 * @example
	 *     $registry->register('admin', 'saveSettings', SaveSettingsHandler::class);
	 */
	public function register(string $controller, string $method, string $handlerClass, bool $allowGuest = false): void {
		$this->routes[$controller][$method]     = $handlerClass;
		$this->guestFlags[$controller][$method] = $allowGuest;
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
	 * Проверяет, разрешён ли гостевой вызов для маршрута.
	 *
	 * @param   string  $controller  Имя контроллера.
	 * @param   string  $method      Имя метода.
	 */
	public function allowsGuest(string $controller, string $method): bool {
		return (bool) ($this->guestFlags[$controller][$method] ?? false);
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

		foreach($context->ajaxPublicMethods() as $method => $meta) {
			$this->register('public', $method, $meta['handler'], $meta['allow_guest']);
		}
	}

}
