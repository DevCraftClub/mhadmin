<?php
//===============================================================
// Файл: CurrentDleUserId.php                                   =
// Путь: devcraft/src/classes/Database/Audit/CurrentDleUserId.php
// Последнее изменение: 2026-09-14 16:20:00                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Database\Audit;

/**
 * Адаптер сессии DLE: идентификатор текущего пользователя для аудита записи.
 *
 * Не доменный сервис: читает только `$member_id['user_id']`. Флаги входа не смотрит.
 * Если ID нет или не больше нуля — возвращает `0` (исключение Constitution: sentinel, не null).
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Database.Audit
 */
final class CurrentDleUserId {

	/**
	 * Возвращает ID пользователя DLE для полей creator / lastEditor.
	 *
	 * @since 200.4.1
	 *
	 * @global array<string, mixed>|null $member_id Данные текущего пользователя DLE.
	 *
	 * @return int ID > 0 либо `0`.
	 */
	public static function resolve(): int {
		global $member_id;

		if(!is_array($member_id) || !isset($member_id['user_id'])) {
			return 0;
		}

		$id = (int) $member_id['user_id'];

		return $id > 0 ? $id : 0;
	}

}
