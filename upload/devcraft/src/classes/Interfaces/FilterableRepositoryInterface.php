<?php
//===============================================================
// Файл: FilterableRepositoryInterface.php                      =
// Путь: devcraft/src/classes/Interfaces/FilterableRepositoryIn…=
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

namespace DevCraft\Core\Interfaces;

/**
 * Контракт репозитория с метаданными для построения фильтров админки.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface FilterableRepositoryInterface {

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
	public function distinctColumnValues(string $column): array;

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
	public function columnBounds(string $column): array;

}
