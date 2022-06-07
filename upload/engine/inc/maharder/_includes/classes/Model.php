<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DataLoader.php');
require_once DLEPlugins::Check(__DIR__ . '/Table.php');

/**
 * Класс по управлению данных в базе данных
 */
class Model {
	use LogGenerator;
	use DataLoader;

	/**
	 * @var Table
	 */
	protected Table $table;

	/**
	 * Конструктор модели
	 *
	 * @param string $model_name //  Название модели / модуля
	 * @param string $table_name //  Название таблицы
	 * @param string $id_name    //  Название идентификатора таблицы
	 * @param array  $vars       //  Параметры таблицы
	 * @param array  $table_keys //  Ключи таблицы
	 * @throws \JsonException
	 */
	public function __construct(string $model_name, string $table_name, string $id_name, array $vars, array $table_keys = []) {
		$this->table = new Table($id_name, $table_name, $model_name, $vars, $table_keys);
		$this->table->checkMigrations();
	}


	/**
	 * Получаем объект по ID
	 *
	 * @param $id
	 *
	 * @return array|mixed
	 */
	public function getSingle($id) {

		if ($id && $id > 0) {
			return $this->load_data($this->table->getName(), [
				'table' => $this->table->getName(),
				'where' => [
					$this->table->getId() => $id
				]
			])[0];
		} else {
			$this->generate_log($this->table->getModel(), 'getSingle', 'ID не должен быть пустым');
		}

		return [];
	}

	/**
	 * Получаем все объекты
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function getAll(array $vars = [
		'limit'   => null,
		'order'   => ['created_date' => 'ASC'],
		'where'   => [],
		'selects' => []
	]): array {

		return $this->load_data($this->table->getName(), [
			'table'   => $this->table->getName(),
			'limit'   => $vars['limit'],
			'order'   => $vars['order'],
			'where'   => $vars['where'],
			'selects' => $vars['selects'],
		]);
	}

	/**
	 * Создаём обьект
	 *
	 * @return array|mixed
	 */
	public function create(array $values) {
		$db = self::getDb();

		try {
			$t_names = [];
			$t_values = [];
			foreach ($this->table->getColumns() as $t) {
				$t_names[] = $t['name'];
				$t_values[] = self::defType($values[$t['name']], $t['type']);
			}
			$sql_insert = 'INSERT INTO ' . PREFIX . "_{$this->table->getName()} (" . implode(", ", $t_names) . ") VALUES (" . implode(", ", $t_values) . ");";

			$db->query($sql_insert);
			$id = $db->insert_id();

			$this->clear_cache($this->table->getModel());

			return $this->getSingle($id);

		} catch (Exception $e) {
			$report = [
				'success' => false,
				'error'   => $e->getMessage()
			];
			$this->generate_log($this->table->getModel(), 'create', $report);

			return $report;
		}
	}

	/**
	 * Удаляем объект
	 *
	 * @param $id
	 *
	 * @return array|bool[]
	 */
	public function delete($id): array {
		$db = self::getDb();

		try {

			$db->query('DELETE FROM ' . PREFIX . "_{$this->table->getName()} WHERE {$this->table->getId()} = {$id}");

			$this->clear_cache();

			return ['success' => true];

		} catch (Exception $e) {
			$report = [
				'success' => false,
				'error'   => $e->getMessage(),
				'message' => 'ID не может быть пустым'
			];
			$this->generate_log($this->table->getModel(), 'delete', $report);

			return $report;
		}

	}

	/**
	 * Обновляем объект по его ID
	 *
	 * @param int $id
	 * @param array $values
	 * @return  array
	 */
	public function update(int $id, array $values = []): array {
		global $member_id;
		$db = self::getDb();

		try {
			$update = [];
			foreach ($this->table->getColumns() as $t) {
				if (isset($values[$t['name']])) {
					$value = self::defType($values[$t['name']], $t['type']);
					$update[] = "{$t['name']} = {$value}";
				}
			}
			$update[] = "updated_date = '{$this->table->getNow()}'";
			$update[] = "editor = '{$member_id['user_id']}'";

			$update = implode(', ', $update);

			if (!empty($update)) {
				$update_sql = 'UPDATE ' . PREFIX . "_{$this->table->getName()} SET {$update} WHERE {$this->table->getId()} = {$id}";
				$db->query($update_sql);
			}

			$this->clear_cache($this->table->getName());

			return [
				'success' => true,
				'data' => $this->getSingle($id)
			];

		} catch (Exception $e) {
			$report = [
				'success' => false,
				'error'   => $e->getMessage(),
				'message' => 'ID не может быть пустым'
			];
			$this->generate_log($this->table->getModel(), 'update', $report);

			return $report;
		}

	}

	/**
	 * Обновляем объект по любому типу
	 *
	 * @param array $where_values
	 * @param array $values
	 * @return array|bool[]
	 */
	public function updateByVal(array $where_values = [], array $values = []): array {
		$db = self::getDb();

		try {
			$update = [];
			foreach ($this->table->getColumns() as $t) {
				if (isset($values[$t['name']])) {
					$value = self::defType($values[$t['name']], $t['type']);
					$update[] = "{$t['name']} = $value";
				}
			}
			$update[] = "updated_date = '{$this->table->getNow()}'";

			$update = implode(', ', $update);

			$where = [];
			foreach ($where_values as $key => $value) $where[$key] = $this->getComparer($value);
			$where = implode(' AND ', $where);

			if (!empty($update)) $db->query('UPDATE ' . PREFIX . "_{$this->table->getName()} SET {$update} WHERE {$where}");

			clear_cache();

			return ['success' => true];

		} catch (Exception $e) {
			$report = [
				'success' => false,
				'error'   => $e->getMessage(),
				'message' => 'ID не может быть пустым'
			];
			$this->generate_log($this->table->getModel(), 'updateByVal', $report);

			return $report;
		}

	}

	/**
	 * Возвращает количество записей в базе данных
	 *
	 * @return int
	 * @throws \JsonException
	 */
	public function count()
	: int {
		return (int) $this->load_data($this->table->getName(), [
			'table'   => $this->table->getName(),
			'selects' => ['count(*) as count'],
		])[0]['count'];
	}

	/**
	 * Чистый запрос в базу данных
	 *
	 * @param $query
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function raw($query) : array {
		return $this->load_data($this->table->getName(), [
			'table'   => $this->table->getName(),
			'sql' => $query,
		]);
	}

	public function getTableName() : string {
		return $this->table->getName();
	}

	public function getTableId() : string {
		return $this->table->getId();
	}

}