<?php

trait DleData {
	/**
	 * Возвращает массив с использованными доп. полями в новости
	 *
	 * @param        $id   // ID объекта
	 * @param string $type // Тип объекта, post или user
	 * @return array|false
	 * @throws \JsonException
	 */
	public function get_used_xfields($id, string $type = 'post') {
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
	 * Загружает все дополнительные поля для новостей и пользователей
	 *
	 * @param string $type
	 * @return array
	 */
	public function loadXfields(string $type = 'post'): array {
		if ('post' === $type) {
			$xf_file = file(ENGINE_DIR . '/data/xfields.txt');
		} elseif ('user' === $type) {
			$xf_file = file(ENGINE_DIR . '/data/xprofile.txt');
		}

		$xf_info = [];
		foreach ($xf_file as $line) {
			$info = explode('|', $line);
			$xf_info[$info[0]] = $info[1];
		}

		return $xf_info;
	}

	/**
	 * Получаем список пользователей
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function getUsers(): array {

		$users = $this->load_data("users", [
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
	 * Получаем простой список категорий на сайте
	 * в виде массива с данными ID и названием
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function getCats(): array {

		$cats = $this->load_data("category", [
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