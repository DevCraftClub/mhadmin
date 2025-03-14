<?php

use JetBrains\PhpStorm\ExpectedValues;

trait DleData {

	protected array $postXfieldKeys
		= [
			'name',
			// 0 - системное имя поля
			'description',
			// 1 - описание поля
			'category',
			// 2 - Разрешённые категории
			'type',
			// 3 - тип поля
			'default',
			// 4 - Значения по умолчанию
			'is_required',
			// 5 - обязательное поле (1 или 0)
			'is_link',
			// 6 - Перекрёстные ссылки (1 или 0)
			'use_editor',
			// 7 - Подключать редактор при добавлении или редактировании публикаций для данного поля
			'is_safe',
			// 8 - Безопасный режим поля (отключение BB тегов и HTML)
			'image_max_size',
			// 9 - Максимальные размеры оригинального изображения
			'image_max_file_size',
			// 10 - Максимальный вес изображения
			'use_watermark',
			// 11 - Наложить водяные знаки
			'image_create_min',
			// 12 - Создать уменьшенную копию
			'image_min_size',
			// 13 - Размеры уменьшенной копии
			'file_ext',
			// 14 - Расширения файлов, допустимых к загрузке
			'file_max_file_size',
			// 15 - Максимальный размер файла допустимый к загрузке на сервер (в килобайтах)
			'gallery_max_images',
			// 16 - Максимальное кол-во изображений
			'yesno_enabled',
			// 17 - Значение по умолчанию для поля да/нет
			'hint',
			// 18 - Подсказка
			'groups_allow_add',
			// 19 - Разрешить добавление для групп
			'groups_allow_view',
			// 20 - Разрешить просмотр для групп
			'separator',
			// 21 - Разделитель
			'image_min_file_size',
			// 22 - Минимальные размеры изображения для загрузки
			'datetime_type',
			// 23 - Формат заполнения
			'datetime_format',
			// 24 - Формат вывода даты
			'datetime_localize',
			// 25 - Локализовывать дату при выводе на сайте
			'datetime_decline',
			// 26 - Склонять дату при выводе на сайте
			'is_public',
			// 27 - Загружать файл как публичный
			'allow_template',
			// 28 - Разрешить вставку тега вывода данного поля в текст новостей
			'use_opengraph',
			// 29 - Использовать изображение в разметке Open Graph
			'lazy_load',
			// 30 - Отложенная загрузка изображений
			'video_max_files',
			// 31 - Количество загружаемых файлов
			'video_max_file_size',
			// 32 - Максимальный размер файла допустимый к загрузке на сервер (в килобайтах)
			'storage',
			// 33 - Хранилище загрузок
			'select_multiple',
			// 34 - Разрешить выбор нескольких значений
			'select_separator',
			// 35 - разделитель для поля "Список"
			'min_chars',
			// 36 - Минимальное количество символов в поле
			'max_chars',
			// 37 - Максимальное количество символов в поле
			'image_max_size_side',
			// 38 - По которой стороне определять максимальный размер оригинального изображения
			'image_min_size_side',
			// 39 - По которой стороне определять размер уменьшенной копии изображения
		];

	protected array $userXfieldKeys
		= [
			'name',
			// 0 - системное имя поля
			'description',
			// 1 - описание поля
			'registration_page',
			// 2 - отображать на странице регистрации
			'type',
			// 3 - тип поля
			'allow_change',
			// 4 - Поле может быть изменено пользователем
			'is_private',
			// 5 - Личное поле
			'default_value',
			// 6 - Значение по умолчанию
			'is_save',
			// 7 - Безопасный режим поля (отключение BB тегов и HTML)
		];

	/**
	 * Возвращает массив с дополнительными полями, использованными в объекте, либо `false`, если данные отсутствуют.
	 *
	 * Метод извлекает данные из базы данных для указанного объекта (поста или пользователя)
	 * и преобразует строку с дополнительными полями в массив с ключами и их значениями.
	 *
	 * @param int    $id   Идентификатор объекта, для которого нужно получить данные.
	 * @param string $type Тип объекта, например, "post" для постов или "user" для пользователей.
	 *                     По умолчанию "post".
	 *
	 * @return array|bool Возвращает массив дополнительных полей объекта в формате
	 *                    ключ => значение, либо `false`, если данные отсутствуют.
	 *
	 * @throws JsonException Исключение выбрасывается, если произошла ошибка при работе с JSON.
	 *
	 * @see load_data() Метод используется для загрузки данных из базы данных.
	 */
	public function get_used_xfields(int $id, #[ExpectedValues(values: ['post', 'user'])]
	string                               $type = 'post'): bool|array {
		if ('post' === $type) {
			$post = $this->load_data("post", [
				'selects' => ['xfields'],
				'where'   => ['id' => $id]
			]);
		} else if ('user' === $type) {
			$post = $this->load_data("users", [
				'selects' => ['xfields'],
				'where'   => ['user_id' => $id]
			]);
		}

		$xfout = false;
		if ($post) {
			$xfout = [];
			foreach (explode('||', $post['xfields']) as $key => $value) {
				$xfout[$key] = $value;
			}
		}

		return $xfout;
	}

	/**
	 * Загружает дополнительные поля для новостей или профилей пользователей.
	 *
	 * Метод извлекает данные из кэша, либо, при его отсутствии, читает данные из файла конфигурации.
	 * Возвращает ассоциативный массив, где ключами являются названия дополнительных полей, а значениями - их
	 * параметры.
	 * Данные загружаются в зависимости от значения параметра `$type`:
	 * - `'post'`: данные загружаются из файла `xfields.txt`.
	 * - `'user'`: данные загружаются из файла `xprofile.txt`.
	 *
	 * Ключи массива, формируемого из файла конфигурации, соответствуют предопределённым ключам, указанным
	 * в свойствах `userXfieldKeys` или `postXfieldKeys`.
	 *
	 * @param string $type Тип данных для загрузки. Возможные значения: `'post'` или `'user'`.
	 *                     По умолчанию - `'post'`.
	 *
	 * @return array|false Ассоциативный массив дополнительных полей, где ключ - название поля, значение - его
	 *                     параметры. Возвращает `false` в случае невозможности прочитать файл.
	 *
	 * @global string ENGINE_DIR Директория, содержащая файлы конфигурации дополнительных полей.
	 *
	 * @see CacheControl::getCache() Метод используется для получения данных из кэша.
	 * @see CacheControl::setCache() Метод используется для сохранения данных в кэше.
	 */
	public function loadXfields(#[ExpectedValues(values: ['post', 'user'])]
								string $type = 'post'): array|false {

		$xfields = CacheControl::getCache($type, 'xfields_full_info');

		if (!$xfields) {
			$filePath = match ($type) {
				'post'          => DataManager::normalizePath(ENGINE_DIR . '/data/xfields.txt'),
				'user', 'users' => DataManager::normalizePath(ENGINE_DIR . '/data/xprofile.txt'),
			};

			$xfKeys = match ($type) {
				'user', 'users' => $this->userXfieldKeys,
				'post'          => $this->postXfieldKeys
			};

			if (!is_readable($filePath)) {
				return false;
			}

			$fileHandle = fopen($filePath, 'rb');
			if ($fileHandle === false) {
				return false;
			}

			try {
				while (($line = fgets($fileHandle)) !== false) {
					$info   = explode('|', $line);
					$result = [];
					foreach ($xfKeys as $index => $key) {
						$result[$key] = $info[$index] ?? null; // Используем null, если значение отсутствует
					}

					$xfields[$result['name']] = $result;
				}
			} finally {
				fclose($fileHandle);
			}

			CacheControl::setCache($type, 'xfields_full_info', $xfields);
		}

		return $xfields;
	}

	/**
	 * Возвращает информацию о дополнительном поле (xfield) на основании его имени и типа.
	 *
	 * @param string $name  Имя дополнительного поля.
	 * @param string $type  Тип данных, для которых запрашивается информация (например, 'post' или 'user').
	 *                      По умолчанию используется 'post'.
	 *
	 * @return array|null Возвращает информацию о поле в виде ассоциативного массива, либо null,
	 *                    если поле не найдено.
	 *
	 * @see loadXfields()
	 * @see CacheControl::getCache()
	 */
	public function getXfieldInfo(string $name, string $type = 'post'): ?array {
		$xfields = $this->loadXfields($type);
		return $xfields[$name];
	}

	/**
	 * Возвращает список пользователей из базы данных.
	 *
	 * Метод загружает данные о пользователях, используя метод `load_data`,
	 * и возвращает массив, где ключами являются идентификаторы пользователей,
	 * а значениями — их имена, отсортированные в алфавитном порядке.
	 *
	 * @return array Ассоциативный массив пользователей, где ключи — идентификаторы пользователей (user_id), а значения
	 *               — их имена.
	 * @throws JsonException
	 * @see load_data() Для загрузки данных из источника.
	 */
	public function getUsers(): array {

		$users   = $this->load_data("users", [
			'selects' => [
				'user_id',
				'name',
				'user_group'
			],
			'order'   => ['name' => 'ASC']
		]);
		$user_ar = [];

		if ($users) {
			$groups = $this->getUserGroups();
			foreach ($users as $uid => $user) {
				$ugroupId                  = (int)$user['user_group'];
				$group                     = $groups[$ugroupId];
				$user_ar[$user['user_id']] = "#{$user['user_id']}: {$user['name']} ({$group})";
			}
		}

		return $user_ar;
	}

	/**
	 * Получает данные пользователя по ID или имени пользователя.
	 *
	 * Если ни идентификатор пользователя, ни имя пользователя не переданы, метод возвращает `false`.
	 * В противном случае выполняется поиск данных в таблице `users` с использованием указанного критерия.
	 *
	 * @param int|null    $id    Идентификатор пользователя. Если указан, используется для поиска.
	 * @param string|null $uname Имя пользователя. Если указан идентификатор не передан, используется имя.
	 *
	 * @return array|false Ассоциативный массив с данными о пользователе, если пользователь найден.
	 *                     Возвращает `false`, если пользователь не найден или если критерии поиска не заданы.
	 *
	 * @throws JsonException
	 * @see load_data() Используется для выполнения запроса к базе данных.
	 */
	public function getUser(?int $id = null, ?string $uname = null): array|false {
		if (!$id && !$uname) return false;

		$criteria = $id ? ['user_id' => $id] : ['name' => $uname];

		$user = $this->load_data("users", [
			'where' => $criteria
		]);

		return $user[0] ?? false;
	}

	/**
	 * Получает список групп пользователей в формате массива с идентификаторами и именами групп.
	 *
	 * Метод обращается к функции `load_data` для загрузки данных о группах пользователей
	 * из таблицы "usergroups" с выборкой полей `id` и `group_name`,
	 * затем преобразует результат в ассоциативный массив с ключами — идентификаторами групп,
	 * и значениями — именами групп, отсортированными по названию группы в алфавитном порядке (ASC).
	 *
	 * @return array Ассоциативный массив, где ключ — идентификатор группы, значение — имя группы.
	 * @throws JsonException
	 * @see load_data() Используется для выборки данных о группах.
	 */
	public function getUserGroups(): array {

		$groups    = $this->load_data("usergroups", [
			'selects' => [
				'id',
				'group_name'
			],
			'order'   => ['group_name' => 'ASC']
		]);
		$groups_ar = [];

		if (count($groups)) {
			foreach ($groups as $gid => $group) {
				$groups_ar[$group['id']] = "#{$group['id']}: {$group['group_name']}";
			}
		}

		return $groups_ar;
	}

	/**
	 * Получает список всех групп пользователей, отсортированных по имени в порядке возрастания.
	 *
	 * Использует метод {@see DataLoader::load_data()} для загрузки данных.
	 *
	 * @return array Массив групп пользователей.
	 * @throws JsonException
	 */
	public function getFullUserGroups(): array {
		// Загрузка данных с сортировкой по имени группы
		$groups = $this->load_data("usergroups", [
			'order' => ['group_name' => 'ASC']
		]);

		// Формирование ассоциативного массива с использованием array_column
		return array_column($groups, null, 'id');
	}

	/**
	 * Возвращает список категорий в виде ассоциативного массива, где ключом является ID категории, а значением — её
	 * название.
	 *
	 * Метод загружает данные из базы данных, используя метод `load_data`, и сортирует их по названию категории в
	 * алфавитном порядке. В результате возвращается массив с ID категорий в качестве ключей и названиями категорий в
	 * качестве значений.
	 *
	 * @return array Ассоциативный массив категорий, где ключ — ID категории, а значение — её название.
	 * @throws JsonException
	 * @see DataLoader::load_data() Для загрузки данных из базы данных
	 */
	public function getCats(): array {

		$cats       = $this->load_data("category", [
			'selects' => [
				'id',
				'name'
			],
			'order'   => ['name' => 'ASC']
		]);
		$categories = [];
		if ($cats) {
			foreach ($cats as $cid => $entry) {
				$categories[$entry['id']] = $entry['name'];
			}
		}

		return $categories;
	}
}