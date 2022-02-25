<?php

namespace MaHarder\classes;

use DateTime;
use Exception;

/**
 * Класс по управлению данных в базе данных
 */
class Model extends Admin {
	
	protected string $model, $table, $id_name;
	public array $table_vars = [];
	
	/**
	 * @param string $model_name    Название модели / модуля
	 * @param string $table_name    Название таблицы
	 * @param string $id_name       Название идентификатора таблицы
	 * @param array $vars           Параметры таблицы
	 */
	public function __construct(string $model_name, string $table_name, string $id_name, array $vars) {
		parent::__construct();
		
		$this->model = $model_name;
		$this->table = $table_name;
		$this->id_name = $id_name;
		$this->table_vars = $vars;
	}
	
	/**
	 * Получаем объект по ID
	 *
	 * @param $id
	 *
	 * @return array|mixed
	 */
	public function getSingle($id) {
		
		if($id && $id > 0)
			return $this->load_data($this->table, [
				'table' => $this->table,
				'where' => [
					$this->id_name => $id
				]
			])[0];
		else
			$this->generate_log($this->model, 'getSingle', 'ID не должен быть пустым');
		
		return [];
	}
	
	/**
	 * Получаем все объекты
	 *
	 * @param array $vars
	 *
	 * @return mixed
	 */
	public function getAll($vars = ['limit' => null, 'order' => ['created_date' => 'ASC'], 'where' => [], 'selects' => []]) {
		
		return $this->load_data($this->table, [
			'table' => $this->table,
			'limit' => $vars['limit'],
			'order' => $vars['order'],
			'where' => $vars['where'],
			'selects' => $vars['selects'],
		]);
	}
	
	/**
	 * Создаём обьект
	 *
	 * @return array|mixed
	 */
	public function create(array $values) {
		global $db;
		
		try {
			$t_names = [];
			$t_values = [];
			foreach($this->table_vars as $t) {
				$t_names[] = $t['name'];
				$t_values[] = $this->defType($values[$t['name']], $t['type']);
			}
			$sql_insert = 'INSERT INTO ' . PREFIX. "_{$this->table} (" . implode(", ", $t_names) . ") VALUES (" . implode(", ", $t_values) . ");";
			
			$db->query($sql_insert);
			$id = $db->insert_id();
			
			clear_cache();
			
			return $this->getSingle($id);
			
		} catch (Exception $e) {
			$report = ['success' => false, 'error' => $e->getMessage()];
			$this->generate_log($this->model, 'create', $report);
			
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
	public function delete($id): array
	{
		global $db;
		
		try {
			
			$db->query('DELETE FROM ' . PREFIX . "_{$this->table} WHERE {$this->id_name} = {$id}");
			
			clear_cache();
			
			return ['success' => true];
			
		} catch (Exception $e) {
			$report = ['success' => false, 'error' => $e->getMessage(), 'message' => 'ID не может быть пустым'];
			$this->generate_log($this->model, 'delete', $report);
			
			return $report;
		}
		
	}
	
	/**
	 * Обновляем объект по его ID
	 *
	 * @param   int     $id
	 * @param   array   $values
	 * @return  array
	 */
	public function update(int $id, array $values = []): array	{
		global $db;
		
		try {
			$update = array();
			foreach($this->table_vars as $t) {
				if (isset($values[$t['name']])) {
					$value = $this->defType($values[$t['name']], $t['type']);
					$update[] = "{$t['name']} = $value";
				}
			}
			$update['update_date'] = new DateTime();
			
			$update = implode(', ', $update);
			
			if (!empty($update))
				$db->query('UPDATE ' . PREFIX . "_{$this->table} SET {$update} WHERE {$this->id_name} = {$id}");
			
			clear_cache();
			
			return ['success' => true];
			
		} catch (Exception $e) {
			$report = ['success' => false, 'error' => $e->getMessage(), 'message' => 'ID не может быть пустым'];
			$this->generate_log($this->model, 'update', $report);
			
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
	public function updateByVal($where_values = [], $values = []): array {
		global $db;
		
		try {
			$update = array();
			foreach($this->table_vars as $t) {
				if (isset($values[$t['name']])) {
					$value = $this->defType($values[$t['name']], $t['type']);
					$update[] = "{$t['name']} = $value";
				}
			}
			$update['update_date'] = new DateTime();
			
			$update = implode(', ', $update);
			
			$where = array();
			foreach ($where_values as $key => $value) $where[$key] = $this->getComparer($value);
			$where = implode(' AND ', $where);
			
			if (!empty($update))
				$db->query('UPDATE ' . PREFIX . "_{$this->table} SET {$update} WHERE {$where}");
			
			clear_cache();
			
			return ['success' => true];
			
		} catch (Exception $e) {
			$report = ['success' => false, 'error' => $e->getMessage(), 'message' => 'ID не может быть пустым'];
			$this->generate_log($this->model, 'updateByVal', $report);
			
			return $report;
		}
		
	}
	
	/**
	 * Вытаскиваем данные из базы данных
	 *
	 * @param array $vars
	 * @return array
	 */
	public function selectData(array $vars = ['where' => [], 'limit' => null, 'order' => []]) : array {
		try {
			
			return $this->load_data($this->table, [
				'table' => $this->table,
				'where' => $vars['where'],
				'limit' => $vars['limit'],
				'order' => $vars['order']
			]);
			
		} catch (Exception $e) {
			$report = ['success' => false, 'error' => $e->getMessage(), 'message' => 'ID не может быть пустым'];
			$this->generate_log($this->model, 'selectData', $report);
			
			return $report;
		}
	}
	
}