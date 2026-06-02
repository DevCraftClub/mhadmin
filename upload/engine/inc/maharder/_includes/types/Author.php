<?php

class Author {
	private string $name;
	private array $contacts = [];
	private array $donations = [];

	/**
	 * @param string $name
	 */
	public function __construct(string $name) { $this->name = $name; }

	public function addContact(string $name, string $link): Author {
		$this->contacts[] = [
			'name' => $name,
			'link' => $link,
		];

		return  $this;
	}

	public function addDonation(string $name, string $value, string $link) {
		$this->donations[] = [
			'name' => $name,
			'value' => $value,
			'link' => $link,
		];

		return  $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getContacts(): array {
		return $this->contacts;
	}

	public function getDonations(): array {
		return $this->donations;
	}


}