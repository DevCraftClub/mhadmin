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
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Module
 */
final class PluginContext {

	/**
	 * @since 200.4.0
	 * @var AdminLink[]
	 */
	private array $menu;

	/**
	 * @since 200.4.0
	 * @var array<string, class-string>
	 */
	private array $ajaxMethods;

	/**
	 * @since 200.4.0
	 * @var array<string, array{handler: class-string, allow_guest: bool}>
	 */
	private array $ajaxPublicMethods;

	/**
	 * @since 200.4.0
	 * @var FormSchema|null
	 */
	private ?FormSchema $settingsSchema = NULL;

	/**
	 * @since 200.4.0
	 * @var array<string, FilterSchema>
	 */
	private array $filterSchemas = [];

	/**
	 * @param   string          $mod         Идентификатор модуля в URL админки.
	 * @param   ModuleManifest  $manifest    Нормализованный манифест.
	 * @param   string          $modulePath  Абсолютный путь к каталогу модуля.
	 */
	public function __construct(
		private readonly string         $mod,
		private readonly ModuleManifest $manifest,
		private readonly string         $modulePath,
	) {
		$this->menu              = $manifest->menu;
		$this->ajaxMethods       = $manifest->ajax->methods;
		$this->ajaxPublicMethods = $manifest->ajax->public;

		AdminLinkResolver::validateStartActions($this->menu);

		$this->loadSchemas();
	}

	/**
	 * Добавляет пункт меню (host-merge сателлитов).
	 */
	public function appendMenuLink(AdminLink $link): void {
		$this->menu[] = $link;
		AdminLinkResolver::validateStartActions($this->menu);
	}

	/**
	 * @param   array<string, class-string>  $methods
	 */
	public function appendAjaxMethods(array $methods): void {
		foreach($methods as $name => $handler) {
			if(!is_string($name) || !is_string($handler) || $handler === '') {
				continue;
			}

			// Host сохраняет свой SettingsHandler; схема сателлитов уже в merge settings.
			if($name === 'settings') {
				continue;
			}

			$this->ajaxMethods[$name] = $handler;
		}
	}

	public function setSettingsSchema(?FormSchema $schema): void {
		$this->settingsSchema = $schema;
	}

	public function mod(): string {
		return $this->mod;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function meta(): array {
		$meta = [
			'name'        => $this->manifest->name,
			'version'     => $this->manifest->version,
			'description' => $this->manifest->description,
			'icon'        => $this->manifest->icon,
			'docsLink'    => $this->manifest->docsLink,
			'siteLink'    => $this->manifest->siteLink,
			'siteId'      => $this->manifest->siteId,
			'licLink'     => $this->manifest->licLink,
			'module_code' => $this->manifest->code ?? $this->mod,
		];

		if($this->manifest->author !== NULL) {
			$meta['author'] = $this->manifest->author->toArray();
		}

		return $meta;
	}

	/**
	 * @return AdminLink[]
	 */
	public function menu(): array {
		return $this->menu;
	}

	public function pageClass(string $action): ?string {
		return AdminLinkResolver::resolvePageClass($this->menu, $action);
	}

	public function defaultAction(): ?string {
		return AdminLinkResolver::defaultAction($this->menu);
	}

	public function settingsSchema(): ?FormSchema {
		return $this->settingsSchema;
	}

	public function filterSchema(string $action): ?FilterSchema {
		return $this->filterSchemas[$action] ?? NULL;
	}

	/**
	 * @return array<string, class-string>
	 */
	public function ajaxMethods(): array {
		return $this->ajaxMethods;
	}

	/**
	 * @return array<string, array{handler: class-string, allow_guest: bool}>
	 */
	public function ajaxPublicMethods(): array {
		return $this->ajaxPublicMethods;
	}

	public function ajaxController(): string {
		return $this->manifest->ajax->controller;
	}

	public function modulePath(): string {
		return $this->modulePath;
	}

	/**
	 * @return list<string>
	 */
	public function jsAssetFiles(): array {
		return $this->manifest->assets->js;
	}

	public function moduleData(): ModuleManifest {
		return $this->manifest;
	}

	/**
	 * @return Changelog[]
	 */
	public function changelog(): array {
		return $this->manifest->changelog;
	}

	/**
	 * Загружает settings.schema.php и Filter/logs.filter.schema.php модуля при наличии.
	 */
	private function loadSchemas(): void {
		$settingsFile = $this->modulePath . '/settings.schema.php';

		if(is_file($settingsFile)) {
			/** Подключает схему настроек модуля. */
			$loaded = require DLEPlugins::Check($settingsFile);

			if($loaded instanceof FormSchema) {
				$this->settingsSchema = $loaded;
			} elseif(is_array($loaded)) {
				$this->settingsSchema = FormSchema::fromArray($loaded);
			}
		}

		$filterFile = $this->modulePath . '/Filter/logs.filter.schema.php';

		if(is_file($filterFile)) {
			/** Подключает схему фильтра журнала модуля. */
			$raw = require DLEPlugins::Check($filterFile);

			if($raw instanceof FilterSchema) {
				$this->filterSchemas['logs'] = $raw;
			} elseif(is_array($raw)) {
				$this->filterSchemas['logs'] = FilterSchema::fromArray($raw);
			}
		}
	}

}
