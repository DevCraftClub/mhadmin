<?php
//===============================================================
// Файл: JsonResponse.php                                       =
// Путь: devcraft/src/classes/Http/JsonResponse.php             =
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

use DevCraft\Core\Interfaces\ResponseInterface;

/**
 * JSON-ответ AJAX с единым контрактом success/data/notice/error.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Http
 */
final class JsonResponse implements ResponseInterface {

	/**
	 * Канал уведомления: всплывающий toast.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const CHANNEL_TOAST = 'toast';

	/**
	 * Канал уведомления: блок notify в интерфейсе.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const CHANNEL_NOTIFY = 'notify';

	/**
	 * Тип уведомления: успех.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const TYPE_SUCCESS = 'success';

	/**
	 * Тип уведомления: предупреждение.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const TYPE_WARNING = 'warning';

	/**
	 * Тип уведомления: ошибка.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const TYPE_ERROR = 'error';

	/**
	 * Тип уведомления: информация.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	public const TYPE_INFO = 'info';

	/**
	 * Создаёт JSON-ответ с заданным телом и HTTP-кодом.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $payload      Тело ответа.
	 * @param   int                   $status_code  HTTP-статус.
	 *
	 * @example
	 *     $response = new JsonResponse(['success' => true, 'data' => []]);
	 */
	public function __construct(
		private readonly array $payload,
		private readonly int   $status_code = 200,
	) {}

	/**
	 * Формирует успешный ответ с toast-уведомлением.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $message  Текст toast.
	 * @param   array<string, mixed>  $data     Полезная нагрузка ответа.
	 * @param   int                   $status   HTTP-статус.
	 *
	 * @return self Экземпляр JSON-ответа.
	 *
	 * @example
	 *     JsonResponse::toast(__('Сохранено'), ['id' => 1])->send();
	 */
	public static function toast(string $message, array $data = [], int $status = 200): self {
		return self::build([
			'success' => true,
			'data'    => $data,
			'notice'  => self::noticePayload(self::CHANNEL_TOAST, $message),
		], $status);
	}

	/**
	 * Формирует успешный ответ без обязательного уведомления.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data          Полезная нагрузка ответа.
	 * @param   string|null           $toastMessage  Необязательный текст toast.
	 *
	 * @return self Экземпляр JSON-ответа.
	 *
	 * @example
	 *     JsonResponse::ok(['items' => $rows])->send();
	 */
	public static function ok(array $data = [], ?string $toastMessage = NULL): self {
		$body = [
			'success' => true,
			'data'    => $data,
		];

		if($toastMessage !== NULL && $toastMessage !== '') {
			$body['notice'] = self::noticePayload(self::CHANNEL_TOAST, $toastMessage);
		}

		return self::build($body, 200);
	}

	/**
	 * Формирует ответ с notify-блоком в интерфейсе.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                     $title    Заголовок уведомления.
	 * @param   string                     $message  Текст уведомления.
	 * @param   string                     $type     Тип уведомления (см. TYPE_*).
	 * @param   array<string, mixed>       $data     Полезная нагрузка ответа.
	 * @param   int                        $status   HTTP-статус.
	 * @param   bool                       $success  Флаг успеха операции.
	 * @param   array<string, mixed>|null  $error    Дополнительный блок ошибки.
	 *
	 * @return self Экземпляр JSON-ответа.
	 *
	 * @example
	 *     JsonResponse::notify(__('Готово'), __('Данные обновлены'), self::TYPE_SUCCESS)->send();
	 */
	public static function notify(
		string $title,
		string $message,
		string $type = self::TYPE_INFO,
		array  $data = [],
		int    $status = 200,
		bool   $success = true,
		?array $error = NULL,
	): self {
		$body = [
			'success' => $success,
			'data'    => $data,
			'notice'  => self::noticePayload(self::CHANNEL_NOTIFY, $message, $title, $type),
		];

		if($error !== NULL) {
			$body['error'] = $error;
		}

		return self::build($body, $status);
	}

	/**
	 * Формирует ответ об ошибке с notify и структурой error.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $title    Заголовок ошибки.
	 * @param   string                $message  Текст ошибки.
	 * @param   string                $code     Машиночитаемый код ошибки.
	 * @param   int                   $status   HTTP-статус.
	 * @param   array<string, mixed>  $extra    Дополнительные ключи: fields, detail, data.
	 *
	 * @return self Экземпляр JSON-ответа.
	 *
	 * @example
	 *     JsonResponse::fail(__('Ошибка'), __('Неверные данные'), 'validation_failed', 422)->send();
	 */
	public static function fail(
		string $title,
		string $message,
		string $code,
		int    $status = 400,
		array  $extra = [],
	): self {
		$error = [
			'code'    => $code,
			'message' => $message,
			'title'   => $title,
		];

		if(isset($extra['fields']) && is_array($extra['fields'])) {
			$error['fields'] = $extra['fields'];
		}

		if(isset($extra['detail'])) {
			$error['detail'] = $extra['detail'];
		}

		$data = isset($extra['data']) && is_array($extra['data'])? $extra['data'] : [];

		return self::build([
			'success' => false,
			'data'    => $data,
			'error'   => $error,
			'notice'  => self::noticePayload(self::CHANNEL_NOTIFY, $message, $title, self::TYPE_ERROR),
		], $status);
	}

	/**
	 * Собирает структуру notice для toast или notify.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $channel  Канал уведомления.
	 * @param   string       $message  Текст сообщения.
	 * @param   string|null  $title    Заголовок (для notify).
	 * @param   string|null  $type     Тип уведомления.
	 *
	 * @return array{channel: string, message: string, title?: string, type?: string} Payload notice.
	 */
	private static function noticePayload(
		string  $channel,
		string  $message,
		?string $title = NULL,
		?string $type = NULL,
	): array {
		$notice = [
			'channel' => $channel,
			'message' => $message,
		];

		if($title !== NULL && $title !== '') {
			$notice['title'] = $title;
		}

		if($type !== NULL && $type !== '') {
			$notice['type'] = $type;
		}

		return $notice;
	}

	/**
	 * Создаёт экземпляр ответа из готового тела.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $body    Тело JSON-ответа.
	 * @param   int                   $status  HTTP-статус.
	 *
	 * @return self Экземпляр JSON-ответа.
	 */
	private static function build(array $body, int $status): self {
		return new self($body, $status);
	}

	/**
	 * Отправляет JSON-ответ клиенту с заголовками и HTTP-кодом.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     JsonResponse::ok(['saved' => true])->send();
	 */
	public function send(): void {
		if(!headers_sent()) {
			http_response_code($this->status_code);
			header('Content-Type: application/json; charset=utf-8');
		}

		echo json_encode($this->payload, JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
	}

}
