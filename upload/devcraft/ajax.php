<?php
//===============================================================
// Файл: ajax.php                                               =
// Путь: devcraft/ajax.php                                      =
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
 * Тонкая точка входа AJAX DevCraft: загрузка DLE, сессии админки и JSON-контроллера.
 *
 * DevCraft — AJAX entry (thin).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Bootstrap
 */

declare(strict_types=1);

error_reporting(E_ALL^E_WARNING^E_DEPRECATED^E_NOTICE);
ini_set('error_reporting', E_ALL^E_WARNING^E_DEPRECATED^E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname(__DIR__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

require_once ENGINE_DIR . '/classes/plugins.class.php';

/** Подключает базовые функции DLE (вспомогательные процедуры движка). */
require_once DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php');

/** Инициализирует минимальную админ-сессию DLE до загрузки init.php. */
require_once DLEPlugins::Check(ROOT_DIR . '/devcraft/src/bootstrap/ajax-session.php');

while(ob_get_level() > 0) {
	ob_end_clean();
}

/** Подключает bootstrap DevCraft (автозагрузка, Paths, Application::boot). */
require_once DLEPlugins::Check(ROOT_DIR . '/devcraft/init.php');

if(!defined('DEVCRAFT_BOOTSTRAPPED')) {
	exit;
}

try {
	(new DevCraft\Core\Http\AjaxController())->run();
} catch(\Throwable $e) {
	global $config;

	$showDetail = !empty($config['display_php_errors']);

	$internalMessage = __('Произошла ошибка при выполнении запроса');

	(new DevCraft\Core\Http\JsonResponse([
		'success' => false,
		'data'    => [],
		'error'   => [
			'code'    => 'internal_error',
			'message' => $internalMessage,
			'title'   => __('Ошибка'),
			'detail'  => $showDetail? $e->getMessage() : NULL,
		],
		'notice'  => [
			'channel' => 'notify',
			'message' => $internalMessage,
			'title'   => __('Ошибка'),
			'type'    => 'error',
		],
	], 500))->send();
}
