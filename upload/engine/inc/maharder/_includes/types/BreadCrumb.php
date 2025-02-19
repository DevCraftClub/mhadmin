<?php

class BreadCrumb {
	/**
	 * Хранит название элемента хлебных крошек.
	 *
	 * @var string|null Название элемента или null, если оно не задано.
	 * @see \BreadCrumb::setName()
	 * @see \BreadCrumb::getName()
	 */
	private ?string $name;
	/**
	 * Ссылка, ассоциированная с хлебной крошкой.
	 *
	 * @var string|null
	 * @see \BreadCrumb::setLink()
	 * @see \BreadCrumb::getLink()
	 */
	private ?string $link = null;

	/**
	 * @param string|null $name
	 * @param string|null $link
	 */
	public function __construct(?string $name, ?string $link) {
		$this->setName($name);
		$this->setLink($link);
	}

	/**
	 * Возвращает имя текущего элемента хлебных крошек.
	 *
	 * @return string|null Имя элемента или null, если имя не установлено.
	 *
	 * @see BreadCrumb::$name
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * Устанавливает имя текущего элемента хлебных крошек.
	 *
	 * @param string|null $name Имя, которое будет установлено. Может быть null.
	 *
	 * @return BreadCrumb Возвращает текущий объект для возможности цепочного вызова методов.
	 *
	 * @see BreadCrumb::$name
	 */
	public function setName(?string $name): BreadCrumb {
		$this->name = $name;
		return $this;
	}

	/**
	 * Возвращает ссылку, связанную с текущим элементом хлебных крошек.
	 *
	 * @return string|null Ссылка или null, если ссылка не установлена.
	 *
	 * @see BreadCrumb::$link
	 */
	public function getLink(): ?string {
		return $this->link;
	}

	/**
	 * Устанавливает ссылку для текущего элемента хлебных крошек.
	 *
	 * @param string|null $link Ссылка, которая будет установлена. Может быть null.
	 *
	 * @return BreadCrumb Возвращает текущий объект для возможности цепочного вызова методов.
	 *
	 * @see BreadCrumb::$link
	 */
	public function setLink(?string $link): BreadCrumb {
		$this->link = $link;
		return $this;
	}

}