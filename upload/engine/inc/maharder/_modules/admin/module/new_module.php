<?php

global $mh;


$modVars = [
	'title' => __('Генератор модулей'),
];

// Настройка хлебных крошек
// Крошки это массив с массивами, которые содержат информацию о ссылке (url) и её названии (name)
// Крошки добавляются в каждом файле модуля с исключением самого главного
$mh->setBreadcrumb(new BreadCrumb($modVars['title'], $mh->getLinkUrl('new_module')));

$htmlTemplate = 'admin/new_module.html';
