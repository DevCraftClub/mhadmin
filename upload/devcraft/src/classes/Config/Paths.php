<?php
//===============================================================
// Файл: Paths.php                                              =
// Путь: devcraft/src/classes/Config/Paths.php                  =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Config;

/**
 * Регистрация и доступ к путям и URL-адресам DevCraft.
 *
 * Определяет константы каталогов плагина и предоставляет хелперы
 * для построения базового URL сайта и AJAX-эндпоинтов.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Config
 */
final class Paths {

	/**
	 * Регистрирует константы путей DevCraft, если они ещё не определены.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     Paths::register();
	 */
	public static function register(): void {
		if(!defined('ROOT_DIR')) {
			define('ROOT_DIR', dirname(__DIR__, 4));
		}

		if(!defined('DEVCRAFT_ROOT')) {
			define('DEVCRAFT_ROOT', ROOT_DIR . '/devcraft');
		}

		if(!defined('DEVCRAFT_SRC')) {
			define('DEVCRAFT_SRC', DEVCRAFT_ROOT . '/src');
		}

		if(!defined('DEVCRAFT_TEMPLATES')) {
			define('DEVCRAFT_TEMPLATES', DEVCRAFT_SRC . '/templates');
		}

		if(!defined('DEVCRAFT_LOCALES')) {
			define('DEVCRAFT_LOCALES', DEVCRAFT_ROOT . '/locales');
		}

		if(!defined('DEVCRAFT_CONFIG')) {
			define('DEVCRAFT_CONFIG', DEVCRAFT_ROOT . '/config');
		}

		if(!defined('DEVCRAFT_MODULES')) {
			define('DEVCRAFT_MODULES', DEVCRAFT_SRC . '/modules');
		}

		if(!defined('DEVCRAFT_CLASSES')) {
			define('DEVCRAFT_CLASSES', DEVCRAFT_SRC . '/classes');
		}

		if(!defined('DEVCRAFT_LOGS')) {
			define('DEVCRAFT_LOGS', DEVCRAFT_ROOT . '/logs');
		}

		if(!defined('DEVCRAFT_CACHE')) {
			define('DEVCRAFT_CACHE', DEVCRAFT_ROOT . '/cache');
		}
	}

	/**
	 * Возвращает абсолютный путь к корню каталога devcraft/.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_ROOT.
	 * @example
	 *        $root = Paths::root();
	 *
	 */
	public static function root(): string {
		return DEVCRAFT_ROOT;
	}

	/**
	 * Возвращает абсолютный путь к каталогу devcraft/src/.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_SRC.
	 * @example
	 *        $src = Paths::src();
	 *
	 */
	public static function src(): string {
		return DEVCRAFT_SRC;
	}

	/**
	 * Возвращает абсолютный путь к каталогу шаблонов Twig.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_TEMPLATES.
	 * @example
	 *        $tplDir = Paths::templates() . '/core';
	 *
	 */
	public static function templates(): string {
		return DEVCRAFT_TEMPLATES;
	}

	/**
	 * Возвращает абсолютный путь к каталогу локалей XLIFF.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_LOCALES.
	 * @example
	 *        $localeDir = Paths::locales();
	 *
	 */
	public static function locales(): string {
		return DEVCRAFT_LOCALES;
	}

	/**
	 * Возвращает абсолютный путь к каталогу конфигурации плагина.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_CONFIG.
	 * @example
	 *        $configDir = Paths::config();
	 *
	 */
	public static function config(): string {
		return DEVCRAFT_CONFIG;
	}

	/**
	 * Возвращает абсолютный путь к каталогу модулей DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_MODULES.
	 * @example
	 *        $adminPath = Paths::modules() . '/Admin';
	 *
	 */
	public static function modules(): string {
		return DEVCRAFT_MODULES;
	}

	/**
	 * Возвращает абсолютный путь к каталогу PHP-классов DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_CLASSES.
	 * @example
	 *        $classesDir = Paths::classes();
	 *
	 */
	public static function classes(): string {
		return DEVCRAFT_CLASSES;
	}

	/**
	 * Возвращает абсолютный путь к каталогу логов плагина.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_LOGS.
	 * @example
	 *        $logFile = Paths::logs() . '/admin.log';
	 *
	 */
	public static function logs(): string {
		return DEVCRAFT_LOGS;
	}

	/**
	 * Возвращает абсолютный путь к каталогу кеша плагина.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение DEVCRAFT_CACHE.
	 * @example
	 *        $cacheDir = Paths::cache();
	 *
	 */
	public static function cache(): string {
		return DEVCRAFT_CACHE;
	}

	/**
	 * Возвращает файловый каталог публичных DevCraft-ресурсов (css/js для AssetsChecker).
	 *
	 * @since 200.4.0
	 *
	 * @return string Абсолютный путь к devcraft/src/templates/core/assets/.
	 * @example
	 *        $assetsDir = Paths::publicAssets();
	 *
	 */
	public static function publicAssets(): string {
		return self::templates() . '/assets';
	}

	/**
	 * Возвращает базовый URL сайта DLE без завершающего слэша.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed> $config Глобальная конфигурация DLE.
	 *
	 * @return string URL из $config['http_home_url'] или «/».
	 * @example
	 *        $home = Paths::base();
	 *
	 */
	public static function base() {
		global $config;

		return rtrim((string) ($config['http_home_url'] ?? '/'), '/');
	}

	/**
	 * Возвращает базовый URL AJAX-входа DevCraft (`/devcraft/ajax.php`).
	 *
	 * @since 200.4.0
	 *
	 * @return string Полный URL точки входа ajax.php.
	 * @example
	 *        $ajaxBase = Paths::ajaxBase();
	 *
	 */
	public static function ajaxBase(): string {
		return self::base() . '/devcraft/ajax.php';
	}

	/**
	 * Формирует полный URL AJAX-запроса с параметрами controller и method.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $method      Имя AJAX-метода.
	 * @param   string  $controller  Идентификатор контроллера (по умолчанию «admin»).
	 *
	 * @return string URL с query-параметрами controller и method.
	 * @example
	 *        $url = Paths::ajaxUrl('saveSettings', 'admin');
	 *
	 */
	public static function ajaxUrl(string $method, string $controller = 'admin'): string {
		$query = http_build_query([
			'controller' => $controller,
			'method'     => $method,
		]);

		return self::ajaxBase() . '?' . $query;
	}

}
