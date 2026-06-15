<?php
//===============================================================
// Файл: EntityPathResolver.php                                 =
// Путь: devcraft/src/classes/Database/EntityPathResolver.php   =
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
	 * Возвращает каталог миграций DevCraft, создавая его при необходимости.
	 *
	 * @since 200.4.0
	 *
	 * @return string Абсолютный путь к каталогу миграций.
	 *
	 * @example
	 *     $path = $resolver->migrationsDirectory();
	 */
	public function migrationsDirectory(): string {
		$directory = Paths::src() . '/database/migrations';

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
