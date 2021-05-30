<?php

// File: maharder.class.php
// Path: /engine/inc/maharder/admin/modules/maharder.class.php
// ============================================================ =
// Author: Maxim Harder <dev@devcraft.club> (c) 2020
// Website: https://www.devcraft.club
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!
//===============================================================


class MHAdmin {

	private
		$cssArr = [
			URL.'/maharder/admin/assets/css/base.css',
			URL.'/maharder/admin/assets/css/icons.css',
			URL.'/maharder/admin/assets/css/tokens.css',
			URL.'/maharder/admin/assets/css/prettify.css',
			URL.'/maharder/admin/assets/css/theme.css',
			URL.'/maharder/admin/assets/editor/themes/default.min.css',
		],
		$jsArr = [
			URL.'/maharder/admin/assets/js/jquery.js',
			URL.'/maharder/admin/assets/js/base.js',
			URL.'/maharder/admin/assets/js/autosize.min.js',
			URL.'/maharder/admin/assets/js/mask.js',
			URL.'/maharder/admin/assets/js/tokens.js',
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
			'author' => [
				'name' => 'Maxim Harder',
				'contacts' => [
					[
						'name' => 'E-Mail',
						'link' => 'mailto:dev@devcraft.club',
					],
					[
						'name' => 'Telegram',
						'link' => 'https://t.me/@MaHarder',
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
						'name' => 'Yandex.Money',
						'value' => '41001454367103',
						'link' => 'https://sobe.ru/na/devcraftclub',
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
		]
	;

	public function __construct() {
		$this->setVar('css_dir', URL.'/maharder/admin/assets/css');
		$this->setVar('js_dir', URL.'/maharder/admin/assets/js');
		$this->setVar('css', $this->htmlStatic($this->cssArr));
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
		$this->preSetMenu();
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
		foreach($vars['selects'] as $s) {
			$file_name .= "_s{$s}";
		}
		foreach($vars['where'] as $id => $key) {
			$file_name .= "_{$id}-{$key}";
			$where[] = "{$id} = '{$key}'";
		}
		foreach($vars['order'] as $n => $sort) {
			$file_name .= "_o{$n}-{$sort}";
			$order[] = "{$n} {$sort}";
		}

		if (!file_exists(ENGINE_DIR . "/cache/system/{$file_name}.php")) {
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

			set_vars($file_name, $data);
		}

		return get_vars($file_name);
	}

	public function htmlStatic($data, $view = 'html', $type = 'css')
	{
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

	public function get_used_xfields($id, $type = 'post')
	{
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
			$xf_info[$info[0]] = $info[1];
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
	 * @param string $path		#	Путь до конфигурации файла
	 * @param string $codename	#	Название модуля, а так-же название конфигурации; без обозначений
	 * @param string $confName	#	Если сохранилась конфигурация в папке /engine/data/, то указать название массива без знака $
	 *
	 * @return array
	 */
	public function getConfig($path, $codename, $confName = '') {
		$settings = [];

		if (file_exists($path.DIRECTORY_SEPARATOR.$codename.'.json')) {
			$settings = json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$codename.'.json'), true);
			foreach ($settings as $name => $value) {
				$settings[$name] = htmlspecialchars_decode($value);
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

}








