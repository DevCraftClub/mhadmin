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

/**
 * Базовый абстрактный класс модели, представляющий типовую сущность базы данных.
 * Класс используется для создания типовых моделей с общими свойствами, такими как
 * уникальный идентификатор (`id`), дата создания (`createdAt`) и дата последнего обновления (`updatedAt`).
 * Предназначен для расширения в конкретных классах моделей, которые реализуют бизнес-логику
 * и определяют дополнительные свойства и методы.
 * ## Свойства:
 * - **`id`** — уникальный первичный ключ записи.
 * - **`createdAt`** — дата и время создания записи.
 * - **`updatedAt`** — дата последнего обновления записи (null, если данных об изменениях нет).
 * ## Методы:
 * Класс предоставляет доступные методы для получения базовой информации о записи:
 * - `getId()` — возвращает уникальный идентификатор сущности.
 * - `getCreatedAt()` — возвращает дату и время создания записи.
 * - `getUpdatedAt()` — возвращает дату последнего обновления или null, если обновления не было.
 * - `getColumnVal(string $name)` — возвращает значение конкретного столбца по его имени (реализуется в наследниках).
 * ## Аннотации:
 * Класс использует аннотации для указания схемы базы данных с помощью библиотеки `Cycle ORM`.
 * Например:
 * - `#[Column()]` определяет параметры поля таблицы.
 * - `#[Index()]` задаёт индексированные столбцы (например, `created_at` для быстрого поиска).
 * ## Расширяемость:
 * - Класс **абстрактный**. Для его использования необходимо создать наследника и, как минимум,
 * реализовать метод `getColumnVal()`, который позволяет получить значение столбца.
 */
#[Index(columns: ['created_at'])]
abstract class BasisModel {
	/**
	 * ID сущности (уникальный первичный ключ).
	 *
	 * @var int $id
	 */
	#[Column(type: 'bigPrimary', primary: true, autoincrement: true)]
	protected int $id;

	/**
	 * Дата и время создания записи. Автоматически проставляется при создании.
	 *
	 * @var \DateTimeImmutable $createdAt
	 */
	#[Column(type: 'datetime', default: 'CURRENT_TIMESTAMP')]
	protected \DateTimeImmutable $createdAt;

	/**
	 * Дата и время последнего обновления записи. Может быть равна null.
	 * Автоматически обновляется при изменении записи.
	 *
	 * @var \DateTimeImmutable|null $updatedAt
	 */
	#[Column(type: 'datetime', nullable: true, default: 'CURRENT_TIMESTAMP')]
	protected ?\DateTimeImmutable $updatedAt = null;

	/**
	 * Получить уникальный идентификатор сущности.
	 *
	 * @return int Уникальный идентификатор сущности.
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * Получить дату и время создания записи.
	 *
	 * @return \DateTimeImmutable Дата и время создания записи.
	 */
	public function getCreatedAt(): DateTimeImmutable {
		return $this->createdAt;
	}

	/**
	 * Получить дату и время последнего обновления записи, если доступно.
	 *
	 * @return \DateTimeImmutable|null Дата и время последнего обновления или null, если обновления не было.
	 */
	public function getUpdatedAt(): ?DateTimeImmutable {
		return $this->updatedAt;
	}

	/**
	 * Получить значение столбца по его имени.
	 *
	 * @param string $name Имя столбца.
	 *
	 * @return mixed Значение столбца.
	 * @throws \InvalidArgumentException Если имя столбца не найдено.
	 */
	abstract public function getColumnVal(string $name): mixed;

}