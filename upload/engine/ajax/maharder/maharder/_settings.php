<?php

global $parsedData, $config;

$concurrentDirectory = ENGINE_DIR . '/inc/maharder/_config';
DataManager::createDir(service : 'maharder', module : 'save_setting', _path : $concurrentDirectory);

$settings = filter_var_array($parsedData, [
	'list_count' => FILTER_VALIDATE_INT,
	'cache_path' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'cache_timer' => FILTER_VALIDATE_INT,
	'language' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'locales_path' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'logs' => FILTER_VALIDATE_BOOLEAN,
	'logs_db' => FILTER_VALIDATE_BOOLEAN,
	'logs_telegram' => FILTER_VALIDATE_BOOLEAN,
	'logs_telegram_api' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'logs_telegram_channel' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'cache_icon' => FILTER_VALIDATE_BOOLEAN,
	'theme' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
]);

if (empty($settings['list_count']) || !isset($settings['list_count'])) {
	$settings['list_count'] = $config['news_number'];
}

if (empty($settings['language']) || !isset($settings['language'])) {
	$settings['language'] = 'ru_RU';
}

if (empty($settings['cache_path']) || !isset($settings['cache_path'])) {
	$settings['cache_path'] = '/engine/inc/maharder/_cache';
}

if (empty($settings['locales_path']) || !isset($settings['locales_path'])) {
	$settings['locales_path'] = '/engine/inc/maharder/_locales';
}

if (empty($settings['cache_timer']) || !isset($settings['cache_timer'])) {
	$settings['cache_timer'] = 60;
}

if (empty($settings['theme']) || !isset($settings['theme'])) {
	$settings['theme'] = 'light';
}

DataManager::saveConfig('maharder',  $settings);
clear_cache();

echo 'ok';
