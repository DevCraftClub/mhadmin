<?php

global $db, $modInfo, $mh, $links;

$g = $modInfo['_get'];
$s = $mh->getConfig($modInfo['module_code']);
if (count($s) === 0) $s = $mh->getConfig('maharder');

switch ($g['action']) {
	default:
		$now_page = $g['page'] ?: 0;
		$count_start = $now_page * $s['list_count'];
		$count_stop = $count_start + $s['list_count'];

		$services = collectServices();
		$sql_all_services = 'SELECT * FROM ' . PREFIX . "_repost_service LIMIT {$count_start},{$count_stop}";
		$services_entries = $db->query($sql_all_services);
		$total_services = $services_entries->num_rows;

		$modVars = [
			'title' => 'Соц. подключения',
			'services' => $services,
			'total_services' => $total_services,
			'service_entries' => $services_entries
		];

		$breadcrumbs[] = [
			'name' => $modVars['title'],
			'url' => $links['services']['href'],
		];

		$htmlTemplate = 'modules/repost/services.html';
		break;

	case 'edit':
	case 'new':

		if(!isset($g['service']) || empty($g['service'])) {
			header("Location: {$links['services']['href']}", true, 302);
			$htmlTemplate = 'modules/repost/services.html';
		}

		require_once(DLEPlugins::Check(getRepostDir()->path . "/services/{$g['service']}/config.php"));
		break;
}

