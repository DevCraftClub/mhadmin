<?php
//===============================================================
// Файл: DataManager.php                                        =
// Путь: engine/inc/maharder/_includes/classes/DataManager.php  =
// Дата создания: 2024-03-22 06:41:38                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

abstract class DataManager {
	/**
	 * Статическое свойство для хранения экземпляра базы данных.
	 *
	 * @var db|null Экземпляр базы данных или null, если он не инициализирован.
	 */
	protected static ?db $db = null;

	/**
	 * Устанавливает подключение к базе данных, если оно еще не было установлено.
	 *
	 * Если глобальная переменная `DBHOST` не определена, подключение
	 * к базе данных конфигурируется через файл `dbconfig.php` в папке ENGINE_DIR.
	 * В противном случае используется глобальная переменная `$db`.
	 *
	 * @return void
	 * @global db|null $db Глобальная переменная, представляющая объект базы данных.
	 * @see self::setDb()
	 * @see DBHOST
	 */
	public static function connect(): void {
		if (is_null(self::$db)) {
			if (!defined('DBHOST')) {
				include_once ENGINE_DIR . '/data/dbconfig.php';
				$db = new db();
			} else {
				global $db;
			}
			self::setDb($db);
		}
	}

	/**
	 * Возвращает объект подключения к базе данных.
	 *
	 * Если подключение еще не было установлено, метод инициирует его вызовом метода connect().
	 *
	 * @return db Объект подключения к базе данных.
	 * @see self::connect() Для установления подключения к базе данных.
	 * @see self::$db self::$db Глобальная переменная, содержащая текущий объект подключения.
	 */
	public static function getDb(): db {
		if (is_null(self::$db)) {
			self::connect();
		}

		return self::$db;
	}

	/**
	 * Устанавливает экземпляр базы данных.
	 *
	 * Метод задает объект базы данных, который будет использоваться в дальнейшем.
	 *
	 * @param db|null $db Экземпляр базы данных или null.
	 *
	 * @return void
	 *
	 * @see self::$db Глобальная переменная, которая хранит объект базы данных.
	 */
	public static function setDb(?db $db): void {
		self::$db = $db;
	}

	/**
	 * Создает аббревиатуру из переданной строки, используя указанное разделение слов.
	 *
	 * Функция извлекает первую букву каждого слова из строки, разделенной указанным
	 * разделителем, и формирует из них аббревиатуру, преобразуя результат в верхний регистр.
	 *
	 * @link https://gist.github.com/duncanmudulo/624e1d5d976b4d4865c1c3a4dc3eff86?permalink_comment_id=3671137#gistcomment-3671137
	 *
	 * @param string $string Исходная строка, из которой нужно сформировать аббревиатуру.
	 * @param string $sep    Разделитель слов в строке (например, пробел, дефис и т.д.).
	 *
	 * @return string Аббревиатура, сформированная из начальных букв всех слов строки в верхнем регистре.
	 *
	 * @throws InvalidArgumentException Если один из параметров передан с некорректным типом.
	 */
	public static function abbr(string $string, string $sep = '_'): string {
		// Преобразуем строку в Title Case и разделяем слова
		$words = array_map(
			fn($word) => mb_substr($word, 0, 1), // Сразу извлекаем первые буквы
			explode($sep, mb_convert_case($string, MB_CASE_TITLE))
		);

		// Формируем аббревиатуру
		$abbreviation = implode('', $words);

		// Определяем длину строки с заполнением до 4 символов
		$lengthWithPadding = str_pad((string)strlen($string), 4, '0', STR_PAD_LEFT);

		// Конечный результат
		return "{$abbreviation}_{$lengthWithPadding}";
	}

	/**
	 * Преобразует указанную директорию в массив, содержащий все папки и файлы из неё.
	 *
	 * Этот метод является обёрткой для функции `dirToArray` и вызывает её с переданными
	 * параметрами. Используется для рекурсивного получения структуры директорий и файлов
	 * в виде массива.
	 *
	 * @version 173.3.0
	 *
	 * @param mixed  ...$_ext Список расширений или других элементов, подлежащих исключению при сканировании.
	 *
	 * @param string $dir     Путь к директории, содержимое которой необходимо преобразовать в массив.
	 *
	 * @return array Ассоциативный массив, где ключами могут быть директории, а значения —
	 *               содержимое этих директорий; файлы представлены в виде элементов массива.
	 *
	 * @see     dirToArray() Используемая глобальная функция для обработки директорий.
	 */
	public static function dirToArray(string $dir, ...$_ext): array {
		return dirToArray($dir, $_ext);
	}

	/**
	 * Создаёт папку(и) по указанным путям с заданными правами доступа.
	 *
	 * Для каждого предоставленного пути создаётся директория, если она ещё не существует.
	 * При возникновении ошибки (например, неудачного создания директории)
	 * ошибка логируется через `LogGenerator::generate_log`.
	 *
	 * @param string $service    Имя службы, используемое при логировании ошибок. По умолчанию `'DataManager'`.
	 * @param string $module     Имя модуля, используемое при логировании ошибок. По умолчанию `'mhadmin'`.
	 * @param int    $permission Уровень прав доступа для создаваемых директорий (в формате восьмеричной системы).
	 *                           По умолчанию `0755`.
	 * @param string ...$paths   Один или несколько путей, которые будут созданы. Каждый путь проверяется индивидуально.
	 *
	 * @return bool Возвращает `true`, если все директории успешно созданы, иначе `false`.
	 *
	 * @throws RuntimeException          Бросается, если директория не может быть создана.
	 * @throws JsonException|Throwable    Может быть вызван, если ошибка логирования связана с кодировкой JSON.
	 */
	public static function createDir(string $service = 'DataManager', string $module = 'mhadmin', int $permission = 0755, string ...$paths): bool {
		foreach ($paths as $path) {
			try {
				// Используем mkdir с проверкой is_dir только при необходимости
				if (!@mkdir($path, $permission, true) && !is_dir($path)) {
					LogGenerator::generateLog(
						$service,
						'createDir',
						[
							__($module, "Путь \"{path}\" не был создан.", ['{path}' => $path]),
						]
					);
				}
			} catch (Throwable $e) {
				// Логируем ошибку на основе исключений
				LogGenerator::generateLog(
					$service,
					'createDir',
					[
						__($module, $e->getMessage()),
					]
				);
				return false;
			}
		}
		return true;
	}

	/**
	 * Объединяет несколько путей в один.
	 * Объединение происходит слева направо. Пустые значения игнорируются.
	 * Возвращаемый путь нормализуется (лишние символы удаляются, контролируется корректность пути).
	 *
	 * @version 180.3.5
	 *
	 * @param string ...$paths Список путей, которые нужно объединить. Каждый аргумент должен быть строкой.
	 *
	 * @return string Возвращает объединённый и нормализованный путь.
	 *
	 * @throws RuntimeException Если произошла ошибка при нормализации пути.
	 * @since   173.3.0
	 *
	 */
	public static function joinPaths(string ...$paths): string {
		// Фильтруем пустые значения
		$filteredPaths = array_filter($paths, fn($path) => $path !== '');

		// Проверяем корректность / между составляющими пути
		$correctedPaths = [];
		foreach ($filteredPaths as $index => $path) {
			// Если это первый элемент, просто добавляем
			if ($index === 0) {
				$correctedPaths[] = $path;
				continue;
			}

			$previous = $correctedPaths[$index - 1];

			// Убираем дублирующиеся "/" на стыке
			if (str_ends_with($previous, '/') && str_starts_with($path, '/')) {
				$correctedPaths[$index - 1] = rtrim($previous, '/');
			} else if (!str_ends_with($previous, '/') && !str_starts_with($path, '/')) {
				$correctedPaths[$index - 1] .= '/';
			}

			$correctedPaths[] = $path;
		}

		// Объединяем пути
		$joinedPath = implode(DIRECTORY_SEPARATOR, $correctedPaths);

		return self::normalizePath($joinedPath);
	}

	/**
	 * Полностью удаляет директорию и все её содержимое, включая файлы и вложенные директории.
	 *
	 * Эта функция рекурсивно обходит заданный путь, удаляя все файлы и вложенные директории,
	 * а затем удаляет саму директорию. Игнорирует защищённую директорию,
	 * путь к которой жёстко прописан в коде.
	 *
	 * @version 173.3.0
	 *
	 * @param string $path Абсолютный путь к директории, которую необходимо удалить.
	 *
	 * @return void
	 *
	 * @throws \UnexpectedValueException В случае, если переданный путь не является директорией
	 *                                   или не может быть прочитан.
	 * @since   173.3.0
	 *
	 */
	public static function deleteDir(string $path): void {
		// Пропускаем защищённую директорию
		if ($path === ENGINE_DIR . '/inc/maharder/_includes/composer') {
			return;
		}

		if (is_dir($path)) {

			// Используем встроенную функциональность для обхода директории
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($iterator as $fileInfo) {
				if ($fileInfo->isFile()) {
					// Удаляем файл
					@unlink($fileInfo->getPathname());
				} else if ($fileInfo->isDir()) {
					// Удаляем пустую директорию
					@rmdir($fileInfo->getPathname());
				}
			}

			// Удаляем саму целевую директорию
			@rmdir($path);
		}
	}

	/**
	 * Преобразует переданный массив в плоский массив ключ-значение, удаляя пустые значения.
	 *
	 * Метод принимает массив (или null) и возвращает новый массив,
	 * где все вложенные массивы разворачиваются в плоскую структуру.
	 * Для элементов с числовыми ключами их значения назначаются в виде ключей и значений одновременно.
	 * Пустые строки или `null` удаляются из итогового массива.
	 *
	 * @deprecated с версии PHP 8.1. Рекомендуется использовать альтернативные методы для работы с массивами.
	 *
	 * @param array|null $args Входной массив для преобразования. Может быть null.
	 *
	 * @return array Плоский массив ключ-значение, где пустые строки и `null` исключены.
	 *
	 * @throws \InvalidArgumentException Если аргумент $args содержит некорректные данные.
	 */
	public static function nameArgs(?array $args): array {
		// Заменено ?? [] для защиты от null
		$arr = $args ?? [];

		// Используем итеративный подход вместо рекурсии для обхода вложенных массивов
		$result = [];
		$stack  = [$arr];

		while ($stack) {
			$current = array_pop($stack);

			foreach ($current as $key => $value) {
				if (is_numeric($key)) {
					if (is_array($value)) {
						$stack[] = $value; // Добавляем вложенный массив в стек для дальнейшей обработки
					} else {
						$result[$value] = $value; // Числовой ключ: добавляем значение в результат
					}
				} else {
					$result[$key] = $value; // Строковый ключ: добавляем пару ключ-значение в результат
				}
			}
		}

		// Фильтрация пустых значений
		return array_filter($result, static fn($value) => $value !== null && $value !== '');
	}

	/**
	 * Приводит значение к указанному типу, если возможно, используя набор фильтров.
	 *
	 * Функция принимает значение и тип, преобразует значение к заданному типу с использованием соответствующего
	 * фильтра. Если преобразование невозможно, возвращается строковое представление значения.
	 *
	 * Поддерживаемые типы:
	 * - `double`, `float`: преобразование в число с плавающей точкой.
	 * - `boolean`, `bool`: преобразование в булево значение.
	 * - `integer`, `int`, `tinyint`: преобразование в целое число.
	 * - `string`: фильтрация как строка.
	 *
	 * Если указанный тип отсутствует в карте типов, функция возвращает строковое представление значения.
	 *
	 * @param mixed  $value Значение, подлежащее преобразованию.
	 * @param string $type  Целевой тип, к которому необходимо привести значение.
	 *                      Должен быть одним из следующих: `double`, `float`, `boolean`, `bool`, `integer`, `int`,
	 *                      `tinyint`, `string`.
	 *
	 * @return float|bool|int|string Преобразованное значение в заданный тип, либо строковое представление значения,
	 *                               если преобразование невозможно или тип неизвестен.
	 *
	 * @throws ValueError Исключение может быть выброшено, если `filter_var` получит некорректный фильтр.
	 */
	public static function defType(mixed $value, string $type): float|bool|int|string {
		// Карта типов для упрощения отображения типов на соответствующие фильтры
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

		// Получаем фильтр или возвращаем строковое представление значения, если тип неизвестен
		$filterType = $typeMap[$type] ?? null;

		if ($filterType) {
			$result = filter_var($value, $filterType);

			// Проверяем, если фильтр вернул `false`, возвращаем исходное значение как строку
			return $result === false ? (string)$value : $result;
		}

		return (string)$value;
	}

	/**
	 * Формирует строку, представляющую оператор сравнения и значение, основанную на переданном значении.
	 *
	 * Эта функция анализирует начальный символ переданного значения, чтобы определить оператор сравнения
	 * (например, '=', '<', '>', 'LIKE', '<>', '<=', '>='). Если переданный оператор — '%', то значение
	 * оборачивается в проценты для использования в операциях LIKE. Если значение начинается с других
	 * специальных символов, оно интерпретируется и оператор определяется автоматически.
	 * При необходимости значение преобразуется в указанный тип.
	 *
	 * @param mixed $value Значение, из которого нужно определить оператор сравнения и само значение для обработки.
	 *                     Начальный символ может определить оператор (например, '%', '<', '>').
	 *                     Если отсутствует специальный символ, используется оператор "=" по умолчанию.
	 *
	 * @return string Строка, представляющая оператор сравнения и значение, готовая для использования
	 *                в SQL- или других выражениях. Например: " = 'some_value'", "< 10", или " LIKE '%abc%'".
	 *
	 * @throws InvalidArgumentException Если передано некорректное значение или тип недопустим для обработки.
	 */
	public static function getComparer(mixed $value): string {
		$firstSigns = ['!', '<', '>', '%'];
		$type       = gettype($value);
		$operator   = '=';

		// Проверяем начальный символ значения, если тип строки
		if (is_string($value) && in_array($value[0] ?? '', $firstSigns, true)) {
			$checkSign = match ($value[1] ?? '') {
				'='     => substr($value, 0, 2), // Например, "<=" или ">="
				default => $value[0],            // Например, "<", ">", "!"
			};

			$value = substr($value, strlen($checkSign));
		} else {
			$checkSign = null;
		}

		// Определяем оператор на основе начального символа
		$operator = match ($checkSign) {
			'!'                  => '<>',
			'<', '>', '<=', '>=' => $checkSign,
			'%'                  => 'LIKE',
			default              => '=',
		};

		// Если оператор LIKE, добавляем "%%" вокруг значения
		if ($operator === 'LIKE') {
			$value = '%' . $value . '%';
		}

		// Преобразуем значение в указанный тип
		$value = self::defType($value, $type);

		return " {$operator} {$value}";
	}

	/**
	 * Сохраняет конфигурационные данные в JSON-файл.
	 *
	 * @version 180.3.5
	 *
	 * @param string      $codename   Имя конфигурационного файла (без расширения).
	 * @param array       $config     Данные конфигурации, которые нужно сохранить.
	 * @param string|null $configPath Путь к директории, где будет храниться файл конфигурации.
	 *                                Если путь не указан, используется значение константы `MH_CONFIG`.
	 *
	 * @return array Содержимое существующего JSON-файла конфигурации или пустой массив, если файл отсутствует.
	 *
	 * @since   180.3.5
	 *
	 * Если файл конфигурации с указанным именем уже существует, возвращает данные, содержащиеся в этом файле.
	 * В противном случае возвращает пустой массив.
	 *
	 * @see     \DataManager::loadJsonConfig Метод, который может использовать файлы конфигурации для загрузки данных.
	 * @see     MH_CONFIG Константа, определяющая путь по умолчанию к директории конфигураций.
	 */
	public static function saveConfig(string $codename, array $config, ?string $configPath = null): array {
		$configPath   = $configPath ?? MH_CONFIG;
		$jsonFilePath = $configPath . DIRECTORY_SEPARATOR . $codename . '.json';

		if (is_file($jsonFilePath)) {
			return json_decode($jsonFilePath, true);
		}

		return [];
	}

	/**
	 * Получает конфигурацию на основе указанного кода имени и пути к файлу JSON.
	 *
	 * Метод сначала пытается загрузить конфигурацию из указанного или стандартного пути.
	 * Если файл не найден, он проверяет, указано ли устаревшее имя конфигурации
	 * для выполнения миграции данных. Если миграция невозможна, возвращается пустой массив.
	 *
	 * @param string      $codename Уникальное кодовое имя конфигурации, соответствующее имени файла JSON.
	 * @param string|null $path     Пользовательский путь к директории с конфигурацией.
	 *                              Если не указан, используется значение по умолчанию.
	 * @param string|null $confName Устаревшее имя конфигурации для миграции. Если не указано, миграция не выполняется.
	 *
	 * @return array Возвращает массив конфигурации. Если файл или миграция недоступны, возвращается пустой массив.
	 *
	 * @throws JsonException Если содержимое JSON-файла некорректно при вызове `loadJsonConfig`.
	 * @throws RuntimeException|Throwable Если ошибка возникает во время миграции конфигурации.
	 */
	public static function getConfig(string $codename, ?string $path = null, ?string $confName = null): array {
		// Используем Null-safe оператор и константу DIRECTORY_SEPARATOR для улучшения читаемости
		$configPath = $path ?? MH_CONFIG;

		// Формируем путь к JSON-файлу
		$jsonFilePath = $configPath . DIRECTORY_SEPARATOR . $codename . '.json';

		// Предпочитаем ранний возврат (early return) для повышения читаемости
		if (is_file($jsonFilePath)) {
			return self::loadJsonConfig($jsonFilePath);
		}

		// Если JSON-файл не найден, но есть старое имя конфигурации, занимаемся миграцией
		return !empty($confName) ? self::migrateOldConfig($codename, $confName, $configPath) : [];
	}

	/**
	 * Загружает JSON-конфигурацию из указанного файла и возвращает её в виде массива.
	 *
	 * Функция читает содержимое файла, парсит его как JSON и возвращает массив данных.
	 * Если элементы данных не являются массивами, они проходят фильтрацию с использованием
	 * `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
	 *
	 * @param string $filePath Путь к файлу с JSON-документом, который требуется загрузить.
	 *
	 * @return array Ассоциативный массив, содержащий данные из файла JSON.
	 *
	 * @throws JsonException Если произошла ошибка при парсинге JSON.
	 * @throws RuntimeException Если файл не существует или недоступен для чтения.
	 */
	private static function loadJsonConfig(string $filePath): array {
		$jsonData = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);

		// Декодируем значения, если они не массивы
		return array_map(
			fn($value) => is_array($value) ? $value : filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			$jsonData
		);
	}

	/**
	 * Мигрирует старый файл конфигурации в новый JSON-формат.
	 *
	 * Функция проверяет наличие старого конфигурационного файла, преобразует его
	 * содержимое в новый формат, сохраняет в указанной директории в формате JSON и
	 * удаляет старый файл. Если файл отсутствует или содержит некорректный формат данных,
	 * выполняются соответствующие обработки ошибок и возвращается пустой массив.
	 *
	 * @param string $configPath Путь к директории, в которой будет сохранен новый файл конфигурации.
	 *
	 * @param string $codename   Уникальный код или имя для конфигурации.
	 * @param string $confName   Название переменной в старом файле конфигурации.
	 *
	 * @return array Вернет массив настроек, извлеченных из старого конфигурационного файла.
	 *
	 * @throws \JsonException Если возникают ошибки при кодировании JSON.
	 * @throws \RuntimeException Если не удается записать файл или выполнить операции с файловой системой.
	 * @throws Throwable
	 *
	 * @see DLEPlugins::Check() Используется для проверки файлового пути.
	 * @see LogGenerator::generateLog() Используется для записи логов в случае ошибок.
	 *
	 */
	private static function migrateOldConfig(string $codename, string $confName, string $configPath): array {
		$oldConfigPath = ENGINE_DIR . '/data/' . $codename . '.php';

		// Проверяем наличие старого файла
		if (!is_file($oldConfigPath)) {
			return [];
		}

		// Загрузка старого файла конфигурации
		$oldFileContent = file_get_contents(DLEPlugins::Check($oldConfigPath));
		if (!$oldFileContent) {
			LogGenerator::generateLog(
				'DataManager',
				'migrateOldConfig',
				__(
					'mhadmin',
					"Невозможно загрузить старый файл конфигурации: {{file}}",
					['{{file}}' => $oldConfigPath]
				)
			);
			return [];
		}

		// Преобразуем старый формат конфигурации ($confName) в возврат значения
		$updatedContent = str_replace("\${$confName} = ", 'return ', $oldFileContent);
		file_put_contents($oldConfigPath, $updatedContent, LOCK_EX);

		// Подключаем файл для получения настроек
		$settings = require DLEPlugins::Check($oldConfigPath);
		if (!is_array($settings)) {
			LogGenerator::generateLog(
				'DataManager',
				'migrateOldConfig',
				__(
					'mhadmin',
					"Не верный формат конфигурации: {{file}}",
					['{{file}}' => $oldConfigPath]
				)
			);
			return [];
		}

		// Конвертируем настройки в JSON и сохраняем в новом формате
		$jsonSettings = json_encode($settings, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		$jsonFilePath = $configPath . DIRECTORY_SEPARATOR . $codename . '.json';
		file_put_contents($jsonFilePath, $jsonSettings);

		// Удаляем старый файл конфигурации
		if (!unlink($oldConfigPath)) {
			LogGenerator::generateLog(
				'DataManager',
				'migrateOldConfig',
				__(
					'mhadmin',
					"Не удалось удалить старый файл конфигурации: {{file}}",
					['{{file}}' => $oldConfigPath]
				)
			);
		}

		return $settings;
	}

	/**
	 * Нормализует путь, очищая его от лишних управляющих символов, точек и других нежелательных элементов.
	 *
	 * Эта функция:
	 * - Удаляет управляющие символы и преобразует все обратные слэши (`\`) в прямые (`/`).
	 * - Убирает текущую (`.`) и родительскую (`..`) директории из пути.
	 * - Удаляет корневой каталог (ROOT_DIR), если путь начинается с него.
	 * - Гарантирует, что путь на Linux начинается с символа `/`.
	 *
	 * @param string $path Входной путь для нормализации.
	 *                     Ожидается абсолютный или относительный путь в виде строки.
	 *
	 * @return string Нормализованный путь.
	 *                Возвращается пустая строка, если входной путь содержит недопустимые символы (например,
	 *                управляющие символы).
	 *
	 * @throws InvalidArgumentException Если константа ROOT_DIR не определена или имеет недопустимый тип.
	 */
	public static function normalizePath(string $path): string {
		// Убираем нежелательные символы и лишние пробелы
		$path = preg_replace('#[\0\\\\]+#', '/', trim($path));

		// Проверяем наличие управляющих символов
		if (preg_match('#\p{C}+#u', $path)) {
			return ''; // Некорректный путь
		}

		$rootDir = rtrim(ROOT_DIR, '/');

		// Избавляемся от нежелательных частей пути
		$path          = str_replace($rootDir, '', $path);
		$pathParts     = explode('/', $path);
		$filteredParts = array_filter($pathParts, static fn($part) => $part !== '.' && $part !== '..' && $part !== '');

		// Собираем нормализованный путь
		$normalizedPath = implode('/', $filteredParts);

		// Убираем корневой каталог ROOT_DIR
		if (str_starts_with($normalizedPath, $rootDir)) {
			$normalizedPath = substr($normalizedPath, strlen($rootDir));
		}

		// Убедимся, что путь начинается с '/' на Linux
		if (PHP_OS_FAMILY === 'Linux' && !str_starts_with($normalizedPath, '/')) {
			$normalizedPath = '/' . $normalizedPath;
		}

		if (!str_contains($rootDir, $normalizedPath)) $normalizedPath = $rootDir . $normalizedPath;

		return $normalizedPath;
	}

	/**
	 * Возвращает префикс, заданный в системе.
	 *
	 * @return string Префикс, используемый в системе.
	 */
	public static function getPrefix(): string {
		return PREFIX;
	}

	/**
	 * Получает префикс пользователя.
	 *
	 * Данный метод возвращает значение константы `USERPREFIX`.
	 *
	 * @return string Префикс пользователя.
	 */
	public static function getUserPrefix(): string {
		return USERPREFIX;
	}

	/**
	 * Конвертирует строку в транслитерацию, форматирует её и удаляет нежелательные символы.
	 *
	 * Эта функция выполняет трансформацию текста с учетом заданного функционала:
	 * - Преобразует строку в транслит с использованием Transliterator или функции `totranslit()`,
	 *   если Transliterator недоступен.
	 * - Удаляет все символы, кроме букв, цифр и пробелов.
	 * - Заменяет пробелы на символы подчеркивания.
	 * - Приводит строку к нижнему регистру.
	 *
	 * Функция упрощает создание URL-совместимых или SEO-оптимизированных строк из произвольных
	 * текстовых данных.
	 *
	 * @param string $input        Исходная строка для преобразования. Должна быть корректной строкой Unicode.
	 *
	 * @return string Отформатированная строка, приведенная к нижнему регистру, состоящая из
	 *                символов латиницы, цифр и подчеркиваний.
	 *
	 * @throws RuntimeException Если ни один из механизмов транслитерации,
	 *                          Transliterator или totranslit, недоступен или некорректен.
	 *
	 * @see   totranslit() Используемая функция в случае, если Transliterator недоступен.
	 *
	 * @since 173.3.0 Функция впервые была добавлена в этой версии.
	 *
	 * @global array $langtranslit Ассоциативный массив, используемый функцией `totranslit()` для
	 *                             выполнения транслитерации. Должен быть заранее определён.
	 *
	 */
	public static function toTranslit(string $input, bool $lowercase = true): string {
		// Попробуем создать объект Transliterator
		$transliterator = class_exists(\Transliterator::class) ? \Transliterator::create(
			'Any-Latin; Latin-ASCII'
		) : null;

		$input = filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		// Выполняем транслитерацию
		$transliterated = $transliterator ? $transliterator->transliterate($input) : totranslit($input);

		// Удаляем все символы, кроме букв, цифр и пробелов
		$filtered = preg_replace('/[^a-zA-Z0-9\.\+\s]/', '', $transliterated);

		// Заменяем пробелы на нижнее подчеркивание
		$underscored = preg_replace('/\s+/', '_', $filtered);

		if ($lowercase) {
			// Приводим текст к нижнему регистру
			$underscored = strtolower($underscored);
		}

		return $underscored ?? '';
	}

	/**
	 * Очищает входные данные, массивы или значения, используя заданные флаги для фильтрации.
	 *
	 * Если входные данные являются массивом, рекурсивно применяется функция очистки ко всем элементам массива.
	 * Если входные данные - одно значение, применяется очистка непосредственно к нему.
	 *
	 * @param mixed      $input Входные данные, которые необходимо очистить. Может быть массивом или единичным
	 *                          значением.
	 * @param array|null $flags Массив флагов для фильтрации значений (используются функции filter_var).
	 *
	 * @return mixed Очищенные данные. Если входные данные - массив, возвращается очищенный массив. Если данные не
	 *               заданы, возвращается null.
	 *
	 * @since 173.3.0
	 */
	public static function sanitizeArrayInput(mixed $input = null, array $flags = null): mixed {

		if (!$input) {
			return null;
		}

		if (is_array($input)) {
			$sanitizedInput = array_filter(
				array_map(
					fn($value)
						=> is_array($value)
						? self::sanitizeArrayInput($value, $flags)
						: self::sanitizeInput(
							$value,
							$flags
						),
					$input
				)
			);
		} else {
			$sanitizedInput = self::sanitizeInput($input, $flags);
		}

		return $sanitizedInput;
	}

	/**
	 * Обрабатывает и фильтрует входящее значение на основе заданных флагов.
	 *
	 * Эта функция принимает значение и массив флагов фильтрации, которые применяются
	 * к значению последовательно, используя функцию `filter_var`.
	 *
	 * @param mixed|null $value Входное значение, которое требуется отфильтровать. Может быть любого типа.
	 *                          По умолчанию — `null`.
	 * @param array|null $flags Массив констант фильтрации (например, FILTER_SANITIZE_STRING, FILTER_VALIDATE_INT),
	 *                          которые будут применяться к значению. По умолчанию — `null`, что означает отсутствие
	 *                          фильтров.
	 *
	 * @return string|null Возвращает отфильтрованное значение в виде строки или `null` в случае, если
	 *                     значение или фильтрация недействительны.
	 *
	 * @since 173.3.0
	 */
	public static function sanitizeInput(mixed $value = null, array $flags = null): ?string {
		if ($flags) {
			foreach ($flags as $flag) {
				$value = filter_var($value, $flag);
			}
		}
		return $value;
	}

	/**
	 * @throws Throwable
	 * @throws JsonException
	 */
	public static function createLockFile(string $path) {
		global $_TIME;

		if (!file_exists($path)) {
			if (!touch($path)) {
				LogGenerator::generateLog(
					'DataManager',
					'createLockFile/touch',
					__('Не удалось сохранить файл блокировки обновлений: {{path}}', ['{{path}}' => $path])
				);
			}

			if (!chmod($path, 0666)) {
				LogGenerator::generateLog(
					'DataManager',
					'createLockFile/chmod',
					__(
						'Не удалось выставить права на запись файла блокировки обновлений: {{path}}',
						['{{path}}' => $path]
					)
				);
			}

			if (!file_put_contents($path, $_TIME, LOCK_EX)) {
				LogGenerator::generateLog(
					'DataManager',
					'createLockFile/file_put_contents',
					__('Не удалось обновить содержимое файла блокировки обновлений: {{path}}', ['{{path}}' => $path])
				);
			}
		}
	}

	/**
	 * Нормализует указанный URL, добавляя дополнительные параметры запроса при необходимости.
	 *
	 * Если URL содержит схему или начинается с двойного слэша, возвращается исходный URL без изменений.
	 * Если URL не содержит схему, добавляется базовый URL, который формируется на основе конфигурации `$config`.
	 * Если путь в URL является относительным, он также дополняется базовым URL.
	 *
	 * @param string $url             URL для нормализации.
	 * @param array  $additionalQuery Ассоциативный массив дополнительных параметров запроса для добавления в URL.
	 *
	 * @return string Нормализованный URL.
	 * @global array $config          Глобальная конфигурация приложения, используется для определения базового URL.
	 * @see str_starts_with() Используется для проверки, начинается ли строка с заданного префикса.
	 */
	public static function normalizeUrl(string $url, array $additionalQuery = []): string {
		global $config;

		// Обрезка пробелов и разбор URL
		$url     = trim($url);
		$urlInfo = parse_url($url);

		// Если URL уже содержит схему или начинается с "//", возвращаем его как есть
		if (isset($urlInfo['scheme']) || str_starts_with($url, '//')) {
			return $url;
		}

		$baseUrl = isset($urlInfo['path']) || !$urlInfo['host'] ? $config['http_home_url'] . $config['admin_path'] : "{$urlInfo['scheme']}://{$urlInfo['host']}";

		// Обрабатываем строку запроса
		$query = [];
		if ($urlInfo['query']) parse_str($urlInfo['query'], $query);

		// Конечный объединённый массив параметров
		$mergedQuery = array_filter(array_merge($query, $additionalQuery));

		// Если путь относительный, добавить базовый URL
		return $baseUrl . '?' . http_build_query($mergedQuery);
	}
}
