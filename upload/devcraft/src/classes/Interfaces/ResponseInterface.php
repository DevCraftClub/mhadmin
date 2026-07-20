<?php
//===============================================================
// Файл: ResponseInterface.php                                  =
// Путь: devcraft/src/classes/Interfaces/ResponseInterface.php  =
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

/**
 * Контракт HTTP-ответа, отправляемого клиенту.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface ResponseInterface {

	/**
	 * Отправляет сформированный ответ клиенту.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     $response = new JsonResponse(['ok' => true]);
	 *     $response->send();
	 */
	public function send(): void;

}
