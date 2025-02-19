<?php

/**
 * Класс для формирования ответов об ошибке.
 *
 * Данный класс наследует {@see AjaxAbstractResponse} и предназначен для создания
 * ответа с сообщением об ошибке. По умолчанию возвращается HTTP-статус 400
 * (Bad Request) и общее сообщение об ошибке.
 *
 * @throws Throwable
 * @see AjaxAbstractResponse::$message
 * @see AjaxAbstractResponse::$success
 * @see AjaxAbstractResponse::$status
 */
class ErrorResponseAjax extends AjaxAbstractResponse {
	/**
	 * Конструктор класса ErrorResponse.
	 *
	 * Устанавливает HTTP-статус ответа, сообщение об ошибке и флаг успешности.
	 * По умолчанию используется статус 400 и стандартное сообщение.
	 *
	 * @param int $status HTTP-статус, который будет установлен для ответа.
	 *                    По умолчанию 400 (Bad Request).
	 *
	 * @throws Throwable
	 * @see AjaxAbstractResponse::$message
	 * @see AjaxAbstractResponse::$success
	 * @see __()
	 * @see AjaxAbstractResponse::$status
	 */
	public function __construct(int $status = 400) {
		$this->status  = $status;
		$this->message = __('Ошибка при обработке данных!');
		$this->success = false;
	}

}
