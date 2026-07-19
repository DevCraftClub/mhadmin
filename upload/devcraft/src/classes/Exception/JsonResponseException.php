<?php
//===============================================================
// Файл: JsonResponseException.php                              =
// Путь: devcraft/src/classes/Exception/JsonResponseException.php =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Exception;

use DevCraft\Core\Http\JsonResponse;

/**
 * Исключение с готовым JSON-ответом DevCraft для единообразной обработки ошибок.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Exception
 */
final class JsonResponseException extends \RuntimeException {

	/**
	 * @since 200.4.0
	 */
	public function __construct(
		private readonly JsonResponse $response,
		string                        $message,
	) {
		parent::__construct($message);
	}

	/**
	 * Возвращает подготовленный JSON-ответ для отправки клиенту.
	 *
	 * @since 200.4.0
	 */
	public function response(): JsonResponse {
		return $this->response;
	}

}
