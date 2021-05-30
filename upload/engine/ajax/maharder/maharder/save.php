<?php

	//	===============================
	//	Настройки модуля | сохраняем
	//	===============================
	//	Автор: Maxim Harder
	//	Сайт: https://maxim-harder.de
	//	Телеграм: http://t.me/MaHarder
	//	===============================
	//	Ничего не менять
	//	===============================
	if (!defined('DATALIFEENGINE')) {
		header('HTTP/1.1 403 Forbidden');
		header('Location: ../../../../');
		exit('Hacking attempt!');
	}

	if (!$is_logged) {
		exit('error');
	}

	if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
		exit('error');
	}

	$method = $_POST['method'];
	if (!$method) {
		exit();
	}
	$save_con = $_POST['data'];

	switch ($method) {
		default:
		case 'settings':
			if (!mkdir($concurrentDirectory = ENGINE_DIR . '/inc/maharder/config', 0777, true)
				&& !is_dir(
					$concurrentDirectory
				)) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
			}
			$file = $concurrentDirectory.'/'.$_POST['module'].'.json';
			$arr = [];

			foreach ($save_con as $id => $d) {
				$name = $d['name'];
				$value = $d['value'];
				$value = htmlspecialchars($value);
				$arr[$name] = $value;
			}

			if (empty($arr['list_count']) || !isset($arr['list_count'])) {
				$arr['list_count'] = $config['news_number'];
			}


			$arr = json_encode($arr, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			file_put_contents($file, $arr);
			clear_cache();

			echo 'ok';

			break;
	}
