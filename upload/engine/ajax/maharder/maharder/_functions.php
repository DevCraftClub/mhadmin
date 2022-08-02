<?php

if (!$is_logged) {
	exit('error');
}

if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
	exit('error');
}

if (!function_exists('clearfilepath')) {
	function clearfilepath($file, $ext = []) {

		$file = trim(str_replace(chr(0), '', (string)$file));
		$file = str_replace(['/', '\\'], '/', $file);

		$path_parts = pathinfo($file);

		if (count($ext)) {
			if (!in_array($path_parts['extension'], $ext)) return '';
		}

		$filename = normalize_name($path_parts['basename'], true);

		if (!$filename) return '';

		$parts = array_filter(explode('/', $path_parts['dirname']), 'strlen');

		$absolutes = [];

		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = normalize_name($part, false);
			}
		}

		$path = implode('/', $absolutes);

		if ($path) {
			return implode('/', $absolutes) . '/' . $filename;
		} else return '';

	}
}

if (!function_exists('execute_query')) {
	function execute_query($id, $query) {
		global $config, $db;

		if (!$query) return;

		if (version_compare($db->mysql_version, '5.6.4', '<')) {
			$storage_engine = "MyISAM";
		} else $storage_engine = "InnoDB";

		$query = str_ireplace(["{prefix}", "{userprefix}", "{charset}", "{engine}"], [PREFIX, USERPREFIX, COLLATE, $storage_engine], $query);

		$db->query_errors_list = [];

		$db->multi_query(trim($query), false);

		$id = (int)$id;

		if (count($db->query_errors_list)) {

			foreach ($db->query_errors_list as $error) {
				$db->query("INSERT INTO " . PREFIX . "_plugins_logs (plugin_id, area, error, type) values ('{$id}', '" . $db->safesql(htmlspecialchars($error['query'],
				                                                                                                                                       ENT_QUOTES,
				                                                                                                                                       $config['charset']),
				                                                                                                                      false) . "', '" . $db->safesql(htmlspecialchars($error['error'],
				                                                                                                                                                                      ENT_QUOTES,
				                                                                                                                                                                      $config['charset'])) . "', 'mysql')");
			}

		}

		$db->query_errors_list = [];

	}
}