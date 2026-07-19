<?php
//===============================================================
// Файл: PluginContext.php                                      =
// Путь: devcraft/src/classes/Module/PluginContext.php          =
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

namespace DevCraft\Core\Module;

use DLEPlugins;
use DevCraft\Types\AdminLink;
use DevCraft\Types\Changelog;
use DevCraft\Types\FormSchema;
use DevCraft\Types\ModuleManifest;
use DevCraft\Types\FilterSchema;
use DevCraft\Core\Admin\AdminLinkResolver;

/**
 * Контекст одного DevCraft-модуля после загрузки manifest.php.
 *
 * Предоставляет меню админки, схемы настроек и фильтров, AJAX-обработчики
 * и метаданные для маршрутизации и рендеринга страниц.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Module
 */
final class PluginContext {

	/**
	 * Полный массив данных из manifest.php.
	 *
	 * @since 200.4.0
	 * @var array<string, mixed>
	 */
	private array $manifest;

	/**
	 * Пункты меню админки модуля.
	 *
	 * @since 200.4.0
	 * @var AdminLink[]
	 */
	private array $menu;

	/**
	 * Карта AJAX-методов: имя метода => FQCN обработчика.
	 *
	 * @since 200.4.0
	 * @var array<string, class-string>
	 */
	private array $ajaxMethods;

	/**
	 * Публичные AJAX-методы сайта: method => [handler, allow_guest].
	 *
	 * @since 200.4.0
	 * @var array<string, array{handler: class-string, allow_guest: bool}>
	 */
	private array $ajaxPublicMethods;

	/**
	 * Схема полей настроек модуля (если есть settings.schema.php).
	 *
	 * @since 200.4.0
	 * @var FormSchema|null
	 */
	private ?FormSchema $settingsSchema = NULL;

	/**
	 * Схемы фильтров по action (например, «logs»).
	 *
	 * @since 200.4.0
	 * @var array<string, FilterSchema>
	 */
	private array $filterSchemas = [];

	/**
	 * Создаёт контекст плагина из загруженного манифеста и пути к модулю.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $mod         Идентификатор модуля в URL админки.
	 * @param   array<string, mixed>  $manifest    Данные из manifest.php.
	 * @param   string                $modulePath  Абсолютный путь к каталогу модуля.
	 *
	 * @example
	 *        $plugin = new PluginContext('devcraft', $manifest, $modulePath);
	 *
	 */
	public function __construct(
		private readonly string $mod,
		array                   $manifest,
		private readonly string $modulePath,
	) {
		$this->manifest          = $manifest;
		$this->menu              = $this->parseMenu($manifest['menu'] ?? []);
		$this->ajaxMethods       = $this->parseAjaxMethods($manifest['ajax']['methods'] ?? []);
		$this->ajaxPublicMethods = $this->parseAjaxPublicMethods($manifest['ajax']['public'] ?? []);

		AdminLinkResolver::validateStartActions($this->menu);

		$this->loadSchemas();
	}

	/**
	 * Возвращает идентификатор модуля (mod) для URL и конфигурации.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение mod из манифеста или переопределения.
	 * @example
	 *        $mod = $plugin->mod();
	 *
	 */
	public function mod(): string {
		return $this->mod;
	}

	/**
	 * Возвращает блок meta манифеста с добавленным module_code.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Метаданные модуля для шаблонов.
	 * @example
	 *        $title = $plugin->meta()['title'] ?? '';
	 *
	 */
	public function meta(): array {
		/** @var array<string, mixed> $meta */
		$meta = $this->manifest['meta'] ?? [];

		$meta['module_code'] = $this->manifest['code'] ?? $this->mod;

		return $meta;
	}

	/**
	 * Возвращает пункты меню админки модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return AdminLink[] Ссылки меню из manifest.php.
	 * @example
	 *        $links = $plugin->menu();
	 *
	 */
	public function menu(): array {
		return $this->menu;
	}

	/**
	 * Возвращает FQCN класса страницы для указанного action.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $action  Имя action из URL.
	 *
	 * @return string|null FQCN класса страницы или null.
	 * @example
	 *        $class = $plugin->pageClass('settings');
	 *
	 */
	public function pageClass(string $action): ?string {
		return AdminLinkResolver::resolvePageClass($this->menu, $action);
	}

	/**
	 * Возвращает action страницы по умолчанию (start) для модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return string|null Имя action или null, если меню пусто.
	 * @example
	 *        $default = $plugin->defaultAction() ?? 'dashboard';
	 *
	 */
	public function defaultAction(): ?string {
		return AdminLinkResolver::defaultAction($this->menu);
	}

	/**
	 * Возвращает схему настроек модуля, если файл settings.schema.php существует.
	 *
	 * @since 200.4.0
	 *
	 * @return FormSchema|null Схема полей или null.
	 * @example
	 *        $schema = $plugin->settingsSchema();
	 *
	 */
	public function settingsSchema(): ?FormSchema {
		return $this->settingsSchema;
	}

	/**
	 * Возвращает схему фильтра для указанного action (например, «logs»).
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $action  Ключ action, для которого загружена схема фильтра.
	 *
	 * @return FilterSchema|null Схема фильтра или null.
	 * @example
	 *        $filter = $plugin->filterSchema('logs');
	 *
	 */
	public function filterSchema(string $action): ?FilterSchema {
		return $this->filterSchemas[$action] ?? NULL;
	}

	/**
	 * Возвращает карту зарегистрированных AJAX-методов модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, class-string> Имя метода => FQCN обработчика.
	 * @example
	 *        $methods = $plugin->ajaxMethods();
	 *
	 */
	public function ajaxMethods(): array {
		return $this->ajaxMethods;
	}

	/**
	 * Возвращает карту публичных AJAX-методов (controller=public).
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, array{handler: class-string, allow_guest: bool}>
	 */
	public function ajaxPublicMethods(): array {
		return $this->ajaxPublicMethods;
	}

	/**
	 * Возвращает идентификатор AJAX-контроллера из манифеста.
	 *
	 * @since 200.4.0
	 *
	 * @return string Значение ajax.controller (по умолчанию «admin»).
	 * @example
	 *        $controller = $plugin->ajaxController();
	 *
	 */
	public function ajaxController(): string {
		return (string) ($this->manifest['ajax']['controller'] ?? 'admin');
	}

	/**
	 * Возвращает абсолютный путь к корневому каталогу модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return string Путь к каталогу модуля на диске.
	 * @example
	 *        $path = $plugin->modulePath();
	 *
	 */
	public function modulePath(): string {
		return $this->modulePath;
	}

	/**
	 * Возвращает список имён JS-файлов из секции assets.js манифеста.
	 *
	 * @since 200.4.0
	 *
	 * @return list<string> Имена файлов относительно Public/.
	 * @example
	 *        $jsFiles = $plugin->jsAssetFiles();
	 *
	 */
	public function jsAssetFiles(): array {
		$assets = $this->manifest['assets']['js'] ?? [];

		if(!is_array($assets)) {
			return [];
		}

		return array_values(array_filter($assets, 'is_string'));
	}

	/**
	 * Формирует объект ModuleManifest для реестра и шаблонов.
	 *
	 * @since 200.4.0
	 *
	 * @return ModuleManifest Агрегированные метаданные модуля.
	 * @example
	 *        $data = $plugin->moduleData();
	 *
	 */
	public function moduleData(): ModuleManifest {
		return ModuleManifest::fromManifest($this->mod, $this->manifest, $this->modulePath);
	}

	/**
	 * Возвращает записи changelog модуля.
	 *
	 * @since 200.4.0
	 *
	 * @return Changelog[] История изменений из changelog.data.php.
	 * @example
	 *        $entries = $plugin->changelog();
	 *
	 */
	public function changelog(): array {
		return $this->moduleData()->changelog;
	}

	/**
	 * Преобразует сырой массив menu из манифеста в объекты AdminLink.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<int, mixed>  $rawMenu  Элементы секции menu manifest.php.
	 *
	 * @return AdminLink[] Только элементы типа AdminLink.
	 */
	private function parseMenu(array $rawMenu): array {
		$links = [];

		foreach($rawMenu as $item) {
			if($item instanceof AdminLink) {
				$links[] = $item;
			}
		}

		return $links;
	}

	/**
	 * Нормализует карту AJAX-методов из манифеста.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $rawMethods  Секция ajax.methods manifest.php.
	 *
	 * @return array<string, class-string> Валидные пары method => handler.
	 */
	private function parseAjaxMethods(array $rawMethods): array {
		$methods = [];

		foreach($rawMethods as $method => $handler) {
			if(is_string($method) && is_string($handler) && $handler !== '') {
				$methods[$method] = $handler;
			}
		}

		return $methods;
	}

	/**
	 * Нормализует карту публичных AJAX-методов из манифеста.
	 *
	 * @param   array<string, mixed>  $rawMethods  Секция ajax.public.
	 *
	 * @return array<string, array{handler: class-string, allow_guest: bool}>
	 */
	private function parseAjaxPublicMethods(array $rawMethods): array {
		$methods = [];

		foreach($rawMethods as $method => $handler) {
			if(!is_string($method) || $method === '') {
				continue;
			}

			if(is_string($handler) && $handler !== '') {
				$methods[$method] = [
					'handler'     => $handler,
					'allow_guest' => false,
				];

				continue;
			}

			if(is_array($handler)) {
				$class = (string) ($handler['handler'] ?? $handler['class'] ?? '');

				if($class === '') {
					continue;
				}

				$methods[$method] = [
					'handler'     => $class,
					'allow_guest' => !empty($handler['allow_guest']),
				];
			}
		}

		return $methods;
	}

	/**
	 * Загружает settings.schema.php и Filter/logs.filter.schema.php модуля при наличии.
	 *
	 * @since 200.4.0
	 */
	private function loadSchemas(): void {
		$settingsFile = $this->modulePath . '/settings.schema.php';

		if(is_file($settingsFile)) {
			/** Подключает схему настроек модуля. */
			$loaded = require DLEPlugins::Check($settingsFile);

			if($loaded instanceof FormSchema) {
				$this->settingsSchema = $loaded;
			}
		}

		$filterFile = $this->modulePath . '/Filter/logs.filter.schema.php';

		if(is_file($filterFile)) {
			/** Подключает схему фильтра журнала модуля. */
			/** @var array<string, mixed> $raw */
			$raw = require DLEPlugins::Check($filterFile);

			if(is_array($raw)) {
				$this->filterSchemas['logs'] = FilterSchema::fromArray($raw);
			}
		}
	}

}
