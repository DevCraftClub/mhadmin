<?php

declare(strict_types=1);

namespace DevCraft\Core\Module;

use Throwable;
use Composer\Autoload\ClassLoader;
use DevCraft\Types\ModuleManifest;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Регистрирует PSR-4 префиксы модулей в ClassLoader Composer при boot.
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Module
 */
final class ModulePsr4Registrar {

	/**
	 * Регистрирует пространства имён всех загруженных манифестов.
	 *
	 * Ошибка одного модуля не прерывает регистрацию остальных.
	 *
	 * @since 200.4.1
	 */
	public static function registerAll(): void {
		try {
			$manifests = DataManager::readManifest();
		} catch(Throwable $throwable) {
			LogGenerator::for(self::class)->log(
				__('Не удалось загрузить манифесты для PSR-4: {msg}', ['{msg}' => $throwable->getMessage()]),
				'warning',
			);

			return;
		}

		foreach($manifests as $manifest) {
			if(!$manifest instanceof ModuleManifest) {
				continue;
			}

			try {
				self::registerOne($manifest);
			} catch(Throwable $throwable) {
				LogGenerator::for(self::class)->log(
					__('PSR-4 модуля «{mod}»: {msg}', [
						'{mod}' => $manifest->id,
						'{msg}' => $throwable->getMessage(),
					]),
					'warning',
				);
			}
		}
	}

	/**
	 * Регистрирует один префикс манифеста → корень каталога модуля.
	 *
	 * @since 200.4.1
	 */
	public static function registerOne(ModuleManifest $manifest): void {
		$prefix = ModuleManifest::normalizeNamespace($manifest->namespace);

		if($prefix === '') {
			return;
		}

		$path = rtrim($manifest->path, '/\\');

		if($path === '' || !is_dir($path)) {
			LogGenerator::for(self::class)->log(
				__('PSR-4: каталог модуля «{mod}» недоступен: {path}', [
					'{mod}'  => $manifest->id,
					'{path}' => $manifest->path,
				]),
				'warning',
			);

			return;
		}

		$base = $path . DIRECTORY_SEPARATOR;
		$loader = self::resolveClassLoader();

		if($loader instanceof ClassLoader) {
			$loader->addPsr4($prefix, $base);

			return;
		}

		self::registerSplFallback($prefix, $base);
	}

	/**
	 * Ищет зарегистрированный ClassLoader Composer.
	 */
	private static function resolveClassLoader(): ?ClassLoader {
		if(!class_exists(ClassLoader::class)) {
			return NULL;
		}

		$loaders = ClassLoader::getRegisteredLoaders();

		if($loaders === []) {
			return NULL;
		}

		if(defined('DC_ROOT')) {
			$key = rtrim((string) DC_ROOT, '/\\') . '/vendor';

			if(isset($loaders[$key])) {
				return $loaders[$key];
			}
		}

		$first = reset($loaders);

		return $first instanceof ClassLoader? $first : NULL;
	}

	/**
	 * Запасной автозагрузчик, если ClassLoader Composer недоступен.
	 */
	private static function registerSplFallback(string $prefix, string $base): void {
		spl_autoload_register(static function (string $class) use ($prefix, $base): void {
			if(!str_starts_with($class, $prefix)) {
				return;
			}

			$relative = substr($class, strlen($prefix));
			$file     = $base . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

			if(is_file($file)) {
				require $file;
			}
		});
	}

}
