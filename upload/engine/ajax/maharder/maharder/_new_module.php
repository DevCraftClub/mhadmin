<?php

if (!$is_logged) {
	exit('error');
}

if ('' == $_REQUEST['user_hash'] or $_REQUEST['user_hash'] != $dle_login_hash) {
	exit('error');
}

$mod_data = array_column(filter_var_array($data['data']), 'value', 'name');

if (empty($mod_data['name']) || empty($mod_data['description']) || empty($mod_data['version'])) {
	echo json_encode(
		[
			'status'  => 'failed',
			'success' => [],
			'failed'  => [],
			'data'    => $mod_data,
			'message' => __('Нужные данные не были заполнены'),
		],
		JSON_UNESCAPED_UNICODE
	);
} else {
	$mod_data['translit'] = totranslit(stripslashes($mod_data['translit']), true, false);

	if (empty($mod_data['icon'])) {
		$mod_data['icon'] = 'fad fa-cogs';
	}

	$dirs = [
		ROOT_DIR . '/engine/ajax/maharder/' . $mod_data['translit'],
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'],
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/module',
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/assets',
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/models',
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/repositories',
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/utils',
		ROOT_DIR . '/engine/inc/maharder/_templates/' . $mod_data['translit'],
	];

	$files = [
		[
			ROOT_DIR . '/engine/ajax/maharder/' . $mod_data['translit'] . '/master.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/ajax_master.php.txt',
			644,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/admin/_modules/' . $mod_data['translit'] . '/assets/.htaccess',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/assets_htaccess.txt',
			644,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/admin/_modules/' . $mod_data['translit'] . '/module/changelog.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/changelog.php.txt',
			644,
		],
		[
			ROOT_DIR . '/engine/inc/' . $mod_data['translit'] . '.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/inc_admin.php.txt',
			644,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/admin/_modules/' . $mod_data['translit'] . '/module/main.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/modules_main.php.txt',
			644,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/_templates/' . $mod_data['translit'] . '/main.html',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/templates_main.html.txt',
			644,
		],
	];

	$success = [
		'dirs'   => [],
		'files'  => [],
		'plugin' => [],
	];
	$fails   = [
		'dirs'   => [],
		'files'  => [],
		'plugin' => [],
	];

	foreach ($dirs as $dir) {
		if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
			LogGenerator::generateLog(
				'maharder',
				'new_module',
				__('Путь "%s" не был создан', ["%s" => $dir])
			);
			$fails['dirs'][] = [
				'dir'     => $dir,
				'message' => __('Ошибка во время создания папки'),
			];
			continue;
		} else if (is_dir($dir)) {
			$fails['dirs'][] = [
				'dir'     => $dir,
				'message' => __('Такая папка уже существует'),
			];
			continue;
		}
		$success['dirs'][] = $dir;
	}

	foreach ($files as $file) {
		if (file_exists($file[0]) && ((int)$mod_data['override'] !== 1 || $mod_data['override'] !== 'on')) {
			$fails['files'][] = [
				'file'    => $file[0],
				'message' => __('Данный файл уже существует.'),
			];
			continue;
		}
		try {
			@touch($file[0]);

			$file_data = file_get_contents($file[1]);
			$file_data = preg_replace('/%latin%/', $mod_data['translit'], $file_data);
			$file_data = preg_replace('/%name%/', $mod_data['name'], $file_data);
			$file_data = preg_replace('/%crowdin_name%/', $mod_data['crowdin_name'] ?? $mod_data['translit'], $file_data);
			$file_data = preg_replace('/%crowdin_state_id%/', $mod_data['crowdin_state_id'] , $file_data);
			$file_data = preg_replace('/%version%/', $mod_data['version'], $file_data);
			$file_data = preg_replace('/%description%/', $mod_data['description'], $file_data);
			$file_data = preg_replace('/%icon%/', $mod_data['icon'], $file_data);
			$file_data = preg_replace('/%link%/', $mod_data['link'], $file_data);
			$file_data = preg_replace('/%docs%/', $mod_data['docs'], $file_data);
			$file_data = preg_replace('/%path%/', $file[0], $file_data);
			$file_data = preg_replace('/%year%/', date('Y'), $file_data);

			file_put_contents($file[0], $file_data);
			chmod($file[0], $file[2]);
			$success['files'][] = $file[0];
		} catch (Exception $e) {
			$fails['files'][] = [
				'file'    => $file[0],
				'message' => $e->getMessage(),
			];
		}
	}

	$langsPath = ENGINE_DIR . '/inc/maharder/_locales';
	$langsTemplate = file_get_contents(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/module_files/module_locale.txt'));

	$targetLangDir = scandir($langsPath);

	foreach ($targetLangDir as $item) {
		// Пропускаем текущую директорию и родительский указатель
		if (in_array($item, ['.', '..']) || !is_dir($langsPath . DIRECTORY_SEPARATOR . $item)) {
			continue;
		}

		// Определяем путь до XLIFF-файла
		$localePath = "{$langsPath}/{$item}/{$mod_data['translit']}.xliff";

		if(file_exists($localePath) && ((int)$mod_data['override'] !== 1 || $mod_data['override'] !== 'on')) {
			$fails['files'][] = [
				'file'    => $localePath,
				'message' => __('Данный файл уже существует.'),
			];
			continue;
		}

		// Обрабатываем соответствие ru_RU отдельной логике
		$langPath = ($item === 'ru_RU')
			? "{$mod_data['translit']}.ru_RU"
			: "{$langsPath}/ru_RU/{$mod_data['translit']}.xliff";

		// Подготавливаем подстановки для шаблона
		$replacements = [
			'%lang_path%'   => $langPath,
			'%source_lang%' => 'ru',
			'%target_lang%' => explode('_', $item)[0],
		];

		// Выполняем замены в шаблоне
		$finalTemplate = str_replace(
			array_keys($replacements),
			array_values($replacements),
			$langsTemplate
		);

		// Сохраняем финальный файл с Lock Exclusive для защиты от других процессов
		file_put_contents($localePath, $finalTemplate, LOCK_EX);
	}

	if ((int)$mod_data['db'] === 1) {
		$dle_connector_name               = $db->safesql(
			htmlspecialchars(trim($mod_data['name']), ENT_QUOTES, $config['charset'])
		);
		$dle_connector_description        = $db->safesql(
			htmlspecialchars(trim($mod_data['description']), ENT_QUOTES, $config['charset'])
		);
		$dle_connector_version            = $db->safesql(
			htmlspecialchars(trim($mod_data['version']), ENT_QUOTES, $config['charset'])
		);
		$dle_connector_dleversion         = $db->safesql(
			htmlspecialchars(trim($config['version_id']), ENT_QUOTES, $config['charset'])
		);
		$dle_connector_dleversion_compare = $db->safesql(trim('>='));
		$dle_connector_active             = 1;
		$dle_connector_posi               = 1;

		$icon = $db->safesql(
			clearfilepath(htmlspecialchars(trim($mod_data['plugin_icon']), ENT_QUOTES, $config['charset']), [
				"gif",
				"jpg",
				"jpeg",
				"png",
				"webp",
			])
		);

		if (!class_exists('DLE_API')) {
			require_once ENGINE_DIR . '/api/api.class.php';
		}

		$dle_api = new DLE_API();
		$dle_api->db = $db;
		$dle_api->dle_config = $config;

		$dle_api->install_admin_module($mod_data['translit'], "{$mod_data['name']} v{$mod_data['version']}", $mod_data['description'], $icon, '1,2');

		$dle_mysqlupgrade = addslashes("INSERT INTO {prefix}_admin_sections (name, title, descr, icon, allow_groups) VALUES ('{$mod_data['translit']}', '{$mod_data['name']} v{$mod_data['version']}', '{$mod_data['description']}', '{$icon}', '1, 2') ON DUPLICATE KEY UPDATE title = '{$mod_data['name']} v{$mod_data['version']}';");
		$dle_mysqlenable  = $dle_mysqlupgrade;
		$dle_mysqldisable = addslashes("DELETE FROM {prefix}_admin_sections WHERE name = '{$mod_data['translit']}';");
		$dle_mysqldelete  = $dle_mysqldisable;

		try {
			$plugin = $db->query('SELECT * FROM ' . PREFIX . "_plugins WHERE name = '{$dle_connector_name}'");
			if ($plugin->num_rows === 0) {
				$prefix = PREFIX;
				$sql_insert = <<<SQL
INSERT INTO {$prefix}_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, filelist, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) VALUES ('{$dle_connector_name}', '{$dle_connector_description}', '{$icon}', '{$dle_connector_version}', '{$dle_connector_dleversion}', '{$dle_connector_dleversion_compare}', $dle_connector_active, '', '{$dle_mysqlupgrade}', '{$dle_mysqlenable}', '{$dle_mysqldisable}', '{$dle_mysqldelete}', 1, '', '', '', '', '', '', '', '', '', 0);
SQL;

				$db->query($sql_insert);
				$plugin_id = $db->insert_id();
				$db->query(
					"INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('" . $db->safesql(
						$member_id['name']
					) . "', '{$_TIME}', '{$_IP}', '116', '{$name}')"
				);
				execute_query($plugin_id, $dle_mysqlenable);

				$success['plugin'][] = [
					'link' => "{$config['']}{$config['']}?mod=plugins&action=edit&id={$plugin_id}",
					'name' => "{$mod_data['name']} v{$mod_data['version']}",
				];
			} else {
				$fails['plugin'][] = [
					'message' => __('Плагин уже существует'),
				];
			}
		} catch (Exception $e) {
			$fails['plugin'][] = [
				'message' => $e->getMessage(),
			];
		}
	}

	clear_cache();

	echo json_encode([
						 'status'  => 'success',
						 'success' => $success,
						 'failed'  => $fails,
					 ],
					 JSON_UNESCAPED_UNICODE);
}
