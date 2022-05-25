<?php


$modVars = [
	'title' => 'Настройки модуля',
	'xfields' => $mh->loadXfields(),
	'user_xfields' => $mh->loadXfields('user'),
];

$htmlTemplate = 'modules/notifications/main.html';