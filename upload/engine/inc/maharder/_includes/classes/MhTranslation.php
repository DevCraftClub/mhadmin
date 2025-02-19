<?php

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

/**
 * Класс для оформления фраз переводов
 *
 * @version 173.3.0
 * @since   173.3.0
 */
abstract class MhTranslation {

	use DataLoader;

	/**
	 * Класс переводчика
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 * @var Translator|null
	 */
	private static ?Translator $translator = null;

	/**
	 * Путь до фраз перевода
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 * @var string|null
	 */
	private static ?string $localization_path = null;
	/**
	 * Название локали
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 * @var string|null
	 */
	private static ?string $locale = null;
	/**
	 * @var bool
	 */
	private static bool $use_translator = false;

	/**
	 * Устанавливает переводчик для модуля с заданными настройками.
	 *
	 * Использует параметры из конфигурации для настройки локализации,
	 * загрузки переводов и их применения.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @throws JsonException Если возникла ошибка при работе с JSON-файлами.
	 *
	 * @return void
	 *
	 * @see DataManager::getConfig() Для получения конфигурационных данных.
	 * @see Translator::setFallbackLocales() Для определения резервных локализаций.
	 * @see Translator::addLoader() Для добавления обработчика загрузки ресурсов перевода.
	 * @see Translator::addResource() Для добавления ресурса перевода.
	 * @see ArrayLoader Для загрузки переводов в виде массива.
	 * @global Translator self::$translator Глобальный объект переводчика для приложения.
	 */
	public static function setTranslator() : void {
		$mh_config = DataManager::getConfig('maharder');
		$path      = $mh_config['locales_path'] ?? ENGINE_DIR . '/inc/maharder/_locales';
		$locale    = $mh_config['language'] ?? 'ru_RU';

		self::setLocalizationPath($path);
		self::setLocale($locale);

		$locale_array = self::getTranslationArray();

		$translator = new Translator(self::getLocale());
		$translator->setFallbackLocales(['ru_RU']);
		$translator->addLoader('array', new ArrayLoader());
		$translator->addResource('array', $locale_array, self::getLocale());
		self::$translator = $translator;
	}

	/**
	 * Возвращает экземпляр переводчика, связанный с текущим модулем.
	 * Если модуль передан как параметр, то он устанавливается перед получением переводчика.
	 * Если переводчик ещё не установлен, он будет автоматически инициализирован для текущего модуля.
	 *
	 * @throws JsonException Генерируется при ошибках работы с JSON во внутренних методах.
	 *
	 * @return Translator|null Экземпляр переводчика или null, если переводчик не установлен.
	 *
	 * @see self::setModule() Используется для установки текущего модуля.
	 * @see self::setTranslator() Используется для инициализации переводчика для модуля.
	 * @see self::$translator Хранит текущий экземпляр переводчика.
	 */
	public static function getTranslator() : ?Translator {
		if (is_null(self::$translator)) {
			self::setTranslator();
		}
		return self::$translator;
	}

	/**
	 * Устанавливает локаль для приложения.
	 *
	 * @param string $locale Новое значение локали.
	 *
	 * @global string|null $locale Глобальная переменная, хранящая текущую локаль.
	 */
	public static function setLocale(string $locale) : void {
		self::$locale = $locale;
	}

	/**
	 * Возвращает текущую локаль или значение по умолчанию ('ru_RU'), если локаль не определена.
	 *
	 * @global string|null $locale Глобальная переменная, используемая для установки локали.
	 * @return string Текущая локаль или значение по умолчанию ('ru_RU').
	 */
	public static function getLocale() : string {
		return self::$locale ?: 'ru_RU';
	}

	/**
	 * Возвращает локализованные данные для указанного языка.
	 *
	 * @param string $locale Код языка, которому требуется соответствующая локализация.
	 *
	 * @return array Массив данных локализации для указанного языка.
	 *
	 * @throws Throwable Если возникают ошибки при работе с кешем или файловой системой.
	 * @throws JsonException Если возникли ошибки при декодировании JSON-данных.
	 *
	 * @see self::getLanguages() Используется для получения списка доступных языков.
	 */
	public static function getLocaleData(string $locale) : array {
		return self::getLanguages()[$locale];
	}

	/**
	 * Возвращает переведённую фразу.
	 *
	 * Метод использует функцию `getTranslationWithParameters` для получения перевода
	 * без указания дополнительных параметров.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @param string $phrase Фраза для перевода.
	 *
	 * @throws JsonException Исключение, выбрасываемое при ошибках обработки JSON
	 *                        (возможные ошибки в логе или настройках переводчика).
	 *
	 * @see self::getTranslationWithParameters Описание метода, используемого для перевода.
	 *
	 * @return string Переведённая строка.
	 */
	public static function getTranslation(string $phrase) : string {
		return self::getTranslationWithParameters($phrase, []);
	}

	/**
	 * Возвращает переведённую фразу с установленными параметрами.
	 *
	 * Перевод осуществляется с использованием зарегистрированного переводчика.
	 * Если переводчик не установлен, он инициализируется вызовом метода `setTranslator`.
	 * Если использование переводчика отключено, возвращается результат без перевода.
	 *
	 * @version 173.3.0
	 *
	 * @param string       $phrase     Исходная фраза, подлежащая переводу.
	 * @param array        $parameters Массив параметров для подстановки в фразу перевода.
	 *
	 * @return string Переведённая фраза или исходная строка при отключённом переводчике.
	 *
	 * @throws JsonException В случае возникновения ошибки при работе с переводчиком.
	 * @global null|object $translator Глобальный объект переводчика, инициализированный ранее.
	 *
	 * @since   173.3.0
	 *
	 * @see     self::$translator Для работы с объектом переводчика.
	 * @see     self::isUseTranslator Метод проверки статуса использования переводчика.
	 * @see     self::nonTranslator Функция обработки строки без использования переводчика.
	 */
	public static function getTranslationWithParameters(string $phrase, array $parameters) : string {
		if (is_null(self::$translator) && self::isUseTranslator()) {
			self::setTranslator();
		}
		if (!self::isUseTranslator()) return self::nonTranslator($phrase, $parameters);

		return self::$translator->trans($phrase, $parameters);
	}

	/**
	 * Возвращает переведённую фразу с учётом параметров множественного числа/склонения.
	 *
	 * Делегирует обработку перевода методу `getTranslationPluralWithParameters`, передавая
	 * пустой массив параметров в качестве третьего аргумента.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @param string $phrase Переводимая фраза.
	 * @param int    $count  Количество, используемое для выбора правильной формы множественного числа.
	 *
	 * @throws JsonException Если в процессе выполнения произошла ошибка обработки JSON.
	 *
	 * @see getTranslationPluralWithParameters()
	 *
	 * @return string Переведённая строка с учётом параметров множественного числа/склонения.
	 */
	public static function getTranslationPlural(string $phrase, int $count) : string {
		return self::getTranslationPluralWithParameters($phrase, $count, []);
	}

	/**
	 * Возвращает переведённую фразу с параметрами множественного числа, учитывая склонения, с дополнительной
	 * поддержкой параметров.
	 *
	 * @version 173.3.0
	 *
	 * @param string       $phrase     Фраза для перевода.
	 * @param int          $count      Число для выбора варианта перевода с учетом склонений.
	 * @param array        $parameters Набор дополнительных параметров для замены в фразе.
	 *
	 * @return string Переведённая фраза с учетом склонений и параметров.
	 *
	 * @throws JsonException Если возникает ошибка при обработке JSON данных.
	 * @since   173.3.0
	 *
	 * @see 	self::$translator Экземпляр текущего транслятора, используется для перевода.
	 * @see     self::isUseTranslator() Проверка, используется ли транслятор.
	 * @see     self::setTranslator() Установка транслятора.
	 * @see     self::getTranslationWithParameters() Получение перевода фразы с подстановкой параметров.
	 * @see     self::nonTranslator() Возвращение строки без перевода.
	 */
	public static function getTranslationPluralWithParameters(string $phrase, int $count, array $parameters): string {
		// Инициализация переводчика, если он еще не установлен
		if (self::$translator === null && self::isUseTranslator()) {
			self::setTranslator();
		}

		// Обогащение параметров информацией о числе
		$parameters += ['%count%' => $count, '{{count}}' => $count];

		// Если переводчик не используется, возвращаем обработанный текст без перевода
		if (!self::isUseTranslator()) {
			return self::nonTranslator($phrase, $parameters);
		}

		// Определение корректной формы множественного числа
		$variants = explode('|~|', self::getTranslationWithParameters($phrase, $parameters));
		$index    = match (true) {
			$count % 10 === 1 && $count % 100 !== 11                                          => 0,
			$count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) => 1,
			default                                                                           => 2,
		};

		return $variants[$index] ?? $variants[0];
	}

	/**
	 * Заменяет плейсхолдеры в строке на указанные значения.
	 *
	 * Метод принимает строку и массив пар "ключ-значение", где каждый ключ - это плейсхолдер,
	 * который заменяется соответствующим значением в строке.
	 *
	 * @param string $phrase Строка, содержащая плейсхолдеры.
	 * @param array  $params Ассоциативный массив, где ключи - плейсхолдеры, а значения - замены.
	 *
	 * @return string Обработанная строка с произведёнными заменами.
	 */
	private static function nonTranslator($phrase, $params = []) {
		foreach ($params as $s => $r) {
			$phrase = str_replace($s, $r, $phrase);
		}
		return $phrase;
	}

	/**
	 * Устанавливает путь до переводимых фраз
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @param    string    $localization_path
	 */
	public static function setLocalizationPath(string $localization_path) : void {
		self::$localization_path = $localization_path;
	}

	/**
	 * Возвращает массив переводов из XLIFF файла в виде ассоциативного массива,
	 * где ключами являются исходные строки, а значениями — переведённые строки.
	 * Если файл перевода отсутствует или возникает ошибка при его обработке,
	 * возвращается пустой массив. Реализована поддержка кеширования для ускорения
	 * получения данных при последующих вызовах.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 * @return array Ассоциативный массив переводов.
	 * @throws JsonException Исключение при ошибке работы с JSON при кэшировании.
	 * @throws Throwable Исключение при неизвестной ошибке в процессе обработки файла.
	 * @see     DataManager::normalizePath() Для нормализации пути к XLIFF файлу.
	 * @see     LogGenerator::generateLog() Для логирования ошибок и предупреждений.
	 * @see     CacheControl::getCache() Для получения данных из кэша.
	 * @see     CacheControl::setCache() Для сохранения данных в кэше.
	 */
	private static function getTranslationArray(): array {
		$directory = DataManager::normalizePath(self::getLocalizationPath() . '/' . self::getLocale());

		if (!is_dir($directory)) {
			LogGenerator::generateLog(
				'MhTranslation',
				'getTranslationArray',
				"Директория с переводами \"{$directory}\" не найдена!",
				"warn"
			);
			self::setUseTranslator(false);
			return [];
		}

		$data = CacheControl::getCache('MhTranslation', 'lang_' . self::getLocale());
		if (!$data) {

			$data = [];
			try {
				$files = DataManager::dirToArray($directory);

				// Чтение и обработка файлов с использованием array_reduce для избежания array_merge в цикле
				$data = array_reduce(
					$files,
					static fn(array $carry, string $fileName): array => [
						...$carry, ...self::parseXliffFile($fileName, $directory)],
					[]
				);

				CacheControl::setCache('MhTranslation', 'lang_' . self::getLocale(), $data);
			} catch (Exception $e) {
				LogGenerator::generateLog(
					'MhTranslation',
					'getTranslationArray',
					"Ошибка чтения и обработки файлов перевода: {$e->getMessage()}",
					"critical"
				);
			}
		}
		return $data;
	}

	private static function parseXliffFile(string $filePath, string $directory): array {
		$data = [];
		if (pathinfo($filePath, PATHINFO_EXTENSION) === 'xliff') {
			$file        = DataManager::normalizePath($directory . '/' . $filePath);
			$fileContent = str_replace(["\n", "\r", "\t"], '', file_get_contents($file));
			$xml         = new SimpleXMLElement($fileContent, LIBXML_NOCDATA);

			if (!empty($xml->file->body->{'trans-unit'})) {
				foreach ($xml->file->body->{'trans-unit'} as $unit) {
					$source = (string)$unit->source;
					$target = trim((string)$unit->target);
					if ($source !== '' && !isset($data[$source])) {
						$data[$source] = $target;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Получает список доступных языков.
	 *
	 * Метод проверяет наличие кэша для списка языков.
	 * Если кэш отсутствует, формирует список языков на основе содержимого директории локализаций.
	 * Полученные данные сохраняются в кэше для последующего использования.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @throws JsonException|Throwable Возникает при ошибке декодирования JSON в момент работы с кэшем.
	 *
	 * @return array Массив доступных языков, где ключ — идентификатор языка, а значение — данные языка.
	 *
	 * @see CacheControl::getCache()
	 * @see CacheControl::setCache()
	 */
	public static function getLanguages() : array {
		$languages = CacheControl::getCache('MhTranslation', 'getLanguages');
		if (!$languages) {
			$languages = [];
			$list = DataManager::dirToArray(self::getLocalizationPath());
			foreach ($list as $l => $files) {
				if (!in_array($l, ['.', '..', '.htaccess'])) {
					$languages[$l] = self::languageList($l);
				}
			}
			CacheControl::setCache('MhTranslation', 'getLanguages', $languages);
		}

		return $languages;
	}

	/**
	 * Возвращает отформатированный список языков с заданным шаблоном формата.
	 *
	 * Поддерживаемые подстановочные шаблоны в строке формата:
	 * - `{original}` - заменяется на переведённое название языка.
	 * - `{english}` - заменяется на английское название языка.
	 * - `{iso2}` - заменяется на двузначный код языка (например: `ru`).
	 * - `{tag}` - заменяется на код языка (например: `ru_RU`).
	 *
	 * Если формат не указан, по умолчанию используется шаблон: "{original} ({english})".
	 *
	 * @version 173.3.0
	 *
	 * @param string|null $format Формат строки для замены подстановочных шаблонов.
	 *
	 * @return array Возвращает массив языков, где каждый элемент содержит:
	 *               - `tag` - код языка,
	 *               - `name` - сгенерированное название языка на основе переданного формата.
	 *
	 * @throws Throwable
	 * @throws JsonException Выбрасывается в случае ошибок при JSON-операциях.
	 * @since   173.3.0
	 *
	 * @see     self::getLanguages() Для получения списка языков.
	 */
	public static function getFormattedLanguageList(string $format = '{original} ({english})'): array {
		// Заменено на применение array_map для повышения читаемости и уменьшения кода
		$languages = self::getLanguages();

		return array_map(static function ($language) use ($format) {
			return [
				'tag' => $language['tag'],
				'name' => str_replace(
					['{original}', '{english}', '{iso2}', '{tag}'],
					[$language['original'], $language['english'], $language['iso2'], $language['tag']],
					$format
				),
			];
		}, $languages);
	}

	/**
	 * Возвращает массив данных о языке на основе переданного кода языка.
	 *
	 * @version 173.3.0
	 * @since   173.3.0
	 *
	 * @param string $lang Код языка, для которого необходимо получить информацию.
	 *
	 * @return array Ассоциативный массив с данными о языке. Содержит следующие ключи:
	 *               - `original` (string): Название языка на его родном языке.
	 *               - `english` (string): Название языка на английском.
	 *               - `iso2` (string): Код ISO 639-1 языка.
	 *               - `tag` (string): Полный тег языка.
	 */
	private static function languageList($lang) : array {
		$langs = [
			'ru_RU' => [
				'original' => 'Русский',
				'english'  => 'Russian',
				'iso2'     => 'ru',
				'tag'      => 'ru_RU',
			],
			'en_US' => [
				'original' => 'Английский',
				'english'  => 'English',
				'iso2'     => 'en',
				'tag'      => 'en_US',
			],
			'de_DE' => [
				'original' => 'Немецкий',
				'english'  => 'German',
				'iso2'     => 'de',
				'tag'      => 'de_DE',
			],
			'uk_UA' => [
				'original' => 'Украинский',
				'english'  => 'Ukrainian',
				'iso2'     => 'uk',
				'tag'      => 'uk_UA',
			],
		];

		return $langs[$lang];
	}

	/**
	 * Устанавливает использование переводчика.
	 *
	 * @param bool $use_translator Указывает, должен ли использоваться переводчик.
	 *
	 * @return void
	 * @global bool $use_translator
	 */
	public static function setUseTranslator(bool $use_translator) : void {
		self::$use_translator = $use_translator;
	}

	/**
	 * Проверяет, используется ли переводчик.
	 *
	 * Метод проверяет, инициализирован ли статический переводчик
	 * (`self::$translator`). Если переменная не равна null, значит переводчик
	 * используется.
	 *
	 * @return bool Возвращает true, если переводчик задан; иначе false.
	 */
	public static function isUseTranslator() : bool {
		return !is_null(self::$translator);
	}

	/**
	 * Получает путь к локализации приложения.
	 *
	 * Метод возвращает путь к директории с файлами локализации. Если путь ранее
	 * не был установлен или пуст, он загружается из конфигурации `mhadmin` и
	 * по умолчанию указывает на директорию `/engine/inc/maharder/_locales`.
	 *
	 * @throws JsonException Если возникает ошибка при загрузке конфигурации.
	 * @return string|null Полный путь к локализации, или null, если ROOT_DIR не определён.
	 *
	 * @see DataManager::getConfig() Используется для получения конфигурации `mhadmin`.
	 * @see self::$localization_path Глобальная переменная, содержащая путь к локализации.
	 */
	public static function getLocalizationPath() : ?string {
		if (is_null(self::$localization_path) || empty(self::$localization_path)) {
			$config                  = DataManager::getConfig('mhadmin');
			$path                    = $config['locales_path'] ?: '/engine/inc/maharder/_locales';
			self::$localization_path = ROOT_DIR . $path;
		}
		return DataManager::normalizePath(self::$localization_path);
	}

	/**
	 * Преобразует XLIFF-файлы переводов в JavaScript-файлы для поддержки локализации на клиентской стороне.
	 *
	 * Метод перебирает доступные языки из функции {@see self::getLanguages()}, кэширует переводы,
	 * преобразует их из XLIFF-файлов и генерирует JavaScript-файлы с переводами.
	 * Если переводы не найдены или директория переводов отсутствует, генерируется лог ошибки.
	 *
	 * @return void
	 * @throws JsonException|Throwable
	 *
	 * @see DataManager::normalizePath() Нормализация пути к файлам.
	 * @see DataManager::dirToArray()    Получение списка файлов из директории.
	 * @see LogGenerator::generateLog()  Генерация логов по различным событиям.
	 *
	 * @see self::getLanguages()         Получение списка доступных языков.
	 * @see CacheControl::getCache()     Управление кэшированием переводов.
	 */
	public static function convertXliffToJs(): void {
		static $hasRun = false;

		if (!$hasRun) {
			foreach (self::getLanguages() as $lang) {
				$cacheKey     = "js_trans_{$lang['tag']}";
				$translations = CacheControl::getCache('MhTranslation', $cacheKey);

				if ($translations === false) {
					$langFilesPath = DataManager::normalizePath(
						sprintf(
							"%s/%s",
							self::getLocalizationPath(),
							$lang['tag']
						)
					);

					if (!is_dir($langFilesPath)) {
						LogGenerator::generateLog(
							'MhTranslation',
							'convertXliffToJs',
							"Директория перевода не найдена: {$langFilesPath}"
						);
						continue;
					}

					$langFiles    = DataManager::dirToArray($langFilesPath);
					$xliffFiles   = array_filter($langFiles, fn($file) => str_ends_with($file, '.xliff'));
					$translations = [];

					foreach ($xliffFiles as $file) {
						$fileTranslations = self::getTranslationArray(
							DataManager::normalizePath(sprintf("%s/%s", $langFilesPath, $file))
						);
						if ($fileTranslations) {
							$translations += $fileTranslations; // Сложение массивов быстрее, чем array_merge
						}
					}
					$translations = array_filter($translations);
					CacheControl::setCache('MhTranslation', $cacheKey, $translations);
				}

				$jsContent  = self::generateJsTranslationContent($translations);
				$outputFile = DataManager::normalizePath(
					sprintf("%s/assets/js/i18n/translation.%s.js", MH_ADMIN, $lang['tag'])
				);

				if ($translations && !file_exists($outputFile)) {
					// Запись файла JS
					if (self::writeJsFile($outputFile, $jsContent)) {
						LogGenerator::generateLog(
							'MhTranslation',
							'convertXliffToJs',
							"Файл перевода успешно преобразован в JS: {$outputFile}",
							'info'
						);
					}
				}

			}

		}
		$hasRun = file_exists(DataManager::normalizePath(sprintf("%s/assets/js/i18n/translation.%s.js", MH_ADMIN, self::getLocale())));

	}

	/**
	 * Генерирует содержимое JavaScript-файла с переводами на основе переданного массива переводов.
	 *
	 * @param array $translations Ассоциативный массив переводов, где ключи являются идентификаторами строк, а значения
	 *                            — переводами.
	 *
	 * @return string Содержимое для JavaScript, включающее объект переводов и экспорт по умолчанию.
	 */
	private static function generateJsTranslationContent(array $translations): string {
		return "const translations = " . json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";\nexport default translations;\n";
	}

	/**
	 * Записывает переданное содержимое в указанный файл JavaScript.
	 *
	 * В случае неуспешной записи генерирует критический лог с использованием метода LogGenerator::generateLog.
	 *
	 * @param string $path    Путь к файлу, в который необходимо записать данные.
	 * @param string $content Содержимое, которое нужно записать в файл.
	 *
	 * @return bool Возвращает true, если запись прошла успешно, или false, если возникла ошибка.
	 *
	 * @throws Throwable
	 * @see LogGenerator::generateLog()
	 */
	private static function writeJsFile(string $path, string $content): bool {
		try {
			$touched = touch($path);
			if (!$touched && !file_exists($path)) {
				LogGenerator::generateLog(
					'MhTranslation',
					'writeJsFile',
					"Ошибка создания JS файла: {$path}",
					'critical'
				);
				return false;
			}
		} catch(Exception $e) {
			LogGenerator::generateLog(
				'MhTranslation',
				'writeJsFile',
				[
					$e->getMessage(),
					"Ошибка создания JS файла: {$path}"
				],
				'critical'
			);
			return false;
		}
		try {
			$created = file_put_contents($path, $content);

			if(!$created) {
				LogGenerator::generateLog(
					'MhTranslation',
					'writeJsFile',
					"Ошибка записи JS файла: {$path}",
					'critical'
				);
				return false;
			}
		} catch (Exception $e) {
			LogGenerator::generateLog(
				'MhTranslation',
				'writeJsFile',
				[
					$e->getMessage(),
					"Ошибка записи JS файла: {$path}"
				],
				'critical'
			);
			return false;
		}

		return true;
	}


}
