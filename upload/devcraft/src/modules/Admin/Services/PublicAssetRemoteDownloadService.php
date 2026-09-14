<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Config\Paths;
use RuntimeException;

/**
 * Загрузка внешнего HTTPS CSS/JS на сервер (лимиты v1).
 */
final class PublicAssetRemoteDownloadService {

	private const int TIMEOUT_SEC = 10;

	private const int MAX_BYTES = 2_097_152;

	/**
	 * @return string Относительный путь от ROOT_DIR к локальной копии
	 */
	public function download(string $url, string $kind): string {
		$url = trim($url);

		if(!str_starts_with(strtolower($url), 'https://')) {
			throw new RuntimeException(__('Разрешены только HTTPS URL'));
		}

		$parts = parse_url($url);
		if($parts === false || empty($parts['host'])) {
			throw new RuntimeException(__('Некорректный URL'));
		}

		$ctx = stream_context_create([
			'http' => [
				'timeout'         => self::TIMEOUT_SEC,
				'follow_location' => 1,
				'max_redirects'   => 3,
				'user_agent'      => 'DevCraftPublicAssets/1.0',
			],
			'ssl'  => [
				'verify_peer'      => true,
				'verify_peer_name' => true,
			],
		]);

		$body = @file_get_contents($url, false, $ctx);
		if($body === false) {
			throw new RuntimeException(__('Не удалось загрузить файл по URL'));
		}

		if(strlen($body) > self::MAX_BYTES) {
			throw new RuntimeException(__('Файл превышает лимит 2 МиБ'));
		}

		$contentType = $this->responseContentType($http_response_header ?? []);
		$this->assertAllowedContentType($kind, $contentType, $url);

		$dir = Paths::cache() . '/public_assets/remote';
		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			throw new RuntimeException(__('Не удалось создать каталог для внешних ресурсов'));
		}

		$ext  = $kind === 'css' ? 'css' : 'js';
		$hash = hash('sha256', $url);
		$file = $dir . '/' . $hash . '.' . $ext;

		if(@file_put_contents($file, $body) === false) {
			throw new RuntimeException(__('Не удалось сохранить загруженный файл'));
		}

		return ltrim(str_replace(ROOT_DIR, '', $file), '/\\');
	}

	/**
	 * @param   list<string>  $headers
	 */
	private function responseContentType(array $headers): string {
		foreach($headers as $line) {
			if(stripos($line, 'Content-Type:') === 0) {
				return trim(substr($line, strlen('Content-Type:')));
			}
		}

		return '';
	}

	private function assertAllowedContentType(string $kind, string $contentType, string $url): void {
		$ct = strtolower(trim(explode(';', $contentType)[0] ?? ''));
		$path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

		$allowed = $kind === 'css'
			? ['text/css', 'application/css', 'text/plain', '']
			: ['application/javascript', 'text/javascript', 'application/x-javascript', 'text/plain', ''];

		if(in_array($ct, $allowed, true)) {
			return;
		}

		// Fallback по расширению, если CDN отдаёт octet-stream
		if(($kind === 'css' && str_ends_with(strtolower($path), '.css'))
			|| ($kind === 'js' && (str_ends_with(strtolower($path), '.js') || str_ends_with(strtolower($path), '.mjs')))) {
			return;
		}

		throw new RuntimeException(__('Недопустимый Content-Type для ресурса'));
	}

}
