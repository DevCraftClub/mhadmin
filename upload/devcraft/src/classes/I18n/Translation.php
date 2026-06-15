<?php
//===============================================================
// Файл: Translation.php                                        =
// Путь: devcraft/src/classes/I18n/Translation.php              =
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

namespace DevCraft\Core\I18n;

use Exception;
use Throwable;
use SimpleXMLElement;
use DevCraft\Core\Config\Paths;
use DevCraft\Types\LanguageData;
use DevCraft\Core\Cache\CacheControl;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Config\DevCraftConfig;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

/**
 * Статический фасад локализации: Symfony Translator, XLIFF-каталоги и JS-словари.
 *
 * Портировано из MhTranslation (mhadmin).
 *
 * @package    DevCraft
 * @since      173.3.0
 * @subpackage Core.I18n
 */
final class Translation {

	/**
	 * Экземпляр Symfony-переводчика для текущей локали.
	 *
	 * @since 173.3.0
	 *
	 * @var Translator|null
	 */
	private static ?Translator $translator = NULL;

	/**
	 * Абсолютный путь к каталогу локализаций.
	 *
	 * @since 173.3.0
	 *
	 * @var string|null
	 */
	private static ?string $localization_path = NULL;

	/**
	 * Текущий тег локали (например, ru_RU).
	 *
	 * @since 173.3.0
	 *
	 * @var string|null
	 */
	private static ?string $locale = NULL;

	/**
	 * Флаг использования переводчика при наличии загруженного словаря.
	 *
	 * @since 173.3.0
	 *
	 * @var bool
	 */
	private static bool $use_translator = false;

	/**
	 * Возвращает словарь переводов для указанной локали без смены активной локали.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return array<string, string> Ассоциативный массив исходная_строка => перевод.
	 *
	 * @example
	 *     $dictionary = Translation::getDictionaryForLocale('en_US');
	 */
	public static function getDictionaryForLocale(string $locale): array {
		$previousLocale = self::$locale;
		self::setLocale($locale);
		$dictionary   = self::loadDictionaryForLocale($locale);
		self::$locale = $previousLocale;

		return $dictionary;
	}

	/**
	 * Генерирует JS-словари переводов из XLIFF для клиентского __().
	 *
	 * @since 173.3.0
	 *
	 * @example
	 *     Translation::convertXliffToJs();
	 */
	public static function convertXliffToJs(): void {
		$outputDir = DataManager::normalizePath(Paths::templates() . '/core/assets/js/i18n');

		if(!is_dir($outputDir) && !DataManager::createDir($outputDir)) {
			LogGenerator::for('Translation')->log(
				"Не удалось создать каталог JS-переводов: {$outputDir}",
				'warn',
			);

			return;
		}

		foreach(self::getLanguages() as $tag => $language) {
			if(!is_string($tag) || $tag === '') {
				continue;
			}

			$outputFile = DataManager::normalizePath($outputDir . '/translation.' . $tag . '.js');
			$xliffMtime = self::localeXliffMtime($tag);

			if(is_file($outputFile) && filemtime($outputFile) !== false && filemtime($outputFile) >= $xliffMtime) {
				continue;
			}

			$dictionary = self::getDictionaryForLocale($tag);

			if($dictionary === [] && !is_file($outputFile)) {
				continue;
			}

			if(!self::writeJsTranslationFile($outputFile, $tag, $dictionary)) {
				LogGenerator::for('Translation')->log(
					"Не удалось записать JS-файл перевода: {$outputFile}",
					'warn',
				);
			}
		}
	}

	/**
	 * Возвращает абсолютный путь к JS-файлу перевода для локали.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return string Нормализованный путь к translation.{locale}.js.
	 *
	 * @example
	 *     $path = Translation::jsTranslationFilePath('ru_RU');
	 */
	public static function jsTranslationFilePath(string $locale): string {
		return DataManager::normalizePath(
			Paths::templates() . '/core/assets/js/i18n/translation.' . $locale . '.js',
		);
	}

	/**
	 * Возвращает время последней модификации JS-файла перевода.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return int Unix-timestamp или 0, если файл отсутствует.
	 *
	 * @example
	 *     $mtime = Translation::jsTranslationFileMtime('ru_RU');
	 */
	public static function jsTranslationFileMtime(string $locale): int {
		$path = self::jsTranslationFilePath($locale);

		if(!is_file($path)) {
			return 0;
		}

		$mtime = filemtime($path);

		return $mtime !== false? $mtime : 0;
	}

	/**
	 * Возвращает двухбуквенный код локали Metro UI для заданного тега.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return string Код ISO 639-1 (например, ru).
	 *
	 * @example
	 *     $metroLocale = Translation::metroLocaleFor('ru_RU');
	 */
	public static function metroLocaleFor(string $locale): string {
		$meta = self::loadLanguageMeta($locale);

		return $meta->iso2;
	}

	/**
	 * Возвращает путь к JS-аддону Metro UI для локали или null, если файл отсутствует.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $metroLocale  Двухбуквенный код локали Metro.
	 *
	 * @return string|null Абсолютный путь к metro.{locale}.js или null.
	 *
	 * @example
	 *     $addon = Translation::metroLocaleAddonPath('de');
	 */
	public static function metroLocaleAddonPath(string $metroLocale): ?string {
		if($metroLocale === '' || $metroLocale === 'en') {
			return NULL;
		}

		$path = DataManager::normalizePath(
			Paths::templates() . '/core/assets/js/i18n/metro.' . $metroLocale . '.js',
		);

		return is_file($path)? $path : NULL;
	}

	/**
	 * Устанавливает переводчик для модуля с заданными настройками.
	 *
	 * Использует параметры из конфигурации для настройки локализации,
	 * загрузки переводов и их применения.
	 *
	 * @since 173.3.0
	 *
	 * @example
	 *     Translation::setTranslator();
	 */
	public static function setTranslator(): void {
		$config = DevCraftConfig::raw();
		$locale = (string) ($config['language'] ?? 'ru_RU');

		if(self::$translator !== NULL && self::$locale === $locale) {
			return;
		}

		$path = (string) ($config['locales_path'] ?? '');

		if($path === '') {
			$path = Paths::locales();
		} elseif(!str_starts_with($path, '/') && !preg_match('#^[A-Za-z]:[\\\\/]#', $path)) {
			$path = ROOT_DIR . $path;
		}

		self::setLocalizationPath($path);
		self::setLocale($locale);

		$locale_array = self::getTranslationArray();

		$translator = new Translator(self::getLocale());
		$translator->setFallbackLocales(['ru_RU']);
		$translator->addLoader('array', new ArrayLoader());
		$translator->addResource('array', $locale_array, self::getLocale());
		self::$translator     = $translator;
		self::$use_translator = $locale_array !== [];
	}

	/**
	 * Сбрасывает состояние переводчика и очищает кэш локализации.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     Translation::reset();
	 */
	public static function reset(): void {
		self::$translator     = NULL;
		self::$locale         = NULL;
		self::$use_translator = false;
		CacheControl::clearCache('Translation');
	}

	/**
	 * Возвращает экземпляр переводчика, связанный с текущим модулем.
	 *
	 * Если переводчик ещё не установлен, он будет автоматически инициализирован.
	 *
	 * @since 173.3.0
	 *
	 * @return Translator|null Экземпляр переводчика или null.
	 *
	 * @example
	 *     $translator = Translation::getTranslator();
	 */
	public static function getTranslator(): ?Translator {
		if(self::$translator === NULL) {
			self::setTranslator();
		}

		return self::$translator;
	}

	/**
	 * Устанавливает локаль для приложения.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $locale  Новое значение локали.
	 *
	 * @example
	 *     Translation::setLocale('en_US');
	 */
	public static function setLocale(string $locale): void {
		self::$locale = $locale;
	}

	/**
	 * Возвращает текущую локаль или значение по умолчанию (ru_RU).
	 *
	 * @since 173.3.0
	 *
	 * @return string Текущая локаль.
	 *
	 * @example
	 *     $locale = Translation::getLocale();
	 */
	public static function getLocale(): string {
		return self::$locale? : 'ru_RU';
	}

	/**
	 * Возвращает локализованные данные для указанного языка.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $locale  Код языка.
	 *
	 * @return array<string, mixed> Массив данных локализации или пустой массив.
	 *
	 * @example
	 *     $data = Translation::getLocaleData('ru_RU');
	 */
	public static function getLocaleData(string $locale): array {
		return self::getLanguages()[$locale] ?? [];
	}

	/**
	 * Возвращает переведённую фразу.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $phrase  Фраза для перевода.
	 *
	 * @return string Переведённая строка.
	 *
	 * @example
	 *     $label = Translation::getTranslation('Настройки');
	 */
	public static function getTranslation(string $phrase): string {
		return self::getTranslationWithParameters($phrase, []);
	}

	/**
	 * Возвращает переведённую фразу с установленными параметрами.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                $phrase      Исходная фраза.
	 * @param   array<string, mixed>  $parameters  Параметры подстановки.
	 *
	 * @return string Переведённая фраза или исходная строка при отключённом переводчике.
	 *
	 * @example
	 *     $text = Translation::getTranslationWithParameters('Привет, {name}!', ['{name}' => 'Максим']);
	 */
	public static function getTranslationWithParameters(string $phrase, array $parameters): string {
		if(self::$translator === NULL && self::isUseTranslator()) {
			self::setTranslator();
		}

		if(!self::isUseTranslator() || self::$translator === NULL) {
			return self::nonTranslator($phrase, $parameters);
		}

		return self::$translator->trans($phrase, $parameters);
	}

	/**
	 * Возвращает переведённую фразу с учётом множественного числа.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $phrase  Переводимая фраза.
	 * @param   int     $count   Количество для выбора формы.
	 *
	 * @return string Переведённая строка с учётом склонения.
	 *
	 * @example
	 *     $text = Translation::getTranslationPlural('{n} файл|~|{n} файла|~|{n} файлов', 3);
	 */
	public static function getTranslationPlural(string $phrase, int $count): string {
		return self::getTranslationPluralWithParameters($phrase, $count, []);
	}

	/**
	 * Возвращает переведённую фразу с параметрами множественного числа.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                $phrase      Фраза для перевода.
	 * @param   int                   $count       Число для выбора варианта склонения.
	 * @param   array<string, mixed>  $parameters  Дополнительные параметры подстановки.
	 *
	 * @return string Переведённая фраза с учётом склонений и параметров.
	 *
	 * @example
	 *     $text = Translation::getTranslationPluralWithParameters(
	 *         '{n} элемент|~|{n} элемента|~|{n} элементов',
	 *         5,
	 *         ['{n}' => 5],
	 *     );
	 */
	public static function getTranslationPluralWithParameters(string $phrase, int $count, array $parameters): string {
		if(self::$translator === NULL && self::isUseTranslator()) {
			self::setTranslator();
		}

		$parameters += ['%count%' => $count, '{{count}}' => $count];

		if(!self::isUseTranslator() || self::$translator === NULL) {
			return self::nonTranslator($phrase, $parameters);
		}

		$variants = explode('|~|', self::getTranslationWithParameters($phrase, $parameters));
		$index    = match (true) {
			$count % 10 === 1 && $count % 100 !== 11                                          => 0,
			$count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) => 1,
			default                                                                           => 2,
		};

		return $variants[$index] ?? $variants[0];
	}

	/**
	 * Устанавливает путь до переводимых фраз.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $localization_path  Абсолютный или относительный путь к каталогу локалей.
	 *
	 * @example
	 *     Translation::setLocalizationPath(Paths::locales());
	 */
	public static function setLocalizationPath(string $localization_path): void {
		self::$localization_path = $localization_path;
	}

	/**
	 * Получает список доступных языков.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, LanguageData> Массив языков: тег => метаданные.
	 *
	 * @example
	 *     $languages = Translation::getLanguages();
	 */
	public static function getLanguages(): array {
		$cacheKey  = 'getLanguages_' . self::languagesMetaVersion();
		$languages = CacheControl::getCache('Translation', $cacheKey);

		if($languages === false || !is_array($languages)) {
			$languages = [];
			$list      = DataManager::dirToArray(self::getLocalizationPath());

			foreach($list as $l => $files) {
				if(!in_array($l, ['.', '..', '.htaccess'], true) && is_string($l)) {
					$languages[$l] = self::loadLanguageMeta($l);
				}
			}

			CacheControl::setCache('Translation', $cacheKey, $languages);
		}

		return self::normalizeLanguages($languages);
	}

	/**
	 * Нормализует список языков после чтения из файлового JSON-кэша.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $languages  Сырые данные языков.
	 *
	 * @return array<string, LanguageData> Массив языков: тег => метаданные.
	 */
	private static function normalizeLanguages(array $languages): array {
		$normalized = [];

		foreach($languages as $tag => $language) {
			if($language instanceof LanguageData) {
				$normalized[(string) $tag] = $language;

				continue;
			}

			if(is_array($language)) {
				$normalized[(string) $tag] = LanguageData::fromArray($language);
			}
		}

		return $normalized;
	}

	/**
	 * Загружает метаданные языка из meta.php или формирует значения по умолчанию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return LanguageData Объект с названиями и кодами языка.
	 *
	 * @example
	 *     $meta = Translation::loadLanguageMeta('de_DE');
	 */
	public static function loadLanguageMeta(string $locale): LanguageData {
		$metaFile = DataManager::normalizePath(self::getLocalizationPath() . '/' . $locale . '/meta.php');

		if(is_file($metaFile)) {
			try {
				/** @var mixed $loaded */
				$loaded = require $metaFile;

				if($loaded instanceof LanguageData) {
					return $loaded;
				}
			} catch(Throwable) {
				LogGenerator::for('Translation')->log(
					__("Не удалось загрузить meta.php для локали \"{locale}\"", ['{locale}' => $locale]),
					'warn',
				);
			}
		}

		$iso2 = strlen($locale) >= 2? strtolower(substr($locale, 0, 2)) : $locale;

		return new LanguageData($locale, $locale, $iso2, $locale);
	}

	/**
	 * Преобразует LanguageData в legacy-формат списка языков.
	 *
	 * @since 200.4.0
	 *
	 * @param   LanguageData  $data  Метаданные языка.
	 *
	 * @return array{original: string, english: string, iso2: string, tag: string}
	 */
	private static function languageMetaToLegacy(LanguageData $data): array {
		return [
			'original' => $data->originalName,
			'english'  => $data->englishName,
			'iso2'     => $data->iso2,
			'tag'      => $data->tag,
		];
	}

	/**
	 * Возвращает версию кэша списка языков по mtime файлов meta.php.
	 *
	 * @since 200.4.0
	 *
	 * @return string Строковое представление максимального mtime.
	 */
	private static function languagesMetaVersion(): string {
		$path = self::getLocalizationPath();

		if($path === NULL || !is_dir($path)) {
			return '0';
		}

		$maxMtime = 0;

		foreach(glob($path . '/*/meta.php')? : [] as $metaFile) {
			$mtime = filemtime($metaFile);

			if($mtime !== false) {
				$maxMtime = max($maxMtime, $mtime);
			}
		}

		return (string) $maxMtime;
	}

	/**
	 * Возвращает отформатированный список языков с заданным шаблоном.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $format  Шаблон подстановки ({originalName}, {englishName}, {iso2}, {tag}).
	 *
	 * @return array<int, array<string, string>> Массив с ключами tag и name.
	 *
	 * @example
	 *     $list = Translation::getFormattedLanguageList('{originalName} ({englishName})');
	 */
	public static function getFormattedLanguageList(string $format = '{originalName} ({englishName})'): array {
		$languages = self::getLanguages();

		return array_map(
			static function(LanguageData $language) use ($format): array {
				return [
					'tag'  => $language->tag,
					'name' => str_replace(
						['{originalName}', '{englishName}', '{original}', '{english}', '{iso2}', '{tag}'],
						[
							$language->originalName,
							$language->englishName,
							$language->originalName,
							$language->englishName,
							$language->iso2,
							$language->tag,
						],
						$format,
					),
				];
			},
			$languages,
		);
	}

	/**
	 * Устанавливает использование переводчика.
	 *
	 * @since 173.3.0
	 *
	 * @param   bool  $use_translator  true — включить переводчик.
	 *
	 * @example
	 *     Translation::setUseTranslator(true);
	 */
	public static function setUseTranslator(bool $use_translator): void {
		self::$use_translator = $use_translator;
	}

	/**
	 * Проверяет, используется ли переводчик.
	 *
	 * @since 173.3.0
	 *
	 * @return bool true, если переводчик активен.
	 *
	 * @example
	 *     $active = Translation::isUseTranslator();
	 */
	public static function isUseTranslator(): bool {
		return self::$use_translator;
	}

	/**
	 * Получает путь к локализации приложения.
	 *
	 * @since 173.3.0
	 *
	 * @return string|null Полный нормализованный путь к каталогу локалей.
	 *
	 * @example
	 *     $path = Translation::getLocalizationPath();
	 */
	public static function getLocalizationPath(): ?string {
		if(self::$localization_path === NULL || self::$localization_path === '') {
			$config = DataManager::getConfig('devcraft');
			$path   = $config['locales_path'] ?? '';

			if($path === '') {
				self::$localization_path = Paths::locales();
			} elseif(!str_starts_with($path, '/') && !preg_match('#^[A-Za-z]:[\\\\/]#', $path)) {
				self::$localization_path = ROOT_DIR . $path;
			} else {
				self::$localization_path = $path;
			}
		}

		return DataManager::normalizePath(self::$localization_path);
	}

	/**
	 * Заменяет плейсхолдеры в строке без использования переводчика.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                $phrase  Исходная строка.
	 * @param   array<string, mixed>  $params  Пары плейсхолдер => значение.
	 *
	 * @return string Обработанная строка.
	 */
	private static function nonTranslator(string $phrase, array $params = []): string {
		foreach($params as $search => $replace) {
			$phrase = str_replace((string) $search, (string) $replace, $phrase);
		}

		return $phrase;
	}

	/**
	 * Возвращает массив переводов для текущей активной локали.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, string> Словарь переводов.
	 */
	private static function getTranslationArray(): array {
		return self::loadDictionaryForLocale(self::getLocale());
	}

	/**
	 * Загружает словарь переводов из XLIFF-файлов указанной локали.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return array<string, string> Ассоциативный массив переводов.
	 */
	private static function loadDictionaryForLocale(string $locale): array {
		$directory = DataManager::normalizePath(self::getLocalizationPath() . '/' . $locale);

		if(!is_dir($directory)) {
			$config = DevCraftConfig::raw();

			if($locale === (string) ($config['language'] ?? 'ru_RU')) {
				LogGenerator::for('Translation')->log(
					"Директория с переводами \"{$directory}\" не найдена!",
					'warn',
				);
				self::setUseTranslator(false);
			}

			return [];
		}

		$data = CacheControl::getCache('Translation', 'lang_' . $locale);

		if($data === false || !is_array($data)) {
			$data = [];

			try {
				$files = self::flattenFileList(DataManager::dirToArray($directory));

				foreach($files as $fileName) {
					if(!is_string($fileName) || pathinfo($fileName, PATHINFO_EXTENSION) !== 'xliff') {
						continue;
					}

					$data = [...$data, ...self::parseXliffFile($fileName, $directory)];
				}

				CacheControl::setCache('Translation', 'lang_' . $locale, $data);
			} catch(Exception $e) {
				LogGenerator::for('Translation')->log(
					"Ошибка чтения и обработки файлов перевода: {$e->getMessage()}",
					'critical',
				);
			}
		}

		return is_array($data)? $data : [];
	}

	/**
	 * Возвращает максимальный mtime XLIFF-файлов локали.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $locale  Тег локали.
	 *
	 * @return int Unix-timestamp или 0.
	 */
	private static function localeXliffMtime(string $locale): int {
		$directory = DataManager::normalizePath(self::getLocalizationPath() . '/' . $locale);

		if(!is_dir($directory)) {
			return 0;
		}

		$maxMtime = 0;

		foreach(glob($directory . '/*.xliff')? : [] as $xliffFile) {
			$mtime = filemtime($xliffFile);

			if($mtime !== false) {
				$maxMtime = max($maxMtime, $mtime);
			}
		}

		return $maxMtime;
	}

	/**
	 * Записывает JS-файл словаря переводов для клиентского DevCraftI18n.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                 $path        Путь к выходному файлу.
	 * @param   string                 $locale      Тег локали.
	 * @param   array<string, string>  $dictionary  Словарь переводов.
	 *
	 * @return bool true при успешной записи.
	 */
	private static function writeJsTranslationFile(string $path, string $locale, array $dictionary): bool {
		$encoded = json_encode(
			$dictionary,
			JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR,
		);

		if(!is_string($encoded)) {
			return false;
		}

		$content = "(function (global) {\n"
		           . "    global.DevCraftI18n = global.DevCraftI18n || {};\n"
		           . "    global.DevCraftI18n.locale = " . json_encode($locale, JSON_UNESCAPED_UNICODE) . ";\n"
		           . "    global.DevCraftI18n.dictionary = {$encoded};\n"
		           . "})(window);\n";

		return file_put_contents($path, $content) !== false;
	}

	/**
	 * Парсит один XLIFF-файл и возвращает пары source => target.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $filePath   Имя или относительный путь файла.
	 * @param   string  $directory  Каталог локали.
	 *
	 * @return array<string, string> Извлечённые переводы.
	 */
	private static function parseXliffFile(string $filePath, string $directory): array {
		$data = [];

		if(pathinfo($filePath, PATHINFO_EXTENSION) !== 'xliff') {
			return $data;
		}

		$file     = DataManager::normalizePath($directory . '/' . $filePath);
		$contents = file_get_contents($file);

		if($contents === false) {
			return $data;
		}

		$fileContent = str_replace(["\n", "\r", "\t"], '', $contents);
		$xml         = new SimpleXMLElement($fileContent, LIBXML_NOCDATA);

		if(!empty($xml->file->body->{'trans-unit'})) {
			foreach($xml->file->body->{'trans-unit'} as $unit) {
				$source = (string) $unit->source;
				$target = trim((string) $unit->target);

				if($source !== '' && !isset($data[$source])) {
					$data[$source] = $target !== ''? $target : $source;
				}
			}
		}

		return $data;
	}

	/**
	 * Разворачивает древовидный список файлов dirToArray в плоский список путей.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<mixed>  $tree  Дерево каталогов от DataManager::dirToArray().
	 *
	 * @return list<string> Относительные пути файлов.
	 */
	private static function flattenFileList(array $tree): array {
		$files = [];

		foreach($tree as $key => $value) {
			if(is_int($key)) {
				if(is_string($value)) {
					$files[] = $value;
				}
			} elseif(is_array($value)) {
				foreach(self::flattenFileList($value) as $nested) {
					$files[] = $key . '/' . $nested;
				}
			}
		}

		return $files;
	}

}
