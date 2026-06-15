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
use DevCraft\Core\Cache\CacheControl;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Агрегирует справочные данные DLE: пользователи, группы, категории, xfields.
 *
 * Порт методов трейта DleData поверх {@see DataLoaderService}.
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
	 * @since 200.4.0
	 *
	 * @param   DataLoaderService  $loader      Сервис загрузки таблиц DLE.
	 * @param   int                $cacheTimer  TTL кеша xfields в секундах.
	 */
	public function __construct(
		private readonly DataLoaderService $loader,
		private readonly int               $cacheTimer = 3600,
	) {}

	/**
	 * Возвращает список пользователей DLE с основными полями.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int, array<string, mixed>> Строки users.
	 *
	 * @example
	 *     $users = Application::instance()->dleData()->users();
	 */
	public function users(): array {
		return $this->loader->loadData([
			'table'   => 'users',
			'selects' => ['user_id', 'name', 'email', 'user_group'],
			'order'   => ['name' => 'ASC'],
		]);
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
	 *     $user = $dleData->user(id: 1);
	 */
	public function user(?int $id = NULL, ?string $uname = NULL): array {
		if($id === NULL && ($uname === NULL || $uname === '')) {
			return [];
		}

		if($id !== NULL) {
			$rows = $this->loader->loadData([
				'table' => 'users',
				'where' => ['user_id' => $id],
			]);
		} else {
			$rows = $this->loader->loadData([
				'table' => 'users',
				'where' => ['name' => $uname],
			]);
		}

		return $rows[0] ?? [];
	}

	/**
	 * Возвращает карту id => group_name для групп пользователей.
	 *
	 * @since 173.3.0
	 *
	 * @return array<int|string, string> Ассоциативный список групп.
	 *
	 * @example
	 *     $groups = $dleData->groups();
	 */
	public function groups(): array {
		$rows = $this->loader->loadData([
			'table'   => 'usergroups',
			'selects' => ['id', 'group_name'],
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
	 *     $rows = $dleData->groupsFull();
	 */
	public function groupsFull(): array {
		return $this->loader->loadData([
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
	 *     $cats = $dleData->categories();
	 */
	public function categories(): array {
		$rows = $this->loader->loadData([
			'table'   => 'category',
			'selects' => ['id', 'name', 'parentid'],
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
	 *     $rows = $dleData->categoriesFull();
	 */
	public function categoriesFull(): array {
		return $this->loader->loadData([
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
	 *     $fields = $dleData->postXfields();
	 */
	public function postXfields(): array {
		return $this->loadXfieldsFromJson('post_xfields', 'xfields.json');
	}

	/**
	 * Загружает описание доп. полей пользователей из userxfields.json.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, array<string, mixed>> Поля по имени.
	 *
	 * @example
	 *     $fields = $dleData->userXfields();
	 */
	public function userXfields(): array {
		return $this->loadXfieldsFromJson('user_xfields', 'userxfields.json');
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
	 *     $xfields = $dleData->parseObjectXfields(42, 'post');
	 */
	public function parseObjectXfields(int $id, string $type = 'post'): array {
		if($type === 'user') {
			$rows = $this->loader->loadData([
				'table'   => 'users',
				'selects' => ['xfields'],
				'where'   => ['user_id' => $id],
			]);
		} else {
			$rows = $this->loader->loadData([
				'table'   => 'post',
				'selects' => ['xfields'],
				'where'   => ['id' => $id],
			]);
		}

		$raw = (string) ($rows[0]['xfields'] ?? '');

		return $this->parseXfieldsString($raw);
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
	private function loadXfieldsFromJson(string $cacheName, string $fileName): array {
		$cached = $this->readJsonCache($cacheName);

		if($cached !== NULL) {
			return $cached;
		}

		$fields = $this->readJsonFields($fileName);
		$this->writeJsonCache($cacheName, $fields);

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
	private function readJsonFields(string $fileName): array {
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
	private function parseXfieldsString(string $raw): array {
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
	 * Читает закешированные xfields, если TTL не истёк.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $cacheName  Ключ кеша.
	 *
	 * @return array<string, array<string, mixed>>|null Поля или null.
	 */
	private function readJsonCache(string $cacheName): ?array {
		$cached = CacheControl::getCache(self::CACHE_TYPE, $cacheName);

		if($cached === false || !is_array($cached)) {
			return NULL;
		}

		$storedAt = (int) ($cached['_stored_at'] ?? 0);
		$fields   = $cached['fields'] ?? NULL;

		if(!is_array($fields)) {
			return NULL;
		}

		if($storedAt > 0 && (time() - $storedAt) >= $this->cacheTimer) {
			return NULL;
		}

		return $fields;
	}

	/**
	 * Записывает xfields в кеш DevCraft.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                               $cacheName  Ключ кеша.
	 * @param   array<string, array<string, mixed>>  $fields     Поля xfields.
	 */
	private function writeJsonCache(string $cacheName, array $fields): void {
		CacheControl::setCache(self::CACHE_TYPE, $cacheName, [
			'_stored_at' => time(),
			'fields'     => $fields,
		]);
	}

}
