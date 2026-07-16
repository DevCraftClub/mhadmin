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
use DevCraft\Types\CacheInputType;
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
	 * Время жизни кэша по умолчанию (минуты): 1 сутки.
	 *
	 * @since 200.4.0
	 */
	private const int DEFAULT_CACHE_TIMER_MINUTES = 1440;

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
			$cachePath = Paths::cache();
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
	 * Возвращает TTL файлового кэша в секундах из настроек DevCraft.
	 *
	 * @since 200.4.0
	 */
	public static function cacheTimer(): int {
		$minutes = (int) (DevCraftConfig::raw()['cache_timer'] ?? self::DEFAULT_CACHE_TIMER_MINUTES);

		return max(0, $minutes) * 60;
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
		self::ensureInitialized();

		$normalizedData = self::normalizeCacheValue($data);
		$envelope       = CacheInputType::wrap($normalizedData);
		$cacheData      = '';

		$directoryPath = self::cacheDirectoryPath($type);
		DataManager::createDir($directoryPath);

		$filePath = self::cacheFilePath($type, $name);

		try {
			$cacheData = json_encode(
				$envelope->toArray(),
				JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES,
			);
		} catch(JsonException $e) {
			LogGenerator::for('CacheControl')->log($e->getMessage());
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
	 * Приводит данные к сериализуемому виду для JSON-кеша.
	 *
	 * Защищён от циклических ссылок объектов и чрезмерной глубины вложенности.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<int, true>  $visited  Уже обработанные object_id.
	 */
	private static function normalizeCacheValue(mixed $value, array &$visited = [], int $depth = 0): mixed {
		if($depth > 32) {
			return NULL;
		}

		if($value === NULL) {
			return NULL;
		}

		if(is_array($value)) {
			return self::normalizeCacheArray($value, $visited, $depth + 1);
		}

		if($value instanceof AbstractType) {
			return self::normalizeCacheValue($value->toArray(), $visited, $depth + 1);
		}

		if(is_object($value)) {
			$objectId = spl_object_id($value);

			if(isset($visited[$objectId])) {
				return NULL;
			}

			$visited[$objectId] = true;

			if(method_exists($value, 'toArray')) {
				return self::normalizeCacheValue($value->toArray(), $visited, $depth + 1);
			}

			return self::normalizeCacheArray(get_object_vars($value), $visited, $depth + 1);
		}

		return $value;
	}

	/**
	 * Приводит массив к сериализуемому виду с общей картой посещённых объектов.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<mixed>       $value
	 * @param   array<int, true>   $visited
	 *
	 * @return array<mixed>
	 */
	private static function normalizeCacheArray(array $value, array &$visited, int $depth): array {
		return array_map(function($item) use ($depth, $visited) { return self::normalizeCacheValue($item, $visited, $depth); }, $value);
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
		self::ensureInitialized();

		if((DevCraftConfig::raw()['debug'] ?? false)) {
			return false;
		}

		$filePath = self::cacheFilePath($type, $name);

		try {
			$data = @file_get_contents($filePath);

			if($data === false) {
				return false;
			}

			$decoded = NULL;

			try {
				$decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
			} catch(Exception) {
				return $data;
			}

			if(!is_array($decoded)) {
				return $decoded;
			}

			if(self::isCacheEnvelope($decoded)) {
				$envelope = CacheInputType::fromArray($decoded);

				if(!self::isStoredAtFresh($envelope->storedAt)) {
					self::deleteCacheFile($filePath);

					return false;
				}

				return $envelope->cacheData;
			}

			if(!self::isFileFresh($filePath)) {
				self::deleteCacheFile($filePath);

				return false;
			}

			return $decoded;
		} catch(Exception $e) {
			LogGenerator::for('CacheControl')->log(
				["Ошибка декодирования JSON: {$e->getMessage()}"],
			);
		}

		return false;
	}

	/**
	 * Проверяет, что массив соответствует формату CacheInputType.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data
	 */
	private static function isCacheEnvelope(array $data): bool {
		return array_key_exists('cacheData', $data) && array_key_exists('storedAt', $data);
	}

	/**
	 * Проверяет актуальность записи по Unix-времени сохранения.
	 *
	 * @since 200.4.0
	 */
	private static function isStoredAtFresh(int $storedAt): bool {
		if($storedAt <= 0) {
			return false;
		}

		return self::isTimestampFresh($storedAt);
	}

	/**
	 * Проверяет актуальность legacy-записи по mtime файла кэша.
	 *
	 * @since 200.4.0
	 */
	private static function isFileFresh(string $filePath): bool {
		$mtime = @filemtime($filePath);

		if($mtime === false) {
			return false;
		}

		return self::isTimestampFresh($mtime);
	}

	/**
	 * Проверяет timestamp с учётом общего TTL кэша.
	 *
	 * @since 200.4.0
	 */
	private static function isTimestampFresh(int $timestamp): bool {
		$timer = self::cacheTimer();

		if($timer <= 0) {
			return true;
		}

		return (time() - $timestamp) < $timer;
	}

	/**
	 * Удаляет устаревший файл кэша.
	 *
	 * @since 200.4.0
	 */
	private static function deleteCacheFile(string $filePath): void {
		if(is_file($filePath)) {
			@unlink($filePath);
		}
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
		self::ensureInitialized();

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

		DataManager::deleteDir(self::cacheDirectoryPath($type));
	}

	/**
	 * Инициализирует путь к кэшу при первом обращении.
	 *
	 * @since 200.4.0
	 */
	private static function ensureInitialized(): void {
		if(self::$path === NULL) {
			self::init();
		}
	}

	/**
	 * Возвращает путь к каталогу типа кэша.
	 *
	 * @since 200.4.0
	 */
	private static function cacheDirectoryPath(string $type): string {
		return DataManager::normalizePath((string) self::$path . DIRECTORY_SEPARATOR . DataManager::toTranslit($type));
	}

	/**
	 * Возвращает путь к файлу записи кэша.
	 *
	 * @since 200.4.0
	 */
	private static function cacheFilePath(string $type, string $name): string {
		return DataManager::normalizePath(
			self::cacheDirectoryPath($type) . DIRECTORY_SEPARATOR . DataManager::toTranslit($name) . '.cache',
		);
	}

}
