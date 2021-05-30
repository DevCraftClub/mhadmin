<?php

// заполняем важную и нужную инофрмацию о модуле
$modInfo = [
	'module_name' => 'MaHarder Assets',
	'module_version' => '2.0.0',
	'module_description' => 'Административная панель для моих разработок, а так-же настройка модуля мультиязычности',
	'module_code' => 'maharder',
	'site_link' => 'https://devcraft.club/downloads/maharder-assets.4/',
	'docs_link' => 'https://devcraft.club/articles/maharder-assets.10/',
	'dle_config' => $config,
	'dle_login_hash' => $dle_login_hash,
];

// Подключаем классы, функции и основные переменные
require_once DLEPlugins::Check(__DIR__.'/maharder/admin/index.php');

$mh = new MHAdmin();

// Подключаем переменные модуля и его функционал
// Используем переменную sites для навигации в модуле
switch ($_GET['sites']) {
	// Страница с логами изменений
	case 'changelog':
		require_once DLEPlugins::Check(MH_ADMIN.'/modules/changelog.php');

		break;

	default:
		require_once DLEPlugins::Check(MH_ADMIN.'/modules/main.php');
		break;
}

$xtraVariable = [
	'links' => $links,
	'breadcrumbs' => $breadcrumbs,
	'settings' => $mh->getConfig(ENGINE_DIR.'/inc/maharder/config', $modInfo['module_code']),
];

$mh->setVars($modInfo);
$mh->setLinks($links);
$mh->setVars($xtraVariable);
$mh->setVars($modVars);

// добавляем дополнительные параметры, что пополняются с модулей,
// в массив и подключем его затем в рендер
$mh->setVar('settings', $mh->getConfig(ENGINE_DIR.'/inc/maharder/config', $modInfo['module_code']));

// Загружаем шаблон
$template = $mh_template->load($htmlTemplate);

// Отображаем всё на сайте
// При помощи array_merge() можно объединить любое кол-во массивов
echo $template->render($mh->getVariables());
