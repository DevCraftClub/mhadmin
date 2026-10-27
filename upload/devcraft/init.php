<?php
//===============================================================
// Файл: init.php                                               =
// Путь: devcraft/init.php                                      =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

/**
 * Точка входа bootstrap: подключение автозагрузки Composer и запуск приложения DevCraft.
 *
 * Определяет константу `DEVCRAFT_BOOTSTRAPPED` и инициализирует ядро плагина один раз
 * за запрос. При отсутствии каталога `vendor/` выводит предупреждение в админке DLE
 * и прерывает загрузку.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Bootstrap
 */

declare(strict_types=1);

if(!defined('DEVCRAFT_BOOTSTRAPPED')) {
	$dcRoot          = defined('ROOT_DIR') ? rtrim((string) ROOT_DIR, '/\\') . '/devcraft' : __DIR__;
	$vendor_autoload = $dcRoot . '/vendor/autoload.php';

	require_once $dcRoot . '/src/bootstrap/composer_marker.php';

	if(!is_file($vendor_autoload) || !dc_composer_initialized()) {
		define('DEVCRAFT_VENDOR_MISSING', true);
		return;
	}

	dc_composer_ensure_stamp();

	define('DEVCRAFT_BOOTSTRAPPED', true);

	/** Подключает автозагрузчик Composer (исключение: без DLEPlugins::Check()). */
	$dcLoader = require $vendor_autoload;
	$dcSourceRoot = realpath($dcRoot . '/src') . DIRECTORY_SEPARATOR;

	// Composer определяет путь, а DLE применяет изменения плагинов к классам DevCraft.
	// Если classmap устарел и путь не существует, пробуем PSR-4 fallback для локальных классов.
	spl_autoload_register(static function (string $class) use ($dcLoader, $dcSourceRoot): void {
		$file = $dcLoader->findFile($class);
		$path = ($file !== false) ? realpath($file) : false;

		if($path === false) {
			$prefixMap = [
				'DevCraft\\Core\\'          => 'classes/',
				'DevCraft\\Builders\\'      => 'classes/Builders/',
				'DevCraft\\Form\\'          => 'classes/Form/',
				'DevCraft\\Modules\\'       => 'modules/',
				'DevCraft\\Enums\\'         => 'classes/Enums/',
				'DevCraft\\Types\\'         => 'classes/Types/',
				'DevCraft\\Utils\\'         => 'classes/Utils/',
				'DevCraft\\Dle\\'           => 'sdk/dle/',
				'JetBrains\\PhpStorm\\'     => 'sdk/dle/Support/PhpStorm/',
			];

			foreach($prefixMap as $prefix => $baseDir) {
				if(!str_starts_with($class, $prefix)) {
					continue;
				}

				$relative = substr($class, strlen($prefix));
				$fallback = $dcSourceRoot . $baseDir . str_replace('\\', '/', $relative) . '.php';

				if(is_file($fallback)) {
					$path = realpath($fallback);
				}

				break;
			}
		}

		if($path === false || !str_starts_with($path, $dcSourceRoot)) {
			return;
		}

		require (class_exists('DLEPlugins', false) ? DLEPlugins::Check($path) : $path);
	}, true, true);

	/** Подключает in-process DLE SDK: глобальные шимы `prepare()` / `query()` и fallback-автозагрузку. */
	require_once DLEPlugins::Check($dcRoot . '/src/sdk/dle/bootstrap.php');

	/** Регистрирует пути каталогов DevCraft в среде выполнения. */
	DevCraft\Core\Config\Paths::register();

	/** Запускает синглтон приложения DevCraft. */
	DevCraft\Core\Application::instance()->boot();
}
