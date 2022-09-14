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

	global $is_logged, $dle_login_hash, $mh_admin, $method, $data;

	if (!$is_logged) {
		exit('error');
	}

	if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
		exit('error');
	}

	require_once DLEPlugins::Check(__DIR__ . '/_functions.php');

	if (!$method) {
		exit();
	}

	$save_con = [];

	foreach (filter_var_array($data['data']) as $id => $d) {
		$name = $d['name'];
		$value = $d['value'];
		$value = htmlspecialchars($value);
		$save_con[$name] = $value;
	}

	switch ($method) {
		case 'settings':
			include DLEPlugins::Check(__DIR__ . '/_settings.php');

			break;

		case 'new_module':
			include DLEPlugins::Check(__DIR__ . '/_new_module.php');

			break;

		case 'check_assets':
			try {
				echo json_encode($mh_admin->checkAssets(),  JSON_UNESCAPED_UNICODE);
			} catch (\Exception $e) {
				LogGenerator::generate_log('maharder', 'check_assets', $e->getMessage());
				echo json_encode([]);
			}


			break;

		case 'save_asset':

			try {
				echo json_encode($mh_admin->save_asset($data['data'], $data['file']), JSON_UNESCAPED_UNICODE);
			} catch (\Exception $e) {
				LogGenerator::generate_log('maharder', 'save_asset', $e->getMessage());
				echo json_encode([]);
			}
			sleep(0.5);
			break;

		case 'check_update':

			try {
				$mh_admin->setRecourceId($data['resource_id']);
				echo json_encode($mh_admin->checkUpdate(), JSON_UNESCAPED_UNICODE);
			} catch (Exception $e) {
				LogGenerator::generate_log('maharder', 'check_update', $e->getMessage());
				echo json_encode([]);
			}

			break;
	}
