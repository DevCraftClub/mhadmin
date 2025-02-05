<?php

/**
 * Абстрактный класс, представляющий базовый ответ в виде JSON.
 * Предназначен для наследования, чтобы реализовать конкретные типы ответов (например, успех или ошибка).
 *
 * @property int    $status  HTTP статус ответа.
 * @property bool   $success Указывает, успешен ли ответ.
 * @property string $message Текстовое сообщение, сопровождающее ответ.
 * @property array  $data    Основные данные ответа.
 * @property array  $meta    Дополнительные мета-данные.
 */
abstract class AjaxAbstractResponse {
	protected int     $status;
	protected bool    $success;
	protected string  $message;
	protected array   $data = [];
	protected array   $meta = [];
	protected ?string $redirect = null;

	/**
	 * Устанавливает HTTP статус ответа.
	 *
	 * @param int $status HTTP статус кода.
	 *
	 * @return self Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$status
	 */
	public function setStatus(int $status): self {
		$this->status = $status;
		return $this;
	}

	/**
	 * Устанавливает сообщение ответа.
	 *
	 * @param string $message Текст сообщения.
	 *
	 * @return self Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$message
	 */
	public function setMessage(string $message): self {
		$this->message = $message;
		return $this;
	}

	/**
	 * Устанавливает данные ответа.
	 *
	 * @param array|string $data Массив данных или просто строка сообщения, которые будут включены в ответ.
	 *
	 * @return self Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$data
	 */
	public function setData(array|string $data): self {
		if (is_array($data)) {
			// Объединяем массив входных данных с текущим массивом $this->data
			$this->data = array_filter(array_merge($this->data, $data));
		} else {
			// Добавляем строку в массив $this->data
			$this->data[] = $data;
		}
		return $this;

	}

	/**
	 * Устанавливает дополнительные мета-данные.
	 *
	 * @param array $meta Массив данных мета-информации.
	 *
	 * @return self Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$meta
	 */
	public function setMeta(array $meta): self {
		$this->meta = $meta;
		return $this;
	}

	/**
	 * Проверяет, указано ли успешное выполнение запроса.
	 *
	 * @return bool Возвращает true, если запрос выполнен успешно, иначе false.
	 * @see AjaxAbstractResponse::$success
	 */
	public function isSuccess(): bool {
		return $this->success;
	}

	/**
	 * Устанавливает статус успешности выполнения операции.
	 *
	 * Метод сохраняет переданное булево значение в свойстве `success`
	 * и возвращает текущий экземпляр объекта для реализации цепочки вызовов.
	 *
	 * @param bool $success Статус успешности выполнения операции.
	 *
	 * @return AjaxAbstractResponse Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$success
	 */
	public function setSuccess(bool $success): AjaxAbstractResponse {
		$this->success = $success;
		return $this;
	}

	/**
	 * Возвращает URL-адрес перенаправления, установленный для отклика Ajax.
	 *
	 * @return null|string URL-адрес перенаправления.
	 * @see $redirect
	 * @see \AjaxAbstractResponse::setRedirect()
	 */
	public function getRedirect(): ?string {
		return $this->redirect;
	}

	/**
	 * Устанавливает URL для перенаправления.
	 *
	 * Метод сохраняет указанный URL в свойстве `redirect` и возвращает
	 * текущий экземпляр объекта для цепочки вызовов.
	 *
	 * @param string $redirect URL для перенаправления.
	 *
	 * @return AjaxAbstractResponse Экземпляр текущего объекта для цепочки вызовов.
	 *
	 * @see AjaxAbstractResponse::$redirect
	 */
	public function setRedirect(string $redirect): AjaxAbstractResponse {
		$this->redirect = $redirect;
		return $this;
	}

	/**
	 * Отправляет HTTP-ответ в формате JSON и завершает выполнение скрипта.
	 *
	 * Устанавливает заголовок ответа с указанием типа содержимого (application/json),
	 * HTTP-статус, тело ответа в формате JSON, а затем завершает выполнение.
	 *
	 * Заголовки и статус берутся из свойств экземпляра класса, а тело ответа
	 * формируется методом {@see AjaxAbstractResponse::buildResponse()}.
	 *
	 * @see AjaxAbstractResponse::buildResponse()
	 * @see AjaxAbstractResponse::$status
	 * @see AjaxAbstractResponse::$success
	 * @see AjaxAbstractResponse::$message
	 * @see AjaxAbstractResponse::$data
	 * @see AjaxAbstractResponse::$meta
	 * @global string HTTP-заголовок Content-Type.
	 * @global int HTTP-статус ответа.
	 */
	public function send(): string {
		header('Content-Type: application/json');
		http_response_code($this->status);
		return $this->buildResponse();
	}

	/**
	 * Формирует тело ответа в формате JSON.
	 *
	 * Метод преобразует свойства текущего экземпляра класса, такие как успех операции,
	 * HTTP-статус, сообщение, данные и мета-информацию, в JSON-строку.
	 *
	 * @return string JSON-представление ответа.
	 *
	 * @see AjaxAbstractResponse::$success
	 * @see AjaxAbstractResponse::$status
	 * @see AjaxAbstractResponse::$message
	 * @see AjaxAbstractResponse::$data
	 * @see AjaxAbstractResponse::$meta
	 */
	protected function buildResponse(): string {
		return json_encode(
			[
				'success'  => $this->success,
				'status'   => $this->status,
				'message'  => $this->message,
				'data'     => $this->data,
				'meta'     => $this->meta,
				'redirect' => $this->redirect
			],
			JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
		);
	}
}