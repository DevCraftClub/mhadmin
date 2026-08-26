<?php
//===============================================================
// Файл: DleDataService.php                                     =
// Путь: devcraft/src/classes/Support/DleDataService.php        =
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

use JsonException;
use DevCraft\Builders\QueryBuilder;
use DevCraft\Core\Enums\SortDirection;
use DevCraft\Core\Cache\CacheControl;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Агрегирует справочные данные DLE: пользователи, группы, категории, xfields.
 *
 * Порт методов трейта DleData поверх {@see DataLoaderService}.
 *
 * Static-фасад: вызовы без инстанса (`DleDataService::users()`).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Support
 */
final class DleDataService {

	/**
	 * Тип кеша для JSON xfields.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private const CACHE_TYPE = 'dledata';

	/**
	 * Запрет инстанцирования (только static API).
	 *
	 * @since 200.4.0
	 */
	private function __construct() {}

	/**
	 * Возвращает список пользователей DLE с основными полями.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int, array<string, mixed>> Строки users.
	 *
	 * @example
	 *     $users = DleDataService::users();
	 */
	public static function users(): array {
		return DataLoaderService::loadData(
			QueryBuilder::create('users')
				->withColumns(['user_id', 'name', 'email', 'user_group'])
				->withOrder(['name' => SortDirection::Asc])
		);
	}

	/**
	 * Возвращает одного пользователя по id или имени.
	 *
	 * @since 173.3.0
	 *
	 * @param   int|null     $id     ID пользователя или null.
	 * @param   string|null  $uname  Имя пользователя или null.
	 *
	 * @return array<string, mixed>|array{} Первая строка или пустой массив.
	 *
	 * @example
	 *     $user = DleDataService::user(id: 1);
	 */
	public static function user(?int $id = NULL, ?string $uname = NULL): array {
		if($id === NULL && ($uname === NULL || $uname === '')) {
			return [];
		}

		$query = QueryBuilder::create('users')->withLimit(1);

		if($id !== NULL) {
			$query = $query->withConditionsItem('user_id', $id);
		} else {
			$query = $query->withConditionsItem('name', $uname);
		}

		return DataLoaderService::loadOne($query);
	}

	/**
	 * Возвращает карту id => group_name для групп пользователей.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int|string, string> Ассоциативный список групп.
	 *
	 * @example
	 *     $groups = DleDataService::groups();
	 */
	public static function groups(): array {
		$rows = DataLoaderService::loadData([
			'table'   => 'usergroups',
			'columns' => ['id', 'group_name'],
			'order'   => ['group_name' => 'ASC'],
		]);

		$groups = [];

		foreach($rows as $row) {
			$groups[(int) $row['id']] = (string) $row['group_name'];
		}

		return $groups;
	}

	/**
	 * Возвращает полные строки таблицы usergroups.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int, array<string, mixed>> Все колонки групп.
	 *
	 * @example
	 *     $rows = DleDataService::groupsFull();
	 */
	public static function groupsFull(): array {
		return DataLoaderService::loadData([
			'table' => 'usergroups',
			'order' => ['group_name' => 'ASC'],
		]);
	}

	/**
	 * Возвращает карту id => name для категорий DLE.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int|string, string> Ассоциативный список категорий.
	 *
	 * @example
	 *     $cats = DleDataService::categories();
	 */
	public static function categories(): array {
		$rows = DataLoaderService::loadData([
			'table'   => 'category',
			'columns' => ['id', 'name', 'parentid'],
			'order'   => ['name' => 'ASC'],
		]);

		$categories = [];

		foreach($rows as $row) {
			$categories[(int) $row['id']] = (string) $row['name'];
		}

		return $categories;
	}

	/**
	 * Возвращает полные строки таблицы category.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int, array<string, mixed>> Все колонки категорий.
	 *
	 * @example
	 *     $rows = DleDataService::categoriesFull();
	 */
	public static function categoriesFull(): array {
		return DataLoaderService::loadData([
			'table' => 'category',
			'order' => ['name' => 'ASC'],
		]);
	}

	/**
	 * Загружает описание доп. полей публикаций из xfields.json.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, array<string, mixed>> Поля по имени.
	 *
	 * @example
	 *     $fields = DleDataService::postXfields();
	 */
	public static function postXfields(): array {
		return self::loadXfieldsFromJson('post_xfields', 'xfields.json');
	}

	/**
	 * Загружает описание доп. полей пользователей из userxfields.json.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, array<string, mixed>> Поля по имени.
	 *
	 * @example
	 *     $fields = DleDataService::userXfields();
	 */
	public static function userXfields(): array {
		return self::loadXfieldsFromJson('user_xfields', 'userxfields.json');
	}

	/**
	 * Разбирает строку xfields записи post или user в ассоциативный массив.
	 *
	 * @since 173.3.0
	 *
	 * @param   int     $id    ID записи или пользователя.
	 * @param   string  $type  Тип объекта: post или user.
	 *
	 * @return array<string, string|null> Имя поля => значение.
	 *
	 * @example
	 *     $xfields = DleDataService::parseObjectXfields(42, 'post');
	 */
	public static function parseObjectXfields(int $id, string $type = 'post'): array {
		if($type === 'user') {
			$rows = DataLoaderService::loadData([
				'table'   => 'users',
				'columns' => ['xfields'],
				'conditions' => ['user_id' => $id],
			]);
		} else {
			$rows = DataLoaderService::loadData([
				'table'   => 'post',
				'columns' => ['xfields'],
				'conditions' => ['id' => $id],
			]);
		}

		$raw = (string) ($rows[0]['xfields'] ?? '');

		return self::parseXfieldsString($raw);
	}

	/**
	 * Загружает xfields из JSON с кешированием.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $cacheName  Ключ кеша.
	 * @param   string  $fileName   Имя файла в ENGINE_DIR/data/.
	 *
	 * @return array<string, array<string, mixed>> Поля xfields.
	 */
	private static function loadXfieldsFromJson(string $cacheName, string $fileName): array {
		$cached = self::readJsonCache($cacheName);

		if($cached !== NULL) {
			return $cached;
		}

		$fields = self::readJsonFields($fileName);
		self::writeJsonCache($cacheName, $fields);

		return $fields;
	}

	/**
	 * Читает и декодирует JSON-файл xfields из каталога data DLE.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $fileName  Имя файла (xfields.json или userxfields.json).
	 *
	 * @return array<string, array<string, mixed>> Секция fields или пустой массив.
	 */
	private static function readJsonFields(string $fileName): array {
		if(!defined('ENGINE_DIR')) {
			LogGenerator::for('DleDataService')->log(__('Константа ENGINE_DIR не определена.'));

			return [];
		}

		$path = DataManager::normalizePath(ENGINE_DIR . '/data/' . $fileName);

		if(!is_readable($path)) {
			LogGenerator::for('DleDataService')->log(__("Файл xfields недоступен для чтения: {path}", ['{path}' => $path]));

			return [];
		}

		$content = file_get_contents($path);

		if($content === false || $content === '') {
			LogGenerator::for('DleDataService')->log(__("Файл xfields пуст: {path}", ['{path}' => $path]));

			return [];
		}

		try {
			/** @var array<string, mixed> $decoded */
			$decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
			$fields  = $decoded['fields'] ?? [];

			return is_array($fields)? $fields : [];
		} catch(JsonException $exception) {
			LogGenerator::for('DleDataService')->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Парсит сериализованную строку xfields DLE формата «name|value||…».
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $raw  Сырыая строка xfields.
	 *
	 * @return array<string, string|null> Имя => значение.
	 */
	private static function parseXfieldsString(string $raw): array {
		if($raw === '') {
			return [];
		}

		$result = [];

		foreach(explode('||', $raw) as $part) {
			if($part === '') {
				continue;
			}

			[$name, $value] = array_pad(explode('|', $part, 2), 2, NULL);

			if($name === NULL || $name === '') {
				continue;
			}

			$result[$name] = $value;
		}

		return $result;
	}

	/**
	 * Читает закешированные xfields.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $cacheName  Ключ кеша.
	 *
	 * @return array<string, array<string, mixed>>|null Поля или null.
	 */
	private static function readJsonCache(string $cacheName): ?array {
		$cached = CacheControl::getCache(self::CACHE_TYPE, $cacheName);

		if($cached === false) {
			return NULL;
		}

		if(is_array($cached) && isset($cached['fields']) && is_array($cached['fields'])) {
			return $cached['fields'];
		}

		if(!is_array($cached)) {
			return NULL;
		}

		return $cached;
	}

	/**
	 * Записывает xfields в кеш DevCraft.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                               $cacheName  Ключ кеша.
	 * @param   array<string, array<string, mixed>>  $fields     Поля xfields.
	 */
	private static function writeJsonCache(string $cacheName, array $fields): void {
		CacheControl::setCache(self::CACHE_TYPE, $cacheName, $fields);
	}

}
