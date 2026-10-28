<?php
//===============================================================
// Файл: TempDownload.php                                       =
// Путь: devcraft/src/modules/Admin/Services/TempDownload.php   =
// Последнее изменение: 2026-10-28 12:00:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Config\Paths;

/**
 * Временный файл для одной отдачи через FileResponse; удаляется в конце запроса.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class TempDownload {

	/**
	 * Резервирует путь временного файла в каталоге кэша.
	 *
	 * @since 200.4.2
	 *
	 * @param   string  $extension  Расширение без точки.
	 *
	 * @return string Абсолютный путь (файл удалится после ответа).
	 *
	 * @throws \RuntimeException Если каталог кэша недоступен для записи.
	 */
	public static function path(string $extension): string {
		$dir = rtrim(Paths::cache(), '/\\') . '/downloads';

		if(!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			throw new \RuntimeException(__('Каталог кэша недоступен для записи'));
		}

		$path = $dir . '/' . bin2hex(random_bytes(8)) . '.' . $extension;

		register_shutdown_function(static function () use ($path): void {
			if(is_file($path)) {
				@unlink($path);
			}
		});

		return $path;
	}

	/**
	 * Записывает текст во временный файл.
	 *
	 * @since 200.4.2
	 *
	 * @return string Абсолютный путь к файлу.
	 *
	 * @throws \RuntimeException Если записать не удалось.
	 */
	public static function write(string $contents, string $extension): string {
		$path = self::path($extension);

		if(file_put_contents($path, $contents) === false) {
			throw new \RuntimeException(__('Не удалось записать временный файл'));
		}

		return $path;
	}

}
