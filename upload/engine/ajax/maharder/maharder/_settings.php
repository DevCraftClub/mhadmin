<?php

if (!$is_logged) {
	exit('error');
}

if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
	exit('error');
}
$concurrentDirectory = ENGINE_DIR . '/inc/maharder/_config';

if (!DataManager::createDir(service : 'maharder', module : 'save_setting', _path : $concurrentDirectory)) {
	LogGenerator::setLogs(1);
}
$file = $concurrentDirectory.'/'.$_POST['module'].'.json';


if (empty($save_con['list_count']) || !isset($save_con['list_count'])) {
	$save_con['list_count'] = $config['news_number'];
}

if (empty($save_con['language']) || !isset($save_con['language'])) {
	$save_con['language'] = 'ru_RU';
}

if (empty($save_con['cache_path']) || !isset($save_con['cache_path'])) {
	$save_con['cache_path'] = '/engine/inc/maharder/_cache';
}

if (empty($save_con['locales_path']) || !isset($save_con['locales_path'])) {
	$save_con['locales_path'] = '/engine/inc/maharder/_locales';
}

if (empty($save_con['cache_timer']) || !isset($save_con['cache_timer'])) {
	$save_con['cache_timer'] = 60;
}

if (empty($save_con['theme']) || !isset($save_con['theme'])) {
	$save_con['theme'] = 'light';
}

file_put_contents($file, json_encode($save_con, JSON_UNESCAPED_UNICODE));
clear_cache();

echo 'ok';
