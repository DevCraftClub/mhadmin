<?php

// File: Admin.php
// Path: /engine/inc/maharder/admin/modules/Admin.php
// ============================================================ =
// Author: Maxim Harder <dev@devcraft.club> (c) 2020
// Website: https://www.devcraft.club
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!
//===============================================================
namespace MaHarder\classes;
use DLEPlugins;

class Admin {

	protected $logs = 0;
	private
		$cache_folder = ENGINE_DIR . '/inc/maharder/_cache',
		$cssArr = [
			URL.'/maharder/admin/assets/css/base.css',
			URL.'/maharder/admin/assets/css/v4-font-face.min.css',
			URL.'/maharder/admin/assets/css/v5-font-face.min.css',
			URL.'/maharder/admin/assets/css/fa_old.css',
			URL.'/maharder/admin/assets/css/icons.css',
			URL.'/maharder/admin/assets/css/tokens.css',
			URL.'/maharder/admin/assets/css/prettify.css',
			URL.'/maharder/admin/assets/css/jquery-confirm.min.css',
			URL.'/maharder/admin/assets/css/theme.css',
			URL.'/maharder/admin/assets/editor/themes/default.min.css',
		],
		$jsArr = [
			URL.'/maharder/admin/assets/js/jquery.js',
			URL.'/maharder/admin/assets/js/base.js',
			URL.'/maharder/admin/assets/js/autosize.min.js',
			URL.'/maharder/admin/assets/js/mask.js',
			URL.'/maharder/admin/assets/js/tokens.js',
			URL.'/maharder/admin/assets/js/jquery-confirm.min.js',
			URL.'/maharder/admin/assets/editor/sceditor.min.js',
			URL.'/maharder/admin/assets/editor/formats/bbcode.js',
			URL.'/maharder/admin/assets/editor/icons/material.js',
			URL.'/maharder/admin/assets/editor/plugins/autosave.js',
			URL.'/maharder/admin/assets/editor/plugins/autoyoutube.js',
			URL.'/maharder/admin/assets/editor/plugins/undo.js',
			URL.'/maharder/admin/assets/editor/languages/ru.js',
			URL.'/maharder/admin/assets/js/theme.js',
		],
		$variables = [
			'css_dir' => '',
			'js_dir' => '',
			'css' => [],
			'js' => [],
			'url' => URL,
			'lic_link' => 'https://devcraft.club/pages/licence-agreement/',
			'author' => [
				'name' => 'Maxim Harder',
				'contacts' => [
					[
						'name' => 'E-Mail',
						'link' => 'mailto:dev@devcraft.club',
					],
					[
						'name' => 'Telegram',
						'link' => 'https://t.me/MaHarder',
					],
					[
						'name' => 'Вебсайт',
						'link' => 'https://devcraft.club/misc/contact',
					],
				],
				'donate' => [
					[
						'name' => 'WME (Webmoney (EU))',
						'value' => 'E275336355586',
						'link' => '',
					],
					[
						'name' => 'WMZ (Webmoney (USD))',
						'value' => 'Z139685140004',
						'link' => '',
					],
					[
						'name' => 'WMR (Webmoney (RU))',
						'value' => 'R127552376453',
						'link' => '',
					],
					[
						'name' => 'PayPal',
						'value' => 'paypal.me/MaximH',
						'link' => 'https://paypal.me/MaximH',
					],
					[
						'name' => 'Ko-Fi',
						'value' => 'ko-fi.com/devcraft',
						'link' => 'https://ko-fi.com/J3J118N1C',
					],
					[
						'name' => 'Yandex.Money (Sobe.ru)',
						'value' => '41001454367103',
						'link' => 'https://sobe.ru/na/devcraftclub',
					],
					[
						'name' => 'Yandex.Money (YooMoney)',
						'value' => '41001454367103',
						'link' => 'https://yoomoney.ru/to/41001454367103',
					],
					[
						'name' => 'DonationAlerts',
						'value' => '/r/maharder',
						'link' => 'https://www.donationalerts.com/r/maharder',
					],
					[
						'name' => 'DevCraft',
						'value' => 'На сайте автора',
						'link' => 'https://devcraft.club/donate/drives/na-chaj.1/',
					],
				],
			],
			'menu' => [],
			'breadcrumbs' => [],
		],
		$assets_arr = [],
		$assets_dir = ENGINE_DIR . '/inc/maharder/admin/assets',
		$asset_file = ENGINE_DIR . '/inc/maharder/_includes/assets.json'
	;

	public function __construct() {
		$this->setVar('css_dir', URL.'/maharder/admin/assets/css');
		$this->setVar('js_dir', URL.'/maharder/admin/assets/js');
		$this->setVar('css', $this->htmlStatic($this->cssArr));
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
		$this->preSetMenu();
		$mh_settings = $this->getConfig('maharder');
		$this->logs = isset($mh_settings['logs']);
		if(file_exists(ENGINE_DIR.'/inc/maharder/admin/assets/css/dark.css'))
			if (isset($mh_settings['theme']) && $mh_settings['theme'] === 'dark' )
				$this->setCss(URL.'/maharder/admin/assets/css/dark.css');

		if (!mkdir($cache_folder = $this->cache_folder, 0755, true)
			&& !is_dir(
				$cache_folder
			)) {
			$this->generate_log('maharder', 'construct', sprintf('Directory "%s" was not created',
																 $cache_folder));
		}

		if(is_dir($cache_folder)) {
			file_put_contents(
				$cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 'Order Deny,Allow
Deny from all'
			);
			chmod($cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 0666);
		}
		
		if ($mh_settings['theme'] === 'dark') $this->setCss(URL.'/maharder/admin/assets/editor/themes/dark.css');

	}

	private function generate_link($name, $href, $type = 'link', $children = []) {
		return [
			'name' => $name,
			'href' => $href,
			'type' => $type,
			'children' => $children
		];
	}

	public function setLink($link) {
		$this->variables['menu'][] = $link;
	}

	public function setLinks($links) {
		foreach ($links as $link)
			$this->setLink($link);
	}

	public function parseAssets($parse = false) {
		if( file_exists($this->asset_file)) {
			if($parse) {
				$this->parse_assets();
			}
		} else {
			$this->parse_assets();
		}
	}

	public function checkAssets($rewrite = false) {
		if(!file_exists($this->asset_file) || $rewrite) {
			$this->prepare_assets($this->dirToArray($this->assets_dir), $this->assets_dir);
			file_put_contents($this->asset_file, json_encode($this->assets_arr, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
		}
		$assets = json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true);
		$files  = json_decode(file_get_contents($this->asset_file), true);

		$info = [
			'on_server' => count($assets),
			'local' => count($files),
			'missing_count' => 0,
			'update_count' => 0,
			'missing' => [],
			'update' => []
		];

		foreach ($assets as $asset => $data) {
			if (isset($files[$asset])) {
				if ($files[$asset]['hash'] !== $data['hash']) {
					$info['update'][$asset] = $data;
					$info['update_count']++;
				}
			} else {
				$info['missing'][$asset] = $data;
				$info['missing_count']++;
			}
		}

		return $info;

	}

	private function parse_assets() {
		$this->prepare_assets($this->dirToArray($this->assets_dir), $this->assets_dir);
		$files  = $this->assets_arr;
		$assets = json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true);

		foreach ($assets as $asset => $data) {
			if (isset($files[$asset])) {
				if ($files[$asset]['hash'] !== $data['hash']) {
					$this->save_asset($data, $asset);
				}
			} else {
				$this->save_asset($data, $asset);
			}
		}

		file_put_contents($this->asset_file, json_encode($assets, JSON_UNESCAPED_UNICODE));
	}

	public function save_asset($data, $file) {

		if(!function_exists('format_bytes')) {
			function format_bytes(int $size){
				$base = log($size, 1024);
				$suffixes = array('', 'KB', 'MB', 'GB', 'TB', 'PB');
				return round(1024 ** ($base - floor($base)), 2) . '' . $suffixes[floor($base)];
			}
		}

		$asset_file = file_get_contents($data['link']);
		if(empty($asset_file) && !empty($data['alt'])) {
			$asset_file = file_get_contents($data['alt']);
		}
		if ($asset_file) {
			$file_path = ENGINE_DIR . '/inc/maharder/admin' . $file;
			if (!mkdir($concurrentDirectory = dirname($file_path), 0777, true) && !is_dir($concurrentDirectory)) {
				$this->generate_log('maharder/admin', 'save_asset', "Путь '{$concurrentDirectory}' не удалось создать");
			}
			if (!file_put_contents($file_path, $asset_file, FILE_USE_INCLUDE_PATH | LOCK_EX) || !is_writable($file_path)) {
				$this->generate_log('maharder/admin', 'save_asset', "Файл '{$file}' не был сохранён!");
			}
			$pathinfo = pathinfo($file_path);
			$stat     = stat($file_path);
			$this->checkAssets(true);
			
			return [
				'realpath' => realpath($file_path),
				'dirname' => $pathinfo['dirname'],
				'basename' => $pathinfo['basename'],
				'filename' => $pathinfo['filename'],
				'extension' => $pathinfo['extension'],
				'mime' => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path),
				'encoding' => finfo_file(finfo_open(FILEINFO_MIME_ENCODING), $file_path),
				'size' => $stat[7],
				'size_string' => format_bytes($stat[7]),
				'atime' => $stat[8],
				'mtime' => $stat[9],
				'permission' => substr(sprintf('%o', fileperms($file_path)), -4),
			];
		}
		return false;
	}

	private function prepare_assets($arr, $dir = __DIR__) {

		foreach($arr as $key => $value) {
			if(is_array($value)) {
				$this->prepare_assets($value, $dir . '/' . $key);
			} else {
				$file = $dir . '/' . $key;
				$file_info = pathinfo($key);
				if(empty($file_info['extension'])) $file = $dir . '/' . $value;
				$dir_arr = str_replace(ENGINE_DIR . '/inc/maharder/admin', '', $file);
				$pathinfo = pathinfo($dir_arr);

				$this->assets_arr[$dir_arr] = [
					'path' => $pathinfo['dirname'],
					'file' => $dir_arr,
					'hash' => hash_file('md5', $file)
				];
			}
		}

	}

	private function preSetMenu() {
		global $lang;
		require_once DLEPlugins::Check(ENGINE_DIR.'/skins/default.skin.php');
		$dle_links_header = [
			'config' => $lang['opt_hopt'],
			'user' => $lang['opt_s_acc'],
			'templates' => $lang['opt_s_tem'],
			'filter' => $lang['opt_s_fil'],
			'others' => $lang['opt_s_oth'],
			'admin_sections' => $lang['admin_other_section'],
		];

		$admin_links = [
			$this->generate_link($lang['header_all'], '?mod=options&action=options'),
			$this->generate_link('', '', 'divider'),
			$this->generate_link('Новости', '', 'dropdown', [
				$this->generate_link($lang['add_news'], '?mod=addnews&action=addnews'),
				$this->generate_link($lang['edit_news'], '?mod=editnews&action=list'),
			]),
			$this->generate_link('', '', 'divider')
		];

		foreach($options as $o => $a) {
			$opt_children = [];
			foreach($a as $c) $opt_children[] = $this->generate_link($c['name'], $c['url']);
			$admin_links[] = $this->generate_link($dle_links_header[$o], '', 'dropdown', $opt_children);
		}

		$this->setLink($this->generate_link('Страницы DLE', '', 'dropdown', $admin_links));

	}

	public function getVariables() {
		return $this->variables;
	}

	public function setVar($name, $value) {
		$this->variables[$name] = $value;
	}

	public function setVars($arr = []) {
		foreach($arr as $name => $value)
			$this->variables[$name] = $value;
	}

	public function setCss($css) {
		if (is_array($css)) foreach ( $css as $file ) $this->cssArr[] = $file;
		else $this->cssArr[] = $css;
		$this->setVar('css', $this->htmlStatic($this->cssArr));
	}

	public function setJs($js) {
		if (is_array($js)) foreach ( $js as $file ) $this->jsArr[] = $file;
		else $this->jsArr[] = $js;
		$this->setVar('js', $this->htmlStatic($this->jsArr));
	}

	protected function defType( $value, $type ) {

		if ($type === 'double') $output = (float)$value;
		elseif ($type === 'boolean') $output = (bool)$value;
		elseif ($type === 'integer') $output = (int)$value;
		else $output = "'{$value}'";

		return $output;
	}

	protected function getComparer( $value) {

		$firstSign = array('!', '<', '>', '%');
		$secondSign = array('=');
		$type = gettype($value);
		$outSign = '=';
		$checkSign = NULL;

		if (!in_array($type, ['integer', 'double', 'boolean']) && in_array($value[0], $firstSign, true)) {
			$checkSign = $value[0];
			if (in_array($value[1], $secondSign, true)){
				$checkSign .= $value[1];
				$value = substr($value, 2);
			} else {
				$value = substr($value, 1);
			}
		}

		if ($checkSign === '!') {
			$outSign = '<>';
		} elseif (in_array($checkSign, array('<', '>', '<=', '>='))) {
			$outSign = $checkSign;
		} elseif ($checkSign === '%') {
			$outSign = 'LIKE';
			$value = '%'. $value .'%';
		}

		$value = $this->defType($value, $type);

		return " {$outSign} {$value}";
	}

	/**
	 * Функция создания кеша запросов,
	 * чтобы сократить кол-во обращений к базе данных
	 *
	 * @param       $name	//	Переменная для названия кеша
	 * @param array $vars 	// 	table	Название таблицы, в противном случае будет браться переменная $name
	 *                     	//	sql		Запрос полностью, если он заполнен, то будет испольняться именно он,
	 *                     				другие значения игнорируются
	 *                      //  where   Массив выборки запроса, прописывается в название файла кеша.
	 *                      //  		Заполняется так: 'поле' => 'значение', 'news_id' => '1'
	 *						//	selects	Массив вывод значений, если он пуст, то будут возвращены все значения
	 *                     				таблицы. Заполняется так: ['Ячейка 1', 'Ячейка 2', ...]
	 *                     				Прописывается в названии файла кеша
	 *                     	//	order	Массив сортировки вывода, прописывается в название файла кеша
	 *                     				Заполняется так: 'поле' => 'Порядок сортировки', 'news_id' => 'ASC'
	 *                     	//	limit	Ограничение вывода запросов, возможно указывать следующие значения:
	 *                     				n 	->	просто максимальное кол-во данных
	 *                                  n,x	->	ограничение вывода,
	 *                                          n - с какого захода начать сбор данных,
	 * 											x - до какого значения делать сбор данных
	 *
	 * @return array
	 */
	public function load_data($name,
							  $vars = array( 'table' => null,
											 'sql' => null,
											 'where' => [],
											 'selects' => [],
											 'order' => [],
											 'limit' => null
							  )
	) {
		global $db;

		$where = [];
		$order = [];
		$file_name = $name;
		$file_suffix = '';
		foreach($vars['selects'] as $s) {
			$file_suffix .= "_s{$s}";
		}
		foreach($vars['where'] as $id => $key) {
			if (is_array($key)) {
				foreach ($key as $k) {
					$file_suffix .= "_{$id}-{$k}";
					$where[] = $id.$this->getComparer($k);
				}
			} else {
				$file_suffix .= "_{$id}-{$key}";
				$where[] = $id.$this->getComparer($key);
			}
		}
		foreach($vars['order'] as $n => $sort) {
			$file_suffix .= "_o{$n}-{$sort}";
			$order[] = "{$n} {$sort}";
		}

		$file_name .= '_' . md5(md5($file_suffix));

		if (!file_exists($this->cache_folder . "/{$name}/{$file_name}.php")) {
			$data = [];
			$prefix = PREFIX;
			if (in_array($name, ['users', 'usergroup'])) $prefix = USERPREFIX;

			$order = implode(', ', $order);
			if(!empty($order)) $order = "ORDER BY {$order}";

			$limit = '';
			if(!empty($vars['limit'])) $limit = "LIMIT {$vars['limit']}";

			if(count($vars['where']) > 0 && $vars['sql'] === null) {
				$selects = implode(",", $vars['selects']);
				if(empty($selects)) $selects = '*';
				$where = implode(' AND ', $where);
				if (!empty($where)) $where = "WHERE {$where}";

				if($vars['table'] !== null) $sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$where} {$order} {$limit}";
				else $sql = "SELECT {$selects} FROM {$prefix}_{$name} {$where} {$order} {$limit}";
			} else {
				if($vars['table'] === null && $vars['sql'] === null) $vars['table'] = $name;

				if ($vars['table'] !== NULL) {
					$selects = implode(",", $vars['selects']);
					if (empty($selects)) $selects = '*';
					$sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$order} {$limit}";
				}
				if ($vars['sql'] !== NULL) $sql = $vars['sql'];
			}

			$db->query($sql);
			while($row = $db->get_row()) {
				$data[] = $row;
			}

			$db->close();

			$this->set_cache($name, $file_name, $data);
		}

		return $this->get_cache($name, $file_name);
	}

	protected function dirToArray($dir): array {

		$result = array();

		$cdir = scandir($dir);
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array('.', '..', '.htaccess'))) {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Очищаем кеш
	 *
	 * @param string $type
	 */
	public function clear_cache( $type = 'all' ) {
		$type = totranslit($type, true, false);

		$dirname = $this->cache_folder;
		if ($type !== 'all') $dirname .= '/' . $type;
		$dir = $this->dirToArray($dirname);

		foreach($dir as $i => $name) {
			try {
				if (is_array($name)) @rmdir($dirname . DIRECTORY_SEPARATOR . $i);
				else @unlink($dirname . DIRECTORY_SEPARATOR . $name);
			} catch (Exception $e) {
				$this->generate_log('maharder', 'clear_cache', $e->getMessage());
			}
		}

		if ($type === 'all') {
			try {
				clear_all_caches();
			} catch ( Exception $e ) {
				clear_cache();
			}
		}
	}

	/**
	 * Получаем кеш
	 *
	 * @param $type
	 * @param $name
	 *
	 * @return array|false|int|mixed
	 */
	private function get_cache($type, $name) {
		$file = totranslit($name, true, false);
		$type = totranslit($type, true, false);

		$data = @file_get_contents( $this->cache_folder . '/' . $type . DIRECTORY_SEPARATOR . $file . '.php' );

		if ( $data !== false ) {

			$data = json_decode( $data, true );
			if ( is_array($data) OR is_int($data) ) return $data;

		}

		return false;

	}

	/**
	 * Сохраняем в кеш
	 *
	 * @param $type
	 * @param $name
	 * @param $data
	 */
	private function set_cache($type, $name, $data) {
		$file = totranslit($name, true, false);
		$type = totranslit($type, true, false);

		if (!mkdir($concurrentDirectory = $this->cache_folder . '/' . $type, 0755, true)
			&& !is_dir(
				$concurrentDirectory
			)) {
			$this->generate_log('maharder', 'set_cache', sprintf('Directory "%s" was not created', $concurrentDirectory));
		}

		if ( is_array($data) OR is_int($data) ) {

			file_put_contents ($concurrentDirectory . DIRECTORY_SEPARATOR . $file . '.php', json_encode( $data, JSON_UNESCAPED_UNICODE |
																						   JSON_UNESCAPED_SLASHES ), LOCK_EX);
			@chmod( $concurrentDirectory . DIRECTORY_SEPARATOR . $file . '.php', 0666 );

		}
	}

	public function htmlStatic($data, $view = 'html', $type = 'css') {
		$out = [];
		if ('html' == $view) {
			if (is_array($data)) {
				foreach ($data as $d) {
					if ('css' == $type) {
						$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
					} elseif ('js' == $type) {
						$out[] = "<script src='{$d}'></script>";
					}
				}
			} else {
				if ('css' == $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
				} elseif ('js' == $type) {
					$out[] = "<script src='{$data}'></script>";
				}
			}
		} elseif ('link' == $view) {
			if (is_array($data)) {
				foreach ($data as $d) {
					$out[] = $d;
				}
			} else {
				$out[] = $data;
			}
		}

		return $out;
	}

	public function get_used_xfields($id, $type = 'post') {
		if ('post' === $type) {
			$post = $this->load_data( "post", ['selects' => ['xfields'], 'where' => ['id' => $id]] );
		} elseif ('user' === $type) {
			$post = $this->load_data( "users", ['selects' => ['xfields'], 'where' => ['user_id' => $id]] );
		}

		$xfout = false;
		if ($post) {
			$xfout  = [];
			$fields = explode('||', $post['xfields']);
			foreach ($fields as $key => $value) {
				$xfout[$key] = $value;
			}
		}

		return $xfout;
	}

	public function loadXfields($type = 'post') {
		if ('post' === $type) {
			$xf_file = file(ENGINE_DIR.'/data/xfields.txt');
		} elseif ('user' === $type) {
			$xf_file = file(ENGINE_DIR.'/data/xprofile.txt');
		}

		$xf_info = [];
		foreach ($xf_file as $line) {
			$info = explode('|', $line);
			$xf_info[$info[0]] = $info[1] . ' (' . $info[0] . ')';
		}

		return $xf_info;
	}

	/**
	 * Получаем список пользователей
	 *
	 * @return array
	 */
	public function getUsers() {

		$users = $this->load_data( "users", ['selects' => ['user_id', 'name'], 'order' => ['name' => 'ASC']] );
		$user_ar = [];

		if($users) {
			foreach ($users as $uid => $user) {
				$user_ar[$user['user_id']] = $user['name'];
			}
		}

		return $user_ar;
	}

	/**
	 * Получаем простой список категорий на сайте
	 * в виде массива с данными ID и названием
	 *
	 * @return array
	 */
	public function getCats() {

		$cats = $this->load_data( "category", ['selects' => ['id', 'name'], 'order' => ['name' => 'ASC']] );
		$categories = [];
		if($cats) {
			foreach ($cats as $cid => $entry) {
				$categories[$entry['id']] = $entry['name'];
			}
		}

		return $categories;
	}

	/**
	 * Получаем настройки модуля, если такие имеются.
	 * Возвращает массив данных.
	 *
	 * @param string $codename #	Название модуля, а так-же название конфигурации; без обозначений
	 * @param string $path     #	Путь до конфигурации файла
	 * @param string $confName #	Если сохранилась конфигурация в папке /engine/data/, то указать название массива без знака $
	 *
	 * @return array
	 */
	public function getConfig( $codename, $path = ENGINE_DIR . '/inc/maharder/_config', $confName = '') {
		$settings = [];

		if (file_exists($path.DIRECTORY_SEPARATOR.$codename.'.json')) {
			$settings = json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$codename.'.json'), true);
			foreach ($settings as $name => $value) {
				if (!is_array($value)) $settings[$name] = htmlspecialchars_decode($value);
			}
		} else {
			if (!empty($confName)) {
				$oldConfig = ENGINE_DIR.'/data/'.$codename.'.php';
				if (file_exists($oldConfig)) {
					$oldFile = file_get_contents((DLEPlugins::Check($oldConfig)));
					$oldFile = str_replace("${$confName} = ", 'return ', $oldFile);
					file_put_contents($oldConfig, $oldFile);
					$oldSettings = include DLEPlugins::Check($oldConfig);
					file_put_contents($path.DIRECTORY_SEPARATOR.$codename.'.json', $oldSettings);
					@unlink($oldConfig);

					foreach($oldSettings as $set => $val) $settings[$set] = $val;
				}
			}
		}

		return $settings;
	}

	public function upload_file() {

	}

	/**
	 * @param        $service
	 * @param        $function_name
	 * @param        $message
	 * @param string $type
	 */
	public function generate_log($service, $function_name, $message, $type = 'error') {
		if($this->logs) {
			$root_dir = dirname(__DIR__, 2);
			$date     = date('[Y-m-d] d.m.Y, H:i');
			$concurrentDirectory = $root_dir . '/_logs/' . $service . '/' . $type;
			if (!mkdir($concurrentDirectory, 0777, true)
				&& !is_dir($concurrentDirectory)) {

				echo "<b>Уведомление</b>:{$type}<br>";
				echo "<b>Модуль</b>:{$service}<br>";
				echo "<b>Функция</b>:{$function_name}<br>";
				echo "<b>Дата и время</b>:{$date}<br>";
				echo "<b>Ошибка</b>:<br>";
				echo "Directory \"{$concurrentDirectory}\" was not created";
			}
			$file    = $concurrentDirectory . '/' . $function_name . '.txt';
			try {
				$message = serialize($message);
			} catch (Exception $e) {

				echo "<b>Уведомление</b>:{$type}<br>";
				echo "<b>Модуль</b>:{$service}<br>";
				echo "<b>Функция</b>:{$function_name}<br>";
				echo "<b>Дата и время</b>:{$date}<br>";
				echo "<b>Ошибка</b>:<br>";
				echo "<pre>";
				print_r($message);
				echo "</pre>";
			}
			file_put_contents($file, "{$date}\n{$message}\n=====================================\n", FILE_APPEND);
		}
	}

}








