<?php
//===============================================================
// Файл: AbstractEntity.php                                     =
// Путь: devcraft/src/classes/Abstracts/AbstractEntity.php      =
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

namespace DevCraft\Core\Abstracts;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior;
use DateTimeImmutable;

/**
 * Базовая ORM-сущность с аудитом создания и изменения записи.
 *
 * @package DevCraft
 * @subpackage Core.Abstracts
 * @since 200.4.0
 */
#[Index(columns: ['created_at'])]
#[Index(columns: ['creator'])]
#[Behavior\CreatedAt(field: 'createdAt', column: 'created_at')]
#[Behavior\UpdatedAt(field: 'updatedAt', column: 'updated_at')]
abstract class AbstractEntity {

	/**
	 * Имя колонки даты создания записи.
	 *
	 * @var string
	 * @since 200.4.0
	 */
	public const ATTR_CREATED_AT = 'created_at';

	/**
	 * Имя колонки даты последнего обновления записи.
	 *
	 * @var string
	 * @since 200.4.0
	 */
	public const ATTR_UPDATED_AT = 'updated_at';

	/**
	 * Дата и время создания записи.
	 *
	 * @var DateTimeImmutable
	 * @since 200.4.0
	 */
	#[Column(type: 'datetime', default: 'CURRENT_TIMESTAMP')]
	public DateTimeImmutable $createdAt;

	/**
	 * Идентификатор пользователя DLE, создавшего запись.
	 *
	 * @var int|null
	 * @since 200.4.0
	 */
	#[Column(type: 'bigInteger', nullable: true, default: NULL)]
	public ?int $creator = NULL;

	/**
	 * Идентификатор пользователя DLE, последним изменившего запись.
	 *
	 * @var int|null
	 * @since 200.4.0
	 */
	#[Column(type: 'bigInteger', nullable: true, default: NULL)]
	public ?int $lastEditor = NULL;

	/**
	 * Дата и время последнего обновления записи.
	 *
	 * @var DateTimeImmutable|null
	 * @since 200.4.0
	 */
	#[Column(type: 'datetime', nullable: true, default: NULL)]
	public ?DateTimeImmutable $updatedAt = NULL;

	/**
	 * Первичный ключ записи.
	 *
	 * @var int
	 * @since 200.4.0
	 */
	#[Column(type: 'bigPrimary', primary: true, autoincrement: true)]
	protected int $id;

	/**
	 * Заполняет поля аудита перед сохранением сущности в базу данных.
	 *
	 * @since 200.4.0
	 *
	 * @global bool                $is_logged  Флаг авторизации пользователя DLE.
	 * @global array<string,mixed> $member_id  Данные текущего пользователя DLE.
	 *
	 * @example
	 *     $entity->beforeSave();
	 *     $database->getManager()->persist($entity)->run();
	 */
	public function beforeSave(): void {
		global $is_logged, $member_id;

		$user_id = NULL;

		if(!empty($is_logged) && is_array($member_id) && isset($member_id['user_id'])) {
			$user_id = (int) $member_id['user_id'];
		}

		if(isset($this->id)) {
			$this->setLastEditor($user_id);
			$this->touchUpdatedAt();
		} else {
			$this->setCreator($user_id);
		}
	}

	/**
	 * Возвращает дату и время создания записи.
	 *
	 * @since 200.4.0
	 *
	 * @return DateTimeImmutable Момент создания.
	 *
	 * @example
	 *     echo $entity->createdAt()->format('Y-m-d');
	 */
	public function createdAt(): DateTimeImmutable {
		return $this->createdAt;
	}

	/**
	 * Возвращает идентификатор создателя записи.
	 *
	 * @since 200.4.0
	 *
	 * @return int|null ID пользователя DLE или `null`.
	 *
	 * @example
	 *     $creatorId = $entity->creator();
	 */
	public function creator(): ?int {
		return $this->creator;
	}

	/**
	 * Возвращает значение колонки сущности по имени.
	 *
	 * @since 200.4.0
	 *
	 * @param string $name Имя колонки или свойства.
	 *
	 * @return mixed Значение колонки.
	 */
	abstract public function getColumnVal(string $name): mixed;

	/**
	 * Возвращает первичный ключ записи.
	 *
	 * @since 200.4.0
	 *
	 * @return int Числовой идентификатор.
	 *
	 * @example
	 *     $id = $entity->id();
	 */
	public function id(): int {
		return $this->id;
	}

	/**
	 * Возвращает идентификатор последнего редактора записи.
	 *
	 * @since 200.4.0
	 *
	 * @return int|null ID пользователя DLE или `null`.
	 *
	 * @example
	 *     $editorId = $entity->lastEditor();
	 */
	public function lastEditor(): ?int {
		return $this->lastEditor;
	}

	/**
	 * Устанавливает идентификатор создателя записи.
	 *
	 * @since 200.4.0
	 *
	 * @param int|null $user_id ID пользователя DLE или `null`.
	 *
	 * @example
	 *     $entity->setCreator(42);
	 */
	public function setCreator(?int $user_id): void {
		$this->creator = $user_id;
	}

	/**
	 * Устанавливает идентификатор последнего редактора записи.
	 *
	 * @since 200.4.0
	 *
	 * @param int|null $user_id ID пользователя DLE или `null`.
	 *
	 * @example
	 *     $entity->setLastEditor(42);
	 */
	public function setLastEditor(?int $user_id): void {
		$this->lastEditor = $user_id;
	}

	/**
	 * Обновляет метку времени последнего изменения записи.
	 *
	 * @since 200.4.0
	 *
	 * @example
	 *     $entity->touchUpdatedAt();
	 */
	public function touchUpdatedAt(): void {
		$this->updatedAt = new DateTimeImmutable();
	}

	/**
	 * Возвращает дату и время последнего обновления записи.
	 *
	 * @since 200.4.0
	 *
	 * @return DateTimeImmutable|null Момент обновления или `null`.
	 *
	 * @example
	 *     $updated = $entity->updatedAt();
	 */
	public function updatedAt(): ?DateTimeImmutable {
		return $this->updatedAt;
	}

}
