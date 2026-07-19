<?php
//===============================================================
// Файл: EnvironmentFactory.php                                 =
// Путь: devcraft/src/classes/Twig/EnvironmentFactory.php       =
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

namespace DevCraft\Core\Twig;

use Twig\Environment;
use DevCraft\Core\Config\Paths;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Cache\CacheRuntime;
use DevCraft\Core\I18n\Translation;
use Twig\Extra\Cache\CacheExtension;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\I18n\TwigTranslatorBridge;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

/**
 * Фабрика Twig Environment с путями модулей и расширением перевода.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Twig
 */
final class EnvironmentFactory {

	/**
	 * Создаёт настроенный экземпляр Twig Environment.
	 *
	 * @since 200.4.0
	 *
	 * @return Environment Экземпляр с загрузчиком шаблонов и i18n-расширением.
	 *
	 * @example
	 *     $twig = EnvironmentFactory::create();
	 *     echo $twig->render('@admin/dashboard.twig', $context);
	 */
	public static function create(): Environment {
		$loader = new FilesystemLoader(Paths::templates());
		self::registerModuleTemplatePaths($loader);

		$useDebug = (bool) (DevCraftConfig::raw()['debug'] ?? false);

		$twig = new Environment($loader, [
			'autoescape' => 'html',
			'cache'      => self::cachePath($useDebug),
			'debug'      => $useDebug,
		]);

		self::ensureTranslationExtension($twig);
		self::registerExtensions($twig);

		return $twig;
	}

	/**
	 * Возвращает путь кеша Twig или отключает кеширование в режиме отладки.
	 *
	 * @since 200.4.0
	 *
	 * @param   bool  $useDebug  Включён ли режим отладки.
	 *
	 * @return string|false Абсолютный путь кеша Twig или false.
	 */
	private static function cachePath(bool $useDebug): string|false {
		if($useDebug) {
			return false;
		}

		$config    = DevCraftConfig::raw();
		$cachePath = trim((string) ($config['cache_path'] ?? Paths::cache()));

		if($cachePath === '') {
			$cachePath = Paths::cache();
		}

		if(!str_starts_with($cachePath, ROOT_DIR)) {
			$cachePath = ROOT_DIR . '/' . ltrim($cachePath, '/\\');
		}

		return rtrim($cachePath, '/\\') . '/twig';
	}

	/**
	 * Подключает Symfony TranslationExtension (фильтр trans, тег {% trans %}).
	 *
	 * @since 200.4.0
	 *
	 * @param   Environment  $twig  Экземпляр Twig.
	 *
	 * @example
	 *     EnvironmentFactory::ensureTranslationExtension($twig);
	 */
	public static function ensureTranslationExtension(Environment $twig): void {
		Translation::setTranslator();

		foreach($twig->getExtensions() as $extension) {
			if($extension instanceof TranslationExtension) {
				return;
			}
		}

		$twig->addExtension(new TranslationExtension(new TwigTranslatorBridge()));
	}

	/**
	 * Регистрирует namespace-пути шаблонов для каждого модуля DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @param   FilesystemLoader  $loader  Загрузчик Twig.
	 */
	private static function registerModuleTemplatePaths(FilesystemLoader $loader): void {
		$modulesRoot = Paths::modules();

		if(!is_dir($modulesRoot)) {
			return;
		}

		foreach(scandir($modulesRoot)? : [] as $entry) {
			if($entry === '.' || $entry === '..') {
				continue;
			}

			$moduleDir = DataManager::normalizePath($modulesRoot . '/' . $entry);

			if(!is_dir($moduleDir)) {
				continue;
			}

			$templatesDir = $moduleDir . '/templates';

			if(!is_dir($templatesDir)) {
				continue;
			}

			$loader->addPath($templatesDir, strtolower($entry));
		}
	}

	private static function registerExtensions(Environment $twig): void {
		$twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {

			public function load($class) {
				if(CacheRuntime::class === $class) {
					return new CacheRuntime(new TagAwareAdapter(new FilesystemAdapter()));
				}
			}

		});

		$twig->addExtension(new CacheExtension());
	}

}
