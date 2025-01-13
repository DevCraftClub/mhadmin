<?php

@ini_set('pcre.recursion_limit', 10000000);
@ini_set('pcre.backtrack_limit', 10000000);
@ini_set('pcre.jit', false);

spl_autoload_register(function ($class_name) {
	global $mh_loader_paths;

	$found = false;

	foreach ($mh_loader_paths as $path) {
		$dir_data = dirToArray($path);
		foreach ($dir_data as $data) {
			if ($class_name === str_replace('.php', '', $data)) {
				include_once DLEPlugins::Check("{$path}/{$data}");
				$found = true;
				break;
			}
		}
		if ($found) break;
	}

});
