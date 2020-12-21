<?php

// Подключаем классы, функции и основные переменные
require_once './maharder/admin/index.php';

// заполняем важную и нужную инофрмацию о модуле
$modInfo = [
	'module_name' => 'Testmodule',
	'module_version' => '1.0.0',
	'module_description' => 'Testmodule description',
	'module_code' => 'test',
	'site_link' => '',
	'docs_link' => '',
];

// Добавляем новую ссылку в меню
// Новая ссылка должна быть массивом
// В массиве должны быть указаны "Название (name)", "Ссылка (href)", "Тип ссылки (type)" и "Подссылки (children)"
// Тип ссылок может быть одним из "link (просто ссылка)", "divider (разделитель)" или "dropdown (выпадающее меню - настроенно до второго уровня)"
// Подссылки имеют тот же формат, что и сами ссылки
$links[] = [
	'name' => 'История изменений',
	'href' => THIS_SELF.'?sites=changelog',
	'type' => 'link',
	'children' => [],
];

// Подключаем переменные модуля и его функционал
// Используем переменную sites для навигации в модуле
switch ($_GET['sites']) {
	// Страница с логами изменений
	case 'changelog':
		require_once MH_DIR.'/modules/changelog.php';

		break;

	default:
		require_once MH_DIR.'/modules/main.php';
		break;
}

// добавляем дополнительные параметры, что пополняются с модулей,
// в массив и подключем его затем в рендер
$xtraVariable = [
	'links' => $links,
	'breadcrumbs' => $breadcrumbs,
];

// Загружаем шаблон
$template = $mh_admin->load($htmlTemplate);

// Отображаем всё на сайте
// При помощи array_merge() можно объединить любое кол-во массивов
echo $template->render(array_merge($variables, $modVars, $modInfo, $xtraVariable));