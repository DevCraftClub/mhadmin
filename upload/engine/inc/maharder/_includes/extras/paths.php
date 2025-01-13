<?php
//===============================================================
// Файл: paths.php                                              =
// Путь: engine/inc/maharder/_includes/extras/paths.php         =
// Дата создания: 2024-04-15 07:27:46                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

if (!defined('DATALIFEENGINE')) {
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../../../../../');

	exit('Hacking attempt!');
}

$current_dir = __DIR__;
if (preg_grep('/(cache)/', explode("\n", $current_dir))) $current_dir = dirname(__FILE__, 5);
else $current_dir = dirname(__FILE__, 6);

if (!defined('ROOT')) {
	define("ROOT", $current_dir);
}
if (!defined('MH_ROOT')) {
	define("MH_ROOT", ROOT . '/engine/inc/maharder');
}
if (!defined('MH_ADMIN')) {
	define("MH_ADMIN", MH_ROOT . '/admin');
}

$mh_models_paths = [
	MH_ROOT . '/_models/Admin',
	// Custom models //
];

$mh_loader_paths = [
	MH_ROOT . '/_includes/classes',
	MH_ROOT . '/_includes/classes/db',
	MH_ROOT . '/_includes/database',
	MH_ROOT . '/_includes/traits',
	MH_ROOT . '/_includes/twigExtensions',
	MH_ROOT . '/_models',
	MH_ROOT . '/_repository/Admin',
	// Custom paths //
];
$mh_loader_paths = array_merge($mh_loader_paths, $mh_models_paths);

include_once MH_ROOT . '/_includes/extras/functions.php';
include_once MH_ROOT . '/_includes/extras/mhLoader.php';
include_once MH_ROOT . '/_includes/vendor/autoload.php';

ComposerAction::destroy();
