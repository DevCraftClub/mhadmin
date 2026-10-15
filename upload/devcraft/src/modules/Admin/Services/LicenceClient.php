<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

/**
 * Проверка ключа лицензии через сервер DevCraft.
 */
final class LicenceClient {

	/**
	 * @return array{hide: bool, message: string}
	 */
	public function check(
		?string $key,
		string $site,
		string $product,
		bool $decline,
		LicenceTransport $transport,
	): array {
		$body = [
			'key'     => $key,
			'site'    => $site,
			'product' => $product,
			'decline' => $decline ? 1 : 0,
		];

		$response = $transport->post($body);
		if($response === null) {
			return [
				'hide'    => false,
				'message' => __('Сервер не ответил'),
			];
		}

		$error = $response['error'] ?? null;
		if($error === 'unknown_key' || (($response['ok'] ?? false) === false && $error === 'unknown_key')) {
			return [
				'hide'    => false,
				'message' => __('Ключ не найден'),
			];
		}

		if(($response['ok'] ?? false) !== true) {
			$message = match ($error) {
				'empty_site' => __('Адрес сайта пустой'),
				default      => __('Ключ не найден'),
			};

			return [
				'hide'    => false,
				'message' => $message,
			];
		}

		$message = '';
		if(!empty($response['support_ended'])) {
			$message = __('Помощь по этой лицензии уже кончилась');
		}

		return [
			'hide'    => true,
			'message' => $message,
		];
	}
}
