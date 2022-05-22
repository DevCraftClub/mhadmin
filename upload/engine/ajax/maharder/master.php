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
	$data = [];

	foreach ($save_con as $id => $d) {
		$name = $d['name'];
		$value = $d['value'];
		$value = htmlspecialchars($value);
		$data[$name] = $value;
	}
	$data = filter_var_array($data);

	switch ($method) {
		case 'settings':
			if (!mkdir($concurrentDirectory = ENGINE_DIR . '/inc/maharder/_config', 0777, true)
				&& !is_dir(
					$concurrentDirectory
				)) {
				$mh->generate_log('', 'save_setting', serialize(sprintf('Папка "%s" не была создана',
																				$concurrentDirectory)));
			}
			$file = $concurrentDirectory.'/'.$_POST['module'].'.json';


			if (empty($data['list_count']) || !isset($data['list_count'])) {
				$data['list_count'] = $config['news_number'];
			}


			$data = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			file_put_contents($file, $data);
			clear_cache();

			echo 'ok';

			break;


	}
