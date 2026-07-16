<?php
//===============================================================
// Файл: Registry.php                                           =
// Путь: devcraft/src/classes/Module/Registry.php               =
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
use DevCraft\Types\ModuleData;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Http\JsonResponseException;

/**
 * Реестр модулей DevCraft и фабрика контекстов плагинов.
 *
 * Сканирует каталог DEVCRAFT_MODULES, загружает manifest.php
 * и предоставляет доступ к PluginContext и метаданным ModuleData.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Module
 */
final class Registry {

	/**
	 * Возвращает карту активных модулей, индексированную по идентификатору mod.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, ModuleData> Метаданные модулей с валидным манифестом.
	 * @example
	 *        $modules = Application::instance()->registry()->modules();
	 *
	 */
	public function modules(): array {
		return DataManager::readManifest();
	}

	/**
	 * Возвращает метаданные одного модуля по идентификатору mod.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $module_id  Идентификатор модуля из manifest.php.
	 *
	 * @return ModuleData|null Данные модуля или null, если модуль не найден.
	 * @example
	 *        $admin = Application::instance()->registry()->module('devcraft');
	 *
	 */
	public function module(string $module_id): ?ModuleData {
		try {
			return DataManager::getManifest($module_id);
		} catch(JsonResponseException) {
			return NULL;
		}
	}

	/**
	 * Возвращает FQCN класса страницы для action модуля.
	 *
	 * При отсутствии action для запрошенного действия использует defaultAction модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $module_id  Идентификатор модуля (mod).
	 * @param   string  $action     Имя action из URL админки.
	 *
	 * @return string|null FQCN класса страницы или null.
	 * @example
	 *        $class = Application::instance()->registry()->page_class('devcraft', 'settings');
	 *
	 */
	public function page_class(string $module_id, string $action): ?string {
		$context = $this->forMod($module_id);

		if($context === NULL) {
			return NULL;
		}

		return $context->pageClass($action) ?? $context->pageClass($context->defaultAction() ?? 'dashboard');
	}

	/**
	 * Ищет контекст плагина по идентификатору mod среди всех каталогов модулей.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $mod  Идентификатор модуля из manifest.php.
	 *
	 * @return PluginContext|null Контекст плагина или null.
	 * @example
	 *        $plugin = Application::instance()->registry()->forMod('devcraft');
	 *
	 */
	public function forMod(string $mod): ?PluginContext {
		if($mod === '') {
			return NULL;
		}

		foreach($this->listModuleDirectories() as $dirName) {
			$context = $this->forModuleDir($dirName);

			if($context !== NULL && $context->mod() === $mod) {
				return $context;
			}
		}

		return NULL;
	}

	/**
	 * Загружает контекст плагина по имени каталога модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $moduleDir    Имя каталога относительно DEVCRAFT_MODULES.
	 * @param   string|null  $modOverride  Переопределение mod из манифеста (необязательно).
	 *
	 * @return PluginContext|null Контекст плагина или null при ошибке загрузки.
	 * @example
	 *        $plugin = Application::instance()->registry()->forModuleDir('Admin', 'devcraft');
	 *
	 */
	public function forModuleDir(string $moduleDir, ?string $modOverride = NULL): ?PluginContext {
		$moduleDir = trim($moduleDir, '/\\');

		if($moduleDir === '' || str_contains($moduleDir, '..')) {
			return NULL;
		}

		$path = DataManager::normalizePath(DEVCRAFT_MODULES . '/' . $moduleDir);

		if(!is_dir($path)) {
			return NULL;
		}

		$manifestFile = $path . '/manifest.php';

		if(!is_file($manifestFile)) {
			return NULL;
		}

		try {
			/** Подключает manifest.php модуля DevCraft. */
			$manifest = require DLEPlugins::Check($manifestFile);

			if(!is_array($manifest)) {
				return NULL;
			}

			$manifestMod  = (string) ($manifest['mod'] ?? '');
			$effectiveMod = $modOverride ?? $manifestMod;

			if($effectiveMod === '') {
				return NULL;
			}

			return new PluginContext($effectiveMod, $manifest, $path);
		} catch(\Throwable $throwable) {
			LogGenerator::for(Registry::class)->log($throwable->getMessage());

			return NULL;
		}
	}

	/**
	 * Возвращает отсортированный список имён каталогов модулей с manifest.php.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] Имена подкаталогов DEVCRAFT_MODULES.
	 */
	private function listModuleDirectories(): array {
		if(!is_dir(DEVCRAFT_MODULES)) {
			return [];
		}

		$entries = scandir(DEVCRAFT_MODULES);

		if($entries === false) {
			return [];
		}

		$directories = [];

		foreach($entries as $entry) {
			if($entry === '.' || $entry === '..') {
				continue;
			}

			$path = DEVCRAFT_MODULES . '/' . $entry;

			if(!is_dir($path)) {
				continue;
			}

			if(!is_file($path . '/manifest.php')) {
				continue;
			}

			$directories[] = $entry;
		}

		sort($directories);

		return $directories;
	}

}
