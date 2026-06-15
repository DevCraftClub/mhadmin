<?php
//===============================================================
// Файл: AjaxHandlerInterface.php                               =
// Путь: devcraft/src/classes/Interfaces/AjaxHandlerInterface.p…=
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

namespace DevCraft\Core\Interfaces;

use DevCraft\Core\Http\AjaxRequest;
use DevCraft\Core\Http\JsonResponse;

/**
 * Контракт обработчика AJAX-запроса модуля DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface AjaxHandlerInterface {

	/**
	 * Обрабатывает входящий AJAX-запрос и формирует JSON-ответ.
	 *
	 * @since 200.4.0
	 *
	 * @param   AjaxRequest  $request  Нормализованный объект входящего запроса.
	 *
	 * @return JsonResponse Ответ для отправки клиенту.
	 *
	 * @example
	 *     $response = $handler->handle(AjaxRequest::fromGlobals());
	 *     $response->send();
	 */
	public function handle(AjaxRequest $request): JsonResponse;

}
