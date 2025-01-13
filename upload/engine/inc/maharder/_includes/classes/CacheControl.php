<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

abstract class CacheControl {

	/**
	 * Путь до кеша
	 *
	 * @version 170.2.10
	 * @var string|null
	 */
	private static ?string $path = null;

	/**
	 * Инициализирует систему, устанавливая путь для кэша.
	 *
	 * Функция получает конфигурацию с помощью DataManager::getConfig(), определяет путь для хранения кэша
	 * (приоритет отдается переданному аргументу `$path`, затем значению из конфигурации, в последнюю очередь
	 * используется стандартный путь), нормализует его и устанавливает через self::setPath().
	 *
	 * @see DataManager::normalizePath() для нормализации пути.
	 * @see DataManager::getConfig() для получения конфигурации.
	 * @see self::setPath() для установки пути.
	 *
	 * @param string|null $path Путь к кэшу. Может быть передан явно. Если не указан, используется путь из конфигурации
	 *                          или стандартный (`/engine/inc/maharder/_cache`).
	 *
	 * @return void
	 * @throws JsonException Если данные конфигурации, получаемые из JSON, не могут быть прочитаны или обработаны.
	 */
	public static function init(string $path = null): void {
		// Получаем конфигурацию для 'maharder'
		$config = DataManager::getConfig('maharder');

		// Определяем путь с приоритетом переданного значения, значения из конфигурации, либо стандартного значения
		$cachePath = $path ??= $config['cache_path'] ?? '/engine/inc/maharder/_cache';

		// Нормализуем и устанавливаем путь
		self::setPath(DataManager::normalizePath(ROOT_DIR . $cachePath));
	}

	/**
	 * @param string $path
	 */
	public static function setPath(string $path): void {
		self::$path = $path;
	}

	public static function getPath(): string|null {
		return self::$path;
	}

	/**
	 * Сохраняет данные в кэше, создавая файл в заданной директории.
	 *
	 * Функция выполняет сериализацию входных данных в JSON и сохраняет их в файл,
	 * расположенный по структуре "корневой путь/тип/имя.php". Если необходимая
	 * директория отсутствует, она будет создана. В случае возникновения ошибок
	 * логирует исключения.
	 *
	 * @see DataManager::toTranslit() Для преобразования имени и типа в транслит.
	 * @see DataManager::normalizePath() Для нормализации пути файла.
	 * @see json_encode() Для кодирования данных в JSON формат.
	 * @see LogGenerator::generateLog() Для логирования ошибок декодирования JSON.
	 *
	 * @param string $name     Имя кэша (идентификатор). Преобразуется в транслитерацию и используется для имени файла.
	 * @param mixed  $data     Данные, которые необходимо сохранить в кэше. Поддерживаются данные, сериализуемые в JSON.
	 *
	 * @param string $fileType Тип или категория кэша. Используется для создания структуры директорий.
	 *
	 * @return void
	 *
	 * @throws JsonException Если произошла ошибка при сериализации данных в JSON.
	 * @throws RuntimeException Если произошла ошибка записи данных в файл или установки прав доступа.
	 */
	public static function setCache(string $type, string $name, mixed $data): void {
		if (is_null(self::$path)) {
			self::init();
		}

		$fileName = DataManager::toTranslit($name);
		$fileType = DataManager::toTranslit($type);

		// Генерируем директорию и создаём её, если необходимо
		$directoryPath = DataManager::normalizePath(self::$path . DIRECTORY_SEPARATOR . $fileType);
		DataManager::createDir('CacheControl', 'set_cache', _path: $directoryPath);

		// Генерируем полный путь файла
		$filePath = DataManager::normalizePath($directoryPath . DIRECTORY_SEPARATOR . $fileName . '.php');
		// Подготовка данных для записи
		try {
			$jsonData = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		} catch (JsonException $e) {
			LogGenerator::generateLog(
				'DataManager',
				'setCache',
				[
					__($module, $e->getMessage()),
				]
			);
		}

		try {
			file_put_contents($filePath, $jsonData, LOCK_EX);
			chmod($filePath, 0666);
		} catch (Throwable $e) {
			LogGenerator::generateLog(
				'DataManager',
				'setCache',
				[
					__($module, "Ошибка записи кэша в файл: {error}", ['{error}' => $e->getMessage()]),
					__($module, "Код ошибки; {code}", ['{code}' => $e->getCode()]),
				]
			);
		}
	}

	/**
	 * Получает данные из кеша, преобразуя имена и типы в транслит и извлекая содержимое файла с последующим
	 * декодированием данных из JSON.
	 *
	 * Инициализирует путь к кешу, если он не был установлен ранее.
	 * Данные извлекаются из файла с учетом типа и имени, которые преобразуются в транслит.
	 * Если файл отсутствует или недоступен, функция возвращает `false`.
	 * Если данные присутствуют, они декодируются из формата JSON и в случае ошибки выбрасывается исключение
	 * `JsonException`.
	 *
	 * @see DataManager::toTranslit() Для преобразования имени и типа в транслит.
	 * @see DataManager::normalizePath() Для нормализации пути файла.
	 * @see json_decode() Для парсинга JSON данных.
	 * @see LogGenerator::generateLog() Для логирования ошибок декодирования JSON.
	 *
	 * @param string $type Тип данных (будет преобразован в транслит).
	 * @param string $name Имя данных (будет преобразовано в транслит).
	 *
	 * @return false|array|int Возвращает массив данных, число или `false` в случае ошибки.
	 *
	 * @throws JsonException Если произошла ошибка при декодировании JSON.
	 */
	public static function getCache(string $type, string $name): false|array|int {
		// Инициализация пути, если это не сделано заранее
		if (self::$path === null) {
			self::init();
		}

		// Преобразованием названий типа и имени занимаемся сразу
		$fileName = DataManager::toTranslit($name);
		$fileType = DataManager::toTranslit($type);

		// Формирование полного пути к файлу
		$filePath = DataManager::normalizePath(sprintf('%s/%s/%s.php', self::$path, $fileType, $fileName));

		// Чтение и декодирование данных из файла
		try {
			$data = @file_get_contents($filePath);
			if ($data === false) {
				return false; // Если файл недоступен, возвращаем false
			}

			// Декодируем JSON с выбрасыванием исключения при ошибке
			return json_decode($data, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
		} catch (JsonException $e) {
			// Логируем ошибку декодирования (если потребуется)
			LogGenerator::generateLog(
				'CacheControl',
				'getCache',
				["Ошибка декодирования JSON: {$e->getMessage()}"]
			);
			return false; // Возвращаем false, если возникла ошибка
		}
	}

	/**
	 * Очищает кеш для указанного типа или списка типов.
	 *
	 * Работает по следующему алгоритму:
	 * - Если передан строковый тип и значение `'all'`, очищается вся директория кеша.
	 * - Если передан массив типов, очистка выполняется рекурсивно для каждого типа.
	 * - В случае конкретного типа, очищается соответствующий подкаталог.
	 *
	 * @since 170.2.10 Метод был добавлен в версии 170.2.10.
	 * @see   self::init() Вызывается для инициализации пути, если он не задан.
	 * @see   DataManager::deleteDir() Удаляет директорию кеша.
	 * @see   DataManager::toTranslit() Преобразует тип в транслит для формирования имени подкаталога.
	 *
	 * @see   self::getPath() Используется для получения пути к директории кеша.
	 *
	 * @param string|array $type Тип кеша, который необходимо очистить.
	 *                           Может быть строкой (`'all'` для очистки всего кеша, либо конкретный тип)
	 *                           или массивом из нескольких типов.
	 *
	 * @return void Функция ничего не возвращает.
	 *
	 * @throws JsonException Исключение может быть выброшено, если возникают ошибки при работе с JSON-библиотекой
	 *                       (например, при вызовах методов, зависящих от внутренних реализаций).
	 *
	 */
	public static function clearCache(string|array $type = 'all'): void {
		if (is_null(self::getPath())) {
			self::init();
		}

		// Если передан массив, рекурсивно очищаем каждый элемент
		if (is_array($type)) {
			foreach ($type as $cacheType) {
				self::clearCache($cacheType);
			}
			return;
		}

		// Обработка типа "all" — очистить всю директорию
		if ($type === 'all') {
			DataManager::deleteDir(self::$path); // Очистка всего пути
			return;
		}

		// Очистка конкретного подтипа кеша
		$cacheDirectory = self::$path . '/' . DataManager::toTranslit($type);
		DataManager::deleteDir($cacheDirectory);
	}


}
