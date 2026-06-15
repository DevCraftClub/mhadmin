<?php
//===============================================================
// Файл: ajax-session.php                                       =
// Путь: devcraft/src/bootstrap/ajax-session.php                =
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
 * Минимальная авторизация админ-сессии для AJAX DevCraft без загрузки init.php.
 *
 * Восстанавливает состояние входа администратора DLE по cookie и session,
 * заполняет глобальные переменные авторизации и определяет константу LOGGED_IN.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @global array<string, mixed>             $config         Конфигурация DLE.
 * @global object                           $db             Экземпляр БД DLE.
 * @global bool                             $is_loged_in    Признак активной админ-сессии.
 * @global array<string, mixed>             $member_id      Данные текущего пользователя (при входе).
 * @global string                           $dle_login_hash Хеш для проверки CSRF в админке DLE.
 * @global array<int, array<string, mixed>> $user_group     Группы пользователей DLE.
 * @subpackage Bootstrap
 */

declare(strict_types=1);

if(!defined('DATALIFEENGINE')) {
	die('Hacking attempt!');
}

global $config, $db, $is_loged_in, $member_id, $dle_login_hash, $user_group;

if(!isset($config) || !is_array($config) || !isset($db)) {
	return;
}

dle_session();

$is_loged_in    = false;
$member_id      = [];
$username       = '';
$cmd5_password  = '';
$post           = false;
$check_log      = false;
$attempt_login  = false;
$_IP            = get_ip();
$_TIME          = time();
$dle_login_hash = sha1(SECURE_AUTH_KEY . ($_SERVER['HTTP_USER_AGENT'] ?? ''));

$user_group = get_vars('usergroup');

if(!$user_group) {
	$user_group = [];

	$db->query('SELECT * FROM ' . USERPREFIX . '_usergroups ORDER BY id ASC');

	while($row = $db->get_row()) {
		$user_group[$row['id']] = [];

		foreach($row as $key => $value) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}
	}

	set_vars('usergroup', $user_group);
	$db->free();
}

if(isset($_SESSION['dle_user_id']) && (int) $_SESSION['dle_user_id'] > 0 && !empty($_SESSION['dle_password'])) {
	$username      = (int) $_SESSION['dle_user_id'];
	$cmd5_password = (string) $_SESSION['dle_password'];
	$post          = false;
	$attempt_login = true;

	if(!isset($_SESSION['check_log'])) {
		$check_log = true;
	}
} elseif(isset($_COOKIE['dle_user_id']) && (int) $_COOKIE['dle_user_id'] > 0 && !empty($_COOKIE['dle_password'])) {
	$username      = (int) $_COOKIE['dle_user_id'];
	$cmd5_password = (string) $_COOKIE['dle_password'];
	$post          = false;
	$check_log     = true;
	$attempt_login = true;
}

if(check_login($username, $cmd5_password, $post, $check_log)) {
	$is_loged_in = true;

	if(!isset($_SESSION['dle_user_id']) && isset($_COOKIE['dle_user_id']) && $_COOKIE['dle_user_id']) {
		session_regenerate_id();
		$_SESSION['dle_user_id']  = $_COOKIE['dle_user_id'];
		$_SESSION['dle_password'] = $_COOKIE['dle_password'];
	}
} else {
	$is_loged_in = false;
}

if(
	$is_loged_in
	&& !empty($config['log_hash'])
	&& (
		!isset($_COOKIE['dle_hash'])
		|| $_COOKIE['dle_hash'] !== ($member_id['hash'] ?? '')
		|| empty($member_id['hash'])
	)
) {
	$is_loged_in = false;
}

if(
	$is_loged_in
	&& ($config['ip_control'] ?? '') === '1'
	&& !check_netz($member_id['logged_ip'] ?? '', $_IP)
) {
	$is_loged_in = false;
}

if(!$is_loged_in && $attempt_login) {
	$member_id = [];
}

if($is_loged_in) {
	if(!defined('LOGGED_IN')) {
		define('LOGGED_IN', true);
	}

	$dle_login_hash = sha1(
		SECURE_AUTH_KEY . $member_id['user_id'] . sha1($member_id['password']) . $member_id['hash'],
	);
}
