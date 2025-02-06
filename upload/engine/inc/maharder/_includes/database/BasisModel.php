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
use Cycle\Annotated\Annotation\Table\Index;

#[Index(columns: ['created_at'])]
#[Index(columns: ['creator'])]
#[Behavior\CreatedAt(field: 'createdAt', column: 'created_at')]
#[Behavior\UpdatedAt(field: 'updatedAt', column: 'updated_at')]
abstract class BasisModel {
	#[Column(type: 'bigPrimary', primary: true, autoincrement: true)]
	protected int                 $id;
	#[Column(type: 'datetime', default: 'CURRENT_TIMESTAMP')]
	protected \DateTimeImmutable  $createdAt;
	#[Column(type: 'datetime', nullable: true, default: null)]
	protected ?\DateTimeImmutable $updatedAt  = null;
	#[Column('bigInteger', nullable: true, default: null)]
	protected ?int                $creator    = null;
	#[Column('bigInteger', nullable: true, default: null)]
	protected ?int                $lastEditor = null;

	public function getId(): int {
		return $this->id;
	}

	public function getCreatedAt(): DateTimeImmutable {
		return $this->createdAt;
	}

	public function getUpdatedAt(): ?DateTimeImmutable {
		return $this->updatedAt;
	}

	public function setCreator(?int $userId) {
		$this->creator = $userId;
	}

	public function setUpdatedDate() {
		$this->updatedAt = new DateTimeImmutable();
	}

	/**
	 * Обновление последнего редактора.
	 *
	 * @param int $userId Идентификатор пользователя, который редактирует.
	 */
	public function setLastEditor(int $userId): void {
		$this->lastEditor = $userId;
	}

	/**
	 * Метод для автоматического обновления поля lastEditor при изменении сущности.
	 * В данном случае можно вызывать его в ручную перед сохранением объекта.
	 */
	public function beforeSave(): void {
		global $is_logged, $member_id;

		$user_id = $is_logged ? $member_id['user_id'] : null;

		if ($this->id) {
			$this->setLastEditor($user_id);
			$this->setUpdatedDate();
		} else {
			$this->setCreator($user_id);
		}
	}

	abstract public function getColumnVal(string $name): mixed;

}