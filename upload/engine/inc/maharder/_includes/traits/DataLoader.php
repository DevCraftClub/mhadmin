<?php

/**
 * Трейт для загрузки данных с использованием кеша или базы данных.
 * Обеспечивает базовые операции с данными, такие как загрузка, сохранение или обновление
 * с интеграцией в систему кеширования и базу данных.
 * Использование:
 * Подключите этот трейт к классу для получения базовой функциональности
 * работы с данными.
 */
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
	public function getCacheFolder(): string {
		return $this->cache_folder;
	}

	/**
	 * @param string $cache_folder
	 */
	public function setCacheFolder(string $cache_folder): void {
		$this->cache_folder = $cache_folder;
	}

	/**
	 * Метод загружает данные из базы данных с использованием механизма кеширования.
	 * Если данные уже закешированы, то они возвращаются из кеша, иначе выполняется запрос
	 * к базе данных, формируется кеш и результат сохраняется на диск.
	 *
	 * @version 173.3.0
	 *
	 * @param string $name         Название кеша, используемого для хранения результата выборки.
	 * @param mixed  ...$_vars     Параметры запроса и настройки кеширования:
	 *                             - `table`   (string|null): Название таблицы. Если не указано, используется $name.
	 *                             - `sql`     (string|null): Полный SQL-запрос. Если указан, игнорируются остальные
	 *                             параметры.
	 *                             - `where`   (array): Условия выборки в формате ['поле' => 'значение'], например,
	 *                             ['news_id' => '1'].
	 *                             - `selects` (array): Поля, выводимые в результате выборки. Например, ['Ячейка 1',
	 *                             'Ячейка 2']. Если массив пуст, возвращаются все столбцы ('*').
	 *                             - `order`   (array): Условия сортировки в формате ['поле' => 'Порядок'], например,
	 *                             ['news_id' => 'ASC'].
	 *                             - `limit`   (string|int|null): Ограничение по количеству возвращаемых строк.
	 *                             Может быть указано как:
	 *                             - `n`   — максимальное количество строк;
	 *                             - `n,x` — с какого индекса начать и до какого ограничить выборку.
	 *
	 * @return array Результат выборки из базы данных, либо из кеша.
	 * @throws JsonException В случае JSON-ошибок при работе с кешем.
	 * @see     DataManager::getDb() Метод для получения экземпляра базы данных.
	 * @see     DataManager::nameArgs() Метод для обработки аргументов.
	 * @see     DataManager::getComparer() Метод для получения оператора сравнения.
	 * @see     DataManager::getConfig() Метод для получения конфигурации.
	 */
	public function load_data(string $name, mixed ...$_vars): array {
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
			while ($row = $db->get_row()) {
				$data[] = $row;
			}

			$db->close();

			$this->set_cache($name, $file_name, $data);
		}

		return $this->get_cache($name, $file_name);
	}

	/**
	 * Очищает кеш указанного типа.
	 * Метод позволяет очистить кеш для переданного типа или полностью, если тип "all".
	 * Использует внутренний метод CacheControl::clearCache для выполнения операции очистки.
	 *
	 * @version 173.3.0
	 *
	 * @param string $type Тип кеша для очистки. По умолчанию — "all" (очистка всех данных кеша).
	 *
	 * @return void
	 * @see     CacheControl::clearCache()
	 */
	public function clear_cache(string $type = 'all'): void {
		CacheControl::clearCache($type);
	}

	/**
	 * Получает кешированные данные для заданного типа и имени.
	 * Метод использует статический метод `CacheControl::getCache()`, чтобы получить данные из файловой системы.
	 *
	 * @global CacheControl Взаимодействие с кешем через файловую систему.
	 * @global LogGenerator Логирование ошибок при работе с кешем.
	 *
	 * @param string $type Тип данных, используемый для построения пути к файлу кеша.
	 * @param string $name Имя данных, используемое для построения пути к файлу кеша.
	 *
	 * @return array|false Возвращает массив декодированных данных, если файл кеша успешно найден и данные корректны,
	 *                     или `false`, если произошла ошибка (например, файл не найден или данные некорректны).
	 * @throws JsonException
	 * @see DataManager::toTranslit() Для преобразования строки в транслит.
	 * @see DataManager::normalizePath() Для нормализации пути к файлу.
	 * @see CacheControl::getCache() Для получения кешированных данных.
	 */
	public function get_cache(string $type, string $name): false|array {
		return CacheControl::getCache($type, $name);
	}

	/**
	 * Сохраняет данные в кеш.
	 * Метод записывает данные в кеш, используя указанный тип и имя.
	 * Для сохранения данных вызывается метод `CacheControl::setCache`, который
	 * обрабатывает директорию и имя файла, записывает данные в формате JSON
	 * и устанавливает необходимые права доступа к файлу.
	 *
	 * @param string $type Тип данных для кеширования (используется для генерации пути директории).
	 * @param string $name Имя, под которым данные будут сохранены (используется для имени файла).
	 * @param mixed  $data Данные, которые будут сохранены в кеш.
	 *
	 * @throws JsonException
	 * @see CacheControl::setCache() Используемый метод для сохранения данных в файловой системе.
	 */
	private function set_cache(string $type, string $name, mixed $data): void {
		CacheControl::setCache($type, $name, $data);
	}

	/**
	 * Возвращает префикс для использования в SQL-запросах.
	 * Метод проверяет, установлен ли префикс. Если префикс не задан, он вызывает метод `setPrefix()`,
	 * чтобы установить его значение. Затем возвращает установившийся префикс.
	 *
	 * @return string Префикс, используемый для построения SQL-запросов.
	 * @see DataLoader::$prefix
	 * @see DataLoader::setPrefix()
	 */
	public function getPrefix(): string {
		if ($this->prefix === null) {
			$this->setPrefix();
		}

		return $this->prefix;
	}

	/**
	 * Устанавливает префикс для использования в загрузке данных.
	 * Если переданное имя соответствует значениям "users" или "usergroup",
	 * то префикс устанавливается в значение константы `USERPREFIX`.
	 * В противном случае используется значение по умолчанию из константы `PREFIX`.
	 *
	 * @global string|null $prefix Глобальная переменная для хранения текущего префикса.
	 *
	 * @param string|null  $name   Имя для определения необходимого префикса.
	 *
	 * @return void
	 */
	public function setPrefix(string $name = null): void {
		$prefix = PREFIX;
		if (in_array($name, ['users', 'usergroup'])) {
			$prefix = USERPREFIX;
		}
		$this->prefix = $prefix;
	}

}