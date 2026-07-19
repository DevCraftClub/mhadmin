<?php
//===============================================================
// Файл: Router.php                                             =
// Путь: devcraft/src/classes/Admin/Router.php                  =
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

use DLEPlugins;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Enums\AdminErrorKind;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Module\PluginContext;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Twig\EnvironmentFactory;
use DevCraft\Core\Interfaces\PageInterface;
use DevCraft\Core\Exception\DevCraftException;

/**
 * Маршрутизатор административных запросов DevCraft.
 *
 * Связывает действие модуля с обработчиком страницы и рендерит layout.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class Router {

	/**
	 * Обрабатывает запрос админки: инициализирует контекст, вызывает страницу и рендерит layout.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed>                                 $config         Глобальная конфигурация DLE.
	 * @global array<string, array<int, array<string, mixed>>>|null $options        Разделы меню DLE.
	 * @global array<string, string>|null                           $lang           Языковые строки DLE.
	 * @global bool|null                                            $is_loged_in    Признак авторизации в DLE.
	 * @global array<int, array<string, mixed>>|null                $user_group     Группы пользователей DLE.
	 * @global array<string, mixed>|null                            $member_id      Текущий пользователь DLE.
	 * @global string|null                                          $dle_login_hash CSRF-хеш DLE.
	 * @global object|null                                          $db             Объект базы данных DLE.
	 *
	 * @param   PluginContext                                       $plugin         Контекст модуля.
	 * @param   string                                              $action         Имя действия или пустая строка для значения по умолчанию.
	 *
	 * @example
	 *     (new Router())->dispatch($plugin, 'dashboard');
	 */
	public function dispatch(PluginContext $plugin, string $action = ''): void {
		/**
		 * Не трогать! Иначе боковое меню не скомпилируется!
		 */
		global $config, $options, $lang, $is_loged_in, $user_group, $member_id, $dle_login_hash, $db;

		if(!defined('LOGGED_IN')) {
			throw new DevCraftException(__('Требуется сессия администратора.'));
		}

		if(isset($_GET['dc_debug']) && $_GET['dc_debug'] === '1') {
			$_SESSION['dc_debug'] = true;
		}

		$mod = $plugin->mod();

		if(!isset($options) && !is_array($options) && defined('ENGINE_DIR')) {
			/** Подключает скин DLE для инициализации $options и бокового меню. */
			require DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php');
		}

		/** @var array<string, array<int, array<string, mixed>>> $dleOptions */
		$dleOptions = is_array($options ?? NULL)? $options : [];
		/** @var array<string, string> $dleLang */
		$dleLang = is_array($lang ?? NULL)? $lang : [];

		$adminContext  = AdminContext::forPlugin($plugin, $dleOptions, $dleLang);
		$errorRenderer = new AdminErrorRenderer();

		Translation::setTranslator();
		Translation::convertXliffToJs();
		EnvironmentFactory::ensureTranslationExtension(Application::instance()->twig());

		if($action === '') {
			$action = $plugin->defaultAction() ?? '';
		}

		if($action === '') {
			$errorRenderer->render(
				AdminErrorKind::Generic,
				__('Ошибка манифеста'),
				__('В манифесте не объявлено стартовое действие (dashboard или index).'),
				500,
				NULL,
				$plugin,
			);

			return;
		}

		$adminContext->setCurrentAction($action);

		$class = $plugin->pageClass($action);

		if($class === NULL || !class_exists($class)) {
			$errorRenderer->render(
				AdminErrorKind::NotFound,
				__('Страница не найдена'),
				__('Неизвестное действие: {action}', ['{action}' => $action]),
				404,
				NULL,
				$plugin,
			);

			return;
		}

		$page = new $class();

		if(!$page instanceof PageInterface) {
			throw new DevCraftException(__('Недопустимый обработчик страницы.'));
		}

		if($page instanceof AbstractPage) {
			$page->bindAdminContext($adminContext);
		}

		$orchestrator = new PageOrchestrator();
		$orchestrated = $orchestrator->prepare($page, $plugin, $action);

		$result = $page->handle();

		$adminContext->finalizeBreadcrumbs($mod, $action);

		$view = $result['view'] ?? 'admin/dashboard.twig';
		$data = array_merge($result['data'] ?? [], $orchestrated);

		$meta = $plugin->meta();

		global $dle_login_hash;

		$data['action']            = $action;
		$data['mod']               = $mod;
		$data['asset_base']        = Application::instance()->public_asset_url();
		$devcraftJsPath            = Paths::templates() . '/core/assets/js/devcraft.js';
		$filterJsPath              = Paths::templates() . '/core/assets/js/filter.js';
		$composerJsPath            = Paths::templates() . '/core/assets/js/composer.js';
		$devcraftMtime             = is_file($devcraftJsPath)? filemtime($devcraftJsPath) : 0;
		$filterMtime               = is_file($filterJsPath)? filemtime($filterJsPath) : 0;
		$data['asset_js_mtime']    = (string) max($devcraftMtime, $filterMtime, 0)? : (string) ($meta['version'] ?? '1.0.0');
		$data['composer_js_mtime'] = is_file($composerJsPath)? (string) filemtime($composerJsPath) : $data['asset_js_mtime'];
		$data['filter_js_mtime']   = $filterMtime > 0? (string) $filterMtime : $data['asset_js_mtime'];
		$data['page_title']        = $data['page_title'] ?? (string) ($meta['name'] ?? 'DevCraft');
		$data['brand_name']        = trim((string) ($meta['name'] ?? ''))? : 'DevCraft';
		$data['dle_login_hash']    = $dle_login_hash ?? '';
		$data['ajax_base_url']     = Paths::ajaxBase();

		$locale      = (string) DevCraftConfig::get('language', 'ru_RU');
		$theme       = (string) DevCraftConfig::get('theme', 'light');
		$metroLocale = Translation::metroLocaleFor($locale);

		$data['devcraft_debug']           = LogGenerator::isDebugEnabled();
		$data['devcraft_theme']           = $theme;
		$data['devcraft_locale']          = $locale;
		$data['devcraft_metro_locale']    = $metroLocale;
		$data['devcraft_html_lang']       = Translation::loadLanguageMeta($locale)->iso2;
		$data['devcraft_metro_i18n_url']  = $this->buildMetroI18nUrl($metroLocale);
		$data['devcraft_translation_url'] = $this->buildTranslationJsUrl($locale);
		$data['module_js_urls']           = $this->buildModuleJsUrls($plugin);

		LogGenerator::for('Router')->debug([
			'mod'       => $mod,
			'action'    => $action,
			'debug'     => $data['devcraft_debug'],
			'theme'     => $theme,
			'html_lang' => $data['devcraft_html_lang'],
		]);

		echo $this->layoutWrap($view, $data, $adminContext, (string) ($meta['version'] ?? '1.0.0'));
	}

	/**
	 * Формирует URL локали Metro UI или null, если addon отсутствует.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $metroLocale  Код локали Metro.
	 *
	 * @return string|null URL JS-локали или null.
	 */
	private function buildMetroI18nUrl(string $metroLocale): ?string {
		$addonPath = Translation::metroLocaleAddonPath($metroLocale);

		if($addonPath === NULL) {
			return NULL;
		}

		$mtime = filemtime($addonPath);

		return Application::instance()->public_asset_url()
		       . 'js/i18n/metro.' . rawurlencode($metroLocale) . '.js?v=' . ($mtime !== false? $mtime : time());
	}

	/**
	 * Формирует URL JS-файла переводов DevCraft для локали.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Код локали DevCraft.
	 *
	 * @return string URL с параметром версии.
	 */
	private function buildTranslationJsUrl(string $locale): string {
		$base  = Application::instance()->public_asset_url();
		$mtime = Translation::jsTranslationFileMtime($locale);

		return $base . 'js/i18n/translation.' . rawurlencode($locale) . '.js?v=' . ($mtime > 0? $mtime : time());
	}

	/**
	 * Собирает URL публичных JS-файлов модуля с версией по mtime.
	 *
	 * @since 200.4.0
	 *
	 * @param   PluginContext  $plugin  Контекст модуля.
	 *
	 * @return list<string> URL скриптов модуля.
	 */
	private function buildModuleJsUrls(PluginContext $plugin): array {
		$urls = [];

		foreach($plugin->jsAssetFiles() as $file) {
			$path = $plugin->modulePath() . '/Public/' . $file;

			if(!is_file($path)) {
				continue;
			}

			$urls[] = Application::instance()->modulePublicAssetUrl($plugin->modulePath())
			          . $file
			          . '?v=' . filemtime($path);
		}

		return $urls;
	}

	/**
	 * Оборачивает содержимое страницы в общий layout админки.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $contentView   Ключ шаблона страницы.
	 * @param   array<string, mixed>  $data          Данные страницы.
	 * @param   AdminContext          $adminContext  Контекст админки.
	 * @param   string                $version       Версия модуля для layout.
	 *
	 * @return string HTML layout.
	 */
	private function layoutWrap(string $contentView, array $data, AdminContext $adminContext, string $version): string {
		$inner = Application::instance()->twig()->render(AbstractPage::resolveViewKey($contentView), $data);

		return Application::instance()->twig()->render('core/layout.twig', array_merge($data, [
			'content' => $inner,
			'title'   => (string) ($data['page_title'] ?? 'DevCraft'),
			'version' => $version,
			'admin'   => $adminContext->toArray(),
		]));
	}

}
