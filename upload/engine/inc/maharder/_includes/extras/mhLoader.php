<?php

	@ini_set('pcre.recursion_limit', 10000000 );
	@ini_set('pcre.backtrack_limit', 10000000 );
	@ini_set('pcre.jit', false);

	if (!function_exists('dirToArray')) {
		function dirToArray($dir, ...$except) {
			$result = [];

			$xcpt = [
				'.',
				'..',
				'.htaccess',
			];
			if ($except) {
				foreach ($except as $ex) $xcpt = array_merge_recursive($xcpt, $ex);
			}

			foreach (scandir($dir, SCANDIR_SORT_NONE) as $key => $value) {
				if ( ! in_array($value, $xcpt, true)) {
					if (is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
						$result[$value] = dirToArray($dir.DIRECTORY_SEPARATOR.$value);
					} else {
						$result[] = $value;
					}
				}
			}

			return $result;
		}
	}

	spl_autoload_register(function ($class_name) {
		global $mh_loader_paths;

		$found = false;

		foreach ($mh_loader_paths as $path) {
			$dir_data = dirToArray($path);
			foreach ($dir_data as $data) {
				if ($class_name ===  str_replace('.php', '', $data)) {
					include_once DLEPlugins::Check("{$path}/{$data}");
					$found = true;
					break;
				}
			}
			if ($found) break;
		}

	});
