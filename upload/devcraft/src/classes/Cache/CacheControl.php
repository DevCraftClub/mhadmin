<?php
//===============================================================
// Файл: CacheControl.php                                       =
// Путь: devcraft/src/classes/Cache/CacheControl.php            =
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

use Devcraft\Cache\FileCachePool;
use DevCraft\Core\Abstracts\AbstractType;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Support\DataManager;
use Throwable;

/**
 * Фасад файлового кэша DevCraft поверх PSR-6 {@see FileCachePool} (dev-tools).
 *
 * Ключ = `{type}/{name}` после translit. Новым кодом предпочтителен {@see pool()}.
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
	 * @since 200.4.0
	 */
	private static ?FileCachePool $pool = NULL;

	/**
	 * Инициализирует путь к кэшу из конфигурации или переданного аргумента.
	 *
	 * @since 200.4.0
	 *
	 * @param   string|null  $path  Явный путь к кэшу; при null — из DevCraftConfig.
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
	 * Устанавливает корневой каталог файлового кэша и пересоздаёт pool.
	 *
	 * @since 200.4.0
	 */
	public static function setPath(string $path): void {
		self::$path = $path;
		self::$pool = NULL;
	}

	/**
	 * Возвращает текущий корневой каталог кэша.
	 *
	 * @since 200.4.0
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
	 * PSR-6 pool (dev-tools).
	 *
	 * @since 200.4.0
	 */
	public static function pool(): FileCachePool {
		self::ensureInitialized();

		return self::$pool;
	}

	/**
	 * Сохраняет данные в файловый кэш по типу и имени.
	 *
	 * @since 200.4.0
	 */
	public static function setCache(string $type, string $name, mixed $data): void {
		$pool = self::pool();
		$key  = self::cacheKey($type, $name);
		$ttl  = self::cacheTimer();
		$item = $pool->getItem($key);
		$item->set(self::normalizeCacheValue($data));

		if($ttl > 0) {
			$item->expiresAfter($ttl);
		} else {
			$item->expiresAfter(null);
		}

		try {
			$pool->save($item);
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
	 * @return mixed Декодированные данные или false при miss/ошибке.
	 *
	 * @since 200.4.0
	 */
	public static function getCache(string $type, string $name): mixed {
		if((DevCraftConfig::raw()['debug'] ?? false)) {
			return false;
		}

		try {
			$item = self::pool()->getItem(self::cacheKey($type, $name));

			if(!$item->isHit()) {
				return false;
			}

			return $item->get();
		} catch(Throwable $e) {
			LogGenerator::for('CacheControl')->log(
				["Ошибка чтения кэша: {$e->getMessage()}"],
			);

			return false;
		}
	}

	/**
	 * Очищает кэш целиком, по типу или списку типов.
	 *
	 * @param   string|array<int, string>  $type  Тип кэша, массив типов или `'all'`.
	 *
	 * @since 200.4.0
	 */
	public static function clearCache(string|array $type = 'all'): void {
		$pool = self::pool();

		if(is_array($type)) {
			foreach($type as $cacheType) {
				self::clearCache($cacheType);
			}

			return;
		}

		if($type === 'all') {
			$pool->clear();

			return;
		}

		$pool->clearNamespace(DataManager::toTranslit($type));
	}

	/**
	 * @since 200.4.0
	 */
	private static function cacheKey(string $type, string $name): string {
		return DataManager::toTranslit($type) . '/' . DataManager::toTranslit($name);
	}

	/**
	 * @param   array<int, true>  $visited
	 */
	private static function normalizeCacheValue(mixed $value, array &$visited = [], int $depth = 0): mixed {
		if($depth > 32) {
			return NULL;
		}

		if($value === NULL) {
			return NULL;
		}

		if(is_array($value)) {
			return array_map(
				function($item) use ($depth, &$visited) {
					return self::normalizeCacheValue($item, $visited, $depth + 1);
				},
				$value,
			);
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

			return self::normalizeCacheValue(get_object_vars($value), $visited, $depth + 1);
		}

		return $value;
	}

	/**
	 * @since 200.4.0
	 */
	private static function ensureInitialized(): void {
		if(self::$path === NULL) {
			self::init();
		}

		if(self::$pool === NULL || self::$pool->getBaseDir() !== self::$path) {
			$ttl        = self::cacheTimer();
			self::$pool = new FileCachePool((string) self::$path, $ttl > 0 ? $ttl : 0);
		}
	}

}
