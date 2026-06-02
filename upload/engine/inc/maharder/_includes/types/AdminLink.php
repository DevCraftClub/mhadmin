<?php

use JetBrains\PhpStorm\ExpectedValues;

class AdminLink {
	/**
	 * @var ?string $parent Родительский элемент. Устанавливается через метод {@see AdminLink::setParent()}.
	 */
	private ?string $parent = null;

	/**
	 * @var ?string $name Имя ссылки. Устанавливается через метод {@see AdminLink::setName()}
	 * и фильтруется с помощью FILTER_SANITIZE_FULL_SPECIAL_CHARS.
	 */
	private ?string $name = null;

	/**
	 * @var ?string $link URL ссылки. Устанавливается через метод {@see AdminLink::setLink()}
	 * и валидируется с помощью FILTER_VALIDATE_URL.
	 */
	private ?string $link = null;

	/**
	 * @var ?string $type Тип ссылки. Устанавливается через метод {@see AdminLink::setType()}.
	 * Ожидаемые значения: 'link', 'dropdown', 'divider', 'data'.
	 * По умолчанию: 'link'.
	 */
	private ?string $type = 'link';

	/**
	 * @var ?string $extra Дополнительная информация. Устанавливается через метод {@see AdminLink::setExtra()}
	 * и фильтруется с помощью FILTER_SANITIZE_FULL_SPECIAL_CHARS.
	 */
	private ?string $extra = null;

	/**
	 * @var array $children Список дочерних элементов. Устанавливается через метод {@see AdminLink::setChildren()}.
	 */
	private array $children = [];

	/**
	 * Конструктор класса AdminLink.
	 *
	 * Инициализирует объект с переданными параметрами, вызывая соответствующие
	 * методы установки свойств.
	 *
	 * @param string|null $parent   Родительская ссылка или идентификатор родительского элемента.
	 * @param string|null $name     Имя элемента, очищенное с использованием специальных символов.
	 * @param string|null $link     Ссылка для элемента, проверенная на валидный URL.
	 * @param string|null $type     Тип элемента. Ожидаются значения: 'link', 'dropdown', 'divider', 'data'.
	 * @param string|null $extra    Дополнительная информация/атрибуты, очищенные от специальных символов.
	 * @param array       $children Дочерние элементы для вложенности.
	 *
	 * @see AdminLink::setParent()
	 * @see AdminLink::setName()
	 * @see AdminLink::setLink()
	 * @see AdminLink::setType()
	 * @see AdminLink::setExtra()
	 * @see AdminLink::setChildren()
	 */
	public function __construct(?string $parent = null, ?string $name = null, ?string $link = null,
								#[ExpectedValues(values: ['link', 'dropdown', 'divider', 'data'])]
								?string $type = null,
								?string $extra = null, array $children = []) {
		$this->setParent($parent);
		$this->setName($name);
		$this->setLink($link);
		$this->setType($type);
		$this->setExtra($extra);
		$this->setChildren($children);
	}

	/**
	 * Возвращает идентификатор родителя текущего объекта AdminLink.
	 *
	 * @return string|null Идентификатор родителя или null, если родитель не установлен.
	 */
	public function getParent(): ?string {
		return $this->parent;
	}

	/**
	 * Устанавливает родительский идентификатор для ссылки.
	 *
	 * @param string|null $parent Идентификатор родителя или null, если родитель отсутствует.
	 * @return AdminLink Возвращает текущий экземпляр класса для цепочки вызовов.
	 */
	public function setParent(?string $parent): AdminLink {
		$this->parent = $parent;
		return $this;
	}

	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * Устанавливает название ссылки после фильтрации входного значения.
	 *
	 * @param string|null $name Название ссылки или null.
	 * @return AdminLink Возвращает текущий экземпляр класса AdminLink.
	 */
	public function setName(?string $name): AdminLink {
		if ($name) $this->name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		return $this;
	}

	/**
	 * Возвращает значение свойства ссылки.
	 *
	 * @return string|null Вернётся строка с URL или `null`, если ссылка не установлена.
	 * @see AdminLink::setLink() Для установки значения.
	 */
	public function getLink(): ?string {
		return $this->link;
	}

	/**
	 * Устанавливает URL-ссылку для текущего объекта класса `AdminLink`.
	 *
	 * Метод проверяет переданную строку на соответствие формату URL с использованием фильтрации через {@see FILTER_VALIDATE_URL}.
	 * Если переданное значение не является допустимым URL, оно будет проигнорировано.
	 *
	 * @param string|null $link URL-адрес, который необходимо установить.
	 *                           Если передан `null` или ссылка недействительна, значение свойства `$link` не будет изменено.
	 *
	 * @return AdminLink Возвращает текущий экземпляр объекта `AdminLink` для возможности цепочки вызовов.
	 *
	 * @see AdminLink::$link Свойство, которое устанавливается данным методом.
	 * @see AdminLink::getLink() Для получения текущего значения ссылки.
	 */
	public function setLink(?string $link): AdminLink {
		if ($link) $this->link = filter_var(DataManager::normalizeUrl($link), FILTER_VALIDATE_URL);
		return $this;
	}

	/**
	 * Возвращает текущий тип элемента.
	 *
	 * Тип может принимать следующие значения: 'link', 'dropdown', 'divider', 'data',
	 * или null, если он не был установлен.
	 *
	 * @return string|null Текущий тип элемента или null, если тип не задан.
	 * @see AdminLink::setType()
	 * @see AdminLink::$type
	 */
	public function getType(): ?string {
		return $this->type;
	}

	/**
	 * Устанавливает тип текущего объекта `AdminLink`.
	 *
	 * Метод позволяет задать тип ссылки из набора допустимых значений.
	 * Если значение передано и соответствует одному из допустимых значений,
	 * оно будет установлено в соответствующее свойство объекта.
	 *
	 * @param string|null $type Тип ссылки. Допустимые значения: `link`, `dropdown`, `divider`, `data`.
	 *                           Если значение не передано, тип останется неизменным.
	 *
	 * @return AdminLink Возвращает текущий экземпляр `AdminLink` для возможности цепочки вызовов.
	 *
	 * @see AdminLink::$type Свойство, которое устанавливается данным методом.
	 * @see AdminLink::getType() Для получения текущего значения типа.
	 */
	public function setType(#[ExpectedValues(values: [
		'link',
		'dropdown',
		'divider',
		'data'
	])] ?string $type): AdminLink {
		if ($type) $this->type = $type;
		return $this;
	}

	/**
	 * Возвращает массив детей текущего объекта.
	 *
	 * @return array Список объектов, являющихся потомками текущего объекта.
	 * @see AdminLink::$children
	 * @see AdminLink::setChildren()
	 */
	public function getChildren(): array {
		return $this->children;
	}

	/**
	 * Устанавливает массив дочерних элементов для текущего объекта.
	 *
	 * Этот метод заменяет существующий массив дочерних элементов переданным массивом.
	 *
	 * @param array $children Массив дочерних объектов, ассоциированных с текущим объектом.
	 *
	 * @return AdminLink Возвращает текущий экземпляр класса для возможности цепочки вызовов.
	 *
	 * @see AdminLink::getChildren() Для получения текущего массива дочерних элементов.
	 * @see AdminLink::addChild() Для добавления дочерних элементов по одному.
	 */
	public function setChildren(array $children): AdminLink {
		$this->children = $children;
		return $this;
	}

	/**
	 * Добавляет дочернюю ссылку к текущему объекту `AdminLink`.
	 *
	 * Если переданная ссылка имеет родительский идентификатор (`parentId`), проверяется, существует ли такой
	 * дочерний элемент у текущего объекта. В случае его существования вызов метода продолжается рекурсивно
	 * для добавления дочерней ссылки. Если дочерний элемент отсутствует, ссылка добавляется как новый дочерний элемент.
	 * Если у переданной ссылки нет родительского идентификатора, она добавляется в общий список дочерних элементов.
	 *
	 * @param AdminLink $link Ссылка, которая должна быть добавлена к текущему объекту в качестве дочерней.
	 *                        Объект будет обработан для проверки иерархии по родительскому идентификатору.
	 *
	 * @return AdminLink Возвращает текущий экземпляр `AdminLink` для возможности дальнейшей цепочки вызовов.
	 *
	 * @see AdminLink::getParent() Для получения идентификатора родительской ссылки.
	 * @see AdminLink::getChildren() Для работы с массивом существующих дочерних ссылок.
	 */
	public function addChild(AdminLink $link): AdminLink {
		$parentId = $link->getParent();
		$children = $this->getChildren();

		// Упростим проверку существования parentId и логики children
		if ($parentId) {
			if (isset($children[$parentId])) {
				$children[$parentId]->addChild($link); // Рекурсивно добавляем потомка
			} else {
				$children[$parentId] = $link; // Если такого дочернего узла еще нет, создаем
			}
		} else {
			// Если parentId отсутствует, добавляем в общий список
			$children[] = $link;
		}

		$this->setChildren($children); // Явно обновляем массив children
		return $this; // Возвращаем текущий объект для цепочки
	}

	/**
	 * Возвращает значение дополнительного параметра.
	 *
	 * @return string|null Дополнительный параметр или null, если он не установлен.
	 */
	public function getExtra(): ?string {
		return $this->extra;
	}

	/**
	 * Устанавливает значение дополнительного параметра и применяет фильтрацию.
	 *
	 * @param string|null $extra Дополнительный параметр, который нужно установить.
	 *                            Если передан, значение будет отфильтровано через {@see FILTER_SANITIZE_FULL_SPECIAL_CHARS}.
	 *
	 * @return AdminLink Возвращает текущий экземпляр класса для цепочки вызовов.
	 */
	public function setExtra(?string $extra): AdminLink {
		if ($extra) $this->extra = filter_var($extra, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		return $this;
	}

}