<?php
//===============================================================
// Файл: maharder.php                                           =
// Путь: engine/inc/maharder.php                                =
// Дата создания: 2024-03-19 14:53:30                           =
// Последнее изменение: 2024-03-19 14:53:30                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

global $breadcrumbs, $mh, $modVars, $mh_template, $htmlTemplate, $config;

use Symfony\Bridge\Twig\Extension\TranslationExtension;

// заполняем важную и нужную информацию о модуле
$modInfo = [
	'module_name'        => 'MaHarder Assets',
	'module_version'     => '173.3.0',
	'module_description' => __('mhadmin', 'Административная панель для моих разработок'),
	'module_code'        => 'maharder',
	'module_id'          => 4,
	'module_icon'        => 'fa-duotone fa-solid fa-robot',
	'site_link'          => 'https://devcraft.club/downloads/maharder-assets.4/',
	'docs_link'          => 'https://readme.devcraft.club/latest/dev/mhadmin/install/',
	'dle_config'         => $config,
	'crowdin_name'       => 'mhаdmin',
	'get'				 => filter_input_array(INPUT_GET)
];

// Подключаем классы, функции и основные переменные
include_once DLEPlugins::Check(__DIR__.'/maharder/admin/index.php');

// Подключаем переменные модуля и его функционал
// Используем переменную sites для навигации в модуле
switch ($_GET['sites']) {
	// Страница с выводом логов
	case 'logs':
		require_once DLEPlugins::Check(MH_ROOT.'/_modules/admin/module/logs.php');
		break;
	// Страница с генератором модуля
	case 'new_module':
		require_once DLEPlugins::Check(MH_ROOT.'/_modules/admin/module/new_module.php');
		break;
	// Страница с логами изменений
	case 'changelog':
		require_once DLEPlugins::Check(MH_ROOT.'/_modules/admin/module/changelog.php');
		break;
	// Главная страница
	default:
		require_once DLEPlugins::Check(MH_ROOT.'/_modules/admin/module/main.php');
		break;
}

$links['new_module'] =	[
		'name' => __('mhadmin', 'Генератор модулей'),
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'].'&sites=new_module',
		'type' => 'link',
		'children' => [],
	];

$links['logs'] =	[
		'name' => __('mhadmin', 'Вывод логов'),
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'].'&sites=logs',
		'type' => 'link',
		'children' => [],
	];

$xtraVariable = [
	'links'       => $links,
	'breadcrumbs' => $breadcrumbs,
	'settings'    => DataManager::getConfig($modInfo['module_code']),
];

$mh->setVars($modInfo);
$mh->setLinks($links);
$mh->setVars($xtraVariable);
$mh->setVars($modVars);

// Устанавливаем язык панели
$mh_template->addExtension(new TranslationExtension(MhTranslation::getTranslator('mhadmin')));

// Загружаем шаблон
$template = $mh_template->load($htmlTemplate);

// Отображаем всё на сайте
// При помощи array_merge() можно объединить любое кол-во массивов
echo $template->render($mh->getVariables());
