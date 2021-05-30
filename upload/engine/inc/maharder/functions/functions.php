<?php

// File: functions.php
// Path: /engine/inc/maharder/admin/modules/functions.php
// ============================================================ =
// Author: Maxim Harder <dev@devcraft.club> (c) 2020
// Website: https://www.devcraft.club
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!
//===============================================================

function letsCurl($section, $vars = array('request' => 'GET', 'header' => [], 'post' => []))
{
	$config = getConfig(ENGINE_DIR.'/inc/maharder/config', 'maharder');

	$curl = curl_init();
	$header = $vars['header'];
	$request = $vars['request'];
	$post = $vars['post'];

	$headerOptions = [
		'Content-Type: application/x-www-form-urlencoded',
		"x-api-key: {$config['api_key']}",
	];

	if (!empty($header)) {
		foreach ($header as $head) {
			$headerOptions[] = $head;
		}
	}

	$curlOptions = [
		CURLOPT_URL => "{$config['api_url']}/{$section}/",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_HTTPHEADER => $headerOptions,
		CURLOPT_CUSTOMREQUEST => $request,
	];
	if (!empty($post)) {
		$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($post);
	}
	curl_setopt_array($curl, $curlOptions);

	$response = curl_exec($curl);

	curl_close($curl);

	return $response;
}

function htmlStatic($data, $view = 'html', $type = 'css')
{
	$out = [];
	if ('html' == $view) {
		if (is_array($data)) {
			foreach ($data as $d) {
				if ('css' == $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
				} elseif ('js' == $type) {
					$out[] = "<script src='{$d}'></script>";
				}
			}
		} else {
			if ('css' == $type) {
				$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
			} elseif ('js' == $type) {
				$out[] = "<script src='{$data}'></script>";
			}
		}
	} elseif ('link' == $view) {
		if (is_array($data)) {
			foreach ($data as $d) {
				$out[] = $d;
			}
		} else {
			$out[] = $data;
		}
	}

	return $out;
}

function getXfields($id, $type = 'post')
{
	global $db;
	if ('post' === $type) {
		$post = letsCurl('post', 'GET', ['id' => $id]);
	} elseif ('user' === $type) {
		$post = letsCurl('users', 'GET', ['user_id' => $id]);
	}

	$post = json_decode($post, true);

	if ($post) {
		$xfout = [];
		$fields = explode('||', $post['xfields']);
		foreach ($fields as $key => $value) {
			$xfout[$key] = $value;
		}
	} else {
		$xfout = false;
	}

	return $xfout;
}

function loadXfields($type = 'post')
{
	if ('post' === $type) {
		$xf_file = file(ENGINE_DIR.'/data/xfields.txt');
	} elseif (('user' === $type)) {
		$xf_file = file(ENGINE_DIR.'/data/xprofile.txt');
	}

	$xf_info = [];
	foreach ($xf_file as $line) {
		$info = explode('|', $line);
		$xf_info[$info[0]] = $info[1];
	}

	return $xf_info;
}

function getUsers()
{

	$users = letsCurl('users');
	$user_ar = [];

	foreach ($users as $uid => $user) {
		$user_ar[$user['user_id']] = $user['name'];
	}

	return $user_ar;
}

function getCats()
{

	$cats = letsCurl('category', 'GET', ['orderby' => 'name']);
	$categories = [];
	while ($entry = $db->get_array($cats)) {
		$categories[$entry['id']] = $entry['name'];
	}

	return $categories;
}

/**
 * Получаем настройки модуля, если такие имеются.
 * Возвращает массив данных.
 *
 * @param string $path		#	Путь до конфигурации файла
 * @param string $codename	#	Название модуля, а так-же название конфигурации; без обозначений
 * @param string $confName	#	Если сохранилась конфигурация в папке /engine/data/, то указать название массива без знака $
 *
 * @return array
 */
function getConfig($path, $codename, $confName = '')
{
	$settings = [];

	if (file_exists($path.DIRECTORY_SEPARATOR.$codename.'.json')) {
		$settings = json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$codename.'.json'), true);
		foreach ($settings as $name => $value) {
			$settings[$name] = htmlspecialchars_decode($value);
		}
	} else {
		if (!empty($confName)) {
			$oldConfig = ENGINE_DIR.'/data/'.$codename.'.php';
			if (file_exists($oldConfig)) {
				$oldFile = file_get_contents((DLEPlugins::Check($oldConfig)));
				$oldFile = str_replace("${$confName} = ", 'return ', $oldFile);
				file_put_contents($oldConfig, $oldFile);
				$oldSettings = include DLEPlugins::Check($oldConfig);
				file_put_contents($path.DIRECTORY_SEPARATOR.$codename.'.json', $oldSettings);
				@unlink($oldConfig);

				foreach($oldSettings as $set => $val) $settings[$set] = $val;
			}
		}
	}

	return $settings;
}

function upload_file() {

}
