<?php
//===============================================================
// Файл: BasisModel.php                                         =
// Путь: engine/inc/maharder/_includes/database/BasisModel.php  =
// Дата создания: 2024-04-15 07:28:52                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

use Cycle\Annotated\Annotation\Column;
use Cycle\ORM\Entity\Behavior;

require_once(DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php'));


#[Behavior\CreatedAt(
	field: 'createdAt',
	column: 'created_at'
)]
#[Behavior\UpdatedAt(
	field: 'updatedAt',
	column: 'updated_at'
)]
class BasisModel {
	#[Column(type: 'bigPrimary', primary: true, autoincrement: true)]
	protected int $id;

	#[Column(type: 'datetime')]
	protected \DateTimeImmutable $createdAt;

	#[Column(type: 'datetime', nullable: true)]
	protected ?\DateTimeImmutable $updatedAt = null;

	public function getId() : int {
		return $this->id;
	}

	public function getCreatedAt() : DateTimeImmutable {
		return $this->createdAt;
	}

	public function getUpdatedAt() : ?DateTimeImmutable {
		return $this->updatedAt;
	}



}