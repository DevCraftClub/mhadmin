<?php
//===============================================================
// Файл: CacheControl.php                                       =
// Путь: devcraft/src/classes/Cache/CacheControl.php            =
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

namespace DevCraft\Core\Cache;

use Exception;
use Throwable;
use JsonException;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Abstracts\AbstractType;

/**
 * Файловый кэш DevCraft: запись, чтение и очистка по типу и имени.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Cache
 */
final class CacheControl {

	/**
	 * Корневой каталог файлового кэша.
	 *
	 * @since 200.4.0
	 *
	 * @var string|null
	 */
	private static ?string $path = NULL;

	/**
	 * Инициализирует путь к кэшу из конфигурации или переданного аргумента.
	 *
	 * @since 200.4.0
	 *
	 * @param   string|null  $path  Явный путь к кэшу; при null — из DevCraftConfig.
	 *
	 * @example
	 *     CacheControl::init();
	 */
	public static function init(?string $path = NULL): void {
		$cachePath = $path ?? (string) (DevCraftConfig::raw()['cache_path'] ?? '');

		if($cachePath === '') {
			$cachePath = Paths::root() . '/cache';
		} elseif(!str_starts_with($cachePath, '/') && !preg_match('#^[A-Za-z]:[\\\\/]#', $cachePath)) {
			$cachePath = ROOT_DIR . $cachePath;
		}

		self::setPath(DataManager::normalizePath($cachePath));
	}

	/**
	 * Устанавливает корневой каталог файлового кэша.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $path  Абсолютный или нормализованный путь к кэшу.
	 *
	 * @example
	 *     CacheControl::setPath('/var/www/devcraft/var/cache');
	 */
	public static function setPath(string $path): void {
		self::$path = $path;
	}

	/**
	 * Возвращает текущий корневой каталог кэша.
	 *
	 * @since 200.4.0
	 *
	 * @return string|null Путь к кэшу или null, если не инициализирован.
	 *
	 * @example
	 *     $cacheDir = CacheControl::getPath();
	 */
	public static function getPath(): ?string {
		return self::$path;
	}

	/**
	 * Сохраняет данные в файловый кэш по типу и имени.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $type  Тип кэша (подкаталог).
	 * @param   string  $name  Имя записи кэша.
	 * @param   mixed   $data  Данные для сохранения; массивы сериализуются в JSON.
	 *
	 * @example
	 *     CacheControl::setCache('dataloader', 'users_list', ['id' => 1]);
	 */
	public static function setCache(string $type, string $name, mixed $data): void {
		if(self::$path === NULL) {
			self::init();
		}

		$fileName  = DataManager::toTranslit($name);
		$fileType  = DataManager::toTranslit($type);
		$cacheData = $data;

		$directoryPath = DataManager::normalizePath(self::$path . DIRECTORY_SEPARATOR . $fileType);
		DataManager::createDir($directoryPath);

		$filePath = DataManager::normalizePath($directoryPath . DIRECTORY_SEPARATOR . $fileName . '.cache');

		if($data instanceof AbstractType || (is_object($data) && $data->hasMethod('toArray'))) {
			$cacheData = $data->toArray();
		}

		if(is_array($cacheData)) {
			try {
				$cacheData = json_encode(
					$data,
					JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES,
				);
			} catch(JsonException $e) {
				LogGenerator::for('CacheControl')->log($e->getMessage());
			}
		}

		try {
			file_put_contents($filePath, (string) $cacheData, LOCK_EX);
			DataManager::setPermission(0666, $filePath);
		} catch(Throwable $e) {
			LogGenerator::for('CacheControl')->log(
				[
					__('Ошибка записи кэша в файл: {error}', ['{error}' => $e->getMessage()]),
					__('Код ошибки: {code}', ['{code}' => (string) $e->getCode()]),
				],
			);
		}
	}

	/**
	 * Читает данные из файлового кэша по типу и имени.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $type  Тип кэша (подкаталог).
	 * @param   string  $name  Имя записи кэша.
	 *
	 * @return mixed Декодированные данные, строка или false при отсутствии/ошибке.
	 *
	 * @example
	 *     $data = CacheControl::getCache('dataloader', 'users_list');
	 */
	public static function getCache(string $type, string $name): mixed {
		if(self::$path === NULL) {
			self::init();
		}

		if((DevCraftConfig::raw()['debug'] ?? false)) {
			return false;
		}

		$fileName = DataManager::toTranslit($name);
		$fileType = DataManager::toTranslit($type);
		$filePath = DataManager::normalizePath(sprintf('%s/%s/%s.cache', self::$path, $fileType, $fileName));

		try {
			$data = @file_get_contents($filePath);

			if($data === false) {
				return false;
			}

			try {
				return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
			} catch(Exception) {
				return $data;
			}
		} catch(JsonException|Exception $e) {
			LogGenerator::for('CacheControl')->log(
				["Ошибка декодирования JSON: {$e->getMessage()}"],
			);
		}

		return false;
	}

	/**
	 * Очищает кэш целиком, по типу или списку типов.
	 *
	 * @since 200.4.0
	 *
	 * @param   string|array<int, string>  $type  Тип кэша, массив типов или `'all'`.
	 *
	 * @example
	 *     CacheControl::clearCache('dataloader');
	 */
	public static function clearCache(string|array $type = 'all'): void {
		if(self::getPath() === NULL) {
			self::init();
		}

		if(is_array($type)) {
			foreach($type as $cacheType) {
				self::clearCache($cacheType);
			}

			return;
		}

		if($type === 'all') {
			DataManager::deleteDir((string) self::$path);

			return;
		}

		$cacheDirectory = self::$path . '/' . DataManager::toTranslit($type);
		DataManager::deleteDir($cacheDirectory);
	}

}
