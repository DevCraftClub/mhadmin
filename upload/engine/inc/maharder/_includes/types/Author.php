<?php

/**
 * Класс, представляющий автора с именем, списком контактов и пожертвований.
 */
class Author {
	private string $name;
	private array  $contacts  = [];
	private array  $donations = [];


	/**
	 * Инициализирует новый экземпляр класса Author с указанным именем.
	 *
	 * @param string $name Имя автора.
	 */
	public function __construct(string $name) {
		$this->name = $name;
	}

	/**
	 * Добавляет контакт в список контактов автора.
	 *
	 * @param string $name Имя контакта.
	 * @param string $link Ссылка или контактная информация.
	 *
	 * @return Author Текущий экземпляр класса для цепочки вызовов.
	 */
	public function addContact(string $name, string $link): Author {
		$this->contacts[] = [
			'name' => $name,
			'link' => $link,
		];

		return $this;
	}

	/**
	 * Добавляет пожертвование в список пожертвований автора.
	 *
	 * @param string $name  Имя донора или источник пожертвования.
	 * @param string $value Сумма или размер пожертвования.
	 * @param string $link  Ссылка или контактная информация источника.
	 *
	 * @return Author Текущий экземпляр класса для цепочки вызовов.
	 */
	public function addDonation(string $name, string $value, string $link) {
		$this->donations[] = [
			'name'  => $name,
			'value' => $value,
			'link'  => $link,
		];

		return $this;
	}

	/**
	 * Получает имя автора.
	 *
	 * @return string Имя автора.
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Получает список контактов автора.
	 *
	 * @return array Массив контактов, где каждый элемент — это ассоциативный массив с ключами 'name' и 'link'.
	 */
	public function getContacts(): array {
		return $this->contacts;
	}

	/**
	 * Получает список пожертвований автора.
	 *
	 * @return array Массив пожертвований, где каждый элемент — это ассоциативный массив с ключами
	 *               'name', 'value' и 'link'.
	 */
	public function getDonations(): array {
		return $this->donations;
	}


}