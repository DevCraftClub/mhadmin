<?php
//===============================================================
// Файл: BasisRepository.php                                    =
// Путь: engine/inc/maharder/_includes/database/BasisRepository.php
// Дата создания: 2024-04-15 07:32:52                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select\Repository;
use Cycle\Schema\Definition\Entity;


/**
 * Базовый репозиторий для работы с сущностями.
 * Предоставляет методы для получения первой, последней записей, лимитированного списка и общего количества элементов.
 */
class BasisRepository extends Repository implements RepositoryInterface {

	/**
	 * Получает первую запись из базы данных, отсортированную по колонке `created_at` по возрастанию.
	 *
	 * @return Entity|null Возвращает первую сущность или null, если записи отсутствуют.
	 */
	public function getFirst(): ?Entity {
		return $this->select()->orderBy('created_at')->limit(1)->fetchOne();
	}

	/**
	 * Получает последнюю запись из базы данных, отсортированную по колонке `created_at` по убыванию.
	 *
	 * @return Entity|null Возвращает последнюю сущность или null, если записи отсутствуют.
	 */
	public function getLast(): ?Entity {
		return $this->select()->orderBy('created_at', 'DESC')->limit(1)->fetchOne();
	}

	/**
	 * Получает список записей, лимитированный заданным количеством и смещением.
	 *
	 * @param int $total Количество записей для выборки.
	 * @param int $start Смещение начала выборки. По умолчанию - 0.
	 *
	 * @return array Массив сущностей, соответствующих указанным критериям.
	 */
	public function limit(int $total, int $start = 0): array {
		return $this->select()->limit($total)->offset($start)->fetchAll();
	}

	/**
	 * Получает общее количество записей в базе данных.
	 *
	 * @return int Количество записей.
	 */
	public function total(): int {
		return $this->select()->count();
	}

}