<?php


$modVars = [
	'title' => 'Генератор модулей',
];

// Настройка хлебных крошек
// Крошки это массив с массивами, которые содержат информацию о ссылке (url) и её названии (name)
// Крошки добавляются в каждом файле модуля с исключением самого главного
$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => THIS_SELF.'?sites=new_module',
];

$htmlTemplate = 'modules/admin/new_module.html';