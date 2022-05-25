<?php

$logs = [
	'2.0.0' => [
		'[NEW|PAID] Вывод списка серий',
		'[NEW|PAID] Добавлен парсер информации',
		'[UPDATE] Работа с MyShows API 2.0',
		'[UPDATE] Обновление до DLE 15.1',
		'[UPDATE] Обновление функционала и совместимость с последней версией MHAdmin (2.0.3)',
	],
	'1.0.2.3' => [
		'Бесплатная и устаревшая версия модуля MyStatus',
	]
];

$modVars = [
	'title' => 'История изменений',
	'module_icon' => 'fad fa-robot',
	'logs' => $logs,
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => $links['changelog']['href'],
];

$htmlTemplate = 'modules/admin/changelog.html';