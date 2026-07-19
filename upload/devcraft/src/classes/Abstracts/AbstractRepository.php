<?php
//===============================================================
// Файл: AbstractRepository.php                                 =
// Путь: devcraft/src/classes/Abstracts/AbstractRepository.php  =
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

namespace DevCraft\Core\Abstracts;

use DevCraft\Core\Application;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\RepositoryInterface;
use Cycle\Database\Injection\Parameter;
use DevCraft\Core\Admin\FilterFormService;
use DevCraft\Core\Interfaces\FilterableRepositoryInterface;

/**
 * Базовый Cycle ORM-репозиторий с фильтрацией, пагинацией и метаданными колонок.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractRepository extends Repository implements RepositoryInterface, FilterableRepositoryInterface {

	/**
	 * Кэш уникальных значений колонок для фильтров.
	 *
	 * @since 200.4.0
	 * @var array<string, list<string>>
	 */
	private array $distinctCache = [];

	/**
	 * Кэш минимальных и максимальных значений колонок.
	 *
	 * @since 200.4.0
	 * @var array<string, array{min: mixed, max: mixed}>
	 */
	private array $boundsCache = [];

	/**
	 * Возвращает самую раннюю запись по дате создания.
	 *
	 * @since 200.4.0
	 *
	 * @return object|null Первая сущность или `null`, если таблица пуста.
	 *
	 * @example
	 *     $first = $repository->getFirst();
	 */
	public function getFirst(): ?object {
		return $this->select()->orderBy(AbstractEntity::ATTR_CREATED_AT)->limit(1)->fetchOne();
	}

	/**
	 * Возвращает самую позднюю запись по дате создания.
	 *
	 * @since 200.4.0
	 *
	 * @return object|null Последняя сущность или `null`, если таблица пуста.
	 *
	 * @example
	 *     $last = $repository->getLast();
	 */
	public function getLast(): ?object {
		return $this->select()->orderBy(AbstractEntity::ATTR_CREATED_AT, 'DESC')->limit(1)->fetchOne();
	}

	/**
	 * Возвращает ограниченный набор записей с заданным смещением.
	 *
	 * @since 200.4.0
	 *
	 * @param   int  $total  Количество записей.
	 * @param   int  $start  Смещение от начала выборки.
	 *
	 * @return array<int, object> Список сущностей.
	 *
	 * @example
	 *     $batch = $repository->limit(20, 40);
	 */
	public function limit(int $total, int $start = 0): array {
		return $this->select()->limit($total)->offset($start)->fetchAll();
	}

	/**
	 * Возвращает общее количество записей в таблице сущности.
	 *
	 * @since 200.4.0
	 *
	 * @return int Число строк.
	 *
	 * @example
	 *     $count = $repository->total();
	 */
	public function total(): int {
		return $this->select()->count();
	}

	/**
	 * Выполняет фильтрованную выборку с пагинацией и сортировкой.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria             Критерии фильтрации.
	 * @param   int                                                    $page                 Номер страницы (начиная с 1).
	 * @param   int                                                    $perPage              Записей на странице.
	 * @param   string                                                 $order                Колонка сортировки.
	 * @param   string                                                 $sort                 Направление (`asc` или `desc`).
	 * @param   list<string>                                           $allowedOrderColumns  Допустимые колонки сортировки.
	 * @param   string                                                 $defaultOrder         Колонка сортировки по умолчанию.
	 *
	 * @return array{items: object[], total: int} Элементы текущей страницы и общее количество.
	 *
	 * @example
	 *     $result = $repository->findFiltered($criteria, 1, 25, 'created_at', 'desc', ['created_at', 'level']);
	 */
	public function findFiltered(
		array  $criteria,
		int    $page,
		int    $perPage,
		string $order,
		string $sort,
		array  $allowedOrderColumns,
		string $defaultOrder = 'created_at',
	): array {
		$select = $this->select();
		$this->applyCriteria($select, $criteria);

		$total = $select->count();
		$page  = max(1, $page);
		$order = in_array($order, $allowedOrderColumns, true)? $order : $defaultOrder;

		/** @var object[] $items */
		$items = $select
			->orderBy($order, FilterFormService::getSort($sort))
			->limit($perPage)
			->offset(($page - 1) * $perPage)
			->fetchAll();

		return [
			'items' => $items,
			'total' => $total,
		];
	}

	/**
	 * Возвращает уникальные значения указанной колонки для выпадающих фильтров.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $column  Имя колонки в таблице сущности.
	 *
	 * @return list<string> Отсортированный список уникальных строковых значений.
	 *
	 * @example
	 *     $levels = $repository->distinctColumnValues('level');
	 */
	public function distinctColumnValues(string $column): array {
		if(isset($this->distinctCache[$column])) {
			return $this->distinctCache[$column];
		}

		$rows = $this
			->select()
			->columns([$column])
			->groupBy($column)
			->orderBy($column)
			->fetchAll();

		$values = [];

		foreach($rows as $row) {
			$value = $this->extractColumnValue($row, $column);

			if($value !== '') {
				$values[] = $value;
			}
		}

		$this->distinctCache[$column] = $values;

		return $values;
	}

	/**
	 * Возвращает минимальное и максимальное значение колонки для диапазонных фильтров.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $column  Имя колонки в таблице сущности.
	 *
	 * @return array{min: mixed, max: mixed} Границы диапазона или `null` при отсутствии данных.
	 *
	 * @example
	 *     $bounds = $repository->columnBounds('created_at');
	 */
	public function columnBounds(string $column): array {
		if(isset($this->boundsCache[$column])) {
			return $this->boundsCache[$column];
		}

		$rows = $this
			->select()
			->columns([$column])
			->orderBy($column)
			->fetchAll();

		if($rows === []) {
			return $this->boundsCache[$column] = ['min' => NULL, 'max' => NULL];
		}

		return $this->boundsCache[$column] = [
			'min' => $this->extractColumnValue($rows[0], $column),
			'max' => $this->extractColumnValue($rows[count($rows) - 1], $column),
		];
	}

	/**
	 * Сохраняет сущность (INSERT или UPDATE) через DatabaseGateway::run.
	 *
	 * Вызывает `beforeSave()` у сущности, если метод есть.
	 *
	 * @since 200.4.1
	 *
	 * @param   object  $entity  Экземпляр сущности для сохранения.
	 *
	 * @return object Та же сущность после persist.
	 *
	 * @example
	 *     $repository->saveEntity($logEntry);
	 */
	public function saveEntity(object $entity): object {
		Application::instance()->database()->run($entity);

		return $entity;
	}

	/**
	 * Удаляет сущность из базы данных.
	 *
	 * @since 200.4.0
	 *
	 * @param   object  $entity  Экземпляр сущности для удаления.
	 *
	 * @return bool Всегда `true` при успешном вызове менеджера ORM.
	 *
	 * @example
	 *     $repository->deleteEntity($logEntry);
	 */
	public function deleteEntity(object $entity): bool {
		Application::instance()->database()->getManager()->delete($entity)->run();

		return true;
	}

	/**
	 * Удаляет первую запись, соответствующую значению колонки.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $column  Имя колонки для поиска.
	 * @param   mixed   $value   Значение для сравнения.
	 *
	 * @return bool `true`, если запись найдена и удалена; иначе `false`.
	 *
	 * @example
	 *     $repository->deleteByColumn('id', 15);
	 */
	public function deleteByColumn(string $column, mixed $value): bool {
		$record = $this->select()->where($column, $value)->fetchOne();

		if($record === NULL) {
			return false;
		}

		return $this->deleteEntity($record);
	}

	/**
	 * Применяет список критериев фильтрации к объекту выборки Cycle ORM.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed                                                  $select    Объект выборки Cycle ORM.
	 * @param   list<array{column: string, op: string, value: mixed}>  $criteria  Критерии фильтрации.
	 */
	protected function applyCriteria(mixed $select, array $criteria): void {
		foreach($criteria as $criterion) {
			$column = (string) ($criterion['column'] ?? '');
			$op     = (string) ($criterion['op'] ?? '');
			$value  = $criterion['value'] ?? NULL;

			if($column === '' || $op === '') {
				continue;
			}

			match ($op) {
				'in'      => $select->where($column, 'in', new Parameter((array) $value)),
				'like'    => $select->where($column, 'like', "%{$value}%"),
				'between' => $this->applyBetween($select, $column, is_array($value)? $value : []),
				default   => NULL,
			};
		}
	}

	/**
	 * Добавляет условие диапазона `between` к выборке.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed                 $select  Объект выборки Cycle ORM.
	 * @param   string                $column  Имя колонки.
	 * @param   array<string, mixed>  $value   Границы диапазона с ключами `from` и `to`.
	 */
	protected function applyBetween(mixed $select, string $column, array $value): void {
		$from = (string) ($value['from'] ?? '');
		$to   = (string) ($value['to'] ?? '');

		if($from === '' || $to === '') {
			return;
		}

		$select->where($column, '>=', $from);
		$select->where($column, '<=', $to);
	}

	/**
	 * Извлекает строковое значение колонки из строки результата выборки.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed   $row     Строка результата (сущность, массив или объект).
	 * @param   string  $column  Имя колонки.
	 *
	 * @return string Строковое представление значения или пустая строка.
	 */
	protected function extractColumnValue(mixed $row, string $column): string {
		if($row instanceof AbstractEntity) {
			$value = $row->getColumnVal($column);

			return $value === NULL? '' : (string) $value;
		}

		if(is_array($row)) {
			if(array_key_exists($column, $row)) {
				return $this->stringifyColumnValue($row[$column]);
			}

			if(array_key_exists(0, $row)) {
				return $this->stringifyColumnValue($row[0]);
			}
		}

		if(is_object($row)) {
			if(method_exists($row, 'getColumnVal')) {
				$value = $row->getColumnVal($column);

				return $value === NULL? '' : (string) $value;
			}

			if(isset($row->{$column})) {
				return $this->stringifyColumnValue($row->{$column});
			}
		}

		return '';
	}

	/**
	 * Преобразует значение колонки в строку для фильтров и отображения.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $value  Исходное значение.
	 *
	 * @return string Строковое представление.
	 */
	protected function stringifyColumnValue(mixed $value): string {
		if($value instanceof \DateTimeInterface) {
			return $value->format('Y-m-d H:i:s');
		}

		return (string) $value;
	}

}
