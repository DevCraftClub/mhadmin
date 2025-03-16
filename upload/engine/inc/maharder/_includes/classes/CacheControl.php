<?php


/**
 * Абстрактный класс для реализации управления кэшированием.
 * Определяет контракт для классов, управляющих различными аспектами кэш-контроля,
 * такими как загрузка конфигурации, миграция или работа с файлами конфигурации.
 * Может быть дополнен реализацией в наследниках для поддержки специфичных стратегий кэширования.
 */
abstract class CacheControl {

	/**
	 * Определяет путь до кеша.
	 * Используется для хранения или извлечения пути, где будут сохраняться
	 * данные кеша. Значение может быть `null`, если путь не был задан.
	 *
	 * @version 170.2.10
	 * @see     CacheControl::getPath() Для получения текущего пути.
	 * @see     CacheControl::setPath() Для задания пути.
	 * @var string|null
	 */
	private static ?string $path = null;

	/**
	 * Инициализирует систему, устанавливая путь для кэша.
	 * Функция получает конфигурацию с помощью DataManager::getConfig(), определяет путь для хранения кэша
	 * (приоритет отдается переданному аргументу `$path`, затем значению из конфигурации, в последнюю очередь
	 * используется стандартный путь), нормализует его и устанавливает через self::setPath().
	 *
	 * @param string|null $path Путь к кэшу. Может быть передан явно. Если не указан, используется путь из конфигурации
	 *                          или стандартный (`/engine/inc/maharder/_cache`).
	 *
	 * @return void
	 * @throws JsonException Если данные конфигурации, получаемые из JSON, не могут быть прочитаны или обработаны.
	 * @see DataManager::normalizePath() для нормализации пути.
	 * @see DataManager::getConfig() для получения конфигурации.
	 * @see self::setPath() для установки пути.
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
	 * Устанавливает значение переменной пути.
	 * Метод задаёт значение для статической переменной `$path`,
	 * которая может быть использована для хранения пути.
	 *
	 * @param string $path Путь, который необходимо установить.
	 *
	 * @see self::$path Хранит значение пути.
	 */
	public static function setPath(string $path): void {
		self::$path = $path;
	}

	/**
	 * Возвращает текущий путь, если он установлен.
	 *
	 * @return string|null Возвращает путь как строку, либо null, если путь не установлен.
	 * @see self:$path Глобальная статическая переменная для хранения пути.
	 */
	public static function getPath(): string|null {
		return self::$path;
	}

	/**
	 * Сохраняет данные в кэше путем создания файла с содержимым в JSON-формате.
	 * Функция выполняет следующие действия:
	 * - Преобразует имя и тип кэша в транслитерацию для формирования имени файла и пути директории.
	 * - Проверяет и создает директорию, если она отсутствует.
	 * - Сериализует переданные данные в JSON и записывает их в файл.
	 * - Логирует возможные ошибки сериализации и записи данных.
	 *
	 * @param string $type Тип кэша. Используется для формирования структуры директорий.
	 * @param string $name Имя кэша (идентификатор). Преобразуется в транслитерацию для формирования имени файла.
	 * @param mixed  $data Данные для сохранения в кэше. Ожидается, что данные поддерживают JSON-сериализацию.
	 *
	 * @return void
	 * @throws JsonException Если возникает ошибка при сериализации данных в JSON.
	 * @throws Throwable Если возникает ошибка при записи данных в файл.
	 * @see DataManager::toTranslit() Для преобразования типа и имени кэша в транслит.
	 * @see DataManager::normalizePath() Для нормализации пути к директории или файлу.
	 * @see DataManager::createDir() Для создания необходимой директории.
	 * @see json_encode() Для кодирования данных в JSON-формат.
	 * @see file_put_contents() Для записи данных в файл.
	 * @see chmod() Для задания прав доступа на созданный файл.
	 * @see LogGenerator::generateLog() Для логирования ошибок при процессах сериализации или записи файла.
	 */
	public static function setCache(string $type, string $name, mixed $data): void {
		if (is_null(self::$path)) {
			self::init();
		}

		$fileName  = DataManager::toTranslit($name);
		$fileType  = DataManager::toTranslit($type);
		$cacheData = $data;

		// Генерируем директорию и создаём её, если необходимо
		$directoryPath = DataManager::normalizePath(self::$path . DIRECTORY_SEPARATOR . $fileType);
		DataManager::createDir('CacheControl', 'set_cache', _path: $directoryPath);

		// Генерируем полный путь файла
		$filePath = DataManager::normalizePath($directoryPath . DIRECTORY_SEPARATOR . $fileName . '.cache');
		// Подготовка данных для записи
		if (is_array($data)) {
			try {
				$cacheData = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			} catch (JsonException $e) {
				LogGenerator::generateLog(
					'DataManager',
					'setCache',
					__($e->getMessage()),
				);
			}
		}

		try {
			file_put_contents($filePath, $cacheData, LOCK_EX);
			chmod($filePath, 0666);
		} catch (Throwable $e) {
			LogGenerator::generateLog(
				'DataManager',
				'setCache',
				[
					__("Ошибка записи кэша в файл: {error}", ['{error}' => $e->getMessage()]),
					__("Код ошибки; {code}", ['{code}' => $e->getCode()]),
				]
			);
		}
	}

	/**
	 * Получает данные из кеша, преобразуя имена и типы в транслит и извлекая содержимое файла с последующим
	 * декодированием данных из JSON.
	 * Инициализирует путь к кешу, если он не был установлен ранее.
	 * Данные извлекаются из файла с учетом типа и имени, которые преобразуются в транслит.
	 * Если файл отсутствует или недоступен, функция возвращает `false`.
	 * Если данные присутствуют, они декодируются из формата JSON и в случае ошибки выбрасывается исключение
	 * `JsonException`.
	 *
	 * @param string $type Тип данных (будет преобразован в транслит).
	 * @param string $name Имя данных (будет преобразовано в транслит).
	 *
	 * @return false|array|int Возвращает массив данных, число или `false` в случае ошибки.
	 * @throws JsonException|Exception|Throwable Если произошла ошибка при декодировании кеша.
	 * @see DataManager::normalizePath() Для нормализации пути файла.
	 * @see LogGenerator::generateLog() Для логирования ошибок декодирования JSON.
	 * @see DataManager::toTranslit() Для преобразования имени и типа в транслит.
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
		$filePath = DataManager::normalizePath(sprintf('%s/%s/%s.cache', self::$path, $fileType, $fileName));

		// Чтение и декодирование данных из файла
		try {
			$data = @file_get_contents($filePath);
			if ($data === false) {
				return false; // Если файл недоступен, возвращаем false
			}

			try {
				// Декодируем JSON с выбрасыванием исключения при ошибке
				$data = json_decode($data, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
			} catch (Exception $e) {
			}

			return $data;
		} catch (JsonException|Exception $e) {
			// Логируем ошибку декодирования (если потребуется)
			LogGenerator::generateLog(
				'CacheControl',
				'getCache',
				["Ошибка декодирования JSON: {$e->getMessage()}"]
			);

		}
		return false; // Возвращаем false, если возникла ошибка
	}

	/**
	 * Очищает кеш для указанного типа или списка типов.
	 * Работает по следующему алгоритму:
	 * - Если передан строковый тип и значение `'all'`, очищается вся директория кеша.
	 * - Если передан массив типов, очистка выполняется рекурсивно для каждого типа.
	 * - В случае конкретного типа, очищается соответствующий подкаталог.
	 *
	 * @since 170.2.10 Метод был добавлен в версии 170.2.10.
	 *
	 * @param string|array $type Тип кеша, который необходимо очистить.
	 *                           Может быть строкой (`'all'` для очистки всего кеша, либо конкретный тип)
	 *                           или массивом из нескольких типов.
	 *
	 * @return void Функция ничего не возвращает.
	 * @throws JsonException Исключение может быть выброшено, если возникают ошибки при работе с JSON-библиотекой
	 *                       (например, при вызовах методов, зависящих от внутренних реализаций).
	 * @see   self::getPath() Используется для получения пути к директории кеша.
	 * @see   self::init() Вызывается для инициализации пути, если он не задан.
	 * @see   DataManager::deleteDir() Удаляет директорию кеша.
	 * @see   DataManager::toTranslit() Преобразует тип в транслит для формирования имени подкаталога.
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
			foreach (MhTranslation::getLanguages() as $lang) {
				@unlink(
					DataManager::normalizePath(sprintf("%s/assets/js/i18n/translation.%s.js", MH_ADMIN, $lang['tag']))
				);
			}
			return;
		}

		// Очистка конкретного подтипа кеша
		$cacheDirectory = self::$path . '/' . DataManager::toTranslit($type);
		DataManager::deleteDir($cacheDirectory);
	}


}
