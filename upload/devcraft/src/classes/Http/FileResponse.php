<?php
//===============================================================
// Файл: FileResponse.php                                       =
// Путь: devcraft/src/classes/Http/FileResponse.php             =
// Последнее изменение: 2026-06-15                              =
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
 * HTTP-ответ с потоковой отдачей файла (скачивание).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Http
 */
final class FileResponse implements ResponseInterface {

	/**
	 * Абсолютный путь к файлу на диске.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	private string $filePath;

	/**
	 * Имя файла для заголовка Content-Disposition.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	private string $downloadName;

	/**
	 * MIME-тип ответа.
	 *
	 * @since 200.4.0
	 *
	 * @var string
	 */
	private string $mimeType;

	/**
	 * @since 200.4.0
	 *
	 * @param   string       $filePath      Абсолютный путь к существующему файлу.
	 * @param   string|null  $downloadName  Имя при скачивании; по умолчанию basename($filePath).
	 * @param   string|null  $mimeType      MIME; по умолчанию application/octet-stream.
	 */
	public function __construct(
		string  $filePath,
		?string $downloadName = NULL,
		?string $mimeType = NULL,
	) {
		$this->filePath     = $filePath;
		$this->downloadName = $downloadName !== NULL && $downloadName !== ''
			? $downloadName
			: basename($filePath);
		$this->mimeType = $mimeType !== NULL && $mimeType !== ''
			? $mimeType
			: 'application/octet-stream';
	}

	/**
	 * Отправляет файл клиенту с заголовками скачивания.
	 *
	 * @since 200.4.0
	 *
	 * @throws \RuntimeException Если файл недоступен для чтения.
	 */
	public function send(): void {
		if(!is_file($this->filePath) || !is_readable($this->filePath)) {
			throw new \RuntimeException(__('Файл недоступен для чтения'));
		}

		$fileSize = filesize($this->filePath);

		if($fileSize === false) {
			throw new \RuntimeException(__('Не удалось определить размер файла'));
		}

		while(ob_get_level() > 0) {
			ob_end_clean();
		}

		if(!headers_sent()) {
			http_response_code(200);
			header('Content-Type: ' . $this->mimeType);
			header('Content-Disposition: attachment; filename="' . $this->escapeFilename($this->downloadName) . '"');
			header('Content-Length: ' . (string) $fileSize);
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
		}

		$readResult = readfile($this->filePath);

		if($readResult === false) {
			throw new \RuntimeException(__('Ошибка чтения файла'));
		}
	}

	/**
	 * Экранирует имя файла для заголовка Content-Disposition.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $filename  Исходное имя файла.
	 *
	 * @return string Безопасное имя для заголовка.
	 */
	private function escapeFilename(string $filename): string {
		$sanitized = str_replace(['"', "\r", "\n"], '', $filename);

		return $sanitized !== '' ? $sanitized : 'download.bin';
	}

}
