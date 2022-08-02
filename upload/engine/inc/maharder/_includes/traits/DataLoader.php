<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

trait DataLoader {

	/**
	 * @var db|null
	 */
	protected static $db = null;
	/**
	 * @var string|null
	 */
	private  $prefix = null;

	/**
	 * Функция подключения к базе данных
	 *
	 * @return void
	 */
	public static function connect(): void {
		if (self::$db === null) {
			if (!defined('DBHOST')) {
				include_once ENGINE_DIR . '/data/dbconfig.php';
				$db = new db();
			} else {
				global $db;
			}
			self::setDb($db);
		}
	}

	/**
	 * @return db
	 */
	public static function getDb(): db {
		if (self::$db === null) self::connect();
		return self::$db;
	}

	/**
	 * @param \db|null $db
	 */
	public static function setDb(?db $db): void {
		self::$db = $db;
	}

	/**
	 * Папка кеша
	 *
	 * @var string
	 */
	private $cache_folder = ENGINE_DIR . '/inc/maharder/_cache';

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
	 * Функция по созданию аббревиатуры для базы данных
	 *
	 * @link https://stackoverflow.com/questions/15830222/text-abbreviation-from-a-string-in-php
	 * @param $string
	 * @param $id
	 * @param $l
	 *
	 * @return string
	 */
	protected static function abbr($string, $id = null, $l = 2) {
		$results = '';                                 // empty string
		$vowels = array('a', 'e', 'i', 'o', 'u', 'y'); // vowels
		preg_match_all('/[A-Z][a-z]*/', ucfirst($string),
		               $m);                            // Match every word that begins with a capital letter, added ucfirst() in case there is no uppercase letter
		foreach ($m[0] as $substring) {
			$substring = str_replace($vowels, '', strtolower($substring));           // String to lower case and remove all vowels
			$results .= preg_replace('/([a-z]{' . $l . '})(.*)/', '$1', $substring); // Extract the first N letters.
		}
		$results .= '_' . str_pad($id, 4, 0, STR_PAD_LEFT); // Add the ID
		return $results;
	}

	/**
	 * Функция создания кеша запросов,
	 * чтобы сократить кол-во обращений к базе данных
	 *
	 * @param string $name                  //    Переменная для названия кеша
	 * @param array  ...$vars               //    table       Название таблицы, в противном случае будет браться
	 *                                      переменная $name
	 *                                      //    sql         Запрос полностью, если он заполнен, то будет испольняться
	 *                                      именно он, другие значения игнорируются
	 *                                      //    where       Массив выборки запроса, прописывается в название файла
	 *                                      кеша.
	 *                                      //                Заполняется так: 'поле' => 'значение', 'news_id' => '1'
	 *                                      //    selects     Массив вывод значений, если он пуст, то будут возвращены
	 *                                      все значения таблицы. Заполняется так: ['Ячейка 1', 'Ячейка 2', ...]
	 *                                      Прописывается в названии файла кеша
	 *                                      //    order       Массив сортировки вывода, прописывается в название файла
	 *                                      кеша Заполняется так: 'поле' => 'Порядок сортировки', 'news_id' => 'ASC'
	 *                                      //    limit       Ограничение вывода запросов, возможно указывать следующие
	 *                                      значения: n    ->    просто максимальное кол-во данных n,x  ->
	 *                                      ограничение вывода, n - с какого захода начать сбор данных, x - до какого
	 *                                      значения делать сбор данных
	 *
	 * @return array
	 * @throws JsonException
	 */
	public function load_data(string $name, array ...$_vars): array {
		$db = self::getDb();

		$vars = [
			'table'   => null,
			'sql'     => null,
			'where'   => [],
			'selects' => [],
			'order'   => [],
			'limit'   => null
		];
		$_vars = self::nameArgs($_vars);
		$vars = array_replace($vars, $_vars);

		$where = [];
		$order = [];
		$file_name = $name;
		$file_suffix = '';
		foreach ($vars['selects'] as $s) {
			$file_suffix .= "_s{$s}";
		}
		foreach ($vars['where'] as $id => $key) {
			if (is_array($key)) {
				foreach ($key as $k) {
					$file_suffix .= "_{$id}-{$k}";
					$where[] = $id . self::getComparer($k);
				}
			} else {
				$file_suffix .= "_{$id}-{$key}";
				$where[] = $id . self::getComparer($key);
			}
		}
		foreach ($vars['order'] as $n => $sort) {
			$file_suffix .= "_o{$n}-{$sort}";
			$order[] = "{$n} {$sort}";
		}

		if (!empty($vars['sql'])) $file_suffix = $vars['sql'];

		$file_name .= '_' . md5(md5($file_suffix));
		$file_path = $this->getCacheFolder() . "/{$name}/{$file_name}.php";

		if (file_exists($file_path)) {
			$file_created = filectime($file_path);
			$now = time();
			$mh_config = $this->getConfig('maharder');
			if (($now - $file_created) >= ($mh_config['cache_timer'] * 60)) @unlink($file_path);
		}

		if (!file_exists($file_path)) {
			$data = [];
			$prefix = $this->getPrefix();

			$order = implode(', ', $order);
			if (!empty($order)) $order = "ORDER BY {$order}";

			$limit = '';
			if (!empty($vars['limit'])) $limit = "LIMIT {$vars['limit']}";

			if (count($vars['where']) > 0 && $vars['sql'] === null) {
				$selects = implode(",", $vars['selects']);
				if (empty($selects)) $selects = '*';
				$where = implode(' AND ', $where);
				if (!empty($where)) $where = "WHERE {$where}";

				if ($vars['table'] !== null) {
					$sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$where} {$order} {$limit}";
				} else $sql = "SELECT {$selects} FROM {$prefix}_{$name} {$where} {$order} {$limit}";
			} else {
				if ($vars['table'] === null && $vars['sql'] === null) $vars['table'] = $name;

				if ($vars['table'] !== null) {
					$selects = implode(",", $vars['selects']);
					if (empty($selects)) $selects = '*';
					$sql = "SELECT {$selects} FROM {$prefix}_{$vars['table']} {$order} {$limit}";
				}
				if ($vars['sql'] !== null) $sql = $vars['sql'];
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
	 * Возвращает указанный путь в виде массива со всеми папками и файлами в нём
	 *
	 * @param $dir       //  Путь, который нужно просканировать
	 * @param ...$except //  Файл или файлы в массиве, которые нужно исключить из массива
	 *
	 * @return array
	 */
	protected static function dirToArray($dir, ...$except): array {

		$result = [];

		$xcpt = [
			'.',
			'..',
			'.htaccess'
		];
		if ($except) {
			$xcpt = array_merge_recursive($xcpt, self::nameArgs($except));
		}

		foreach (scandir($dir, SCANDIR_SORT_NONE) as $key => $value) {
			if (!in_array($value, $xcpt, true)) {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Очищаем кеш
	 *
	 * @param string $type
	 */
	public function clear_cache($type = 'all'): void {

		$dirname = $this->cache_folder;
		if ($type !== 'all') {
			if (is_array($type)) {
				foreach ($type as $key) $this->clear_cache($key);
			} else {
				$type = totranslit($type, true, false);
				$dirname .= '/' . $type;
				foreach (self::dirToArray($dirname) as $i => $name) {
					try {
						if (is_array($name)) {
							@rmdir($dirname . DIRECTORY_SEPARATOR . $i);
						} else @unlink($dirname . DIRECTORY_SEPARATOR . $name);
					} catch (Exception $e) {
						LogGenerator::generate_log('maharder', 'clear_cache', $e->getMessage());
					}
				}
			}
		}

	}

	/**
	 * Получаем кеш
	 *
	 * @param $type
	 * @param $name
	 *
	 * @return array|false|int|mixed
	 * @throws JsonException
	 */
	public function get_cache($type, $name) {
		$file = totranslit($name, true, false);
		$type = totranslit($type, true, false);

		$data = @file_get_contents($this->cache_folder . '/' . $type . DIRECTORY_SEPARATOR . $file . '.php');

		if ($data !== false) {

			$data = json_decode($data, true);
			if (is_array($data) || is_int($data)) return $data;

		}

		return false;

	}

	/**
	 * Сохраняем в кеш
	 *
	 * @param $type
	 * @param $name
	 * @param $data
	 *
	 * @throws \JsonException
	 */
	private function set_cache($type, $name, $data): void {
		$file = totranslit($name, true, false);
		$type = totranslit($type, true, false);

		if (!mkdir($concurrentDirectory = $this->cache_folder . '/' . $type, 0755, true) && !is_dir($concurrentDirectory)) {
			LogGenerator::generate_log('maharder', 'set_cache', sprintf('Directory "%s" was not created', $concurrentDirectory));
		}

		if (is_array($data) or is_int($data)) {

			file_put_contents($concurrentDirectory . DIRECTORY_SEPARATOR . $file . '.php',
			                  json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), LOCK_EX);
			@chmod($concurrentDirectory . DIRECTORY_SEPARATOR . $file . '.php', 0666);

		}
	}

	/**
	 * Создаёт папку по указанному пути
	 *
	 * @param string ...$_path //  Может содержать несколько путей, коротые будут объеденены в один
	 *
	 * @return bool
	 * @throws \Monolog\Handler\MissingExtensionException
	 */
	protected static function createDir(string ...$_path): bool {
		$path = str_replace(['\/\/', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, '//'], DIRECTORY_SEPARATOR, implode('/', $_path));

		if (!mkdir($path, 0777, true) && !is_dir($path)) {
			LogGenerator::generate_log('DataLoader', 'createDir', "Путь \"{$path}\" не был создан");
			return false;
		}

		return true;
	}

	/**
	 * Преобразует ... аргументы в понятный масссив
	 *
	 * @param array|null $args
	 *
	 * @return array
	 */
	public static function nameArgs(?array $args): array {
		$returnArr = [];

		foreach ($args as $id => $arg) {
			if (is_numeric($id) && is_array($arg)) {
				$returnArr = array_merge($returnArr, self::nameArgs($arg));
			} elseif (is_numeric($id) && !is_array($arg)) {
				$returnArr[$arg] = $arg;
			} elseif (is_array($id)) {
				$returnArr = array_merge($returnArr, self::nameArgs($id));
			} else {
				$returnArr[$id] = $arg;
			}
		}

		return array_filter($returnArr, static function($value) { return !is_null($value) && $value !== ''; });
	}

	/**
	 * Проверяет файл на верный тип и конвертирует его
	 *
	 * @param $value //  Значение
	 * @param $type  //  Проверяемый тип файла
	 *
	 * @return bool|float|int|string
	 */
	protected static function defType($value, $type) {

		if (in_array($type, [
			'double',
			'float'
		])) {
			$output = (float)$value;
		} elseif (in_array($type, [
			'boolean',
			'bool'
		])) {
			$output = (bool)$value;
		} elseif (in_array($type, [
			'integer',
			'int',
			'tinyint'
		])) {
			$output = (int)$value;
		} else $output = "'{$value}'";

		return $output;
	}

	/**
	 * Обрабатывает значение на сверяющие знаки и возвращает в нужном параметре обратно
	 *
	 * @param $value //  Значение со знаками сравнения в начале
	 *
	 * @return string
	 */
	protected static function getComparer($value): string {

		$firstSign = [
			'!',
			'<',
			'>',
			'%'
		];
		$secondSign = ['='];
		$type = gettype($value);
		$outSign = '=';
		$checkSign = null;

		if (!in_array($type, [
				'integer',
				'double',
				'boolean'
			]) && in_array($value[0], $firstSign, true)) {
			$checkSign = $value[0];
			if ($value[1] === $secondSign) {
				$checkSign .= $value[1];
				$value = substr($value, 2);
			} else {
				$value = substr($value, 1);
			}
		}

		if ($checkSign === '!') {
			$outSign = '<>';
		} elseif (in_array($checkSign, [
			'<',
			'>',
			'<=',
			'>='
		])) {
			$outSign = $checkSign;
		} elseif ($checkSign === '%') {
			$outSign = 'LIKE';
			$value = '%' . $value . '%';
		}

		$value = self::defType($value, $type);

		return " {$outSign} {$value}";
	}

	/**
	 * Получаем настройки модуля, если такие имеются.
	 * Возвращает массив данных.
	 *
	 * @param string $codename Название модуля, а так-же название конфигурации; без обозначений
	 * @param string $path     Путь до конфигурации файла
	 * @param string $confName Если сохранилась конфигурация в папке /engine/data/, то указать название массива
	 *                         без знака $
	 *
	 * @return array
	 */
	public static function getConfig($codename, $path = ENGINE_DIR . '/inc/maharder/_config', $confName = ''): array {
		$settings = [];

		if (is_file($path . DIRECTORY_SEPARATOR . $codename . '.json')) {
			$settings = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . $codename . '.json'), true);
			foreach ($settings as $name => $value) {
				if (!is_array($value)) $settings[$name] = htmlspecialchars_decode($value);
			}
		} else {
			if (!empty($confName)) {
				$oldConfig = ENGINE_DIR . '/data/' . $codename . '.php';
				if (is_file($oldConfig)) {
					$oldFile = file_get_contents(DLEPlugins::Check($oldConfig));
					$oldFile = str_replace("\${$confName} = ", 'return ', $oldFile);
					file_put_contents($oldConfig, $oldFile, LOCK_EX);
					$oldSettings = include DLEPlugins::Check($oldConfig);
					if (is_array($oldSettings)) $oldSettings = json_encode($oldSettings, JSON_UNESCAPED_UNICODE);
					file_put_contents($path . DIRECTORY_SEPARATOR . $codename . '.json', $oldSettings);
					@unlink($oldConfig);

					foreach (json_decode($oldSettings, true) as $set => $val) $settings[$set] = $val;
				}
			}
		}

		return $settings;
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string {
		if ($this->prefix === null) $this->setPrefix();
		return $this->prefix;
	}

	/**
	 */
	public function setPrefix(?string $name = null): void {
		$prefix = PREFIX;
		if (in_array($name, [
			'users',
			'usergroup'
		])) {
			$prefix = USERPREFIX;
		}
		$this->prefix = $prefix;
	}
}