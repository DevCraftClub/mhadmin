<?php
//===============================================================
// Файл: Admin.php                                              =
// Путь: engine/inc/maharder/_includes/classes/Admin.php        =
// Последнее изменение: 2024-03-14 15:47:43                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

/**
 * @since 2.0.0
 */
class Admin {
	use DataLoader;
	use DleData;
	use AssetsChecker;

	/**
	 * Массив со стилями
	 *
	 * @var array $cssArr
	 */
	private array $cssArr = [
		URL . '/maharder/admin/assets/css/base.css',
		URL . '/maharder/admin/assets/css/icons.css',
		URL . '/maharder/admin/assets/css/tokens.css',
		URL . '/maharder/admin/assets/css/prettify.css',
		URL . '/maharder/admin/assets/css/jquery-confirm.min.css',
		URL . '/maharder/admin/assets/css/theme.css',
		URL . '/maharder/admin/assets/editor/themes/default.min.css',
	];
	/**
	 * Массив со скриптами
	 *
	 * @var array $jsArr
	 */
	private array $jsArr = [
		URL . '/maharder/admin/assets/js/jquery.js',
		URL . '/maharder/admin/assets/js/base.js',
		URL . '/maharder/admin/assets/js/autosize.min.js',
		URL . '/maharder/admin/assets/js/mask.js',
		URL . '/maharder/admin/assets/js/tokens.js',
		URL . '/maharder/admin/assets/js/jquery.timeago.js',
		URL . '/maharder/admin/assets/js/timeago/jquery.timeago.ru.js',
		URL . '/maharder/admin/assets/js/jquery-confirm.min.js',
		URL . '/maharder/admin/assets/editor/sceditor.min.js',
		URL . '/maharder/admin/assets/editor/formats/bbcode.js',
		URL . '/maharder/admin/assets/editor/icons/material.js',
		URL . '/maharder/admin/assets/editor/plugins/autosave.js',
		URL . '/maharder/admin/assets/editor/plugins/autoyoutube.js',
		URL . '/maharder/admin/assets/editor/plugins/undo.js',
		URL . '/maharder/admin/assets/editor/languages/ru.js',
		URL . '/maharder/admin/assets/js/theme.js',
	];
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
	 * author.contacts.name - Название контактной информации, к примеру E-Mail
	 * author.contacts.link - Ссылка для связи, к примеру mailto:dev@devcraft.club
	 * author.donate - Массив с информацией по финансовой поддержке
	 * author.donate.name - Название платёжной системы, к примеру PayPal
	 * author.donate.value - Описание платёжной системы, к примеру paypal.me/MaximH
	 * author.donate.link - Ссылка платёжной системы, к примеру https://paypal.me/MaximH
	 * menu - Массив со ссылками для меню сайта
	 * breadcrumbs - Массив со ссылками на хлебные крошки
	 *
	 * @var array $variables
	 */
	private array $variables = [
		'css_dir'     => '',
		'js_dir'      => '',
		'css'         => [],
		'js'          => [],
		'url'         => URL,
		'lic_link'    => 'https://devcraft.club/pages/licence-agreement/',
		'author'      => [],
		'menu'        => [],
		'breadcrumbs' => [],
	];

	/**
	 * Конструктор класса
	 * Создаёт нужные параметры для изначального старта
	 *
	 * @version 170.2.10
	 * @throws JsonException
	 */
	public function __construct() {
		$this->setVar('css_dir', URL . '/maharder/admin/assets/css');
		$this->setVar('js_dir', URL . '/maharder/admin/assets/js');
		$this->setVar('css', $this->htmlStatic($this->cssArr));
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
		$this->preSetMenu();

		$mh_settings = DataManager::getConfig('maharder');

		if (file_exists(ENGINE_DIR . '/inc/maharder/admin/assets/css/dark.css') && isset($mh_settings['theme']) && $mh_settings['theme'] === 'dark') {
			$this->setCss(URL . '/maharder/admin/assets/css/dark.css');
		}
		$cache_folder = $this->getCacheFolder();
		DataManager::createDir($cache_folder);

		if (is_dir($cache_folder)) {
			file_put_contents($cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 'Order Deny,Allow
Deny from all');
			chmod($cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 0666);
		}

		if ($mh_settings['theme'] === 'dark') {
			$this->setCss(URL . '/maharder/admin/assets/editor/themes/dark.css');
		}

		$this->setAuthor();



	}

	private function setAuthor() : void {
		$this->variables['author'] = [
			'name'     => 'Maxim Harder',
			'contacts' => [
				[
					'name' => __('mhadmin', 'E-Mail'),
					'link' => 'mailto:dev@devcraft.club',
				],
				[
					'name' => __('mhadmin', 'Telegram'),
					'link' => 'https://t.me/MaHarder',
				],
				[
					'name' => __('mhadmin', 'Вебсайт'),
					'link' => 'https://devcraft.club/misc/contact',
				],
			],
			'donate'   => [
				[
					'name'  => 'PayPal',
					'value' => 'paypal.me/MaximH',
					'link'  => 'https://paypal.me/MaximH',
				],
				[
					'name'  => 'Ko-Fi',
					'value' => 'ko-fi.com/devcraft',
					'link'  => 'https://ko-fi.com/J3J118N1C',
				],
				[
					'name'  => 'Yandex.Money (Sobe.ru)',
					'value' => '41001454367103',
					'link'  => 'https://sobe.ru/na/devcraftclub',
				],
				[
					'name'  => 'Yandex.Money (YooMoney)',
					'value' => '41001454367103',
					'link'  => 'https://yoomoney.ru/to/41001454367103',
				],
				[
					'name'  => 'DonationAlerts',
					'value' => '/r/maharder',
					'link'  => 'https://www.donationalerts.com/r/maharder',
				],
			],
		];
	}


	/**
	 * Возвращает массив с данными о ссылке
	 *
	 * @param    string         $name        Название ссылки
	 * @param    string         $href        Ссылка
	 * @param    string         $type        Тип ссылки: link - простая ссылка, divider - разделитель, dropdown -
	 *                                       выпадающее меню, data - оформляет элемент как div со скрытым
	 *                                       дополнительным параметром $data_val
	 * @param    array          $children    Дочерние ссылки, если есть
	 * @param    string|null    $data_val    Дополнительный параметр для выпадающего меню
	 *
	 * @return array
	 */
	public static function generate_link(string $name, string $href, string $type = 'link', array $children = [], ?string $data_val = null) : array {
		return [
			'name'     => $name,
			'href'     => $href,
			'type'     => $type,
			'children' => $children,
			'data'     => $data_val
		];
	}

	/**
	 * Добавляет ссылку в массив меню
	 *
	 * @param    array    $link
	 *
	 * @return void
	 */
	public function setLink(array $link) : void {
		$this->variables['menu'][] = $link;
	}

	/**
	 * Добавляет несколько ссылок в массив меню
	 *
	 * @param    array    $links
	 *
	 * @return void
	 */
	public function setLinks(array $links) : void {
		foreach ($links as $link) {
			$this->setLink($link);
		}
	}

	/**
	 * Подготовка меню для админки
	 * Берёт все доступные ссылки меню из админки самой DLE
	 *
	 * @return void
	 */
	private function preSetMenu() : void {
		global $lang;
		require_once DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php');
		$dle_links_header = [
			'config'         => $lang['opt_hopt'],
			'user'           => $lang['opt_s_acc'],
			'templates'      => $lang['opt_s_tem'],
			'filter'         => $lang['opt_s_fil'],
			'others'         => $lang['opt_s_oth'],
			'admin_sections' => $lang['admin_other_section'],
		];

		$admin_links = [
			self::generate_link($lang['header_all'], '?mod=options&action=options'),
			self::generate_link('', '', 'divider'),
			self::generate_link(__('mhadmin', 'Новости'), '', 'dropdown', [
				self::generate_link($lang['add_news'], '?mod=addnews&action=addnews'),
				self::generate_link($lang['edit_news'], '?mod=editnews&action=list'),
			]),
			self::generate_link('', '', 'divider')
		];

		foreach ($options as $o => $a) {
			$opt_children = [];
			foreach ($a as $c)
				$opt_children[] = self::generate_link($c['name'], $c['url']);
			$admin_links[] = self::generate_link($dle_links_header[$o], '', 'dropdown', $opt_children);
		}

		$this->setLink(self::generate_link(__('mhadmin', 'Страницы DLE'), '', 'dropdown', $admin_links));

	}

	/**
	 * Возвращает переменные
	 *
	 * @return array
	 */
	public function getVariables() : array {
		return $this->variables;
	}

	/*
	 * Добавляет новые или обновляет старые переменные
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setVar(string $name, mixed $value) : void {
		$this->variables[$name] = $value;
	}

	/**
	 * Добавляет или обновляет несколько переменных
	 *
	 * @param    array    $arr    Массив с данными
	 *
	 * @return void
	 */
	public function setVars(array $arr = []) : void {
		foreach ($arr as $name => $value) {
			$this->setVar($name, $value);
		}
	}

	/**
	 * Добавляет новый CSS-файл в массив
	 *
	 * @param    array|string    $css    //  Ссылка на файл
	 *
	 * @return void
	 */
	public function setCss(array|string $css) : void {
		$this->cssArr = array_merge($this->cssArr, (array) $css);
		$this->setVar('css', $this->htmlStatic($this->cssArr));
	}

	/**
	 * Добавляет новый JS-файл в массив
	 *
	 * @param    array|string    $js    //  Ссылка на файл
	 *
	 * @return void
	 */
	public function setJs(array|string $js) : void {
		$this->jsArr = array_merge($this->jsArr, (array) $js);
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
	}

	/**
	 * Обрабатывает ссылки на статичные файлы в HTML формат, добавляет к ним нужные теги
	 *
	 * @param    string|array    $data
	 * @param    string          $view
	 * @param    string          $type
	 *
	 * @return array
	 */
	public function htmlStatic(string|array $data, string $view = 'html', string $type = 'css') : array {
		$out = [];
		if ('html' === $view) {
			if (is_array($data)) {
				foreach ($data as $d) {
					if ('css' === $type) {
						$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
					} elseif ('js' === $type) {
						$out[] = "<script src='{$d}'></script>";
					}
				}
			} else {
				if ('css' === $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
				} elseif ('js' === $type) {
					$out[] = "<script src='{$data}'></script>";
				}
			}
		} elseif ('link' === $view) {
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

	/**
	 * TODO: доработать
	 * @return void
	 */
	public function upload_file() { }


}

