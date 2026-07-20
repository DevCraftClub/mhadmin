<?php
//===============================================================
// Файл: AdminContext.php                                       =
// Путь: devcraft/src/classes/Admin/AdminContext.php            =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Admin;

use DevCraft\Types\Author;
use DevCraft\Types\AdminLink;
use DevCraft\Core\Application;
use DevCraft\Types\BreadCrumb;
use DevCraft\Core\Module\PluginContext;

/**
 * Контекст административной панели для текущего запроса модуля.
 *
 * Содержит меню, хлебные крошки, URL ассетов и метаданные автора.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class AdminContext {

	/**
	 * Определение название главной страницы.
	 * По умолчанию: Главная
	 *
	 * @since 200.4.0
	 * @var string|null
	 */
	public ?string $dashboardName = NULL;

	/**
	 * Ссылка на пользовательское соглашение между автором и пользователем.
	 * По умолчанию: https://devcraft.club/pages/licence-agreement/
	 *
	 * @since 200.4.0
	 * @var string|null
	 */
	public ?string $licenseAgreementLink = NULL;

	/**
	 * Кэшированное меню админки.
	 *
	 * @since 200.4.0
	 * @var AdminLink[]
	 */
	private array $menu = [];

	/**
	 * Хлебные крошки текущей страницы.
	 *
	 * @since 200.4.0
	 * @var BreadCrumb[]
	 */
	private array $breadcrumbs = [];

	/**
	 * URL подключаемых CSS-файлов.
	 *
	 * @since 200.4.0
	 * @var string[]
	 */
	private array $cssUrls = [];

	/**
	 * URL подключаемых JS-файлов.
	 *
	 * @since 200.4.0
	 * @var string[]
	 */
	private array $jsUrls = [];

	/**
	 * Данные автора модуля.
	 *
	 * @since 200.4.0
	 * @var Author
	 */
	private Author $author;

	/**
	 * Базовый URL сайта.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private string $url;

	/**
	 * Текущее действие админки.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private string $currentAction = '';

	/**
	 * Создаёт контекст админки для указанного модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   PluginContext                                    $plugin   Контекст модуля.
	 * @param   array<string, array<int, array<string, mixed>>>  $options  Разделы меню DLE.
	 * @param   array<string, string>                            $lang     Языковые строки DLE.
	 */
	public function __construct(
		PluginContext $plugin,
		array         $options,
		array         $lang,
	) {
		$moduleData = $plugin->moduleData();

		$this->author               = $moduleData->author ?? Author::fromArray([]);
		$this->licenseAgreementLink = $moduleData->licLink;
		$this->url                  = $this->resolveSiteUrl();
		$this->menu                 = (new MenuComposer())->compose(new DleMenuBuilder(), $plugin, $options, $lang);
		$this->cssUrls              = $this->baseCssUrls();
		$this->jsUrls               = $this->baseJsUrls();
		$this->dashboardName        = __('Главная');
	}

	/**
	 * Фабричный метод создания контекста для модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   PluginContext                                    $plugin   Контекст модуля.
	 * @param   array<string, array<int, array<string, mixed>>>  $options  Разделы меню DLE.
	 * @param   array<string, string>                            $lang     Языковые строки DLE.
	 *
	 * @return self Новый экземпляр контекста.
	 *
	 * @example
	 *     $ctx = AdminContext::forPlugin($plugin, $options, $lang);
	 */
	public static function forPlugin(PluginContext $plugin, array $options, array $lang): self {
		return new self($plugin, $options, $lang);
	}

	/**
	 * Устанавливает текущее действие админки.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $action  Имя действия.
	 *
	 * @return self Текущий экземпляр для цепочки вызовов.
	 *
	 * @example
	 *     $ctx->setCurrentAction('logs');
	 */
	public function setCurrentAction(string $action): self {
		$this->currentAction = $action;

		return $this;
	}

	/**
	 * Возвращает текущее действие админки.
	 *
	 * @since 200.4.0
	 *
	 * @return string Имя действия.
	 *
	 * @example
	 *     $action = $ctx->currentAction();
	 */
	public function currentAction(): string {
		return $this->currentAction;
	}

	/**
	 * Возвращает меню админки.
	 *
	 * @since 200.4.0
	 *
	 * @return AdminLink[] Список корневых пунктов меню.
	 *
	 * @example
	 *     $menu = $ctx->menu();
	 */
	public function menu(): array {
		return $this->menu;
	}

	/**
	 * Возвращает хлебные крошки страницы.
	 *
	 * @since 200.4.0
	 *
	 * @return BreadCrumb[] Список крошек.
	 *
	 * @example
	 *     $crumbs = $ctx->breadcrumbs();
	 */
	public function breadcrumbs(): array {
		return $this->breadcrumbs;
	}

	/**
	 * Возвращает данные автора модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return Author Объект автора.
	 *
	 * @example
	 *     $author = $ctx->author();
	 */
	public function author(): Author {
		return $this->author;
	}

	/**
	 * Возвращает базовый URL сайта.
	 *
	 * @since 200.4.0
	 *
	 * @return string URL без завершающего слэша.
	 *
	 * @example
	 *     $siteUrl = $ctx->url();
	 */
	public function url(): string {
		return $this->url;
	}

	/**
	 * Возвращает ссылку на лицензионное соглашение.
	 *
	 * @since 200.4.0
	 *
	 * @return string URL соглашения.
	 *
	 * @example
	 *     $licUrl = $ctx->licLink();
	 */
	public function licLink(): string {
		return $this->licenseAgreementLink;
	}

	/**
	 * Добавляет хлебную крошку в конец списка.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $title  Заголовок крошки.
	 * @param   string|null  $url    Необязательная ссылка.
	 *
	 * @return self Текущий экземпляр для цепочки вызовов.
	 *
	 * @example
	 *     $ctx->addBreadcrumb(__('Журнал'), '?mod=devcraft&action=logs');
	 */
	public function addBreadcrumb(string $title, ?string $url = NULL): self {
		$this->breadcrumbs[] = new BreadCrumb($title, $url);

		return $this;
	}

	/**
	 * Дополняет крошки ссылкой на dashboard и сохраняет крошки страницы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $mod     Имя модуля.
	 * @param   string  $action  Текущее действие.
	 *
	 * @example
	 *     $ctx->finalizeBreadcrumbs('devcraft', 'settings');
	 */
	public function finalizeBreadcrumbs(string $mod, string $action): void {
		$dashboardUrl = '?mod=' . rawurlencode($mod) . '&action=dashboard';
		$pageCrumbs   = $this->breadcrumbs;

		$this->breadcrumbs = [];

		if($action === 'dashboard') {
			$this->addBreadcrumb($this->dashboardName);

			return;
		}

		$this->addBreadcrumb($this->dashboardName, $dashboardUrl);

		foreach($pageCrumbs as $crumb) {
			$this->breadcrumbs[] = $crumb;
		}
	}

	/**
	 * Регистрирует дополнительный CSS-файл.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $url  URL стиля.
	 *
	 * @return self Текущий экземпляр для цепочки вызовов.
	 *
	 * @example
	 *     $ctx->addCss($assetBase . 'css/custom.css');
	 */
	public function addCss(string $url): self {
		if($url !== '' && !in_array($url, $this->cssUrls, true)) {
			$this->cssUrls[] = $url;
		}

		return $this;
	}

	/**
	 * Регистрирует дополнительный JS-файл.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $url  URL скрипта.
	 *
	 * @return self Текущий экземпляр для цепочки вызовов.
	 *
	 * @example
	 *     $ctx->addJs($assetBase . 'js/custom.js');
	 */
	public function addJs(string $url): self {
		if($url !== '' && !in_array($url, $this->jsUrls, true)) {
			$this->jsUrls[] = $url;
		}

		return $this;
	}

	/**
	 * Возвращает список URL CSS-файлов.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] URL стилей.
	 *
	 * @example
	 *     $css = $ctx->cssUrls();
	 */
	public function cssUrls(): array {
		return $this->cssUrls;
	}

	/**
	 * Возвращает список URL JS-файлов.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] URL скриптов.
	 *
	 * @example
	 *     $js = $ctx->jsUrls();
	 */
	public function jsUrls(): array {
		return $this->jsUrls;
	}

	/**
	 * Преобразует контекст в массив для передачи в Twig.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Ассоциативный массив данных layout.
	 *
	 * @example
	 *     $data['admin'] = $adminContext->toArray();
	 */
	public function toArray(): array {
		return [
			'menu'                 => array_map(
				static fn(AdminLink $link): array => $link->toArray(),
				$this->menu,
			),
			'breadcrumbs'          => array_map(
				static fn(BreadCrumb $crumb): array => $crumb->toArray(),
				$this->breadcrumbs,
			),
			'author'               => $this->author->toArray(),
			'url'                  => $this->url,
			'licenseAgreementLink' => $this->licenseAgreementLink,
			'css_urls'             => $this->cssUrls,
			'js_urls'              => $this->jsUrls,
			'current_action'       => $this->currentAction,
		];
	}

	/**
	 * Определяет базовый URL сайта из глобальной конфигурации DLE.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed> $config Глобальная конфигурация DLE.
	 *
	 * @return string URL без завершающего слэша.
	 */
	private function resolveSiteUrl(): string {
		global $config;

		return rtrim((string) ($config['http_home_url'] ?? '/'), '/');
	}

	/**
	 * Формирует базовый список CSS DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] URL стилей Metro и DevCraft.
	 */
	private function baseCssUrls(): array {
		$base = Application::instance()->public_asset_url();

		return [
			$base . 'css/metro.css',
			$base . 'css/icons.css',
			$base . 'css/devcraft.css',
		];
	}

	/**
	 * Формирует базовый список JS DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] URL скриптов Metro и DevCraft.
	 */
	private function baseJsUrls(): array {
		$base = Application::instance()->public_asset_url();

		return [
			$base . 'js/metro.js',
			$base . 'js/devcraft.js',
		];
	}

}
