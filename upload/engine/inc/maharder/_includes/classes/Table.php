<?php

require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DataLoader.php');

/**
 * Класс для преобразования таблицы и её колонок
 *
 * @author Maxim Harder
 */
class Table {
	use LogGenerator;
	use DataLoader;

	/**
	 * @var string|null
	 */
	private ?string $model           = null;
	private ?string $name            = null;
	private ?string $id              = null;
	private ?string $migrations_path = null;
	/**
	 * @var array
	 */
	private array $columns = [], $col_keys = [
		'inside' => [], 'after' => [],
	];
	/**
	 * @var DateTime
	 */
	private ?string $now = null;

	/**
	 * Конструктор
	 *
	 * @param string         $id       //  Название ID колонки
	 * @param string         $name     //  Название таблицы
	 * @param string         $model    //  Название модели
	 * @param array          $vars     //  Параметры колонок
	 * @param array          $col_keys //  Параметры ключей таблицы
	 * @param string|null $now      //  Параметр текущей даты и времени
	 *
	 * @throws \JsonException
	 */
	public function __construct(string $id, string $name, string $model, array $vars, array $col_keys, string $now = null) {
		$this->setId($id);
		$this->setName($name);
		$this->setModel($model);
		$this->setColumns($vars);
		if($now !== 0) $this->setNow($now); else $this->setNow();
		$this->setPrefix($name);
		foreach($col_keys as $key) $this->setColKeys($this->setKey($key['name'], $key));
	}

	/**
	 * @return string|null
	 */
	public function getMigrationsPath()
	: ?string {
		if($this->migrations_path === null) $this->setMigrationsPath();
		return $this->migrations_path;
	}

	/**
	 * @param string|null $migrations_path
	 */
	public function setMigrationsPath(?string $migrations_path = null)
	: void {
		$path = $migrations_path ?? (ENGINE_DIR . "/inc/maharder/_migrations/{$this->getModel()}/{$this->getName()}");

		if(self::createDir($path)) $this->migrations_path = $path;
	}


	/**
	 *  Функция подготавливает и проверяет тип колонки
	 *
	 * @param $attributes
	 *
	 * @return mixed
	 */
	private function prepare_table_attributes($attributes) {
		$allowed_fields = [
			'name', 'type', 'comment', 'unique', 'primary', 'null'
		];
		$fields = [];

		$type = strtolower($attributes['type']);
		switch($type) {
			case 'string':
			case 'varchar':
			case 'char':
				$fields = [
					'limit', 'default',
				];
				if($type === 'string') $attributes['type'] = 'varchar';
				break;

			case 'integer':
			case 'int':
			case 'smallint':
			case 'tinyint':
			case 'mediumint':
			case 'bigint':
			case 'bool':
			case 'boolean':
				if($type === 'integer') $attributes['type'] = 'int';
				if(in_array($type, [
					'bool', 'boolean'
				])) {
					$attributes['type'] = 'tinyint';
					$attributes['limit'] = 1;
				}
				$fields = [
					'limit', 'default', 'unique', 'auto_inc',
				];
				break;

			case 'decimal':
			case 'numeric':
			case 'float':
			case 'real':
			case 'double precision':
			case 'double':
				$fields = [
					'limit', 'default',
				];
				break;

			case 'date':
			case 'time':
			case 'datetime':
			case 'timestamp':
			case 'year':
			case 'text':
			case 'longtext':
			case 'tinytext':
			case 'mediumtext':
				$fields = ['default',];
				break;

		}

		$allowed_fields = array_merge($allowed_fields, $fields);

		foreach($attributes as $name => $value) {
			if(!in_array($name, $allowed_fields) || ($value === false || $value === null)) unset($attributes[$name]);
		}

		return $attributes;
	}

	/**
	 * Устанавливает ключи для таблицы
	 *
	 * @param string $name          //  Название ключа
	 * @param array  ...$vars       //  Перечень важных дополнительных параметров:
	 *                              //      -   type - тип вставления ключей в код, поддерживаются: 'inside', 'modify',
	 *                              'change', 'delete', 'create', 'update', 'new', 'drop', 'alter'
	 *                              //      -   column - название колонки, к которой будет применяться модификация,
	 *                              может быть как массивом, так и прстым значением
	 *                              //      -   table - название таблицы, в которой будет применены ключи. По
	 *                              умолчанию: используется таблица модели
	 *                              //      -   target_table - нужно для ключа Foreign Key, указывает к которой будет
	 *                              применяться ключ
	 *                              //      -   target_column - нужно для ключа Foreign Key, указывает к которой
	 *                              колонке относится ключ
	 *
	 * @return string
	 * @throws JsonException
	 */
	private function setKey(string $name, array ...$vars)
	: string {

		$vars = self::nameArgs($vars);

		if(empty($name)) {
			$this->generate_log('Table', 'setKey', [
				'Параметр \$name пустой', $name
			]);
			return '';
		}

		if(empty($vars)) {
			$this->generate_log('Table', 'setKey', [
				'Массив \$vars пустой', $vars
			]);
			return '';
		}

		$type = $vars['type'];
		if(empty($type)) {
			$this->generate_log('Table', 'setKey', [
				'Параметр type в массиве \$vars либо пустой, либо отсутствует', $vars
			]);
			return '';
		}

		if(!in_array($type, [
			'inside', 'modify', 'change', 'delete', 'create', 'update', 'new', 'drop', 'alter'
		])) {
			$this->generate_log('Table', 'setKey', [
				'Параметр type в массиве \$vars либо не соответствует inside, delete, create или update', $vars
			]);
			return '';
		}

		$table_col = $vars['column'];
		if(empty($table_col)) {
			$this->generate_log('Table', 'setKey', [
				'Параметр column в массиве \$vars либо пустой, либо отсутствует', $vars
			]);
			return '';
		}

		$table_name = $vars['table'] ?: $this->name;
		if(empty($table_name)) {
			$this->generate_log('Table', 'setKey', [
				'Параметр table в массиве \$vars либо пустой, либо отсутствует', $vars
			]);
			return '';
		}

		if(is_array($table_col)) {
			$cols = [];
			foreach($table_col as $col) $cols[] = "Column_name = '{$col}'";
			$where = implode(' OR ', $cols);
		} else $where = "Column_name = '{$table_col}'";

		$keys = is_array($table_col) ? implode(',', $table_col) : $table_col;

		$indexes = $type === 'inside'
			? []
			: $this->load_data("{$table_name}_indexes", [
				'table' => $table_name, 'sql' => "SHOW INDEXES FROM {$table_name} WHERE {$where}",
				'where' => ['Column_name' => $table_col]
			]);

		$name = strtolower($name);
		$exists = false;
		$key_name = '';

		switch($name) {
			case 'primary':
				$key_name = 'primary';
				foreach($indexes as $i) {
					if(strtolower($i['Key_name']) === $key_name) {
						$exists = true;
						break;
					}
				}
				break;

			case 'foreign':
				if(empty($vars['target_table'])) {
					$this->generate_log('Table', 'setKey', [
						'Параметр target_table в массиве \$vars либо пустой, либо отсутствует', $vars
					]);
					return '';
				}
				$vars['target_table'] = $this->getPrefix() . $vars['target_table'];
				if(empty($vars['target_column'])) {
					$this->generate_log('Table', 'setKey', [
						'Параметр target_column в массиве \$vars либо пустой, либо отсутствует', $vars
					]);
					return '';
				}
				$key_name = "{$table_name}_{$keys}_{$vars['target_table']}_{$vars['target_column']}_fk";
				foreach($indexes as $i) {
					if(strtolower($i['Key_name']) === $key_name) {
						$exists = true;
						break;
					}
				}
				break;

			case 'unique':
				$key_name = "{$table_name}_{$keys}_uk";
				foreach($indexes as $i) {
					if(strtolower($i['Key_name']) === $key_name) {
						$exists = true;
						break;
					}
				}
				break;
		}

		return $this->keyAction((string)$type, $exists, $table_name, $name, $key_name, $keys, $vars);
	}

	/**
	 * Вспомогательная функция для подготовки создания ключей для таблицы
	 *
	 * @param string $type          //  Тип действия ключа: inside, create, edit, drop
	 * @param bool   $exists        //  Проверка на существующий ключ
	 * @param string $table         //  Название таблицы
	 * @param string $key_type      //  Тип ключа: primary, foreign, unique
	 * @param string $key_name      //  Название ключа
	 * @param string $keys          //  Колонки, которые будут учитываться при создания ключа
	 * @param array  ...$vars       //  Дополнительные параметры:
	 *                              //                      -   on_delete - Важный параметр для ключа Foreign Key,
	 *                              может содержать: 'RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION', 'SET DEFAULT'
	 *                              //                      -   on_update - Важный параметр для ключа Foreign Key,
	 *                              может содержать: 'RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION', 'SET DEFAULT'
	 *
	 * @return string
	 */
	private function keyAction(
		string $type, bool $exists, string $table, string $key_type, string $key_name, string $keys, array ...$vars
	)
	: string {
		$param_string = '';
		$opts = [
			'RESTRICT', 'CASCADE', 'SET NULL', 'NO ACTION', 'SET DEFAULT'
		];
		$ondelete = (isset($vars['on_delete']) && (in_array(strtoupper($vars['on_delete']), $opts)))
			? " ON DELETE {$vars['on_delete']}" : '';
		$onupdate = (isset($vars['on_update']) && (in_array(strtoupper($vars['on_update']), $opts)))
			? " ON UPDATE {$vars['on_update']}" : '';

		switch($type) {

			case 'inside':
				if($exists) return $param_string;

				if($key_type === 'primary') $param_string = "PRIMARY KEY ({$keys})";
				if($key_type
				   === 'foreign') $param_string = "CONSTRAINT {$key_name} FOREIGN KEY ({$keys}) REFERENCES {$vars['target_table']} ({$vars['target_column']}){$ondelete}{$onupdate}";
				if($key_type === 'unique') $param_string = "CONSTRAINT {$key_name} UNIQUE ({$keys})";
				break;

			case 'new':
			case 'create':
				if($exists) return $param_string;

				if($key_type === 'primary') $param_string = "ALTER TABLE {$table} ADD PRIMARY KEY ({$keys});";
				if($key_type
				   === 'foreign') $param_string = "ALTER TABLE {$table} ADD CONSTRAINT {$key_name} FOREIGN KEY ({$keys}) REFERENCES {$vars['target_table']} ({$vars['target_column']}){$ondelete}{$onupdate};";
				if($key_type
				   === 'unique') $param_string = "ALTER TABLE {$table} ADD CONSTRAINT {$key_name} UNIQUE ({$keys});";

				break;

			case 'delete':
			case 'drop':
				if(!$exists) return $param_string;

				if($key_type === 'primary') $param_string = "ALTER TABLE {$table} DROP PRIMARY KEY;";
				if($key_type === 'foreign') $param_string = "ALTER TABLE {$table} DROP FOREIGN KEY {$key_name};";
				if($key_type === 'unique') $param_string = "ALTER TABLE {$table} DROP INDEX {$key_name};";
				break;

			case 'edit':
			case 'modify':
			case 'change':
				if($exists) $param_string .= $this->keyAction(
					'delete', $exists, $table, $key_type, $key_name, $keys, ...$vars
				);
				$param_string .= $this->keyAction('create', $exists, $table, $key_type, $key_name, $keys, ...$vars);
				break;
		}

		return $param_string;
	}

	/**
	 * Готовим установленные данные для миграции
	 * Добавляет
	 *
	 * @return array[]
	 *
	 * TODO: Имплементировать комментарий колонки
	 */
	private function prepareTable()
	: array {
		global $member_id;

		$table_attrs = [
			'name'   => null, 'type' => null, 'limit' => 255, 'null' => false, 'default' => null, 'auto_inc' => false,
			'unique' => false, 'primary' => false, 'comment' => null,
		];

		$prepared_table = [
			[
				'name' => $this->id, 'type' => 'int', 'auto_inc' => true, 'primary' => true,
			]
		];
		foreach($this->getColumns() as $id => $col) {
			if(is_array($col)) {
				$col = $this->prepare_table_attributes(array_replace_recursive($table_attrs, $col));
				$prepared_table[] = $col;
			}
		}
		$prepared_table[] = [
			'name' => 'creator', 'type' => 'int', 'default' => $member_id['user_id']
		];
		$prepared_table[] = [
			'name' => 'editor', 'type' => 'int', 'default' => $member_id['user_id']
		];
		$prepared_table[] = [
			'name' => 'created_date', 'type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP'
		];
		$prepared_table[] = [
			'name' => 'updated_date', 'type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP'
		];

		return $prepared_table;
	}

	/**
	 * Проверка миграций
	 * Если миграций нет или были найдены изменения в модели, то создаёт миграцию
	 * Изменения можно просмотреть в файлах, которые создаются при миграции.
	 *
	 * @param $path // Путь до последнего файла миграции
	 *
	 * @throws JsonException
	 */
	public function checkMigrations(?string $path = null)
	: void {
		$dir = ($path !== null) ? "{$path}/{$this->model}" : $this->getMigrationsPath();
		if(!mkdir($dir, 0777, true) && !is_dir($dir)) {
			$this->generate_log('ModelClass', 'checkMigrations', "Папка '{$dir}' не была создана!");
		}
		$migrations = self::dirToArray($dir, '_init.json');
		$info_file = "{$dir}/_init.json";
		if(!is_file($info_file)) {
			$file = "{$dir}/000_init.sql";
			$mig_data = [
				'last_updated' => $this->now, 'last_migration' => '', 'file' => ''
			];
			file_put_contents($info_file, json_encode($mig_data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
		} else {
			$mig_data = json_decode(file_get_contents($info_file), true, 512, JSON_THROW_ON_ERROR);
			$file = $dir . '/' . end($migrations);
		}
		if($mig_data['file'] !== $file) {

			$mig_ch = $this->generateMigration($file);
			if($mig_ch['changes']) {
				$mig_data['last_migration'] = $this->getNow();
				$mig_data['file'] = $mig_ch['file'];
				file_put_contents($info_file, json_encode($mig_data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
			}
		}

	}

	/**
	 * Создаём файл миграции
	 *
	 * @param $file
	 *
	 * @return array
	 * @throws JsonException
	 */
	private function generateMigration($file)
	: array {
		$db = self::getDb();

		$table = $this->prepareTable();
		preg_match('/^.+(\d{3,})_.+\.sql$/', $file, $output_array);
		$last_mig_id = (int)$output_array[1];
		$next_mig_id = isset($output_array[1]) ? $last_mig_id+1 : $last_mig_id;
		$new_mig_id = str_pad($next_mig_id, 3, '0', STR_PAD_LEFT);

		$table_name = "{$this->getPrefix()}_{$this->getName()}";
		$sql = [];
		$_file = [$new_mig_id];

		$table_check = $db->super_query("SHOW TABLES LIKE '{$table_name}'");
		if(count($table_check) === 0 || $last_mig_id == 0) {
			$_file[] = 'init';
			$sql_temp = "create table {$table_name} (";
			$sql_arr = [];
			foreach($table as $t) {
				$sql_arr[] = $this->generate_column_sql($t);
			}
			$sql_arr = array_unique(array_merge($sql_arr, $this->getColKeys('inside')));
			$sql_temp .= implode(',', $sql_arr);
			$sql_temp .= ") ENGINE=InnoDB;";
			$sql_temp .= implode('', $this->getColKeys('after'));
			$sql[] = $sql_temp;

		} else {
			foreach($table as $t) {
				$col_check = $db->query("SHOW COLUMNS FROM `{$table_name}` LIKE '{$t['name']}';");
				if(!(bool)$col_check->num_rows()) {
					$_file[] = "add_column_{$t['name']}";
					$sql[] = "ALTER TABLE {$table_name} ADD {$t['name']}" . $this->generate_column_sql($t) . ';';
				} else {
					$_file[] = "modify_column_{$t['name']}";
					$sql[] = "ALTER TABLE {$table_name} MODIFY {$t['name']}" . $this->generate_column_sql($t) . ';';
				}
			}
		}

		$file_name = substr(implode('_', $_file), 0, 100);
		$dir = is_file($file) ? dirname($file) : $file;
		$file_path = str_replace('//', '/', "{$dir}/{$file_name}.sql");
		$temp_file_path = str_replace('//', '/', "{$dir}/_temp.sql");
		$sql_data = implode('\n\r', $sql);
		file_put_contents($temp_file_path, $sql_data);
		$changes = false;
		if(hash_file('md5', $temp_file_path) !== hash_file('md5', $file)) {
			file_put_contents($file_path, $sql_data);

			if(count($table_check) === 0 || $last_mig_id === 0) {
				foreach($sql as $q) $db->query($q);
			}

			$changes = true;

		}
		@unlink($temp_file_path);

		return [
			'changes' => $changes, 'file' => $file_path
		];
	}

	/**
	 * Генерирует строку колонки по параметрам
	 *
	 * @param array $columns
	 *
	 * @return string
	 * @throws JsonException
	 */
	private function generate_column_sql(array $columns)
	: string {
		$sql = "{$columns['name']} {$columns['type']}";
		if(isset($columns['limit'])) $sql .= "({$columns['limit']})";
		if(!empty($columns['auto_inc'])) $sql .= " auto_increment";
		if(!empty($columns['default'])) $sql .= " default {$columns['default']}";
		if(isset($columns['null']) && !$columns['null']) $sql .= " not null";
		if(!empty($columns['null'])) $sql .= " null";
		if(isset($columns['primary']) && $columns['primary']) $this->setColKeys(
			$this->setKey('primary', [
				'type' => 'inside', 'column' => $columns['name']
			]), 'inside'
		);

		return $sql;
	}

	/**
	 * Возвращает название колонки с ID
	 *
	 * @return string
	 */
	public function getId()
	: string {
		return $this->id;
	}

	/**
	 * Устанавливает название колонки с ID
	 *
	 * @param string $id
	 */
	public function setId(string $id)
	: void {
		$this->id = $id;
	}

	/**
	 * Возвращает название таблицы
	 *
	 * @return string
	 */
	public function getName()
	: string {
		return $this->name;
	}

	/**
	 * Устанавливает название таблицы
	 *
	 * @param string $name
	 */
	public function setName(string $name)
	: void {
		$this->name = $name;
	}

	/**
	 * Возвращает название модели
	 *
	 * @return string
	 */
	public function getModel()
	: string {
		return $this->model;
	}

	/**
	 * Устанавливает название модели
	 *
	 * @param string $model
	 */
	public function setModel(string $model)
	: void {
		$this->model = $model;
	}

	/**
	 * Возвращает все колонки таблицы в виде массива
	 *
	 * @return array
	 */
	public function getColumns()
	: array {
		return $this->columns;
	}

	/**
	 * Устанавливает колонки таблицы
	 *
	 * @param array|string $vars
	 */
	public function setColumns($vars)
	: void {
		if(is_array($vars)) {
			$this->columns = array_merge($this->columns, $vars);
		} else $this->columns[] = $vars;
	}

	/**
	 * Возвращает ключи в виде массива
	 *
	 * @param null $type //  Параметр возврата определённого типа, по умолчанию - все
	 *
	 * @return array
	 */
	public function getColKeys($type = null)
	: array {
		if($type === null) {
			return $this->col_keys;
		} else return $this->col_keys[$type];
	}

	/**
	 * Добавляет ключи (Primary, Foreign Key, Unique) к базе данных
	 *
	 * @param string|array $col_keys        // Может быть простым текстом, либо массивом
	 * @param string       $type            // Устанавливает тип ключа: (Можно использовать любой другой тип)
	 *                                      // inside - Устанавливает ключи в функии создания таблицы
	 *                                      // after - Устанавливает ключи уже после функции создания таблицы
	 */
	public function setColKeys($col_keys, string $type = 'after')
	: void {
		if(!empty($col_keys)) {
			if(is_array($col_keys)) {
				$this->col_keys[$type] = array_unique(array_merge($col_keys, $this->col_keys[$type]));
			} else $this->col_keys[$type][] = $col_keys;
		}
	}

	/**
	 * Возвращает временной штамп
	 *
	 * @return string
	 */
	public function getNow() : string {
		if($this->now === null) $this->setNow();
		return $this->now;
	}

	/**
	 * Устанавливает текущую дату в формате цифрового штампа
	 *
	 * @param string|null $now //  Если значение пустое, то используется глобальное значение
	 */
	public function setNow(string $now = null)
	: void {
		$this->now = date("Y-m-d H:i:s");
		if($now !== null) $this->now = $now;
	}




}