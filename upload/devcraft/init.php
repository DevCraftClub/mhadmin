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
	$vendor_autoload = __DIR__ . '/vendor/autoload.php';

	if(!is_file($vendor_autoload)) {
		define('DEVCRAFT_VENDOR_MISSING', true);
		return;
	}

	define('DEVCRAFT_BOOTSTRAPPED', true);

	/** Подключает автозагрузчик Composer (исключение: без DLEPlugins::Check()). */
	require_once $vendor_autoload;

	/** Регистрирует пути каталогов DevCraft в среде выполнения. */
	DevCraft\Core\Config\Paths::register();

	/** Запускает синглтон приложения DevCraft. */
	DevCraft\Core\Application::instance()->boot();
}
