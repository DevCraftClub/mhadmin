<?php
//===============================================================
// Файл: master.php                                             =
// Путь: engine/ajax/maharder/maharder/master.php               =
// Дата создания: 2024-03-19 13:40:12                           =
// Последнее изменение: 2024-03-19 13:40:11                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

global $parsedData;
if (!defined('DATALIFEENGINE')) {
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../../../../');
	exit('Hacking attempt!');
}

global $mh_admin, $method, $data;
require_once DLEPlugins::Check(__DIR__ . '/_functions.php');

if (!$method) {
	echo (new ErrorResponseAjax())->setData(__('Метод запроса не установлен!'))->send();
	exit;
}

$save_con = filter_var_array($parsedData);

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
			LogGenerator::generateLog('mhadmin', 'check_assets', $e->getMessage());
			echo json_encode([]);
		}


		break;

	case 'save_asset':

		try {
			echo json_encode($mh_admin->save_asset($save_con['data'], $save_con['file']), JSON_UNESCAPED_UNICODE);
		} catch (\Exception $e) {
			LogGenerator::generateLog('mhadmin', 'save_asset', $e->getMessage());
			echo json_encode([]);
		}
		sleep(0.5);
		break;

	case 'check_update':

		try {
			$mh_admin->setRecourceId($save_con['resource_id']);
			echo json_encode($mh_admin->checkUpdate(), JSON_UNESCAPED_UNICODE);
		} catch (Exception $e) {
			LogGenerator::generateLog('mhadmin', 'check_update', $e->getMessage());
			echo json_encode([]);
		}

		break;

	case 'delete_log':

		try {
			$log = new MhDB(MhLog::class);
			echo json_encode($log->delete($data['id']), JSON_UNESCAPED_UNICODE);
		} catch (Exception $e) {
			LogGenerator::generateLog('mhadmin', 'delete_log', $e->getMessage());
			echo '';
		} catch (Throwable $e) {
		}

		break;
}
