<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

/**
 * Доставка запроса проверки ключа на сервер лицензий.
 */
interface LicenceTransport {

	/**
	 * @param   array<string, mixed>  $body
	 *
	 * @return array<string, mixed>|null Ответ сервера или null, если сервер молчит.
	 */
	public function post(array $body): ?array;
}

/**
 * Живой запрос на devcraft.club.
 */
final class HttpLicenceTransport implements LicenceTransport {

	public const URL = 'https://devcraft.club/index.php?licence-server/check';

	public function post(array $body): ?array {
		$context = stream_context_create([
			'http' => [
				'method'  => 'POST',
				'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
				'content' => http_build_query($body),
				'timeout' => 10,
				'ignore_errors' => true,
			],
		]);

		$response = @file_get_contents(self::URL, false, $context);
		if($response === false || $response === '') {
			return null;
		}

		$decoded = json_decode($response, true);
		if(!is_array($decoded)) {
			return null;
		}

		return $decoded;
	}
}
