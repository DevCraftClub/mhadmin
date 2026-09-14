<?php
//===============================================================
// Файл: CreatedByHook.php                                      =
// Путь: devcraft/src/classes/Database/Audit/CreatedByHook.php  =
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

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use DevCraft\Core\Abstracts\AbstractEntity;

/**
 * Поведение Cycle: при создании записи пишет creator и lastEditor одним ID.
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Database.Audit
 */
final class CreatedByHook {

	/**
	 * Заполняет автора создания и правки из адаптера сессии DLE.
	 *
	 * @since 200.4.1
	 *
	 * @param   OnCreate  $event  Событие постановки INSERT в очередь.
	 */
	public static function invoke(OnCreate $event): void {
		$id = CurrentDleUserId::resolve();

		$event->state->register('creator', $id);
		$event->state->register('lastEditor', $id);

		if($event->entity instanceof AbstractEntity) {
			$event->entity->setCreator($id);
			$event->entity->setLastEditor($id);
		}
	}

}
