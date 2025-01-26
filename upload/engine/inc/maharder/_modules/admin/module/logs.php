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

use Spiral\Pagination\Paginator;
use Cycle\Database\Injection\Parameter;

$filterKeys = ['filter_plugin' => FILTER_REQUIRE_ARRAY, 'filter_type' => FILTER_REQUIRE_ARRAY, 'filter_fn' => FILTER_REQUIRE_ARRAY];
$inputFilters = TwigFilter::getDefaultFilters($filterKeys);
$GET_DATA = filter_input_array(INPUT_GET, $inputFilters);

foreach ($filterKeys as $key => $filter) {
	$GET_DATA[$key] = isset($_GET[$key]) ? DataManager::sanitizeArrayInput(
		$_GET[$key],
		[FILTER_SANITIZE_FULL_SPECIAL_CHARS]
	) : null;
}

$mh_config = DataManager::getConfig('maharder');

$repo        = $MHDB->repository(MhLog::class);
$twigFilter  = new TwigFilter($repo);
$whereClause = null;

$filters = [];
if ($GET_DATA['filter_plugin']) $filters[] = [
	'plugin' => ['in' => new Parameter($GET_DATA['filter_plugin'])]
];
if ($GET_DATA['filter_type']) $filters[] = [
	'log_type' => ['in' => new Parameter($GET_DATA['filter_type'])]
];
if ($GET_DATA['filter_fn']) $filters[] = [
	'fn_name' => ['in' => new Parameter($GET_DATA['filter_fn'])]
];

if (count($filters)) $whereClause['@and'] = $filters;
$mh_logs = $repo->select()->where($whereClause);

$cur_page    = $GET_DATA['page'] ?? 1;
$total_pages = (int)@ceil($mh_logs->count() / $mh_config['list_count']);
$start       = isset($GET_DATA['page']) ? (((int)$cur_page - 1) * $mh_config['list_count']) : 0;
$order       = $GET_DATA['order'] ?? 'time';
$sort        = TwigFilter::getSort($GET_DATA['sort'] ?? 'DESC');
$mh_logs     = $mh_logs->orderBy($order, $sort);
$paginator   = new Paginator($mh_config['list_count']);
$paginator->withPage($cur_page)->paginate($mh_logs);

$modVars = [
	'title'       => __('Вывод логов'),
	'logs'        => $mh_logs->fetchAll(),
	'total_pages' => $total_pages,
	'page'        => $cur_page,
	'order'       => $order,
	'sort'        => $sort,
	'filters'     => array_merge(
		$twigFilter->createFilter('plugin', 'tags', __('Плагин')),
		$twigFilter->createFilter('type', 'tags', __('Тип'), 'log_type'),
		$twigFilter->createFilter('fn', 'tags', __('Функция'), 'fn_name'),
	)
];

$mh->setBreadcrumb(new BreadCrumb($modVars['title'], THIS_SELF . '?' . http_build_query($GET_DATA)));

if ($cur_page > 1) {
	$mh->setBreadcrumb(new BreadCrumb(__('mhadmin', 'Страница %page%', ['%page%' => $cur_page]), THIS_SELF . '?' . http_build_query($GET_DATA)));
}

$htmlTemplate = 'admin/logs.html';
