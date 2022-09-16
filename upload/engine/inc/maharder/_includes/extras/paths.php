<?php

	if ( ! defined('DATALIFEENGINE')) {
		header('HTTP/1.1 403 Forbidden');
		header('Location: ../../../../../');

		exit('Hacking attempt!');
	}

	$current_dir = __DIR__;
	if (preg_grep('/(cache)/', explode("\n", $current_dir))) $current_dir = dirname(__FILE__, 5);
	else $current_dir = dirname(__FILE__, 6);

	if ( ! defined('ROOT')) {
		define("ROOT", $current_dir);
	}
	if ( ! defined('MH_ROOT')) {
		define("MH_ROOT", ROOT.'/engine/inc/maharder');
	}
	if ( ! defined('MH_ADMIN')) {
		define("MH_ADMIN", MH_ROOT.'/admin');
	}

	$mh_loader_paths = [
		MH_ROOT.'/_includes/classes',
		MH_ROOT.'/_includes/traits',
		// Custom paths //
	];

	if(!function_exists('__')) {
		function __($mod, $phrase, $arr = [], $c = 0){
			return $phrase;
		}
	}

	include_once MH_ROOT.'/_includes/extras/mhLoader.php';
	include_once ENGINE_DIR.'/inc/maharder/_includes/vendor/autoload.php';
