<?php

global $member_id, $_TIME, $_IP, $name, $parsedData, $db, $config;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$mod_data = filter_var_array(array_column($parsedData, 'value', 'name'), [
	'name'             => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'translit'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'description'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'version'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'icon'             => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'plugin_icon'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'crowdin_name'     => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'crowdin_state_id' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'link'             => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'docs'             => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	'db'               => FILTER_VALIDATE_BOOL,
	'override'         => FILTER_VALIDATE_BOOL,
]);

if (empty($mod_data['name']) || empty($mod_data['description']) || empty($mod_data['version'])) {
	echo (new ErrorResponseAjax())->setData(__('Нужные данные не были заполнены'))->setMeta(
		['name', 'description', 'version']
	)->send();
	exit;
} else {
	if (empty($mod_data['translit'])) $mod_data['translit'] = $mod_data['name'];
	$mod_data['translit'] = totranslit(stripslashes($mod_data['translit']), true, false);

	if (empty($mod_data['icon'])) {
		$mod_data['icon'] = 'fad fa-cogs';
	}

	if (empty($mod_data['plugin_icon'])) {
		$mod_data['plugin_icon'] = "engine/inc/maharder/_modules/{$mod_data['translit']}/assets/icon.png";
	}

	$dirs = [
		ROOT_DIR . '/engine/ajax/maharder/' . $mod_data['translit'],
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'],
		ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/classes',
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
			0755,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/assets/.htaccess',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/assets_htaccess.txt',
			0755,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/module/changelog.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/changelog.php.txt',
			0755,
		],
		[
			ROOT_DIR . '/engine/inc/' . $mod_data['translit'] . '.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/inc_admin.php.txt',
			0755,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/_modules/' . $mod_data['translit'] . '/module/main.php',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/modules_main.php.txt',
			0755,
		],
		[
			ROOT_DIR . '/engine/inc/maharder/_templates/' . $mod_data['translit'] . '/main.html',
			ENGINE_DIR . '/inc/maharder/_includes/module_files/templates_main.html.txt',
			0755,
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
		DataManager::createDir('mhadmin', 'ajax/new_module', paths: $dir);
		if (!is_dir($dir)) {
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
		}
		$success['dirs'][] = $dir;
	}

	foreach ($files as [$targetFile, $sourceFile, $perms]) {
		// Проверяем существование файла, не дублируя индексы массива
		if (file_exists($targetFile)) {
			if (!$mod_data['override']) {
				$fails['files'][] = [
					'file'    => $targetFile,
					'message' => __('Данный файл уже существует.'),
				];
				continue;
			}
		}

		try {
			$targetDir = dirname($targetFile);
			$targetFile = DataManager::normalizePath($targetFile);

			if(!touch($targetFile)) {
				$error = error_get_last();
				LogGenerator::generateLog(
					'maharder',
					'ajax/new_module',
					[
						__('Файл "%s" не был создан', ["%s" => $targetFile]),
						$error
					]
				);
			}

			// Считываем и заменяем данные через единую функцию
			$file_data = file_get_contents($sourceFile);

			$replacements = [
				'%latin%'            => $mod_data['translit'],
				'%name%'             => $mod_data['name'],
				'%crowdin_name%'     => $mod_data['crowdin_name'] ?? $mod_data['translit'],
				'%crowdin_stat_id%'  => $mod_data['crowdin_state_id'],
				'%version%'          => $mod_data['version'],
				'%description%'      => $mod_data['description'],
				'%icon%'             => $mod_data['icon'],
				'%link%'             => $mod_data['link'],
				'%docs%'             => $mod_data['docs'],
				'%path%'             => str_replace(ROOT_DIR, '', $targetFile),
				'%year%'             => date('Y'),
			];

			$file_data = str_replace(array_keys($replacements), array_values($replacements), $file_data);

			// Записываем данные обратно в файл
			if(!file_put_contents($targetFile, $file_data, LOCK_EX)) {
				$error = error_get_last();
				LogGenerator::generateLog('MH Admin', 'ajax/new_module', [
					__('Невозможно записать файл "%s"', ["%s" => $targetFile]),
					$error
				]);
			}
			chmod($targetFile, $perms);

			$success['files'][] = $targetFile;
		} catch (Exception $e) {
			$fails['files'][] = [
				'file'    => $targetFile,
				'message' => $e->getMessage(),
			];
		}
	}

	$langsPath     = ENGINE_DIR . '/inc/maharder/_locales';
	$langsTemplate = file_get_contents(
		DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/module_files/module_locale.txt')
	);

	$targetLangDir = DataManager::dirToArray($langsPath);

	foreach ($targetLangDir as $item) {
		// Пропускаем текущую директорию и родительский указатель
		if (in_array($item, ['.', '..']) || !is_dir($langsPath . DIRECTORY_SEPARATOR . $item)) {
			continue;
		}

		// Определяем путь до XLIFF-файла
		$localePath = "{$langsPath}/{$item}/{$mod_data['translit']}.xliff";

		if (file_exists($localePath) && ((int)$mod_data['override'] !== 1 || $mod_data['override'] !== 'on')) {
			$fails['files'][] = [
				'file'    => $localePath,
				'message' => __('Данный файл уже существует.'),
			];
			continue;
		}

		// Обрабатываем соответствие ru_RU отдельной логике
		$langPath = ($item === 'ru_RU') ? "{$mod_data['translit']}.ru_RU" : "{$langsPath}/ru_RU/{$mod_data['translit']}.xliff";

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

	if ($mod_data['db']) {
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

		$dle_api             = new DLE_API();
		$dle_api->db         = $db;
		$dle_api->dle_config = $config;

		$dle_api->install_admin_module(
			$mod_data['translit'],
			"{$mod_data['name']} v{$mod_data['version']}",
			$mod_data['description'],
			$icon,
			'1,2'
		);

		$dle_mysqlupgrade = <<<SQL
INSERT INTO {prefix}_admin_sections (name, title, descr, icon, allow_groups) VALUES ('{$mod_data['translit']}', '{$mod_data['name']} v{$mod_data['version']}', '{$mod_data['description']}', '{$icon}', '1, 2') ON DUPLICATE KEY UPDATE title = '{$mod_data['name']} v{$mod_data['version']}';
SQL;
		$dle_mysqlenable  = $dle_mysqlupgrade;
		$dle_mysqldisable = "DELETE FROM {prefix}_admin_sections WHERE name = '{$mod_data['translit']}';";
		$dle_mysqldelete  = $dle_mysqldisable;
		$dle_notice       = "<ul><li><b>" . __('Ссылка на плагин') . "</b>: <a href=\"{$mod_data['link']}\" target=\"_blank\">" . __('Перейти') . "</a></li><li><b>" . __('Документация') . "</b>: <a href=\"{$mod_data['docs']}\" target=\"_blank\">" . __('Перейти') . "</a></li></ul>";

		try {
			$plugin = $db->query('SELECT * FROM ' . PREFIX . "_plugins WHERE name = '{$dle_connector_name}'");
			if ($plugin->num_rows === 0) {
				$prefix     = PREFIX;
				$sql_insert = <<<SQL
INSERT INTO {$prefix}_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, filelist, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) VALUES ('{$dle_connector_name}', '{$dle_connector_description}', '{$icon}', '{$dle_connector_version}', '{$dle_connector_dleversion}', '{$dle_connector_dleversion_compare}', $dle_connector_active, '', '{$dle_mysqlupgrade}', '{$dle_mysqlenable}', '{$dle_mysqldisable}', '{$dle_mysqldelete}', 1, '', '', '', '', '', '', '', '', '{$dle_notice}', 1);
SQL;

				$db->query($sql_insert);
				$plugin_id = $db->insert_id();
				$db->query(
					"INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('" . $db->safesql($member_id['name']) . "', '{$_TIME}', '{$_IP}', '116', '{$name}')"
				);
				execute_query($plugin_id, $dle_mysqlenable);

				$plugin_files = [];
				$plugin_files[] = <<<SQL
INSERT INTO {$prefix}_plugins_files (`plugin_id`, `file`, `action`, `searchcode`, `replacecode`, `active`, `searchcount`,
                                      `replacecount`, `filedisable`, `filedleversion`, `fileversioncompare`)
VALUES ({$plugin_id}, 'engine/inc/maharder/_includes/extras/paths.php', 'before', '// Custom models //', '// {$mod_data['name']}
  MH_MODULES . "/{$mod_data['latin']}/models",
  // {$mod_data['name']}
'                                                                               , 1, 0, 0, 1, '', '==');
SQL;
				$plugin_files[] = <<<SQL
INSERT INTO {$prefix}_plugins_files (`plugin_id`, `file`, `action`, `searchcode`, `replacecode`, `active`, `searchcount`,
                                      `replacecount`, `filedisable`, `filedleversion`, `fileversioncompare`)
VALUES ({$plugin_id}, 'engine/inc/maharder/_includes/extras/paths.php', 'before', '// Custom paths //', '// {$mod_data['name']}
    MH_MODULES . "/{$mod_data['latin']}/classes",
	MH_MODULES . "/{$mod_data['latin']}/repositories",
	MH_MODULES . "/{$mod_data['latin']}/utils",
  // {$mod_data['name']}
', 1, 0, 0, 1, '', '==');
SQL;

				foreach ($plugin_files as $query) {
					$db->query($query);
				}

				$success['plugin'][] = [
					'link' => "{$config['http_home_url']}{$config['admin_path']}?mod=plugins&action=edit&id={$plugin_id}",
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

	echo (new SuccessResponseAjax)->setData(__('Плагин "{plugin}" был создан!', ['{plugin}' => $mod_data['name']]))->setMeta([$success, $fails])->send();
	exit;
}
