<?php

use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

/**
 * Класс для оформления фраз переводов
 *
 * @version 2.0.9
 * @since   2.0.9
 */
abstract class MhTranslation {

	use DataLoader;

	/**
	 * Класс переводчика
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 * @var Translator|null
	 */
	private static ?Translator $translator = null;

	/**
	 * Путь до фраз перевода
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 * @var string|null
	 */
	private static ?string $localization_path = null;
	/**
	 * Названия модуля, к которому будет применяться перевод
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 * @var string|null
	 */
	private static ?string $module = null;
	/**
	 * Название локали
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 * @var string|null
	 */
	private static ?string $locale = null;
	/**
	 * @var bool
	 */
	private static bool $use_translator = false;

	/**
	 * Инициализация класса
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $module
	 *
	 * @throws JsonException
	 * @return void
	 */
	public static function setTranslator(string $module) : void {
		$mh_config = DataManager::getConfig('maharder');
		$path      = $mh_config['locales_path'] ?? ENGINE_DIR . '/inc/maharder/_locales';
		$locale    = $mh_config['language'] ?? 'ru_RU';

		self::setLocalizationPath($path);
		self::setModule($module);
		self::setLocale($locale);

		$locale_array = self::getTranslationArray();

		$translator = new Translator(self::getLocale());
		$translator->setFallbackLocales(['ru_RU']);
		$translator->addLoader('array', new ArrayLoader());
		$translator->addResource('array', $locale_array, self::getLocale());
		self::$translator = $translator;
	}

	/**
	 * @param    string|null    $module
	 *
	 * @throws JsonException
	 * @return Translator|null
	 */
	public static function getTranslator(?string $module = null) : ?Translator {
		if (!is_null($module)) {
			self::setModule($module);
		}
		if (is_null(self::$translator)) {
			self::setTranslator(self::getModule());
		}
		return self::$translator;
	}


	/**
	 * @param    string    $module
	 */
	public static function setModule(string $module) : void {
		self::$module = $module;
	}

	/**
	 * @return string
	 */
	public static function getModule() : string {
		return self::$module ?: 'mhadmin';
	}

	/**
	 * Проставляем локаль
	 *
	 * @param    string    $locale
	 */
	public static function setLocale(string $locale) : void {
		self::$locale = $locale;
	}

	/**
	 * @return string
	 */
	public static function getLocale() : string {
		return self::$locale ?: 'ru_RU';
	}

	/**
	 * Возвращает переведённую фразу
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $phrase
	 *
	 * @throws JsonException
	 * @return string
	 */
	public static function getTranslation(string $phrase) : string {
		return self::getTranslationWithParameters($phrase, []);
	}

	/**
	 * Возвращает переведённую фразу с установленными параметрами
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $phrase
	 * @param    array     $parameters
	 *
	 * @throws JsonException
	 * @return string
	 */
	public static function getTranslationWithParameters(string $phrase, array $parameters) : string {
		if (is_null(self::$translator) && self::isUseTranslator()) {
			self::setTranslator(self::getModule());
		}
		if (!self::isUseTranslator()) return self::nonTranslator($phrase, $parameters);

		return self::$translator->trans($phrase, $parameters);
	}

	/**
	 * Возвращает переведённую фразу с параметрами множественного числа / со склонениями
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $phrase
	 * @param    int       $count
	 *
	 * @throws JsonException
	 * @return string
	 */
	public static function getTranslationPlural(string $phrase, int $count) : string {
		return self::getTranslationPluralWithParameters($phrase, $count, []);
	}

	/**
	 * Возвращает переведённую фразу с параметрами множественного числа / со склонениями и дополнительными фразами
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $phrase
	 * @param    int       $count
	 * @param    array     $parameters
	 *
	 * @throws JsonException
	 * @return string
	 */
	public static function getTranslationPluralWithParameters(string $phrase, int $count, array $parameters) : string {
		if (is_null(self::$translator) && self::isUseTranslator()) {
			self::setTranslator(self::getModule());
		}
		$parameters = array_merge($parameters, ['%count%' => $count, '{{count}}' => $count]);

		if (!self::isUseTranslator()) {
			return self::nonTranslator($phrase, $parameters);
		}

		$phrases = explode('|~|', self::getTranslationWithParameters($phrase, $parameters));

		return $phrases[($count % 10 === 1 && $count % 100 !== 11)
			? 0
			: ($count % 10 >= 2 &&
			$count % 10 <= 4 &&
			($count % 100 < 10 || $count % 100 >= 20) ? 1 : 2)];

	}

	private static function nonTranslator($phrase, $params = []) {
		foreach ($params as $s => $r) {
			$phrase = str_replace($s, $r, $phrase);
		}
		return $phrase;
	}

	/**
	 * Устанавливает путь до переводимых фраз
	 *
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param    string    $localization_path
	 */
	public static function setLocalizationPath(string $localization_path) : void {
		self::$localization_path = $localization_path;
	}

	/**
	 * @version 2.0.9
	 * @since   2.0.9
	 * @throws JsonException
	 * @return array
	 */
	private static function getTranslationArray() : array {
		$file = DataManager::normalizePath(sprintf(
			"%s/%s/%s.xliff",
			self::getLocalizationPath(),
			self::getLocale(),
			self::getModule()
		));
		$data = [];

		if (!file_exists($file)) {
			LogGenerator::generateLog(
				'MhTranslation', 'getTranslationArray', "Файл перевода \"{$file}\" не был найден!"
			);
			self::setUseTranslator(false);

			return [];
		}
		$data = CacheControl::getCache('MhTranslation', 'lang_' . self::getLocale());
		if (!$data) {
			$file_content = preg_replace('/([\n\r\t])/', '', file_get_contents($file));
			$xml          = simplexml_load_string($file_content, "SimpleXMLElement", LIBXML_NOCDATA);
			$array        = (array) $xml->file->body;

			foreach ($array['trans-unit'] as $d) {
				$data[(string) $d->source] = trim((string) $d->target);
			}
			CacheControl::setCache('MhTranslation', 'lang_' . self::getLocale(), $data);
		}
		return $data;
	}

	/**
	 * @version 2.0.9
	 * @since   2.0.9
	 * @throws JsonException
	 * @return array
	 */
	public static function getLanguages() : array {
		$languages = CacheControl::getCache('MhTranslation', 'getLanguages');
		if (!$languages) {
			$list = scandir(self::getLocalizationPath());
			foreach ($list as $l) {
				if (!in_array($l, ['.', '..', '.htaccess'])) {
					$languages[$l] = self::languageList($l);
				}
			}
			CacheControl::setCache('MhTranslation', 'getLanguages', $languages);
		}

		return $languages;
	}

	/**
	 * Возвращает отформатированный список
	 * {original} - Заменяет текст на переведённое название языка
	 * {english} - Заменяет текст на английское название языка
	 * {iso2} - Заменяет текст на двузначный код языка (к примеру: ru)
	 * {tag} - Заменяет текст на код языка (к примеру: ru_RU)
	 * По умолчанию: {original} ({english})
	 *
	 * @version 2.0.9
	 * @sincs   2.0.9
	 *
	 * @param    string|null    $format
	 *
	 * @throws JsonException
	 * @return array
	 */
	public static function getFormattedLanguageList(string $format = null) : array {
		$fl = [];
		$f  = !is_null($format) ? $format : '{original} ({english})';
		foreach (self::getLanguages() as $c => $l) {
			$fl[] = [
				'tag'  => $l['tag'],
				'name' => str_replace(
					['{original}', '{english}', '{iso2}', '{tag}'],
					[$l['original'], $l['english'], $l['iso2'], $l['tag']],
					$f
				),
			];
		}

		return $fl;
	}

	/**
	 * @version 2.0.9
	 * @since   2.0.9
	 *
	 * @param $lang
	 *
	 * @return array
	 */
	private static function languageList($lang) : array {
		$langs = [
			'ru_RU' => [
				'original' => __('mhadmin', 'Русский'),
				'english'  => 'Russian',
				'iso2'     => 'ru',
				'tag'      => 'ru_RU',
			],
			'en_US' => [
				'original' => __('mhadmin', 'Английский'),
				'english'  => 'English',
				'iso2'     => 'en',
				'tag'      => 'en_US',
			],
			'de_DE' => [
				'original' => __('mhadmin', 'Немецкий'),
				'english'  => 'German',
				'iso2'     => 'de',
				'tag'      => 'de_DE',
			],
			'uk_UA' => [
				'original' => __('mhadmin', 'Украинский'),
				'english'  => 'Ukrainian',
				'iso2'     => 'uk',
				'tag'      => 'uk_UA',
			],
		];

		return $langs[$lang];
	}

	/**
	 * @param    bool    $use_translator
	 */
	public static function setUseTranslator(bool $use_translator) : void {
		self::$use_translator = $use_translator;
	}

	/**
	 * @return bool
	 */
	public static function isUseTranslator() : bool {
		return !is_null(self::$translator);
	}

	/**
	 * @throws JsonException
	 * @return string|null
	 */
	public static function getLocalizationPath() : ?string {
		if (is_null(self::$localization_path) || empty(self::$localization_path)) {
			$config                  = DataManager::getConfig('mhadmin');
			self::$localization_path = $config['locales_path'] ?: '/engine/inc/maharder/_locales';
		}
		return ROOT_DIR . self::$localization_path;
	}


}