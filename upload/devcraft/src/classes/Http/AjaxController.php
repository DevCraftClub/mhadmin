<?php
//===============================================================
// Файл: AjaxController.php                                     =
// Путь: devcraft/src/classes/Http/AjaxController.php           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Http;

use DevCraft\Core\Application;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Exception\JsonResponseException;
use DevCraft\Core\Support\AdminAccess;

/**
 * Диспетчер AJAX-запросов DevCraft: аутентификация, маршрутизация, ответ.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Http
 */
final class AjaxController {

	/**
	 * Обрабатывает текущий AJAX-запрос и отправляет ответ (JSON или файл).
	 *
	 * @since 200.4.0
	 *
	 * @global string $dle_login_hash Хеш сессии DLE для CSRF-проверки.
	 * @global bool   $is_loged_in    Флаг авторизации администратора DLE.
	 * @global bool   $is_logged      Флаг авторизации участника сайта.
	 *
	 * @example
	 *     (new AjaxController())->run();
	 */
	public function run(): void {
		global $dle_login_hash, $is_loged_in, $is_logged;

		Translation::setTranslator();

		$request  = AjaxRequest::fromGlobals();
		$isPublic = $request->controller === 'public';

		$requestHash = (string) ($_REQUEST['user_hash'] ?? '');

		if($requestHash === '' || !isset($dle_login_hash) || $requestHash !== $dle_login_hash) {
			JsonResponse::fail(
				__('Ошибка'),
				__('Недопустимый хеш сессии'),
				'auth_failed',
				403,
			)->send();

			return;
		}

		if(!$isPublic && empty($is_loged_in)) {
			JsonResponse::fail(
				__('Ошибка'),
				__('Требуется аутентификация'),
				'auth_failed',
				403,
			)->send();

			return;
		}

		if(!$isPublic && !AdminAccess::allowsAjaxMod($request->mod)) {
			JsonResponse::fail(
				__('Ошибка'),
				__('Недостаточно прав'),
				'forbidden',
				403,
			)->send();

			return;
		}

		$registry    = new AjaxRouteRegistry();
		$adminPlugin = Application::instance()->registry()->forMod('devcraft');

		if($adminPlugin !== NULL) {
			$registry->loadFromManifest($adminPlugin);
		}

		$plugin = Application::instance()->registry()->forMod($request->mod);

		if($plugin !== NULL && $plugin !== $adminPlugin) {
			$registry->loadFromManifest($plugin);
		}

		$handlerClass = $registry->resolve($request->controller, $request->method);

		if($handlerClass === NULL || !class_exists($handlerClass)) {
			JsonResponse::fail(
				__('Ошибка'),
				__('Неизвестный AJAX-метод: {method} (mod={mod}, controller={controller})', [
					'{method}'     => $request->method,
					'{mod}'        => $request->mod,
					'{controller}' => $request->controller,
				]),
				'unknown_method',
				404,
			)->send();

			return;
		}

		if($isPublic && !$registry->allowsGuest($request->controller, $request->method) && empty($is_logged)) {
			JsonResponse::fail(
				__('Ошибка'),
				__('Требуется авторизация на сайте'),
				'auth_failed',
				403,
			)->send();

			return;
		}

		$handler = new $handlerClass();

		try {
			if($handler instanceof AjaxHandlerInterface) {
				$response = $handler->handle($request);

				if($response instanceof ResponseInterface) {
					$response->send();

					return;
				}
			}

			if(method_exists($handler, 'handle')) {
				$handler->handle();

				return;
			}
		} catch(JsonResponseException $e) {
			$e->response()->send();

			return;
		} catch(\Throwable $e) {
			$this->sendInternalError($e);

			return;
		}

		JsonResponse::fail(
			__('Ошибка'),
			__('Обработчик недоступен для вызова: {method} (mod={mod}, controller={controller})', [
				'{method}'     => $request->method,
				'{mod}'        => $request->mod,
				'{controller}' => $request->controller,
			]),
			'unknown_method',
			500,
		)->send();
	}

	/**
	 * Отправляет JSON-ответ о внутренней ошибке с опциональной детализацией.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed> $config Глобальные настройки DLE.
	 *
	 * @param   \Throwable          $e      Исключение, возникшее в обработчике.
	 *
	 */
	private function sendInternalError(\Throwable $e): void {
		global $config;

		$showDetail = !empty($config['display_php_errors']);
		$extra      = [];

		if($showDetail) {
			$extra['detail'] = $e->getMessage();
		}

		JsonResponse::fail(
			__('Ошибка'),
			__('Произошла ошибка при выполнении запроса'),
			'internal_error',
			500,
			$extra,
		)->send();
	}

}
