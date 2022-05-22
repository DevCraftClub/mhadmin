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
require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DataLoader.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DleData.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/AssetsChecker.php');

class Admin {
	use LogGenerator;
	use DataLoader;
	use DleData;
	use AssetsChecker;

	private /**
	 * Массив со стилями
	 *
	 * @var string[]
	 */
		$cssArr = [
		URL . '/maharder/admin/assets/css/base.css', URL . '/maharder/admin/assets/css/icons.css',
		URL . '/maharder/admin/assets/css/tokens.css', URL . '/maharder/admin/assets/css/prettify.css',
		URL . '/maharder/admin/assets/css/jquery-confirm.min.css', URL . '/maharder/admin/assets/css/theme.css',
		URL . '/maharder/admin/assets/editor/themes/default.min.css',
	], /**
	 * Массив со скриптами
	 *
	 * @var string[]
	 */
		$jsArr = [
		URL . '/maharder/admin/assets/js/jquery.js', URL . '/maharder/admin/assets/js/base.js',
		URL . '/maharder/admin/assets/js/autosize.min.js', URL . '/maharder/admin/assets/js/mask.js',
		URL . '/maharder/admin/assets/js/tokens.js', URL . '/maharder/admin/assets/js/jquery-confirm.min.js',
		URL . '/maharder/admin/assets/editor/sceditor.min.js', URL . '/maharder/admin/assets/editor/formats/bbcode.js',
		URL . '/maharder/admin/assets/editor/icons/material.js',
		URL . '/maharder/admin/assets/editor/plugins/autosave.js',
		URL . '/maharder/admin/assets/editor/plugins/autoyoutube.js',
		URL . '/maharder/admin/assets/editor/plugins/undo.js', URL . '/maharder/admin/assets/editor/languages/ru.js',
		URL . '/maharder/admin/assets/js/theme.js',
	],

		/**
		 * Массив с различными переменными для шаблонизатора
		 * css_dir - Папка со стилями
		 * js_dir - Папка со скриптами
		 * css - Массив со стилями
		 * js - Массив со скриптами
		 * url - Обычная ссылка для верного отображения стилей и скриптов на сайте
		 * lic_link - Ссылка на пользовательское соглашение
		 * author - Массив с информацией об авторе модуля
		 * author.name - Имя пользователя
		 * author.contacts - Массив с контактной информацией с автором
		 * author.contacts.name - Название контактной информации, к приерму E-Mail
		 * author.contacts.link - Ссылка для связи, к приерму mailto:dev@devcraft.club
		 * author.donate - Массив с информацией по финансовой поддержке
		 * author.donate.name - Название платёжной системы, к приерму PayPal
		 * author.donate.value - Описание платёжной системы, к приерму paypal.me/MaximH
		 * author.donate.link - Ссылка платёжной системы, к приерму https://paypal.me/MaximH
		 * menu - Массив с ссылками для меню сайта
		 * breadcrumbs - Массив с ссылками на хлебные крошки
		 *
		 * @var array
		 */
		$variables = [
		'css_dir'  => '', 'js_dir' => '', 'css' => [], 'js' => [], 'url' => URL,
		'lic_link' => 'https://devcraft.club/pages/licence-agreement/', 'author' => [
			'name'      => 'Maxim Harder', 'contacts' => [
				[
					'name' => 'E-Mail', 'link' => 'mailto:dev@devcraft.club',
				], [
					'name' => 'Telegram', 'link' => 'https://t.me/MaHarder',
				], [
					'name' => 'Вебсайт', 'link' => 'https://devcraft.club/misc/contact',
				],
			], 'donate' => [
				[
					'name' => 'WME (Webmoney (EU))', 'value' => 'E275336355586', 'link' => '',
				], [
					'name' => 'WMZ (Webmoney (USD))', 'value' => 'Z139685140004', 'link' => '',
				], [
					'name' => 'PayPal', 'value' => 'paypal.me/MaximH', 'link' => 'https://paypal.me/MaximH',
				], [
					'name' => 'Ko-Fi', 'value' => 'ko-fi.com/devcraft', 'link' => 'https://ko-fi.com/J3J118N1C',
				], [
					'name' => 'Yandex.Money (Sobe.ru)', 'value' => '41001454367103',
					'link' => 'https://sobe.ru/na/devcraftclub',
				], [
					'name' => 'Yandex.Money (YooMoney)', 'value' => '41001454367103',
					'link' => 'https://yoomoney.ru/to/41001454367103',
				], [
					'name' => 'DonationAlerts', 'value' => '/r/maharder',
					'link' => 'https://www.donationalerts.com/r/maharder',
				],
			],
		], 'menu'  => [], 'breadcrumbs' => [],
	]
	;

	/**
	 * Конструктор класса
	 * Создаёт нужные параметры для изначального старта
	 */
	public function __construct() {
		$this->setVar('css_dir', URL . '/maharder/admin/assets/css');
		$this->setVar('js_dir', URL . '/maharder/admin/assets/js');
		$this->setVar('css', $this->htmlStatic($this->cssArr));
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
		$this->preSetMenu();
		$mh_settings = $this->getConfig('maharder');
		$this->logs = isset($mh_settings['logs']);
		if(file_exists(ENGINE_DIR . '/inc/maharder/admin/assets/css/dark.css')) if(isset($mh_settings['theme'])
		                                                                           && $mh_settings['theme']
		                                                                              === 'dark') $this->setCss(
			URL . '/maharder/admin/assets/css/dark.css'
		);

		if(!mkdir($cache_folder = $this->getCacheFolder(), 0755, true) && !is_dir($cache_folder)) {
			$this->generate_log('maharder', 'construct', sprintf('Directory "%s" was not created', $cache_folder));
		}

		if(is_dir($cache_folder)) {
			file_put_contents(
				$cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 'Order Deny,Allow
Deny from all'
			);
			chmod($cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 0666);
		}

		if($mh_settings['theme'] === 'dark') $this->setCss(URL . '/maharder/admin/assets/editor/themes/dark.css');

	}


	/**
	 * Возвращает массив с данными о ссылке
	 *
	 * @param string      $name     //  Название ссылки
	 * @param string      $href     //  Ссылка
	 * @param string      $type     //  Тип ссылки: link - простая ссылка, divider - разделитель, dropdown - выпадающее меню, data - оформляет элемент как div со скрытым дополнительным параметром $data_val
	 * @param array       $children //  Дочерние ссылки, если есть
	 * @param string|null $data_val //  Дополнительный параметр для выпадающего меню
	 *
	 * @return array
	 */
	public static function generate_link(string $name, string $href, string $type = 'link', array $children = [], ?string $data_val = null)
	: array {
		return [
			'name' => $name, 'href' => $href, 'type' => $type, 'children' => $children, 'data' => $data_val
		];
	}

	/**
	 * Добавляет ссылку в массив меню
	 *
	 * @param $link
	 *
	 * @return void
	 */
	public function setLink($link) {
		$this->variables['menu'][] = $link;
	}

	/**
	 * Добавляет несколько ссылок в массив меню
	 *
	 * @param $links
	 *
	 * @return void
	 */
	public function setLinks($links) {
		foreach($links as $link) {
			$this->setLink($link);
		}
	}

	/**
	 * Подготовка меню для админки
	 * Берёт все доступные ссылки меню из админки самой DLE
	 *
	 * @return void
	 */
	private function preSetMenu() {
		global $lang;
		require_once DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php');
		$dle_links_header = [
			'config'         => $lang['opt_hopt'], 'user' => $lang['opt_s_acc'], 'templates' => $lang['opt_s_tem'],
			'filter'         => $lang['opt_s_fil'], 'others' => $lang['opt_s_oth'],
			'admin_sections' => $lang['admin_other_section'],
		];

		$admin_links = [
			self::generate_link($lang['header_all'], '?mod=options&action=options'),
			self::generate_link('', '', 'divider'), self::generate_link('Новости', '', 'dropdown', [
				self::generate_link($lang['add_news'], '?mod=addnews&action=addnews'),
				self::generate_link($lang['edit_news'], '?mod=editnews&action=list'),
			]), self::generate_link('', '', 'divider')
		];

		foreach($options as $o => $a) {
			$opt_children = [];
			foreach($a as $c) $opt_children[] = self::generate_link($c['name'], $c['url']);
			$admin_links[] = self::generate_link($dle_links_header[$o], '', 'dropdown', $opt_children);
		}

		$this->setLink(self::generate_link('Страницы DLE', '', 'dropdown', $admin_links));

	}

	/**
	 * Возвращает переменные
	 *
	 * @return array
	 */
	public function getVariables() {
		return $this->variables;
	}

	/*
	 * Добавляет новые или обновляет старые переменные
	 */
	public function setVar($name, $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Добавлает или обновляет несколько переменных
	 *
	 * @param $arr //  Массив с данными
	 *
	 * @return void
	 */
	public function setVars($arr = []) {
		foreach($arr as $name => $value) {
			$this->setVar($name, $value);
		}
	}

	/**
	 * Добавляет новый CSS-файл в массив
	 *
	 * @param $css //  Ссылка на файл
	 *
	 * @return void
	 */
	public function setCss($css) {
		if(is_array($css)) {
			foreach($css as $file) $this->cssArr[] = $file;
		} else $this->cssArr[] = $css;
		$this->setVar('css', $this->htmlStatic($this->cssArr));
	}

	/**
	 * Добавляет новый JS-файл в массив
	 *
	 * @param $js //  Ссылка на файл
	 *
	 * @return void
	 */
	public function setJs($js) {
		if(is_array($js)) {
			foreach($js as $file) $this->jsArr[] = $file;
		} else $this->jsArr[] = $js;
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
	}

	/**
	 * Обрабатывает ссылки на статичные файлы в HTML формат, добавляет к ним нужные теги
	 *
	 * @param $data
	 * @param $view
	 * @param $type
	 *
	 * @return array
	 */
	public function htmlStatic($data, $view = 'html', $type = 'css') {
		$out = [];
		if('html' == $view) {
			if(is_array($data)) {
				foreach($data as $d) {
					if('css' == $type) {
						$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
					} elseif('js' == $type) {
						$out[] = "<script src='{$d}'></script>";
					}
				}
			} else {
				if('css' == $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
				} elseif('js' == $type) {
					$out[] = "<script src='{$data}'></script>";
				}
			}
		} elseif('link' == $view) {
			if(is_array($data)) {
				foreach($data as $d) {
					$out[] = $d;
				}
			} else {
				$out[] = $data;
			}
		}

		return $out;
	}

	public function upload_file() {}


}

