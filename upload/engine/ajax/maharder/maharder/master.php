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

	require_once DLEPlugins::Check(__DIR__ . '/_functions.php');

	$method = $_POST['method'];
	if (!$method) {
		exit();
	}
	$save_con = filter_var_array($_POST['data']);
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
			include DLEPlugins::Check(__DIR__ . '/_settings.php');

			break;

		case 'new_module':
			include DLEPlugins::Check(__DIR__ . '/_new_module.php');

			break;

		case 'check_assets':
			try {
				echo json_encode($mh_admin->checkAssets(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			} catch (\Exception $e) {
				$mh_admin->generate_log('maharder', 'check_assets', serialize($e->getMessage()));
				echo json_encode([]);
			}


			break;

		case 'save_asset':

			try {
				echo json_encode($mh_admin->save_asset($_POST['data']['data'], $_POST['data']['file']), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			} catch (\Exception $e) {
				$mh_admin->generate_log('maharder', 'save_asset', serialize($e->getMessage()));
				echo json_encode([]);
			}
			sleep(0.5);
			break;
	}
