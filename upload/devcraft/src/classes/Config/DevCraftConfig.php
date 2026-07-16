<?php
//===============================================================
// Файл: DevCraftConfig.php                                     =
// Путь: devcraft/src/classes/Config/DevCraftConfig.php         =
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

namespace DevCraft\Core\Config;

use DLEPlugins;
use DevCraft\Types\FormField;
use DevCraft\Types\FormSchema;
use DevCraft\Core\Support\DataManager;

/**
 * Доступ к настройкам плагина DevCraft с учётом схемы и значений по умолчанию.
 *
 * Загружает конфигурацию из DataManager, применяет миграцию устаревших ключей
 * и кеширует разрешённый массив настроек.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Config
 */
final class DevCraftConfig {

	/**
	 * Кешированная схема настроек из settings.schema.php.
	 *
	 * @since 200.4.0
	 * @var FormSchema|null
	 */
	private static ?FormSchema $schema = NULL;

	/**
	 * Признак активной загрузки settings.schema.php (защита от рекурсии __() ↔ schema).
	 *
	 * @since 200.4.0
	 * @var bool
	 */
	private static bool $schemaLoading = false;

	/**
	 * Кеш разрешённых значений настроек после применения схемы.
	 *
	 * @since 200.4.0
	 * @var array<string, mixed>|null
	 */
	private static ?array $resolvedCache = NULL;

	/**
	 * Проверяет, считается ли значение настройки пустым.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $value  Проверяемое значение.
	 *
	 * @return bool True, если значение NULL, пустая строка или 0.
	 * @example
	 *        $isEmpty = DevCraftConfig::isEmptyValue($raw['debug'] ?? null);
	 *
	 */
	public static function isEmptyValue(mixed $value): bool {
		return $value === NULL || $value === '' || $value === 0;
	}

	/**
	 * Подставляет значение по умолчанию из схемы, если сырое значение пустое.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormField  $field  Описание поля из FormSchema.
	 * @param   mixed      $raw    Сырое значение из хранилища настроек.
	 *
	 * @return mixed Разрешённое значение поля.
	 * @example
	 *        $debug = DevCraftConfig::resolveField($field, $raw['debug'] ?? null);
	 *
	 */
	public static function resolveField(FormField $field, mixed $raw): mixed {
		if(self::isEmptyValue($raw) && $field->default !== NULL) {
			return $field->default;
		}

		return $raw;
	}

	/**
	 * Возвращает сырой массив настроек из JSON без загрузки settings.schema.php.
	 *
	 * Используется при инициализации переводчика и в __(), чтобы избежать цикла
	 * schema → __() → DevCraftConfig::all() → schema.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Настройки из devcraft.json с миграцией ключей.
	 * @example
	 *        $locale = DevCraftConfig::raw()['language'] ?? 'ru_RU';
	 *
	 */
	public static function raw(): array {
		return DataManager::getConfig('devcraft');
	}

	/**
	 * Возвращает полный массив настроек DevCraft с применённой схемой и кешем.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Разрешённые настройки плагина.
	 * @example
	 *        $config = DevCraftConfig::all();
	 *        $timer  = CacheControl::cacheTimer(); // секунды, cache_timer в минутах
	 *
	 */
	public static function all(): array {
		if(self::$resolvedCache !== NULL) {
			return self::$resolvedCache;
		}

		$raw      = self::raw();
		$schema   = self::schema();
		$resolved = [];

		foreach($schema->allFields() as $field) {
			$resolved[$field->id] = self::resolveField($field, $raw[$field->id] ?? NULL);
		}

		foreach($raw as $key => $value) {
			if(!array_key_exists($key, $resolved)) {
				$resolved[$key] = $value;
			}
		}

		self::$resolvedCache = $resolved;

		return $resolved;
	}

	/**
	 * Возвращает одну настройку по ключу с необязательным значением по умолчанию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $key      Идентификатор настройки.
	 * @param   mixed   $default  Значение, если ключ отсутствует.
	 *
	 * @return mixed Значение настройки или $default.
	 * @example
	 *        $debug = DevCraftConfig::get('debug', false);
	 *
	 */
	public static function get(string $key, mixed $default = NULL): mixed {
		$all = self::all();

		return $all[$key] ?? $default;
	}

	/**
	 * Сбрасывает внутренний кеш разрешённых настроек.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     DevCraftConfig::resetCache();
	 */
	public static function resetCache(): void {
		self::$resolvedCache = NULL;
	}

	/**
	 * Проверяет, выполняется ли в данный момент require settings.schema.php.
	 *
	 * @since 200.4.0
	 *
	 * @return bool true во время загрузки схемы настроек.
	 */
	public static function isSchemaLoading(): bool {
		return self::$schemaLoading;
	}

	/**
	 * Загружает и кеширует FormSchema из settings.schema.php модуля Admin.
	 *
	 * @since 200.4.0
	 *
	 * @return FormSchema Схема полей настроек DevCraft.
	 *
	 * @throws \RuntimeException Если файл схемы отсутствует или возвращает неверный тип.
	 */
	private static function schema(): FormSchema {
		if(self::$schema instanceof FormSchema) {
			return self::$schema;
		}

		if(self::$schemaLoading) {
			throw new \RuntimeException('Циклическая загрузка схемы настроек DevCraft.');
		}

		self::$schemaLoading = true;

		try {
			$settingsFile = DEVCRAFT_MODULES . '/Admin/settings.schema.php';

			if(!is_file($settingsFile)) {
				throw new \RuntimeException("Файл настроек devcraft не найден: {$settingsFile}");
			}

			/** Подключает схему настроек модуля Admin. */
			$loaded = require DLEPlugins::Check($settingsFile);

			if(!$loaded instanceof FormSchema) {
				throw new \RuntimeException('Недопустимая схема настроек devcraft.');
			}

			self::$schema = $loaded;

			return self::$schema;
		} finally {
			self::$schemaLoading = false;
		}
	}

}
