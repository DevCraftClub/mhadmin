<?php
//===============================================================
// Файл: SortDirection.php                                      =
// Путь: devcraft/src/classes/Enums/SortDirection.php           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Enums;

use InvalidArgumentException;
use Cycle\Database\Query\SelectQuery;

/**
 * Направление сортировки для SelectQuery / DataLoader.
 *
 * Cycle ORM даёт только строковые константы {@see SelectQuery::SORT_ASC} /
 * {@see SelectQuery::SORT_DESC}; этот enum — типизированная обёртка.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Enums
 */
enum SortDirection: string {

	/**
	 * По возрастанию.
	 *
	 * @since 200.4.0
	 */
	case Asc = 'ASC';

	/**
	 * По убыванию.
	 *
	 * @since 200.4.0
	 */
	case Desc = 'DESC';

	/**
	 * Создаёт вариант по строке ASC/DESC (без учёта регистра).
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $direction  Строка направления.
	 *
	 * @return self Соответствующий вариант.
	 *
	 * @throws InvalidArgumentException Если значение неизвестно.
	 *
	 * @example
	 *     $dir = SortDirection::fromString('desc');
	 */
	public static function fromString(string $direction): self {
		return match (strtoupper(trim($direction))) {
			'ASC'   => self::Asc,
			'DESC'  => self::Desc,
			default => throw new InvalidArgumentException(
				__('Неизвестное направление сортировки: {dir}', ['{dir}' => $direction])
			),
		};
	}

	/**
	 * Возвращает константу Cycle SelectQuery для orderBy.
	 *
	 * @since 200.4.0
	 *
	 * @return string SelectQuery::SORT_ASC или SelectQuery::SORT_DESC.
	 *
	 * @example
	 *     $select->orderBy('name', SortDirection::Asc->toCycle());
	 */
	public function toCycle(): string {
		return match ($this) {
			self::Asc  => SelectQuery::SORT_ASC,
			self::Desc => SelectQuery::SORT_DESC,
		};
	}

}
