<?php
//===============================================================
// Файл: EntityPathResolver.php                                 =
// Путь: devcraft/src/classes/Database/EntityPathResolver.php   =
// Последнее изменение: 2026-10-28 12:00:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Database;

use DevCraft\Core\Config\Paths;
use DevCraft\Core\Module\Registry;
use DevCraft\Core\Support\DataManager;

/**
 * Разрешает пути к моделям сущностей и каталогу миграций DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Database
 */
final class EntityPathResolver {

	/**
	 * Создаёт резолвер на основе реестра модулей.
	 *
	 * @since 200.4.0
	 *
	 * @param   Registry  $registry  Реестр DevCraft-модулей.
	 *
	 * @example
	 *     $resolver = new EntityPathResolver(Application::instance()->registry());
	 */
	public function __construct(
		private readonly Registry $registry,
	) {}

	/**
	 * Возвращает каталоги Models всех модулей с PHP-файлами сущностей.
	 *
	 * @since 200.4.0
	 *
	 * @return string[] Список абсолютных путей к каталогам Models.
	 *
	 * @example
	 *     $dirs = $resolver->entityModelDirectories();
	 */
	public function entityModelDirectories(): array {
		$directories = [];

		$coreComposerModels = Paths::src() . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . 'Models';
		if(is_dir($coreComposerModels) && $this->hasConcreteEntityFiles($coreComposerModels)) {
			$directories[] = $coreComposerModels;
		}

		foreach($this->registry->modules() as $module) {
			if($module->path === '' || !is_dir($module->path)) {
				continue;
			}

			$modelsDir = $module->path . DIRECTORY_SEPARATOR . 'Models';

			if(!is_dir($modelsDir) || !$this->hasConcreteEntityFiles($modelsDir)) {
				continue;
			}

			$directories[] = $modelsDir;
		}

		return $directories;
	}

	/**
	 * Возвращает основной каталог миграций (модуль Admin), создавая его при необходимости.
	 *
	 * @since 200.4.0
	 *
	 * @return string Абсолютный путь к `modules/Admin/Migrations`.
	 *
	 * @example
	 *     $path = $resolver->migrationsDirectory();
	 */
	public function migrationsDirectory(): string {
		$directory = Paths::modules() . '/Admin/Migrations';

		DataManager::createDir($directory);

		return $directory;
	}

	/**
	 * Возвращает все каталоги `Migrations` модулей: сначала Admin, затем остальные существующие.
	 *
	 * @since 200.4.2
	 *
	 * @return list<string> Абсолютные пути к каталогам миграций.
	 */
	public function migrationDirectories(): array {
		$main        = $this->migrationsDirectory();
		$directories = [realpath($main) ?: $main];

		foreach($this->registry->modules() as $module) {
			$dir = $module->path === ''? false : realpath($module->path . DIRECTORY_SEPARATOR . 'Migrations');

			if($dir !== false && is_dir($dir) && !in_array($dir, $directories, true)) {
				$directories[] = $dir;
			}
		}

		return $directories;
	}

	/**
	 * Каталог миграций модуля, которому принадлежит файл модели.
	 *
	 * Модели из `modules/{Name}/Models` → `modules/{Name}/Migrations`; прочие (Composer) → Admin.
	 *
	 * @since 200.4.2
	 *
	 * @param   string  $modelFile  Абсолютный путь к файлу модели.
	 *
	 * @return string Абсолютный путь к каталогу миграций (создаётся при необходимости).
	 */
	public function migrationsDirectoryForModel(string $modelFile): string {
		$modelsDir = dirname($modelFile);
		$modules   = realpath(Paths::modules()) ?: Paths::modules();
		$moduleDir = dirname($modelsDir);

		if(basename($modelsDir) !== 'Models' || (realpath(dirname($moduleDir)) ?: '') !== $modules) {
			return $this->migrationsDirectory();
		}

		$directory = $moduleDir . DIRECTORY_SEPARATOR . 'Migrations';

		DataManager::createDir($directory);

		return $directory;
	}

	/**
	 * Проверяет наличие хотя бы одного PHP-файла в каталоге моделей.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $directory  Абсолютный путь к каталогу Models.
	 *
	 * @return bool true, если найден хотя бы один .php-файл.
	 */
	private function hasConcreteEntityFiles(string $directory): bool {
		$entries = scandir($directory);

		if($entries === false) {
			return false;
		}

		foreach($entries as $entry) {
			if($entry === '.' || $entry === '..') {
				continue;
			}

			if(!str_ends_with($entry, '.php')) {
				continue;
			}

			$path = $directory . DIRECTORY_SEPARATOR . $entry;

			if(!is_file($path)) {
				continue;
			}

			return true;
		}

		return false;
	}

}
