<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

trait DataLoader {
	/**
	 * @var string|null
	 */
	private ?string $prefix = null;

	/**
	 * Папка кеша
	 *
	 * @var string
	 */
	private string $cache_folder = ENGINE_DIR . '/inc/maharder/_cache';

	/**
	 * @return string
	 */
	public function getCacheFolder() : string {
		return $this->cache_folder;
	}

	/**
	 * @param    string    $cache_folder
	 */
	public function setCacheFolder(string $cache_folder) : void {
		$this->cache_folder = $cache_folder;
	}

	/**
	 * Функция создания кеша запросов,
	 * чтобы сократить кол-во обращений к базе данных
	 *
	 * @version 2.0.9
	 *
	 * @param    mixed     ...$_vars    - table - Название таблицы, в противном случае будет браться переменная $name
	 *                                  - sql - Запрос полностью, если он заполнен, то будет исполняться именно он,
	 *                                  другие значения игнорируются
	 *                                  - where - Массив выборки запроса, прописывается в название файла кеша.
	 *                                  Заполняется так: 'поле' => 'значение', 'news_id' => '1'
	 *                                  - selects - Массив вывод значений, если он пуст, то будут возвращены все
	 *                                  значения таблицы. Заполняется так: ['Ячейка 1', 'Ячейка 2', ...]. Прописывается
	 *                                  в названии файла кеша
	 *                                  - order - Массив сортировки вывода, прописывается в название файла кеша.
	 *                                  Заполняется так: 'поле' => 'Порядок сортировки', 'news_id' => 'ASC'
	 *                                  - limit - Ограничение вывода запросов, возможно указывать следующие значения: n
	 *                                  -> просто максимальное кол-во данных n,x  ->  ограничение вывода, n - с какого
	 *                                  захода начать сбор данных, x - до какого значения делать сбор данных
	 *
	 * @param    string    $name        Переменная для названия кеша
	 *
	 * @throws JsonException
	 * @return array
	 */
	public function load_data(string $name, mixed ...$_vars) : array {
		$db = DataManager::getDb();

		$vars  = [
			'table'   => null,
			'sql'     => null,
			'where'   => [],
			'selects' => [],
			'order'   => [],
			'limit'   => null,
		];
		$_vars = DataManager::nameArgs($_vars);
		$vars  = array_replace($vars, $_vars);

		$where       = [];
		$order       = [];
		$file_name   = $name;
		$file_suffix = '';
		foreach ($vars['selects'] as $s) {
			$file_suffix .= "_s{$s}";
		}
		foreach ($vars['where'] as $id => $key) {
			if (is_array($key)) {
				foreach ($key as $k) {
					$file_suffix .= "_{$id}-{$k}";
					$where[]     = $id . DataManager::getComparer($k);
				}
			} else {
				$file_suffix .= "_{$id}-{$key}";
				$where[]     = $id . DataManager::getComparer($key);
			}
		}
		foreach ($vars['order'] as $n => $sort) {
			$file_suffix .= "_o{$n}-{$sort}";
			$order[]     = "{$n} {$sort}";
		}

		if (!empty($vars['sql'])) {
			$file_suffix = $vars['sql'];
		}

		if (!empty($vars['limit'])) {
			$file_suffix .= "_l{$vars['limit']}";
		}

		$file_name .= '_' . md5(md5($file_suffix));
		$file_path = $this->getCacheFolder() . "/{$name}/{$file_name}.php";

		if (file_exists($file_path)) {
			$file_created = filectime($file_path);
			$now          = time();
			$mh_config    = DataManager::getConfig('maharder');
			if (($now - $file_created) >= ($mh_config['cache_timer'] * 60)) {
				@unlink($file_path);
			}
		}

		if (!file_exists($file_path)) {
			$data   = [];
			$prefix = $this->getPrefix();

			$order = implode(', ', $order);
			if (!empty($order)) {
				$order = "ORDER BY {$order}";
			}

			$limit = '';
			if (!empty($vars['limit'])) {
				$limit = "LIMIT {$vars['limit']}";
			}

			if (count($vars['where']) > 0 && $vars['sql'] === null) {
				$selects = implode(",", $vars['selects']);
				if (empty($selects)) {
					$selects = '*';
				}
				$where = implode(' AND ', $where);
				if (!empty($where)) {
					$where = "WHERE {$where}";
				}

				if ($vars['table'] !== null) {
					$sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$where} {$order} {$limit}";
				} else {
					$sql = "SELECT {$selects} FROM {$prefix}_{$name} {$where} {$order} {$limit}";
				}
			} else {
				if ($vars['table'] === null && $vars['sql'] === null) {
					$vars['table'] = $name;
				}

				if ($vars['table'] !== null) {
					$selects = implode(",", $vars['selects']);
					if (empty($selects)) {
						$selects = '*';
					}
					$sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$order} {$limit}";
				}
				if ($vars['sql'] !== null) {
					$sql = $vars['sql'];
				}
			}

			$db->query($sql);
			while (
			$row = $db->get_row()) {
				$data[] = $row;
			}

			$db->close();

			$this->set_cache($name, $file_name, $data);
		}

		return $this->get_cache($name, $file_name);
	}

	/**
	 * Очищаем кеш
	 *
	 * @version 2.0.9
	 *
	 * @param    string    $type
	 */
	public function clear_cache(string $type = 'all') : void {
		CacheControl::clearCache($type);
	}

	/**
	 * Получаем кеш
	 *
	 * @param    string    $type
	 * @param    string    $name
	 *
	 * @return array|false
	 */
	public function get_cache(string $type, string $name) : false|array {
		return CacheControl::getCache($type, $name);
	}

	/**
	 * Сохраняем в кеш
	 *
	 * @param    string    $type
	 * @param    string    $name
	 * @param    mixed     $data
	 */
	private function set_cache(string $type, string $name, mixed $data) : void {
		CacheControl::setCache($type, $name, $data);
	}

	/**
	 * @return string
	 */
	public function getPrefix() : string {
		if ($this->prefix === null) {
			$this->setPrefix();
		}

		return $this->prefix;
	}

	/**
	 */
	public function setPrefix(string $name = null) : void {
		$prefix = PREFIX;
		if (in_array($name, [
			'users',
			'usergroup',
		])) {
			$prefix = USERPREFIX;
		}
		$this->prefix = $prefix;
	}

}