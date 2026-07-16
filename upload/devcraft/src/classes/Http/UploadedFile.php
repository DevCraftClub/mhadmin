<?php

declare(strict_types=1);

namespace DevCraft\Core\Http;

use RuntimeException;

/**
 * Обёртка над элементом $_FILES для multipart-загрузок в админке DevCraft.
 */
final class UploadedFile {

	/**
	 * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
	 */
	private function __construct(
		private readonly array $file,
	) {}

	/**
	 * Создаёт экземпляр из ключа $_FILES.
	 */
	public static function fromFilesKey(string $key): self {
		$file = $_FILES[$key] ?? null;

		if(!is_array($file)) {
			throw new RuntimeException(__('Файл не передан'));
		}

		$error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

		if($error !== UPLOAD_ERR_OK) {
			throw new RuntimeException(__('Ошибка загрузки файла'));
		}

		$tmp = (string) ($file['tmp_name'] ?? '');

		if($tmp === '' || !is_uploaded_file($tmp)) {
			throw new RuntimeException(__('Временный файл загрузки недоступен'));
		}

		return new self($file);
	}

	public function originalName(): string {
		return basename((string) ($this->file['name'] ?? 'file'));
	}

	public function tmpName(): string {
		return (string) ($this->file['tmp_name'] ?? '');
	}

	public function size(): int {
		return (int) ($this->file['size'] ?? 0);
	}

	public function mime(): string {
		return (string) ($this->file['type'] ?? '');
	}

	/**
	 * @return array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int}
	 */
	public function toArray(): array {
		return $this->file;
	}

	/**
	 * @param list<string> $allowed Расширения без точки, в нижнем регистре
	 */
	public function assertExtension(array $allowed): void {
		$ext = strtolower(pathinfo($this->originalName(), PATHINFO_EXTENSION));

		if($ext === '' || $allowed === [] || !in_array($ext, $allowed, true)) {
			$list = $allowed !== [] ? implode(', ', $allowed) : __('нет');

			throw new RuntimeException(__('Недопустимое расширение файла. Разрешено: {ext}', ['{ext}' => $list]));
		}
	}

	public function moveTo(string $targetPath): void {
		$dir = dirname($targetPath);

		if(!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new RuntimeException(__('Не удалось создать каталог для файлов'));
		}

		if(!move_uploaded_file($this->tmpName(), $targetPath)) {
			throw new RuntimeException(__('Не удалось сохранить загруженный файл'));
		}
	}

}
