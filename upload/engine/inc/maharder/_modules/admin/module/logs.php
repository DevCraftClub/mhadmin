<?php
//===============================================================
// Файл: logs.php                                               =
// Путь: engine/inc/maharder/admin/modules/admin/logs.php       =
// Дата создания: 2024-04-08 14:47:06                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

global $mh, $MHDB;

$mh_config = DataManager::getConfig('maharder');
$mh_logs = $MHDB->getAll(MhLog::class);

$cur_page    = $_GET['page'] ?? 1;
$total_pages = (int) @ceil(count($mh_logs) / $mh_config['list_count']);
$start       = isset($_GET['page']) ? (((int) $cur_page - 1) * $mh_config['list_count']) : 0;
$order       = $_GET['order'] ?? 'time';
$sort        = $_GET['sort'] ?? 'ASC';

$logs_data = $MHDB->paginate(MhLog::class, $order, $sort,  $mh_config['list_count'], $cur_page);

$modVars = [
	'title'       => __('mhadmin', 'Вывод логов'),
	'logs'        => $logs_data,
	'total_pages' => $total_pages,
	'page'        => $cur_page,
	'order'       => $order,
	'sort'        => $sort,
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url'  => THIS_SELF . '?sites=logs&order=' . $order . '&sort=' . $sort,
];
if ($cur_page > 1) {
	$breadcrumbs[] = [
		'name' => __('mhadmin', 'Страница %page%', ['%page%' => $cur_page]),
		'url'  => THIS_SELF . '?sites=logs&page='. $cur_page . 'order=' . $order . '&sort=' . $sort,
	];
}

$htmlTemplate = 'admin/logs.html';
