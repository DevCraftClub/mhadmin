<?php

if (!$is_logged) {
	exit('error');
}

if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
	exit('error');
}

if (empty($data['name']) || empty($data['description']) || empty($data['version'])) {
	echo json_encode(                                                                                                         [
		                                                                                                                          'status'  => 'failed',
		                                                                                                                          'success' => [],
		                                                                                                                          'failed'  => [],
		                                                                                                                          'data'    => $data,
		                                                                                                                          'message' => 'Нужные данные не были заполнены'
	                                                                                                                          ],
	                                                                                                                          JSON_UNESCAPED_UNICODE);
} else {

	if (empty($data['translit'])) $data['translit'] = totranslit(stripslashes($data['name']), true, false);
	$data['translit'] = totranslit(stripslashes($data['translit']), true, false);

	if (empty($data['icon'])) $data['icon'] = 'fad fa-cogs';

	$dirs = [
		ROOT_DIR . '/engine/ajax/maharder/' . $data['translit'],
		ROOT_DIR . '/engine/inc/maharder/' . $data['translit'],
		ROOT_DIR . '/engine/inc/maharder/admin/modules/' . $data['translit'],
		ROOT_DIR . '/engine/inc/maharder/admin/assets/img/' . $data['translit'],
		ROOT_DIR . '/engine/inc/maharder/admin/templates/modules/' . $data['translit'],
	];

	$files = [
		[
			ROOT_DIR . '/engine/ajax/maharder/' . $data['translit'] . '/master.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/ajax_master.php.txt',
			644
		],
		[
			ROOT_DIR . '/engine/inc/' . $data['translit'] . '.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/inc_admin.php.txt',
			644
		],
		[
			ROOT_DIR . '/engine/inc/maharder/admin/modules/' . $data['translit'] . '/main.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/modules_main.php.txt',
			644
		],
		[
			ROOT_DIR . '/engine/inc/maharder/admin/templates/modules/' . $data['translit'] . '/main.html',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/templates_main.html.txt',
			644
		]
	];

	$success = [
		'dirs'   => [],
		'files'  => [],
		'plugin' => []
	];
	$fails = [
		'dirs'   => [],
		'files'  => [],
		'plugin' => []
	];

	foreach ($dirs as $dir) {
		if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
			LogGenerator::generate_log('maharder', 'new_module', serialize(sprintf('Directory "%s" was not created', $dir)));
			$fails['dirs'][] = [
				'dir'     => $dir,
				'message' => 'Ошибка во время создания папки'
			];
			continue;
		} elseif (is_dir($dir)) {
			$fails['dirs'][] = [
				'dir'     => $dir,
				'message' => 'Такая папка уже существует'
			];
			continue;
		}
		$success['dirs'][] = $dir;
	}

	foreach ($files as $file) {
		if (file_exists($file[0]) && ((int)$data['override'] !== 1 || $data['override'] !== 'on')) {
			$fails['files'][] = [
				'file'    => $file[0],
				'message' => 'Данный файл уже существует.'
			];
			continue;
		}
		try {
			@touch($file[0]);

			$file_data = file_get_contents($file[1]);
			$file_data = preg_replace('/%latin%/', $data['translit'], $file_data);
			$file_data = preg_replace('/%name%/', $data['name'], $file_data);
			$file_data = preg_replace('/%version%/', $data['version'], $file_data);
			$file_data = preg_replace('/%description%/', $data['description'], $file_data);
			$file_data = preg_replace('/%icon%/', $data['icon'], $file_data);
			$file_data = preg_replace('/%link%/', $data['link'], $file_data);
			$file_data = preg_replace('/%docs%/', $data['docs'], $file_data);
			$file_data = preg_replace('/%path%/', $file[0], $file_data);
			$file_data = preg_replace('/%year%/', date('Y'), $file_data);

			file_put_contents($file[0], $file_data);
			chmod($file[0], $file[2]);
			$success['files'][] = $file[0];

		} catch (Exception $e) {
			$fails['files'][] = [
				'file'    => $file[0],
				'message' => $e->getMessage()
			];
		}
	}

	if ((int)$data['db'] === 1) {
		$dle_connector_name = $db->safesql(htmlspecialchars(trim($data['name']), ENT_QUOTES, $config['charset']));
		$dle_connector_description = $db->safesql(htmlspecialchars(trim($data['description']), ENT_QUOTES, $config['charset']));
		$dle_connector_version = $db->safesql(htmlspecialchars(trim($data['version']), ENT_QUOTES, $config['charset']));
		$dle_connector_dleversion = $db->safesql(htmlspecialchars(trim($config['version_id']), ENT_QUOTES, $config['charset']));
		$dle_connector_dleversion_compare = $db->safesql(trim('>='));
		$dle_connector_active = 1;
		$dle_connector_posi = 1;

		$icon = $db->safesql(clearfilepath(htmlspecialchars(trim($data['plugin_icon']), ENT_QUOTES, $config['charset']), [
			"gif",
			"jpg",
			"jpeg",
			"png",
			"webp"
		]));

		$dle_mysqlupgrade = "INSERT INTO {prefix}_admin_sections (name, title, descr, icon, allow_groups) VALUES ('{$data['translit']}', '{$data['name']} v{$data['version']}', '{$data['description']}', '{$icon}', '1, 2') ON DUPLICATE KEY UPDATE title = '{$data['name']} v{$data['version']}';";
		$dle_mysqlenable = $dle_mysqlupgrade;
		$dle_mysqldisable = "DELETE FROM {prefix}_admin_sections WHERE name = '{$data['translit']}';";
		$dle_mysqldelete = $dle_mysqldisable;

		try {

			$plugin = $db->query('SELECT * FROM ' . PREFIX . "_plugins WHERE name = '{$dle_connector_name}'");
			if ($plugin->num_rows === 0) {
				$db->query('INSERT INTO ' . PREFIX . "_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, filelist, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) VALUES ('{$dle_connector_name}', '{$dle_connector_description}', '{$icon}', '{$dle_connector_version}', '{$dle_connector_dleversion}', '{$dle_connector_dleversion_compare}', $dle_connector_active, '', '{$dle_mysqlupgrade}', '{$dle_mysqlenable}', '{$dle_mysqldisable}', '{$dle_mysqldelete}', 1, '', '', '', '', '', '', '', '', '', 0)");
				$plugin_id = $db->insert_id();
				$db->query("INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('" . $db->safesql($member_id['name']) . "', '{$_TIME}', '{$_IP}', '116', '{$name}')");
				execute_query($plugin_id, $dle_mysqlenable);

				$success['plugin'][] = [
					'link' => "{$config['']}{$config['']}?mod=plugins&action=edit&id={$plugin_id}",
					'name' => "{$data['name']} v{$data['version']}"
				];
			} else {
				$fails['plugin'][] = [
					'message' => "Плагин уже существует"
				];
			}
		} catch (Exception $e) {
			$fails['plugin'][] = [
				'message' => $e->getMessage(),
			];
		}

	}

	clear_cache();

	echo json_encode(                                                                                                         [
		                                                                                                                          'status'  => 'success',
		                                                                                                                          'success' => $success,
		                                                                                                                          'failed'  => $fails
	                                                                                                                          ],
	                                                                                                                          JSON_UNESCAPED_UNICODE);

}