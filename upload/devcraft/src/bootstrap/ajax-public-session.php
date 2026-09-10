<?php
//===============================================================
// Файл: ajax-public-session.php                                =
// Путь: devcraft/src/bootstrap/ajax-public-session.php         =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
//===============================================================

/**
 * Сессия участника сайта (не админ) для публичного AJAX DevCraft.
 *
 * Загружает cookie/session DLE без требования allow_admin.
 * Гости допустимы: $is_logged = false, $dle_login_hash по User-Agent.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Bootstrap
 */

declare(strict_types=1);

if(!defined('DATALIFEENGINE')) {
	die('Hacking attempt!');
}

global $config, $db, $member_id, $dle_login_hash, $user_group, $is_logged;

if(!isset($config) || !is_array($config) || !isset($db)) {
	return;
}

dle_session();

$member_id      = [];
$is_logged      = false;
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
	$member_id = $db->super_query(
		'SELECT * FROM ' . USERPREFIX . "_users WHERE user_id='" . (int) $_SESSION['dle_user_id'] . "'",
	);

	if(
		isset($member_id['user_id'], $member_id['password'])
		&& (int) $member_id['user_id'] > 0
		&& md5($member_id['password']) === (string) $_SESSION['dle_password']
		&& ($member_id['banned'] ?? '') !== 'yes'
	) {
		$is_logged = true;
	} else {
		$member_id = [];
	}
} elseif(isset($_COOKIE['dle_user_id']) && (int) $_COOKIE['dle_user_id'] > 0 && !empty($_COOKIE['dle_password'])) {
	$member_id = $db->super_query(
		'SELECT * FROM ' . USERPREFIX . "_users WHERE user_id='" . (int) $_COOKIE['dle_user_id'] . "'",
	);

	if(
		isset($member_id['user_id'], $member_id['password'])
		&& (int) $member_id['user_id'] > 0
		&& md5($member_id['password']) === (string) $_COOKIE['dle_password']
		&& ($member_id['banned'] ?? '') !== 'yes'
	) {
		$is_logged                = true;
		$_SESSION['dle_user_id']  = $member_id['user_id'];
		$_SESSION['dle_password'] = md5($member_id['password']);
	} else {
		$member_id = [];
	}
}

if(
	$is_logged
	&& !empty($config['log_hash'])
	&& (
		!isset($_COOKIE['dle_hash'])
		|| $_COOKIE['dle_hash'] !== ($member_id['hash'] ?? '')
		|| empty($member_id['hash'])
	)
) {
	$is_logged = false;
	$member_id = [];
}

if($is_logged) {
	$dle_login_hash = sha1(
		SECURE_AUTH_KEY . $member_id['user_id'] . sha1($member_id['password']) . $member_id['hash'],
	);
}
