<?php

// заполняем важную и нужную инофрмацию о модуле


$modInfo = [
	'module_name' => 'MaHarder Assets',
	'module_version' => '2.0.4',
	'module_description' => 'Административная панель для моих разработок',
	'module_code' => 'maharder',
	'module_icon' => 'fad fa-robot',
	'site_link' => 'https://devcraft.club/downloads/maharder-assets.4/',
	'docs_link' => 'https://devcraft.club/articles/maharder-assets.10/',
	'dle_config' => $config,
];

// Подключаем классы, функции и основные переменные
include_once DLEPlugins::Check(__DIR__.'/maharder/admin/index.php');
include_once DLEPlugins::Check(MH_ROOT.'/_includes/classes/Admin.php');

$mh = new Admin();

// Подключаем переменные модуля и его функционал
// Используем переменную sites для навигации в модуле
switch ($_GET['sites']) {
	// Страница с с генератором модуля
	case 'new_module':
		require_once DLEPlugins::Check(MH_ADMIN.'/modules/admin/new_module.php');
		break;
	// Страница с логами изменений
	case 'changelog':
		require_once DLEPlugins::Check(MH_ADMIN.'/modules/admin/changelog.php');
		break;
	// Главная страница
	default:
		require_once DLEPlugins::Check(MH_ADMIN.'/modules/admin/main.php');
		break;
}

$links['new_module'] =	[
		'name' => 'Генератор модулей',
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'].'&sites=new_module',
		'type' => 'link',
		'children' => [],
	];

$xtraVariable = [
	'links' => $links,
	'breadcrumbs' => $breadcrumbs,
	'settings' => $mh->getConfig($modInfo['module_code']),
];

$mh->setVars($modInfo);
$mh->setLinks($links);
$mh->setVars($xtraVariable);
$mh->setVars($modVars);

// Загружаем шаблон
$template = $mh_template->load($htmlTemplate);

// Отображаем всё на сайте
// При помощи array_merge() можно объединить любое кол-во массивов
echo $template->render($mh->getVariables());
