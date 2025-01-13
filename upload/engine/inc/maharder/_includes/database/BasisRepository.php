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


class BasisRepository extends Repository implements RepositoryInterface {

	public function getFirst() : ?Entity {
		return $this->select()->orderBy('created_at')->limit(1)->fetchOne();
	}
	public function getLast() : ?Entity {
		return $this->select()->orderBy('created_at', 'DESC')->limit(1)->fetchOne();
	}

}