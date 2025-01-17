<?php

use JetBrains\PhpStorm\ExpectedValues;

trait DleData {
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
	public function get_used_xfields(
		int $id,
		#[ExpectedValues(values: ['post', 'user'])]
		string $type = 'post'
	) : bool|array {
		if ('post' === $type) {
			$post = $this->load_data("post", [
				'selects' => ['xfields'],
				'where'   => ['id' => $id]
			]);
		} elseif ('user' === $type) {
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
	 * Метод считывает данные из файла конфигурации и возвращает ассоциативный массив, где ключами являются
	 * названия дополнительных полей, а значениями - их параметры.
	 *
	 * В зависимости от переданного параметра `$type`, данные могут быть загружены либо для новостей, либо
	 * для пользователей:
	 * - При значении `'post'`, данные загружаются из файла `xfields.txt`.
	 * - При значении `'user'`, данные загружаются из файла `xprofile.txt`.
	 *
	 * @param string $type Тип данных для загрузки, по умолчанию `'post'`. Возможные значения: `'post'` или `'user'`.
	 *
	 * @return array Ассоциативный массив дополнительных полей. Ключ - название поля, значение - параметр поля.
	 *
	 * @global string ENGINE_DIR Директория, откуда считываются файлы конфигурации дополнительных полей.
	 *
	 * @see file() Используется для чтения содержимого файла.
	 */
	public function loadXfields(
		#[ExpectedValues(values: ['post', 'user'])]
		string $type = 'post'
	) : array {
		if ('post' === $type) {
			$xf_file = file(ENGINE_DIR . '/data/xfields.txt');
		} elseif ('user' === $type) {
			$xf_file = file(ENGINE_DIR . '/data/xprofile.txt');
		}

		$xf_info = [];
		foreach ($xf_file as $line) {
			$info              = explode('|', $line);
			$xf_info[$info[0]] = $info[1];
		}

		return $xf_info;
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
	public function getUsers() : array {

		$users   = $this->load_data("users", [
			'selects' => [
				'user_id',
				'name'
			],
			'order'   => ['name' => 'ASC']
		]);
		$user_ar = [];

		if ($users) {
			foreach ($users as $uid => $user) {
				$user_ar[$user['user_id']] = $user['name'];
			}
		}

		return $user_ar;
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
	 * @see load_data() Для загрузки данных из базы данных
	 */
	public function getCats() : array {

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