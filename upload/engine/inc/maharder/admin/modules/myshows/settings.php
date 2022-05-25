<?php

//===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
// Mod: MyStatus
// File: main.php
// Path: /engine/inc/maharder/admin/modules/mystatus/main.php
// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  =
// Author: Maxim Harder <dev@devcraft.club> (c) 2022
// Website: https://devcraft.club
// Telegram: http://t.me/MaHarder
// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  =
// Do not change anything!
//===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===

global $mh, $config;

$mh_config = $mh->getConfig('maharder');
$mystatus_cfg = $mh->getConfig('mystatus');

$modVars = [
	'title' => 'Настройки модуля',
	'settings' => $mystatus_cfg,
	'xfields' => $mh->loadXfields(),
	'users' => $mh->getUsers()
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => $links['settings']['href'],
];

$htmlTemplate = 'modules/myshows/settings.html';
