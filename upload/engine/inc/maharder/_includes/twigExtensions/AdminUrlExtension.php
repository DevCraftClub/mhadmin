<?php

//namespace MaHarder\classes;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AdminUrlExtension extends AbstractExtension implements GlobalsInterface {

	protected static function getServerData(): array {
		return $_SERVER;
	}

	protected static function getUserHash(): string {
		global $dle_login_hash;

		return $dle_login_hash;
	}

	protected static function getDleConfig(): array {
		global $config;

		return $config;
	}

	protected static function getGetParams(): ?array {
		return filter_input_array(INPUT_GET);
	}

	protected static function getPostParams(): ?array {
		return filter_input_array(INPUT_POST);
	}

	protected static function getThisSelf() {
		return self::getServerData()['PHP_SELF'];
	}

	protected static function getThisHost() {
		return self::getServerData()['HTTP_HOST'];
	}

	protected static function getThisRoot() {
		return self::getServerData()['DOCUMENT_ROOT'];
	}

	/**
	 * Получает URL для статических ресурсов на основе данных текущего сервера.
	 *
	 * Метод формирует URL-адрес, используя протокол (`http` или `https`), исходя из значения
	 * `HTTPS` в массиве данных сервера, а также хост (`HTTP_HOST`). Формируемый URL
	 * ведет к каталогу `/engine/inc` приложения.
	 *
	 * @return string URL для статических ресурсов.
	 * @see AdminUrlExtension::getServerData()
	 * @see AdminUrlExtension::getThisHost()
	 */
	protected static function getAssetsUrl(): string {
		return (isset(self::getServerData()['HTTPS']) && 'on' === self::getServerData(
			)['HTTPS'] ? 'https' : 'http') . '://' . self::getThisHost() . '/engine/inc/maharder/admin/assets';
	}

	/**
	 * Получает URL модуля, основываясь на данных сервера.
	 *
	 * Возвращает реферальный URL из `HTTP_REFERER`, если он установлен.
	 * Если `HTTP_REFERER` отсутствует, возвращает `REQUEST_URI` или
	 * текущий скрипт вместе с параметрами запроса (`QUERY_STRING`), если другие данные недоступны.
	 *
	 * @return string URL модуля.
	 * @see AdminUrlExtension::getServerData()
	 * @see AdminUrlExtension::getThisSelf()
	 */
	protected static function getModulesUrl() {
		return (!empty(self::getServerData()['HTTP_REFERER']))
			? self::getServerData()['HTTP_REFERER']
			: ((!empty(self::getServerData()['REQUEST_URI'])) ? self::getServerData(
			)['REQUEST_URI'] : self::getThisSelf() . "?" . self::getServerData()['QUERY_STRING']);
	}

	/**
	 * Парсит и преобразует URL в стандартный формат.
	 *
	 * Преобразует символы в URL, удаляя лишние пробелы, табуляции и символы перевода строки,
	 * и создаёт корректную строку URL с обновленными параметрами запроса.
	 *
	 * @param string $url Строка URL для обработки.
	 *
	 * @return string Обработанный URL.
	 */
	public function parseUrl(string $url): string {
		$parts = parse_url(trim(str_replace(['&amp;', '\t', '\n'], ['&', '', ''], $url)));
		parse_str($parts['query'], $_url_data);

		foreach ($_url_data as $param => $value) {
			$_url_data[$param] = $value;
		}

		$url_path = '';
		if (isset($parts['scheme'])) $url_path .= "{$parts['scheme']}://";
		if (isset($parts['host'])) $url_path .= $parts['host'];
		if (isset($parts['path'])) $url_path .= $parts['path'];

		return "{$url_path}?" . http_build_query($_url_data);
	}

	/**
	 * Возвращает массив глобальных данных для использования в приложении.
	 *
	 * Метод собирает различные данные, такие как URL ресурсов, настройки пользователя,
	 * серверные данные, параметры запроса `GET` и `POST`, а также информацию о локали и языках.
	 * Эти данные могут быть использованы, например, для настройки интерфейса или работы с конфигурацией приложения.
	 *
	 * @return array Ассоциативный массив глобальных данных:
	 * - `assets_url` (`string`): URL статических ресурсов (см. {@see AdminUrlExtension::getAssetsUrl()}).
	 * - `plugin_url` (`string`): URL модуля (см. {@see AdminUrlExtension::getModulesUrl()}).
	 * - `dle_login_hash` (`string`): Хэш авторизации пользователя (см. {@see AdminUrlExtension::getUserHash()}).
	 * - `dle_config` (`array`): Конфигурация DataLife Engine (см. {@see AdminUrlExtension::getDleConfig()}).
	 * - `_server` (`array`): Суперглобальный массив `$_SERVER` (см. {@see AdminUrlExtension::getServerData()}).
	 * - `_get` (`array|null`): Параметры запроса `GET` (см. {@see AdminUrlExtension::getGetParams()}).
	 * - `_post` (`array|null`): Параметры запроса `POST` (см. {@see AdminUrlExtension::getPostParams()}).
	 * - `languages` (`array`): Форматированный список языков (см. {@see MhTranslation::getFormattedLanguageList()}).
	 * - `selected_lang` (`string`): Текущая локаль/язык (см. {@see MhTranslation::getLocale()}).
	 * - `lang_data` (`array`): Данные о текущей локали (см. {@see MhTranslation::getLocaleData()}).
	 *
	 * @see AdminUrlExtension::getAssetsUrl()
	 * @see AdminUrlExtension::getModulesUrl()
	 * @see AdminUrlExtension::getUserHash()
	 * @see AdminUrlExtension::getDleConfig()
	 * @see AdminUrlExtension::getServerData()
	 * @see AdminUrlExtension::getGetParams()
	 * @see AdminUrlExtension::getPostParams()
	 * @see MhTranslation::getFormattedLanguageList()
	 * @see MhTranslation::getLocale()
	 * @see MhTranslation::getLocaleData()
	 */
	public function getGlobals(): array {

		return [
			'assets_url'     => self::getAssetsUrl(),
			'plugin_url'     => self::getModulesUrl(),
			'dle_login_hash' => self::getUserHash(),
			'dle_config'     => self::getDleConfig(),
			'_server'        => self::getServerData(),
			'_get'           => self::getGetParams(),
			'_post'          => self::getPostParams(),
			'languages'      => MhTranslation::getFormattedLanguageList(),
			'selected_lang'  => MhTranslation::getLocale(),
			'lang_data'      => MhTranslation::getLocaleData(MhTranslation::getLocale()),

		];
	}

	/**
	 * Возвращает массив функций Twig, доступных в данном расширении.
	 *
	 * @return array Массив объектов TwigFunction, предоставляющих функции для использования в Twig-шаблонах.
	 * @see \AdminUrlExtension::parseUrl Метод, который используется в качестве привязанной функции Twig.
	 */
	public function getFunctions(): array {
		return [
			new TwigFunction('parse_url', [$this, 'parseUrl'])
		];
	}
}