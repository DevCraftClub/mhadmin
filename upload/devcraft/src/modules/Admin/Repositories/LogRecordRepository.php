<?php
//===============================================================
// Файл: LogRecordRepository.php                                =
// Путь: devcraft/src/modules/Admin/Repositories/LogRecordRepos…=
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

namespace DevCraft\Modules\Admin\Repositories;

use Ramsey\Uuid\Uuid;
use DevCraft\Core\Abstracts\AbstractRepository;

/**
 * Репозиторий записей журнала DevCraft (`devcraft_logs`).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
class LogRecordRepository extends AbstractRepository {

	/**
	 * Удаляет запись журнала по строковому UUID.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $uuid  Строковое представление UUID записи.
	 *
	 * @return bool `true`, если запись найдена и удалена.
	 *
	 * @example
	 *     $deleted = $repository->deleteByUuid('550e8400-e29b-41d4-a716-446655440000');
	 */
	public function deleteByUuid(string $uuid): bool {
		try {
			$uuidObj = Uuid::fromString($uuid);
		} catch(\Throwable) {
			return false;
		}

		return $this->deleteByColumn('uuid', $uuidObj);
	}

}
