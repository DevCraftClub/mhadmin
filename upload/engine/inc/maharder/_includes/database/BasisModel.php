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
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Index(columns: ['created_at'])]
abstract class BasisModel {
	#[Column(type: 'bigPrimary', primary: true, autoincrement: true)]
	protected int $id;

	#[Column(type: 'datetime', default: 'CURRENT_TIMESTAMP')]
	protected \DateTimeImmutable $createdAt;

	#[Column(type: 'datetime', nullable: true, default: 'CURRENT_TIMESTAMP')]
	protected ?\DateTimeImmutable $updatedAt = null;

	public function getId(): int {
		return $this->id;
	}

	public function getCreatedAt(): DateTimeImmutable {
		return $this->createdAt;
	}

	public function getUpdatedAt(): ?DateTimeImmutable {
		return $this->updatedAt;
	}

	abstract public function getColumnVal(string $name): mixed;

}