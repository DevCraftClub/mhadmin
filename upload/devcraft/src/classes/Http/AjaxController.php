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
use DevCraft\Core\Admin\AdminErrorRenderer;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Interfaces\ResponseInterface;
use DevCraft\Core\Interfaces\AjaxHandlerInterface;
use DevCraft\Core\Exception\JsonResponseException;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Support\AdminAccess;
use DevCraft\Core\Support\ExceptionDetailFormatter;

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

		$mark = static function (string $name): void {
			$t0                                = $GLOBALS['__dc_ajax_t0'] ?? hrtime(TRUE);
			$GLOBALS['__dc_ajax_marks'][$name] = round((hrtime(TRUE) - $t0) / 1e6, 2);
		};

		Translation::setTranslator();
		$mark('translator');

		$request  = AjaxRequest::fromGlobals();
		$isPublic = $request->controller === 'public';

		$registry    = new AjaxRouteRegistry();
		$adminPlugin = Application::instance()->registry()->forMod('devcraft');

		if ($adminPlugin !== NULL) {
			$registry->loadFromManifest($adminPlugin);
		}

		$plugin = Application::instance()->registry()->forMod($request->mod);

		if ($plugin !== NULL && $plugin !== $adminPlugin) {
			$registry->loadFromManifest($plugin);
		}

		$mark('registry');

		$handlerClass = $registry->resolve($request->controller, $request->method);

		if ($handlerClass === NULL || !class_exists($handlerClass)) {
			LogGenerator::for(AjaxController::class)->log([
				'error'        => __('Неизвестный AJAX-метод: {method} (mod={mod}, controller={controller})', [
					'{method}'     => $request->method,
					'{mod}'        => $request->mod,
					'{controller}' => $request->controller,
				]),
				'handlerClass' => $handlerClass,
				'plugin'       => $plugin,
				'request'      => $request,
			]);
			$this->sendTimed(JsonResponse::fail(
				__('Ошибка'),
				__('Неизвестный AJAX-метод: {method} (mod={mod}, controller={controller})', [
					'{method}'     => $request->method,
					'{mod}'        => $request->mod,
					'{controller}' => $request->controller,
				]),
				'unknown_method',
				404,
			),
				$mark);

			return;
		}

		$allowGuest = $isPublic && $registry->allowsGuest($request->controller, $request->method);

		if (!$allowGuest) {
			$requestHash = (string) ($_REQUEST['user_hash'] ?? '');

			if ($requestHash === '' || !isset($dle_login_hash) || $requestHash !== $dle_login_hash) {
				LogGenerator::for(AjaxController::class)->log([
					'error'          => __('Недопустимый хеш сессии'),
					'requestHash'    => $requestHash,
					'dle_login_hash' => $dle_login_hash,
				]);
				$this->sendTimed(JsonResponse::fail(
					__('Ошибка'),
					__('Недопустимый хеш сессии'),
					'auth_failed',
					403,
				),
					$mark);

				return;
			}
		}

		$mark('auth_ok');

		if (!$isPublic && empty($is_loged_in)) {
			$this->sendTimed(JsonResponse::fail(
				__('Ошибка'),
				__('Требуется аутентификация'),
				'auth_failed',
				403,
			),
				$mark);

			return;
		}

		if (!$isPublic && !AdminAccess::allowsAjaxMod($request->mod)) {
			$this->sendTimed(JsonResponse::fail(
				__('Ошибка'),
				__('Недостаточно прав'),
				'forbidden',
				403,
			),
				$mark);

			return;
		}

		$mark('access_ok');

		if ($isPublic && !$allowGuest && empty($is_logged)) {
			$this->sendTimed(JsonResponse::fail(
				__('Ошибка'),
				__('Требуется авторизация на сайте'),
				'auth_failed',
				403,
			),
				$mark);

			return;
		}

		$handler = new $handlerClass();

		try {
			if ($handler instanceof AjaxHandlerInterface) {
				$response = $handler->handle($request);
				$mark('handler');

				if ($response instanceof ResponseInterface) {
					$this->sendTimed($response, $mark);

					return;
				}
			}

			if (method_exists($handler, 'handle')) {
				$handler->handle();
				$mark('handler');

				return;
			}
		} catch (JsonResponseException $e) {
			$mark('handler_fail');
			LogGenerator::for(AjaxController::class)->log([
				'error'          => $e->getMessage(),
				'requestHash'    => $requestHash,
				'dle_login_hash' => $dle_login_hash,
				'handlerClass'   => $handlerClass,
				'plugin'         => $plugin,
				'request'        => $request,
			]);
			$this->sendTimed($e->response(), $mark);

			return;
		} catch (\Throwable $e) {
			$mark('handler_fail');
			LogGenerator::for(AjaxController::class)->log([
				'error'          => $e->getMessage(),
				'requestHash'    => $requestHash,
				'dle_login_hash' => $dle_login_hash,
				'handlerClass'   => $handlerClass,
				'plugin'         => $plugin,
				'request'        => $request,
			]);
			$this->sendInternalError($e);

			return;
		}

		LogGenerator::for(AjaxController::class)->log([
			'error'          => __('Обработчик недоступен для вызова: {method} (mod={mod}, controller={controller})', [
				'{method}'     => $request->method,
				'{mod}'        => $request->mod,
				'{controller}' => $request->controller,
			]),
			'requestHash'    => $requestHash,
			'dle_login_hash' => $dle_login_hash,
			'handlerClass'   => $handlerClass,
			'plugin'         => $plugin,
			'request'        => $request,
		]);

		$this->sendTimed(JsonResponse::fail(
			__('Ошибка'),
			__('Обработчик недоступен для вызова: {method} (mod={mod}, controller={controller})', [
				'{method}'     => $request->method,
				'{mod}'        => $request->mod,
				'{controller}' => $request->controller,
			]),
			'unknown_method',
			500,
		),
			$mark);
	}

	/**
	 * При debug — добавляет pipeline_ms в JSON и отправляет ответ.
	 *
	 * @param   callable(string): void  $mark
	 */
	private function sendTimed(ResponseInterface $response, callable $mark): void {
		$mark('before_send');

		if ($response instanceof JsonResponse && LogGenerator::isDebugEnabled()) {
			$marks    = is_array($GLOBALS['__dc_ajax_marks'] ?? NULL)
				? $GLOBALS['__dc_ajax_marks']
				: [];
			$response = $response->withData([
				'pipeline_ms' => $marks,
			]);
		}

		$response->send();
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
		$isSchema   = str_contains($e->getMessage(), 'Undefined schema');
		$extra      = [];

		// Схема Cycle — всегда с подсказкой: иначе на боевом сайте не видно, почему нет модели.
		if ($showDetail || $isSchema) {
			$extra['detail'] = (new ExceptionDetailFormatter(
				Application::instance()->registry(),
			))->formatBrief($e);
		}

		if ($isSchema) {
			$extra['data'] = ['migrations_url' => AdminErrorRenderer::migrationsUrl()];
		}

		LogGenerator::for('DevCraft')->log(
			$e::class . ': ' . $e->getMessage(),
			'error',
		);

		JsonResponse::fail(
			__('Ошибка'),
			__('Произошла ошибка при выполнении запроса'),
			'internal_error',
			500,
			$extra,
		)->send();
	}

}
