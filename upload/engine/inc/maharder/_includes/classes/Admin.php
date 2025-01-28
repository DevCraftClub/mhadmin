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

use JetBrains\PhpStorm\ExpectedValues;

/**
 * Класс Admin предназначен для управления административной панелью проекта.
 *
 * Содержит функционал для подключения файлов CSS и JS, настройки переменных
 * окружения, работы с меню и шапками страниц. Использует трейты для
 * загрузки данных, взаимодействия с DLE и проверки файлов ресурсов.
 *
 * @since 2.0.0
 */
class Admin {
	use DataLoader;
	use DleData;
	use AssetsChecker;

	/**
	 * Список CSS-файлов, используемых в разделе администратора.
	 *
	 * @var array
	 * @see Admin::setCss() Метод для добавления CSS-файлов.
	 * @global string URL Глобальная переменная, содержащая базовый URL проекта.
	 */
	private array $cssArr
		= [
			URL . '/maharder/admin/assets/css/base.css',
			URL . '/maharder/admin/assets/css/icons.css',
			URL . '/maharder/admin/assets/css/tokens.css',
			URL . '/maharder/admin/assets/css/prettify.css',
			URL . '/maharder/admin/assets/css/jquery-confirm.min.css',
			URL . '/maharder/admin/assets/css/theme.css',
			URL . '/maharder/admin/assets/editor/themes/default.min.css',
		];
	/**
	 * Массив со скриптами, которые используются в административной панели.
	 *
	 * Скрипты представляют собой коллекцию путей к JavaScript-файлам,
	 * необходимых для работы функциональных компонентов интерфейса.
	 *
	 * @var array $jsArr Массив содержит ссылки на JavaScript-файлы,
	 *                   таких, как библиотеки, редактор SCEditor и его плагины,
	 *                   а также прочие утилиты для интерфейса административной панели.
	 * @global string URL Глобальная переменная, определяющая базовый URL приложения.
	 * @see Admin::setJs() Метод для добавления пользовательских JavaScript-файлов.
	 */
	private array $jsArr
		= [
			URL . '/maharder/admin/assets/js/jquery.js',
			URL . '/maharder/admin/assets/js/base.js',
			URL . '/maharder/admin/assets/js/autosize.min.js',
			URL . '/maharder/admin/assets/js/mask.js',
			URL . '/maharder/admin/assets/js/tokens.js',
			URL . '/maharder/admin/assets/js/jquery.timeago.js',
			URL . '/maharder/admin/assets/js/timeago/jquery.timeago.ru.js',
			URL . '/maharder/admin/assets/js/jquery-confirm.min.js',
			URL . '/maharder/admin/assets/js/clipboard.js',
			URL . '/maharder/admin/assets/editor/sceditor.min.js',
			URL . '/maharder/admin/assets/editor/formats/bbcode.js',
			URL . '/maharder/admin/assets/editor/icons/material.js',
			URL . '/maharder/admin/assets/editor/plugins/autosave.js',
			URL . '/maharder/admin/assets/editor/plugins/autoyoutube.js',
			URL . '/maharder/admin/assets/editor/plugins/undo.js',
			URL . '/maharder/admin/assets/js/theme.js',
		];
	/**
	 * Массив с различными переменными для шаблонизатора.
	 *
	 * Используется для настройки и передачи данных, таких как пути к стилям и скриптам, информация об авторе,
	 * ссылки для хлебных крошек, а также настройки меню и другие параметры.
	 *
	 * Описание ключей:
	 * - **css_dir**: строка, папка со стилями.
	 * - **js_dir**: строка, папка со скриптами.
	 * - **css**: массив, содержит список подключаемых стилей.
	 * - **js**: массив, содержит список подключаемых скриптов.
	 * - **url**: строка, ссылка для корректного отображения стилей и скриптов на сайте.
	 * - **lic_link**: строка, ссылка на пользовательское соглашение.
	 * - **author**: массив, информация об авторе модуля:
	 *   - **author.name**: строка, имя автора.
	 *   - **author.contacts**: массив, список контактной информации автора:
	 *     - **author.contacts.name**: строка, название контактного средства (например, E-Mail).
	 *     - **author.contacts.link**: строка, ссылка для связи (например, mailto:dev@devcraft.club).
	 *   - **author.donate**: массив, данные о поддержке:
	 *     - **author.donate.name**: строка, название платёжной системы (например, PayPal).
	 *     - **author.donate.value**: строка, описание платёжной системы (например, paypal.me/MaximH).
	 *     - **author.donate.link**: строка, ссылка платёжной системы (например, https://paypal.me/MaximH).
	 * - **menu**: массив, ссылки для меню сайта.
	 * - **breadcrumbs**: массив, ссылки на хлебные крошки.
	 *
	 * @var array $variables Значение свойств этой переменной задаётся по умолчанию или изменяется через методы класса.
	 * @see Admin::setVar() Для установки/обновления одного или нескольких значений.
	 * @see Admin::getVariables() Для получения массива переменных.
	 * @see Admin::preSetMenu() Для предварительной настройки параметров меню.
	 * @see Admin::setDefaultAuthor() Для настройки информации об авторе.
	 * @see Admin::setLinks() Для присвоения ссылок (например, для меню или хлебных крошек).
	 */
	private array $variables
		= [
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
	 * Хранит данные для построения хлебных крошек в административной панели.
	 *
	 * @var array
	 * @see Admin::setBreadcrumb() Используется для установки значений хлебных крошек.
	 * @see Admin::getBreadcrumb() Используется для получения текущих значений хлебных крошек.
	 */
	private array $breadcrumb = [];
	private array $links = [];

	/**
	 * Конструктор класса.
	 * Инициализирует параметры для начальной загрузки системы,
	 * включая настройки файлов CSS и JS, меню, темы и создание
	 * базовых директорий кеша.
	 *
	 * @version 170.2.10
	 *
	 * @throws JsonException|Throwable Генерируется при ошибках обработки JSON, связанных с конфигурацией.
	 * @see     setVar() Используется для установки значений в массив переменных.
	 * @see     htmlStatic() Используется для генерации HTML-кода подключения CSS и JS.
	 * @see     preSetMenu() Устанавливает базовые настройки меню.
	 * @see     DataManager::getConfig() Загружает конфигурацию из JSON-файла.
	 * @see     DataManager::createDir() Создает директорию для хранения кеша.
	 */
	public function __construct() {
		$this->setVar('css_dir', URL . '/maharder/admin/assets/css');
		$this->setVar('js_dir', URL . '/maharder/admin/assets/js');
		$this->setVar('css', $this->htmlStatic($this->cssArr));
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));

		$mh_settings = DataManager::getConfig('maharder');

		if (file_exists(
				ENGINE_DIR . '/inc/maharder/admin/assets/css/dark.css'
			) && isset($mh_settings['theme']) && $mh_settings['theme'] === 'dark') {
			$this->setCss(URL . '/maharder/admin/assets/css/dark.css');
		}
		$cache_folder = $this->getCacheFolder();
		DataManager::createDir($cache_folder);

		if (is_dir($cache_folder)) {
			file_put_contents(
				$cache_folder . DIRECTORY_SEPARATOR . '.htaccess',
				'Order Deny,Allow
Deny from all'
			);
			chmod($cache_folder . DIRECTORY_SEPARATOR . '.htaccess', 0666);
		}

		if ($mh_settings['theme'] === 'dark') {
			$this->setCss(URL . '/maharder/admin/assets/editor/themes/dark.css');
		}

		$this->setDefaultAuthor();
		$this->setJs(URL . '/maharder/admin/assets/editor/languages/' . MhTranslation::getLocaleData(MhTranslation::getLocale())['iso2'] . '.js'
		);

	}

	/**
	 * Устанавливает информацию об авторе и сохраняет её в массиве глобальных переменных.
	 *
	 * Информация об авторе включает:
	 * - Имя автора;
	 * - Контактные данные (например, E-Mail, Telegram, Вебсайт);
	 * - Данные о пожертвованиях (например, PayPal, Ko-Fi, Yandex.Money, DonationAlerts).
	 *
	 * Контактные данные локализуются с использованием функции `__()`.
	 *
	 * @return void
	 * @throws Throwable В случае ошибок при добавлении информации или работе с объектом Author.
	 *
	 * @see variables Глобальный массив, где сохраняется информация об авторе.
	 * @see __ Функция локализации строк.
	 * @see Author Класс для работы с информацией об авторе.
	 */
	private function setDefaultAuthor(): void {
		$this->variables['author'] = (new Author('Maxim Harder'))->addContact(__('E-Mail'), 'mailto:dev@devcraft.club')
																 ->addContact(__('Telegram'), 'https://t.me/MaHarder')
																 ->addContact(__('Вебсайт'), 'https://devcraft.club/misc/contact')
																 ->addDonation(__('PayPal'), 'paypal.me/MaximH', 'https://paypal.me/MaximH')
																 ->addDonation(__('Ko-Fi'), 'ko-fi.com/devcraft', 'https://ko-fi.com/J3J118N1C')
																 ->addDonation(__('YooMoney'), '41001454367103', 'https://yoomoney.ru/to/41001454367103')
																 ->addDonation(__('DonationAlerts'), '/r/maharder', 'https://www.donationalerts.com/r/maharder');

	}

	/**
	 * Устанавливает объект автора в массив переменных.
	 *
	 * @param Author $autor Объект автора, содержащий информацию об имени, контактах и пожертвованиях.
	 *
	 * @see Author
	 * @global array $variables
	 */
	public function setAuthor(Author $autor): void {
		$this->variables['author'] = $autor;

	}

	/**
	 * Создаёт экземпляр класса AdminLink с данными о ссылке.
	 *
	 * @param string      $name       Название ссылки.
	 * @param string      $href       URL-адрес ссылки.
	 * @param string      $type       Тип ссылки. Возможные значения:
	 *                                - link: простая ссылка;
	 *                                - divider: разделитель;
	 *                                - dropdown: выпадающее меню;
	 *                                - data: элемент div с дополнительным скрытым параметром $data_val.
	 * @param array       $children   Массив дочерних ссылок, если имеются.
	 * @param string|null $data_val   Дополнительный скрытый параметр, используемый для типа data.
	 *
	 * @return AdminLink Объект класса AdminLink с заданными параметрами.
	 *
	 * @see AdminLink
	 */
	public static function generate_link(string $name, string $href, string $type = 'link', array $children = [], ?string $data_val = null): AdminLink {
		return new AdminLink(name: $name, link: $href, type: $type, extra: $data_val, children: $children);
	}

	/**
	 * Возвращает информацию о ссылке из меню.
	 *
	 * @param string $name Имя ссылки в меню.
	 * @return bool|AdminLink Объект AdminLink, если ссылка найдена, или false, если ссылка отсутствует.
	 */
	public function getLinkInfo(string $name): bool|AdminLink {
		return $this->variables['menu'][$name] ?? false;
	}

	/**
	 * Возвращает имя ссылки на основе предоставленного имени.
	 *
	 * Метод извлекает информацию о ссылке с помощью метода getLinkInfo().
	 * Если информация найдена, возвращается имя ссылки через метод getName().
	 * В противном случае возвращается false.
	 *
	 * @param string $name Имя, по которому производится поиск информации о ссылке.
	 * @return string|false Имя ссылки или false, если информация не найдена.
	 *
	 * @see getLinkInfo()
	 * @see AdminLink::getName()
	 */
	public function getLinkName(string $name): string|false {
		$info = $this->getLinkInfo($name);
		return $info ? $info->getName() : false;
	}

	/**
	 * Возвращает URL ссылки по указанному имени.
	 *
	 * Метод извлекает информацию о ссылке с помощью метода getLinkInfo().
	 * Если информация найдена, возвращается URL ссылки.
	 * Если информация отсутствует, возвращается false.
	 *
	 * @param string $name Имя ссылки для поиска.
	 * @return string|false URL ссылки, если информация доступна, или false, если ссылка не найдена.
	 *
	 * @see self::getLinkInfo() Используется для получения информации о ссылке.
	 * @see AdminLink::getLink() Используется для получения URL из объекта ссылки.
	 */
	public function getLinkUrl(string $name): string|false {
		$info = $this->getLinkInfo($name);
		return $info ? $info->getLink() : false;
	}

	/**
	 * Устанавливает ссылку в массив меню для указанного родительского элемента.
	 *
	 * Этот метод добавляет объект `AdminLink` в массив `menu`, вложенный в
	 * переменную `variables`, обеспечивая возможность хранения структуры меню
	 * с учетом родительских элементов. Основное назначение метода — организовать
	 * меню по принципу вложенности.
	 *
	 * @param AdminLink $link Объект, представляющий ссылку.
	 *                        Объект содержит данные о названии, адресе,
	 *                        типе и вложенных элементах.
	 * @param string $parent Идентификатор родительского элемента,
	 *                       к которому будет добавлена ссылка.
	 *
	 * @return void
	 * @see self::$variables
	 * @see self::generate_link()
	 */
	public function setLink(AdminLink $link, string $parent): void {
		$this->variables['menu'][$parent] = $link;
	}

	/**
	 * Добавляет несколько ссылок в массив меню.
	 *
	 * Использует метод `setLink` для добавления каждой ссылки.
	 *
	 * @param array  $links     Массив ссылок, которые необходимо добавить. Каждая ссылка представлена
	 *                          массивом, содержащим параметры ссылки.
	 *
	 * @return void
	 *
	 * @see Admin::setLink() Используется для добавления отдельной ссылки.
	 * @global array $variables Глобальная переменная для хранения данных меню и других свойств.
	 */
	public function setLinks(array $links): void {
		foreach ($links as $link) {
			$this->setLink($link);
		}
	}

	/**
	 * Получает массив переменных, связанных с модулем.
	 *
	 * Метод возвращает ассоциативный массив с переменными, используемыми в модуле,
	 * такими как параметры путей (CSS, JS), массивы подключаемых файлов, дополнительные ссылки, меню
	 * и настройки. Если передано название переменной, метод возвращает только значение данной переменной.
	 *
	 * @param string|null $name Название переменной для выборки. Если не указано, возвращаются все переменные.
	 * @return array Ассоциативный массив переменных модуля или значение конкретной переменной.
	 * @see Admin::$variables Переменные модуля, содержат информацию для работы и настроек.
	 * @see Admin::setVars() Устанавливает значения переменных модуля.
	 * @see Admin::setLinks() Добавляет ссылки в переменные модуля.
	 */
	public function getVariables(?string $name = null): array {
		return $name ? $this->variables[$name] : $this->variables;
	}

	/**
	 * Устанавливает значение переменной или обновляет существующую.
	 *
	 * Метод добавляет новую переменную в массив `$variables` или обновляет значение
	 * уже существующей, идентифицируемой по имени.
	 *
	 * @param string $name      Имя переменной, которую нужно добавить или обновить.
	 * @param mixed  $value     Значение переменной, которое нужно установить.
	 *
	 * @global array $variables Массив, содержащий текущие данные переменных,
	 *                          представлен по умолчанию с такими ключами, как 'css_dir', 'js_dir',
	 *                          'css', 'js', 'url', 'lic_link', 'author', 'menu', 'breadcrumbs'.
	 *
	 * @see $variables
	 */
	public function setVar(
		#[ExpectedValues(values:
			[
				'css_dir', 'js_dir',
				'css', 'js',
				'url', 'lic_link',
				'author', 'menu', 'breadcrumbs'
			])]
		string $name, mixed $value): void {
		$this->variables[$name] = $value;
	}

	/**
	 * Устанавливает или обновляет несколько переменных в массиве `$variables`.
	 *
	 * Метод принимает ассоциативный массив и обновляет соответствующие переменные с использованием
	 * метода {@see Admin::setVar}. Если ключ из массива отсутствует в допустимых значениях,
	 * он все равно будет добавлен.
	 *
	 * @param array $arr Ассоциативный массив, где ключи — это имена переменных, а значения — их значения.
	 *
	 * @return void
	 *
	 * @see Admin::setVar() Используется для установки значения конкретной переменной.
	 * @see Admin::$variables Массив, содержащий существующие переменные.
	 */
	public function setVars(array $arr = []): void {
		foreach ($arr as $name => $value) {
			$this->setVar($name, $value);
		}
	}

	/**
	 * Добавляет новый CSS-файл в массив `cssArr` и обновляет соответствующую переменную `css`.
	 *
	 * Метод принимает путь или массив путей к CSS-файлам и добавляет их
	 * в массив `cssArr`. Впоследствии массив обрабатывается с помощью функции
	 * `htmlStatic`, которая генерирует массив HTML-тегов `<link>`.
	 * Обновленный массив HTML-тегов записывается в переменную `css` через метод `setVar`.
	 *
	 * @param array|string $css Ссылка или массив ссылок на CSS-файл(ы), которые нужно добавить.
	 *
	 * @see Admin::$cssArr Для получения информации о массиве коллекции CSS-файлов.
	 * @see Admin::setVar Для обновления переменных в массиве `variables`.
	 * @see Admin::htmlStatic Для преобразования массива в список HTML-тегов.
	 *
	 * @return void
	 */
	public function setCss(array|string $css): void {
		$this->cssArr = array_merge($this->cssArr, (array)$css);
		$this->setVar('css', $this->htmlStatic($this->cssArr));
	}

	/**
	 * Добавляет новый JavaScript файл в массив `$jsArr`.
	 *
	 * Метод принимает один или несколько путей до JavaScript файлов и объединяет их
	 * с массивом `$jsArr`. После этого обновляет переменную `js` с помощью вызова метода `htmlStatic`
	 * для формирования HTML-тегов `<script>`, необходимых для подключения этих файлов.
	 *
	 * @param array|string $js Путь или массив путей к JavaScript файлам, которые будут добавлены.
	 *
	 * @return void
	 *
	 * @see Admin::$jsArr Для получения информации о array $jsArr Массив, содержащий пути к JavaScript файлам.
	 * @see Admin::setVar() Для обновления переменной `js`.
	 * @see Admin::htmlStatic() Для формирования HTML-кода на основе массива JavaScript файлов.
	 */
	public function setJs(array|string $js): void {
		$this->jsArr = array_merge($this->jsArr, (array)$js);
		$this->setVar('js', $this->htmlStatic($this->jsArr, 'html', 'js'));
	}

	/**
	 * Обрабатывает массив или строку с данными ссылок на статичные файлы и формирует HTML-разметку
	 * или массив ссылок в зависимости от переданных параметров.
	 *
	 * Формирование HTML производится для типов файлов `css` и `js`, добавляя соответствующие теги
	 * (`<link>` или `<script>`). В случае, если используются ссылки, возвращается только массив ссылок.
	 *
	 * @param string|array $data Массив или строка с данными ссылок на статичные файлы.
	 * @param string       $view Тип возвращаемого результата. Может быть `html` (по умолчанию)
	 *                           для формирования HTML-кода или `link` для возвращения ссылок.
	 * @param string       $type Тип обрабатываемого ресурса. Поддерживаются `css` (по умолчанию) и `js`.
	 *
	 * @return array Массив с HTML-строками или ссылками в зависимости от параметра `$view`.
	 *
	 * @see Admin::$cssArr Глобальный массив CSS-файлов, передаваемый для обработки.
	 * @see Admin::$jsArr Глобальный массив JavaScript-файлов, передаваемый для обработки.
	 *
	 * @see self::setCss() Метод для добавления и обработки CSS-файлов.
	 * @see self::setJs() Метод для добавления и обработки JS-файлов.
	 * @see self::__construct() Конструктор, инициализирующий работу с CSS и JS.
	 */
	public function htmlStatic(string|array $data, string $view = 'html', string $type = 'css'): array {
		$out = [];
		if ('html' === $view) {
			if (is_array($data)) {
				foreach ($data as $d) {
					if ('css' === $type) {
						$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
					} else if ('js' === $type) {
						$out[] = "<script src='{$d}'></script>";
					}
				}
			} else {
				if ('css' === $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
				} else if ('js' === $type) {
					$out[] = "<script src='{$data}'></script>";
				}
			}
		} else if ('link' === $view) {
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
	 * Возвращает текущий массив данных хлебных крошек.
	 *
	 * @return array Массив хлебных крошек.
	 * @see Admin::$breadcrumb
	 */
	public function getBreadcrumb(): array {
		return $this->breadcrumb;
	}

	/**
	 * Добавляет объект хлебной крошки в массив крошек
	 *
	 * @param BreadCrumb $crumb      Объект хлебной крошки для установки.
	 *
	 * @return void
	 *
	 * @see BreadCrumb
	 * @global array     $breadcrumb Список всех хлебных крошек.
	 */
	public function setBreadcrumb(BreadCrumb $crumb): void {
		$this->breadcrumb[] = $crumb;

	}



	/**
	 * TODO: доработать
	 *
	 * @return void
	 */
	public function upload_file() { }

}
