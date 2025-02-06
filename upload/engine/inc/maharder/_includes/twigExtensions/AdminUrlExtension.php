<?php

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AdminUrlExtension extends AbstractExtension implements GlobalsInterface {

	/**
	 * Извлекает информацию о пользователе по его идентификатору.
	 *
	 * Метод обращается к глобальному объекту `$mh` и вызывает его метод `getUser` с переданным идентификатором
	 * пользователя. Если пользователь с указанным идентификатором не найден, возвращается пустой массив.
	 *
	 * @param int     $user_id Идентификатор пользователя, информацию о котором необходимо получить.
	 *
	 * @return array Массив данных пользователя или пустой массив, если пользователь не найден.
	 * @throws JsonException Если возникает ошибка при обработке JSON данных (если таковая может появиться внутри
	 *                       `getUser`).
	 * @global object $mh      Глобальный объект, предоставляющий метод `getUser` для работы с данными пользователей.
	 * @see DataLoader::load_data() Для получения данных из базы.
	 * @see DataLoader::getDb() Для инициализации базы данных.
	 */
	protected static function getUserInfo(int $user_id): array {
		global $mh;
		$user = $mh->getUser($user_id);

		return $user ?: [];
	}

	/**
	 * Возвращает информацию о текущем пользователе, если он авторизован,
	 * в противном случае возвращает пустой массив.
	 *
	 * @return array Информация о текущем пользователе или пустой массив, если пользователь не авторизован.
	 * @throws JsonException Может выбросить исключение при работе с JSON.
	 * @see AdminUrlExtension::getUserInfo()
	 *
	 * @global array $member_id Массив с информацией об идентификаторе текущего пользователя.
	 * @global bool  $is_logged Флаг, указывающий, авторизован ли текущий пользователь.
	 */
	protected static function getCurrentUser(): array {
		global $member_id, $is_logged;
		if ($is_logged) {
			return self::getUserInfo((int)$member_id['user_id']);
		}
		return [];
	}

	/**
	 * Возвращает массив данных о сервере.
	 *
	 * @return array Массив данных о сервере.
	 * @global array $_SERVER Глобальная переменная, содержащая данные о сервере и окружении.
	 *
	 */
	protected static function getServerData(): array {
		return $_SERVER;
	}

	/**
	 * Получает хэш текущего пользователя из глобальной переменной.
	 *
	 * @return string Хэш аутентификации пользователя.
	 * @global string $dle_login_hash Хэш аутентификации пользователя.
	 *
	 */
	protected static function getUserHash(): string {
		global $dle_login_hash;

		return $dle_login_hash;
	}

	/**
	 * Возвращает глобальную конфигурацию DLE.
	 *
	 * @return array Возвращает массив конфигурации.
	 * @global array $config Глобальный массив конфигурации.
	 */
	protected static function getDleConfig(): array {
		global $config;

		return $config;
	}

	/**
	 * Получает и возвращает параметры запроса, переданные через метод GET.
	 *
	 * Используется функция filter_input_array для фильтрации входящих данных.
	 *
	 * @return array|null Массив параметров GET-запроса или null, если параметры отсутствуют.
	 * @global int INPUT_GET Константа, определяющая использование массива GET.
	 * @see filter_input_array()
	 */
	protected static function getGetParams(): ?array {
		return filter_input_array(INPUT_GET);
	}

	/**
	 * Получает параметры из массива POST в виде ассоциативного массива.
	 *
	 * Использует функцию filter_input_array() для получения данных из
	 * глобального массива $_POST с учетом фильтрации.
	 *
	 * @return array|null Ассоциативный массив параметров POST или null, если массив пустой либо не существует.
	 * @see filter_input_array()
	 * @global array $_POST Глобальный массив данных POST-запроса.
	 */
	protected static function getPostParams(): ?array {
		return filter_input_array(INPUT_POST);
	}

	/**
	 * Возвращает значение ключа 'PHP_SELF' из массива данных сервера.
	 *
	 * Метод обращается к статическому методу {@see getServerData},
	 * который возвращает массив данных сервера ($_SERVER),
	 * и извлекает из него информацию о текущем скрипте.
	 *
	 * @return string|null Строка, содержащая путь к исполняемому скрипту (PHP_SELF),
	 * если такой ключ существует, иначе null.
	 */
	protected static function getThisSelf() {
		return self::getServerData()['PHP_SELF'];
	}

	/**
	 * Возвращает название текущего хоста из серверных данных.
	 *
	 * Использует данные, полученные от сервера для извлечения значения ключа 'HTTP_HOST'.
	 *
	 * @return string|null Название текущего хоста или null, если ключ 'HTTP_HOST' отсутствует.
	 * @see self::getServerData() Метод для получения данных сервера.
	 *
	 * @global array $_SERVER Глобальный массив данных сервера.
	 */
	protected static function getThisHost() {
		return self::getServerData()['HTTP_HOST'];
	}

	/**
	 * Получает корневой каталог сервера.
	 *
	 * Этот метод использует данные сервера для определения пути к корневому каталогу,
	 * основываясь на значении 'DOCUMENT_ROOT'.
	 *
	 * @return string Путь к корневому каталогу сервера.
	 * @see self::getServerData() Для получения данных сервера.
	 * @global array $_SERVER Глобальный массив с данными сервера.
	 */
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
	 * серверные данные, параметры запросов `GET` и `POST`, а также информацию о локали и языках.
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
	 * - `current_user` (`array`): Данные о текущем пользователе (см. {@see AdminUrlExtension::getCurrentUser()}).
	 *
	 * @throws JsonException|Throwable
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
	 * @see AdminUrlExtension::getCurrentUser()
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
			'current_user'   => self::getCurrentUser()

		];
	}

	/**
	 * Возвращает массив функций Twig, доступных в данном расширении.
	 *
	 * @return array Массив объектов TwigFunction, предоставляющих функции для использования в Twig-шаблонах.
	 * Каждая функция представляет определённую логику, доступную для вызова в Twig-шаблонах:
	 * - `parse_url`: вызывает метод {@see \AdminUrlExtension::parseUrl}.
	 * - `userInfo`: вызывает метод {@see \AdminUrlExtension::getUserInfo}.
	 */
	public function getFunctions(): array {
		return [
			new TwigFunction('parse_url', [$this, 'parseUrl']),
			new TwigFunction('userInfo', [$this, 'getUserInfo'])
		];
	}
}