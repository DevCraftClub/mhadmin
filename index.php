<?php


// Подключаем классы, функции и основные переменные
require_once './maharder/admin/index.php';

// Подключаем переменные модуля и его функционал
// Используем переменную sites для навигации в модуле

switch ($_GET['sites']) {
	// Страница с логами изменений
//	case 'changelog':


//		break;

	default:
		require_once MH_DIR . '/modules/main.php';
		break;
}

// Загружаем шаблон
$template = $mh_admin->load($htmlTemplate);

// Отображаем всё на сайте
echo $template->render(array_merge($variables, $modVars));
