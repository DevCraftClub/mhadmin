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

global $is_logged, $dle_login_hash;

if (!$is_logged) {
	echo (new ErrorResponseAjax())->setData(__('Не авторизованный пользователь'))->send();
	exit;
}

if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
	echo (new ErrorResponseAjax())->setData(__('Авторизованный пользователь не имеет права на данную операцию!'))->send(
	);
	exit;
}

$data = filter_input_array(INPUT_POST, [
	'module' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'file'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'action' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'method' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'data'   => FILTER_REQUIRE_ARRAY,
]);
if (!$data) {
	echo (new ErrorResponseAjax())->setData(__('Метод запроса неверен!'))->send();
	exit;
}

$module     = $data['module'];
$mod_file   = $data['file'];
$action     = $data['action'];
$method     = $data['method'];
$parsedData = $data['data'];

if (!$parsedData && !is_array($_POST['data'])) {
	$newData = filter_input_array(INPUT_POST, [
		'data' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
	]);
	parse_str($newData['data'], $parsedData);
} else {
	if (is_array($_POST['data'])) $parsedData = filter_var_array($_POST['data']);
	else parse_str($_POST['data'], $parsedData);
}
include_once(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php'));

$mh_admin = new MhAjax();

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