<?php
//===============================================================
// Файл: UpdatedByHook.php                                      =
// Путь: devcraft/src/classes/Database/Audit/UpdatedByHook.php  =
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

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnUpdate;
use DevCraft\Core\Abstracts\AbstractEntity;

/**
 * Поведение Cycle: при обновлении записи пишет только lastEditor.
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Database.Audit
 */
final class UpdatedByHook {

	/**
	 * Обновляет lastEditor; creator не трогает.
	 *
	 * @since 200.4.1
	 *
	 * @param   OnUpdate  $event  Событие постановки UPDATE в очередь.
	 */
	public static function invoke(OnUpdate $event): void {
		$id = CurrentDleUserId::resolve();

		$event->state->register('lastEditor', $id);

		if($event->entity instanceof AbstractEntity) {
			$event->entity->setLastEditor($id);
		}
	}

}
