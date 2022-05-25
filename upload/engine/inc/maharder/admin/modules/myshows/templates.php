<?php

global $mh;

$mh->setCss(URL . '/maharder/admin/assets/css/bootstrap-suggest.css');
$mh->setJs(URL . '/maharder/admin/assets/js/bootstrap-suggest.min.js');

$modVars = [
	'title' => 'Настройка шаблонов',
	'template_data' => $mh->getConfig('mystatus_templates')
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => $links['templates']['href'],
];

$htmlTemplate = 'modules/myshows/templates.html';