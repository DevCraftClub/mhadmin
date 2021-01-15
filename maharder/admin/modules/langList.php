<?php

// Настройка хлебных крошек
// Крошки это массив с массивами, которые содержат информацию о ссылке (url) и её названии (name)
// Крошки добавляются в каждом файле модуля с исключением самого главного
$breadcrumbs[] = [
	'name' => 'Список языков',
	'url' => THIS_SELF.'?sites=langlist',
];

$modVars = [
	'title' => 'Список языков',
	'module_icon' => 'fad fa-flag',
	'langlist' => [
		[
			'id' => 1,
			'code' => 'ru_RU',
			'iso2' => 'ru',
			'name' => 'Русский',
			'int' => 'Russian',
			'flag' => 'flag_ru.png',
			'folder' => 'Russian',
			'active' => true,
		],
		[
			'id' => 2,
			'code' => 'ru_RU',
			'iso2' => 'ru',
			'name' => 'Русский',
			'int' => 'Russian',
			'flag' => 'flag_ru.png',
			'folder' => 'Russian',
			'active' => true,
		],
		[
			'id' => 3,
			'code' => 'ru_RU',
			'iso2' => 'ru',
			'name' => 'Русский',
			'int' => 'Russian',
			'flag' => 'flag_ru.png',
			'folder' => 'Russian',
			'active' => true,
		],
	],
];

$htmlTemplate = 'modules/langList.html';