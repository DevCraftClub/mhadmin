<?php

/**
 * Представляет успешный HTTP-ответ.
 *
 * @see AjaxAbstractResponse Для базового функционала и свойств.
 * @see __ Функция перевода для строки сообщения.
 */
class SuccessResponseAjax extends AjaxAbstractResponse {
	/**
	 * Создаёт объект успешного ответа.
	 *
	 * Устанавливает HTTP-статус (по умолчанию 200), сообщение (локализуется через функцию __)
	 * и флаг успешного выполнения.
	 *
	 * @param int $status HTTP-статус успешного ответа (по умолчанию 200).
	 *
	 * @throws Throwable
	 * @see AjaxAbstractResponse::$message
	 * @see AjaxAbstractResponse::$success
	 * @see AjaxAbstractResponse::$status
	 */
	public function __construct(int $status = 200) {
		$this->status  = $status;
		$this->message = __('Успех!');
		$this->success = true;
	}
}