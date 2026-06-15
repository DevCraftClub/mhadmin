<?php
//===============================================================
// Файл: DataManager.php                                        =
// Путь: devcraft/src/classes/Support/DataManager.php           =
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

namespace DevCraft\Core\Support;

use Throwable;
use JsonException;
use FilesystemIterator;
use RecursiveIteratorIterator;
use DevCraft\Core\Config\Paths;
use RecursiveDirectoryIterator;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Утилиты работы с файлами, конфигурацией JSON и санитизацией ввода DLE.
 *
 * Порт статических методов legacy DataManager (без подключения к БД).
 *
 * @package    DevCraft
 * @since      173.3.0
 * @subpackage Core.Support
 */
final class DataManager {

	/**
	 * Формирует аббревиатуру из слов строки с суффиксом длины.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $string  Исходная строка.
	 * @param   string  $sep     Разделитель слов.
	 *
	 * @return string Аббревиатура вида «Abbr_0008».
	 *
	 * @example
	 *     $code = DataManager::abbr('example_field');
	 */
	public static function abbr(string $string, string $sep = '_'): string {
		$words = array_map(
			static fn(string $word): string => mb_substr($word, 0, 1),
			explode($sep, mb_convert_case($string, MB_CASE_TITLE)),
		);

		$abbreviation      = implode('', $words);
		$lengthWithPadding = str_pad((string) strlen($string), 4, '0', STR_PAD_LEFT);

		return "{$abbreviation}_{$lengthWithPadding}";
	}

	/**
	 * Рекурсивно преобразует каталог в массив файлов и подкаталогов.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $dir         Путь к каталогу.
	 * @param   mixed   ...$ignored  Расширения или элементы для игнорирования.
	 *
	 * @return array<mixed> Дерево каталога.
	 *
	 * @example
	 *     $tree = DataManager::dirToArray($path, 'php', 'twig');
	 */
	public static function dirToArray(string $dir, mixed ...$ignored): array {
		if(count($ignored) === 1 && is_array($ignored[0])) {
			$extensions = $ignored[0];
		} else {
			$extensions = $ignored;
		}

		return self::scanDirectory($dir, $extensions);
	}

	/**
	 * Создаёт каталоги с правами 0755, если они ещё не существуют.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  ...$paths  Пути для создания.
	 *
	 * @return bool true при успехе для всех путей.
	 *
	 * @example
	 *     DataManager::createDir($cacheDir, $configDir);
	 */
	public static function createDir(string ...$paths): bool {
		$permission = 0755;

		foreach($paths as $path) {
			try {
				if(is_dir($path)) {
					continue;
				}

				if(!mkdir($path, $permission, true) && !is_dir($path)) {
					LogGenerator::for('DataManager')->log(
						__('Путь "{path}" не был создан. Вызвано из {caller}.', [
							'{path}'   => $path,
							'{caller}' => LogGenerator::resolveCallerContext([self::class]),
						]),
					);

					return false;
				}
			} catch(Throwable $e) {
				LogGenerator::for('DataManager')->log(
					__('Ошибка: {error}. Вызвано из {caller}.', [
						'{error}'  => $e->getMessage(),
						'{caller}' => LogGenerator::resolveCallerContext([self::class]),
					]),
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * Создаёт каталоги с указанными правами доступа.
	 *
	 * @since 200.4.0
	 *
	 * @param   int     $permission  Права в восьмеричном виде.
	 * @param   string  ...$paths    Пути для создания.
	 *
	 * @return bool true при успехе для всех путей.
	 */
	public static function setPermission(int $permission, string ...$paths): bool {
		foreach($paths as $path) {
			try {
				chmod($path, $permission);
			} catch(Throwable $e) {
				LogGenerator::for('DataManager')->log(
					__('Ошибка: {error}. Вызвано из {caller}.', [
						'{error}'  => $e->getMessage(),
						'{caller}' => LogGenerator::resolveCallerContext([self::class]),
					]),
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * Объединяет несколько сегментов пути в один нормализованный путь.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  ...$paths  Сегменты пути.
	 *
	 * @return string Объединённый путь.
	 *
	 * @example
	 *     $path = DataManager::joinPaths(ROOT_DIR, 'devcraft', 'var/cache');
	 */
	public static function joinPaths(string ...$paths): string {
		$filteredPaths = array_filter($paths, static fn(string $path): bool => $path !== '');

		$correctedPaths = [];

		foreach($filteredPaths as $index => $path) {
			if($index === 0) {
				$correctedPaths[] = $path;
				continue;
			}

			$previous = $correctedPaths[$index - 1];

			if(str_ends_with($previous, DIRECTORY_SEPARATOR) && str_starts_with($path, DIRECTORY_SEPARATOR)) {
				$correctedPaths[$index - 1] = rtrim($previous, DIRECTORY_SEPARATOR);
			} elseif(!str_ends_with($previous, DIRECTORY_SEPARATOR) && !str_starts_with($path, DIRECTORY_SEPARATOR)) {
				$correctedPaths[$index - 1] .= DIRECTORY_SEPARATOR;
			}

			$correctedPaths[] = $path;
		}

		return str_replace(
			[DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, '//', '\\\\', '\/'],
			DIRECTORY_SEPARATOR,
			implode(DIRECTORY_SEPARATOR, $correctedPaths),
		);
	}

	/**
	 * Рекурсивно удаляет каталог, кроме защищённых vendor и var/cache.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $path  Абсолютный путь к каталогу.
	 *
	 * @example
	 *     DataManager::deleteDir($tempDir);
	 */
	public static function deleteDir(string $path): void {
		$root      = rtrim(Paths::root(), DIRECTORY_SEPARATOR);
		$protected = [
			$root . DIRECTORY_SEPARATOR . 'vendor',
			Paths::cache(),
			Paths::config(),
			Paths::templates(),
		];

		foreach($protected as $skip) {
			if($path === $skip || str_starts_with($path, $skip . DIRECTORY_SEPARATOR)) {
				return;
			}
		}

		if(!is_dir($path)) {
			return;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST,
		);

		foreach($iterator as $fileInfo) {
			if($fileInfo->isFile()) {
				@unlink($fileInfo->getPathname());
			} elseif($fileInfo->isDir()) {
				@rmdir($fileInfo->getPathname());
			}
		}

		@rmdir($path);
	}

	/**
	 * Преобразует вложенный массив аргументов в плоский ассоциативный массив.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<mixed>|null  $args  Исходные аргументы или null.
	 *
	 * @return array<mixed> Именованные аргументы без пустых значений.
	 */
	public static function nameArgs(?array $args): array {
		$arr    = $args ?? [];
		$result = [];
		$stack  = [$arr];

		while($stack !== []) {
			$current = array_pop($stack);

			foreach($current as $key => $value) {
				if(is_numeric($key)) {
					if(is_array($value)) {
						$stack[] = $value;
					} else {
						$result[$value] = $value;
					}
				} else {
					$result[$key] = $value;
				}
			}
		}

		return array_filter($result, static fn($value): bool => $value !== NULL && $value !== '');
	}

	/**
	 * Приводит значение к типу через filter_var по имени SQL/PHP-типа.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed   $value  Исходное значение.
	 * @param   string  $type   Имя типа (int, string, bool и т.д.).
	 *
	 * @return float|bool|int|string Приведённое значение.
	 */
	public static function defType(mixed $value, string $type): float|bool|int|string {
		static $typeMap = [
			'double'  => FILTER_VALIDATE_FLOAT,
			'float'   => FILTER_VALIDATE_FLOAT,
			'boolean' => FILTER_VALIDATE_BOOLEAN,
			'bool'    => FILTER_VALIDATE_BOOLEAN,
			'integer' => FILTER_VALIDATE_INT,
			'int'     => FILTER_VALIDATE_INT,
			'tinyint' => FILTER_VALIDATE_INT,
			'string'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		];

		$filterType = $typeMap[$type] ?? NULL;

		if($filterType) {
			$result = filter_var($value, $filterType);

			return $result === false? (string) $value : $result;
		}

		return (string) $value;
	}

	/**
	 * Формирует SQL-фрагмент сравнения с оператором из префикса значения.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed  $value  Значение с опциональным префиксом оператора (!, <, >, %).
	 *
	 * @return string Фрагмент « OP value» для SQL.
	 */
	public static function getComparer(mixed $value): string {
		$firstSigns = ['!', '<', '>', '%'];
		$type       = gettype($value);
		$operator   = '=';

		if(is_string($value) && in_array($value[0] ?? '', $firstSigns, true)) {
			$checkSign = match ($value[1] ?? '') {
				'='     => substr($value, 0, 2),
				default => $value[0],
			};

			$value = substr($value, strlen($checkSign));
		} else {
			$checkSign = NULL;
		}

		$operator = match ($checkSign) {
			'!'                  => '<>',
			'<', '>', '<=', '>=' => $checkSign,
			'%'                  => 'LIKE',
			default              => '=',
		};

		if($operator === 'LIKE') {
			$value = '%' . $value . '%';
		}

		$value = self::defType($value, $type);

		return " {$operator} {$value}";
	}

	/**
	 * Сохраняет конфигурацию модуля в JSON-файл.
	 *
	 * @since 180.3.5
	 *
	 * @param   string                $codename    Имя конфигурации (имя файла без .json).
	 * @param   array<string, mixed>  $config      Данные конфигурации.
	 * @param   string|null           $configPath  Каталог конфигурации или null для Paths::config().
	 *
	 * @example
	 *     DataManager::saveConfig('app', $settings);
	 */
	public static function saveConfig(string $codename, array $config, ?string $configPath = NULL): void {
		$configPath   = $configPath ?? Paths::config();
		$jsonFilePath = self::normalizePath($configPath . DIRECTORY_SEPARATOR . $codename . '.json');

		file_put_contents(
			$jsonFilePath,
			json_encode($config, JSON_UNESCAPED_UNICODE),
			LOCK_EX,
		);
	}

	/**
	 * Загружает конфигурацию из JSON или мигрирует legacy PHP-конфиг DLE.
	 *
	 * @since 173.3.0
	 *
	 * @param   string       $codename  Имя конфигурации.
	 * @param   string|null  $path      Каталог JSON или null.
	 * @param   string|null  $confName  Имя legacy PHP-конфига для миграции.
	 *
	 * @return array<string, mixed> Ассоциативный массив настроек.
	 *
	 * @example
	 *     $settings = DataManager::getConfig('app');
	 */
	public static function getConfig(string $codename, ?string $path = NULL, ?string $confName = NULL): array {
		$configPath   = $path ?? Paths::config();
		$jsonFilePath = $configPath . DIRECTORY_SEPARATOR . $codename . '.json';

		if(is_file($jsonFilePath)) {
			try {
				return self::loadJsonConfig($jsonFilePath);
			} catch(JsonException) {
				LogGenerator::for('DataManager')->log(
					__('Некорректный JSON в файле конфигурации: {file}', ['{file}' => $jsonFilePath]),
				);

				return [];
			}
		}

		return $confName !== NULL && $confName !== ''
			? self::migrateOldConfig($codename, $confName, $configPath)
			: [];
	}

	/**
	 * Нормализует путь относительно ROOT_DIR и realpath.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $path  Исходный путь.
	 *
	 * @return string Абсолютный нормализованный путь или пустая строка при ошибке.
	 *
	 * @example
	 *     $safe = DataManager::normalizePath($userPath);
	 */
	public static function normalizePath(string $path): string {
		$path = preg_replace('#[\0\\\\]+#', DIRECTORY_SEPARATOR, trim($path)) ?? '';

		if(preg_match('#\p{C}+#u', $path)) {
			return '';
		}

		$rootDir       = rtrim(ROOT_DIR, DIRECTORY_SEPARATOR);
		$path          = str_replace($rootDir, '', $path);
		$pathParts     = explode(DIRECTORY_SEPARATOR, $path);
		$filteredParts = array_filter(
			$pathParts,
			static fn(string $part): bool => $part !== '.' && $part !== '..' && $part !== '',
		);

		$normalizedPath = implode(DIRECTORY_SEPARATOR, $filteredParts);

		if(str_starts_with($normalizedPath, $rootDir)) {
			$normalizedPath = substr($normalizedPath, strlen($rootDir));
		}

		if(PHP_OS_FAMILY === 'Linux' && $normalizedPath !== '' && !str_starts_with($normalizedPath, '/')) {
			$normalizedPath = DIRECTORY_SEPARATOR . $normalizedPath;
		}

		if(!str_contains($rootDir, $normalizedPath)) {
			$normalizedPath = self::joinPaths($rootDir, $normalizedPath);
		}

		$normalizedPath = str_replace(['\\\\', '//', '\/'], ['\\', '/', DIRECTORY_SEPARATOR], $normalizedPath);
		$realPath       = realpath($normalizedPath);

		return $realPath !== false? $realPath : $normalizedPath;
	}

	/**
	 * Возвращает префикс таблиц DLE (константа PREFIX).
	 *
	 * @since 173.3.0
	 *
	 * @return string Значение PREFIX или пустая строка.
	 *
	 * @example
	 *     $prefix = DataManager::getPrefix();
	 */
	public static function getPrefix(): string {
		return defined('PREFIX')? PREFIX : '';
	}

	/**
	 * Возвращает префикс пользовательских таблиц DLE (USERPREFIX).
	 *
	 * @since 173.3.0
	 *
	 * @return string Значение USERPREFIX или пустая строка.
	 *
	 * @example
	 *     $userPrefix = DataManager::getUserPrefix();
	 */
	public static function getUserPrefix(): string {
		return defined('USERPREFIX')? USERPREFIX : '';
	}

	/**
	 * Транслитерирует строку в латинский идентификатор с подчёркиваниями.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $input      Исходная строка.
	 * @param   bool    $lowercase  Приводить результат к нижнему регистру.
	 *
	 * @return string Транслитерированная строка.
	 *
	 * @example
	 *     $slug = DataManager::toTranslit('Пример текста');
	 */
	public static function toTranslit(string $input, bool $lowercase = true): string {
		$transliterator = class_exists(\Transliterator::class, false)
			? \Transliterator::create('Any-Latin; Latin-ASCII')
			: NULL;

		$input = (string) filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		if($transliterator) {
			$transliterated = $transliterator->transliterate($input);
		} elseif(function_exists('totranslit')) {
			$transliterated = totranslit($input);
		} else {
			$transliterated = $input;
		}

		$filtered    = preg_replace('/[^a-zA-Z0-9\.\+\s]/', '', (string) $transliterated) ?? '';
		$underscored = preg_replace('/\s+/', '_', $filtered) ?? '';

		if($lowercase) {
			$underscored = strtolower($underscored);
		}

		return $underscored;
	}

	/**
	 * Рекурсивно санитизирует массив ввода через filter_var.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed       $input  Входные данные.
	 * @param   array|null  $flags  Флаги filter_var или null.
	 *
	 * @return mixed Санитизированный массив или скаляр, null при пустом вводе.
	 *
	 * @example
	 *     $clean = DataManager::sanitizeArrayInput($_GET['filter_rules'], [FILTER_SANITIZE_FULL_SPECIAL_CHARS]);
	 */
	public static function sanitizeArrayInput(mixed $input = NULL, ?array $flags = NULL): mixed {
		if($input === NULL || $input === [] || $input === '') {
			return NULL;
		}

		if(is_array($input)) {
			return array_filter(
				array_map(
					static fn($value) => is_array($value)
						? self::sanitizeArrayInput($value, $flags)
						: self::sanitizeInput($value, $flags),
					$input,
				),
			);
		}

		return self::sanitizeInput($input, $flags);
	}

	/**
	 * Санитизирует скалярное значение через один или несколько filter_var.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed       $value  Сырое значение.
	 * @param   array|null  $flags  Цепочка флагов filter_var.
	 *
	 * @return string|null Строка или null при неудачной фильтрации.
	 */
	public static function sanitizeInput(mixed $value = NULL, ?array $flags = NULL): ?string {
		if($flags) {
			foreach($flags as $flag) {
				$filtered = filter_var($value, $flag);

				if($filtered === false) {
					return NULL;
				}

				$value = $filtered;
			}
		}

		if($value === NULL || $value === false) {
			return NULL;
		}

		return (string) $value;
	}

	/**
	 * Создаёт lock-файл обновлений с меткой времени.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $path  Путь к lock-файлу.
	 *
	 * @example
	 *     DataManager::createLockFile($lockPath);
	 */
	public static function createLockFile(string $path): void {
		global $_TIME;
		if(file_exists($path)) {
			return;
		}

		$lockTimestamp = $_TIME ?? (string) time();

		if(!touch($path)) {
			LogGenerator::for('DataManager')->log(
				__('Не удалось сохранить файл блокировки обновлений: {path}', ['{path}' => $path]),
			);
		}

		if(!self::setPermission(0666, $path)) {
			LogGenerator::for('DataManager')->log(
				__('Не удалось выставить права на запись файла блокировки обновлений: {path}', ['{path}' => $path]),
			);
		}

		if(!file_put_contents($path, $lockTimestamp, LOCK_EX)) {
			LogGenerator::for('DataManager')->log(
				__('Не удалось обновить содержимое файла блокировки обновлений: {path}', ['{path}' => $path]),
			);
		}
	}

	/**
	 * Нормализует относительный URL админки DLE с дополнительными query-параметрами.
	 *
	 * @since 173.3.0
	 *
	 * @global array<string, mixed>    $config           Глобальная конфигурация DLE.
	 *
	 * @param   array<string, string>  $additionalQuery  Дополнительные параметры.
	 *
	 * @param   string                 $url              Исходный URL или query.
	 *
	 * @return string Полный URL админки с query.
	 *
	 * @example
	 *     $url = DataManager::normalizeUrl('?mod=devcraft', ['action' => 'logs']);
	 */
	public static function normalizeUrl(string $url, array $additionalQuery = []): string {
		global $config;

		$url     = trim($url);
		$urlInfo = parse_url($url)? : [];

		if(isset($urlInfo['scheme']) || str_starts_with($url, '//')) {
			return $url;
		}

		$baseUrl = isset($urlInfo['path']) || empty($urlInfo['host'])
			? ($config['http_home_url'] ?? '/') . ($config['admin_path'] ?? 'admin.php')
			: "{$urlInfo['scheme']}://{$urlInfo['host']}";

		$query = [];

		if(!empty($urlInfo['query'])) {
			parse_str($urlInfo['query'], $query);
		}

		$mergedQuery = array_filter(array_merge($query, $additionalQuery));

		return $baseUrl . '?' . http_build_query($mergedQuery);
	}

	/**
	 * Загружает и санитизирует JSON-конфигурацию из файла.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $filePath  Путь к .json файлу.
	 *
	 * @return array<string, mixed> Декодированный массив.
	 */
	private static function loadJsonConfig(string $filePath): array {
		$jsonData = json_decode(
			(string) file_get_contents($filePath),
			true,
			512,
			JSON_THROW_ON_ERROR,
		);

		return array_map(
			static fn($value) => is_array($value)
				? $value
				: filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			$jsonData,
		);
	}

	/**
	 * Мигрирует legacy PHP-конфиг DLE в JSON и удаляет старый файл.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $codename    Имя конфигурации DevCraft.
	 * @param   string  $confName    Имя переменной legacy-конфига.
	 * @param   string  $configPath  Каталог для JSON.
	 *
	 * @return array<string, mixed> Загруженные настройки.
	 */
	private static function migrateOldConfig(string $codename, string $confName, string $configPath): array {
		if(!defined('ENGINE_DIR')) {
			return [];
		}

		$oldConfigPath = ENGINE_DIR . '/data/' . $confName . '.php';

		if(!is_file($oldConfigPath)) {
			return [];
		}

		$checkPath = class_exists('DLEPlugins', false)
			? DLEPlugins::Check($oldConfigPath)
			: $oldConfigPath;

		$oldFileContent = file_get_contents($checkPath);

		if($oldFileContent === false || $oldFileContent === '') {
			LogGenerator::for('DataManager')->log(
				__('Невозможно загрузить старый файл конфигурации: {file}', ['{file}' => $oldConfigPath]),
			);

			return [];
		}

		$updatedContent = str_replace("\${$confName} = ", 'return ', $oldFileContent);
		file_put_contents($oldConfigPath, $updatedContent, LOCK_EX);

		/** Загружает legacy PHP-конфиг после преобразования в return-массив. */
		/** @var mixed $settings */
		$settings = require $checkPath;

		if(!is_array($settings)) {
			LogGenerator::for('DataManager')->log(
				__('Не верный формат конфигурации: {file}', ['{file}' => $oldConfigPath]),
			);

			return [];
		}

		$jsonFilePath = $configPath . DIRECTORY_SEPARATOR . $codename . '.json';

		file_put_contents(
			$jsonFilePath,
			json_encode($settings, JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),
			LOCK_EX,
		);

		if(!@unlink($oldConfigPath)) {
			LogGenerator::for('DataManager')->log(
				__('Не удалось удалить старый файл конфигурации: {file}', ['{file}' => $oldConfigPath]),
			);
		}

		return $settings;
	}

	/**
	 * Рекурсивно сканирует каталог в массив (внутренний метод dirToArray).
	 *
	 * @since 173.3.0
	 *
	 * @param   string        $dir                Путь к каталогу.
	 * @param   array<mixed>  $ignoredExtensions  Игнорируемые имена и расширения.
	 *
	 * @return array<mixed> Дерево каталога.
	 */
	private static function scanDirectory(string $dir, array $ignoredExtensions = []): array {
		$defaultIgnored = ['.', '..', '.htaccess'];
		$ignoredItems   = array_merge($defaultIgnored, $ignoredExtensions);

		$resolvedDir = $dir;

		if(defined('ENGINE_DIR') && defined('ROOT_DIR')) {
			$resolvedDir = str_replace(
				ENGINE_DIR,
				ROOT_DIR . DIRECTORY_SEPARATOR . 'engine',
				$dir,
			);
		}

		if(!is_dir($resolvedDir)) {
			return [];
		}

		$filesAndDirs = scandir($resolvedDir, SCANDIR_SORT_NONE);

		if($filesAndDirs === false) {
			return [];
		}

		$result = [];

		foreach($filesAndDirs as $item) {
			if(in_array($item, $ignoredItems, true)) {
				continue;
			}

			$itemPath = $resolvedDir . DIRECTORY_SEPARATOR . $item;

			if(is_dir($itemPath)) {
				$result[$item] = self::scanDirectory($itemPath, $ignoredExtensions);
			} else {
				$result[] = $item;
			}
		}

		return $result;
	}

}
