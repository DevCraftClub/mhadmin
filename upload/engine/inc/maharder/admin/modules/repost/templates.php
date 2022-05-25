<?php

global $db, $modInfo, $mh, $links;

$g = $modInfo['_get'];
$s = $mh->getConfig($modInfo['module_code']);

$now_page = $g['page'] ?: 0;
$count_start = $now_page * $s['list_count'];
$count_stop = $count_start + $s['list_count'];
$filter_name = $g['fname'];
$filter_value = $g['fvalue'];

$repost_entries = $db->super_query('SELECT * FROM ' . PREFIX . "_repost LIMIT {$count_start},{$count_stop}");
$total_entries = count($repost_entries);
//$services

$modVars = [
	'title' => 'Шаблоны',
	'reposts' => $total_entries,
	'total_entries' => $total_entries
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => $links['templates']['href'],
];

$htmlTemplate = 'modules/repost/templates.html';