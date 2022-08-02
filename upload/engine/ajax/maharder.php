<?php
//	===============================
//	AJAX функции
//	===============================
//	Автор: Maxim Harder
//	Сайт: https://maxim-harder.de
//	Телеграм: http://t.me/MaHarder
//	===============================
//	Ничего не менять
//	===============================


if (!defined('DATALIFEENGINE')) {
	header("HTTP/1.1 403 Forbidden");
	header('Location: ../../../../');
	die("Hacking attempt!");
}

if (!$is_logged) die("error");

if ($_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash) {
	die("error");
}

$data = $_POST;
if (!$data) die();

$module = $data['module'];
$mod_file = $data['file'];
$action = $data['action'];

include_once(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/classes/Ajax.php'));
include_once(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/classes/LogGenerator.php'));

$mh_admin = new Ajax();

if (file_exists(DLEPlugins::Check(ENGINE_DIR . '/ajax/maharder/' . $module . '/' . $mod_file . '.php'))) {

	if (file_exists(DLEPlugins::Check(ENGINE_DIR . '/data/' . $module . '.php'))) {
		include_once(DLEPlugins::Check(ENGINE_DIR . '/data/' . $module . '.php'));
	}

	include_once(DLEPlugins::Check(ENGINE_DIR . '/ajax/maharder/' . $module . '/' . $mod_file . '.php'));

} else {

	header("HTTP/1.1 403 Forbidden");
	header('Location: ../../');
	die("Hacking attempt!");

}