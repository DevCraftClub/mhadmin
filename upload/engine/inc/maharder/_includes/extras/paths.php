<?php

if (!defined('DATALIFEENGINE')) {
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../../../../../');

	exit('Hacking attempt!');
}

if(!defined('MH_ROOT')) define("MH_ROOT", ENGINE_DIR . '/inc/maharder');
if(!defined('MH_ADMIN')) define("MH_ADMIN", MH_ROOT . '/admin');
if(!defined('ROOT')) define("ROOT", ROOT_DIR);

include_once ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php';
include_once __DIR__ . '/autoloader.php';