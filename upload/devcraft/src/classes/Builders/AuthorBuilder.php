<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\Author;

/**
 * Fluent-строитель блока автора модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class AuthorBuilder {

	/** @var array<int, array{name: string, link: string}> */
	private array $contacts = [];

	/** @var array<int, array{name: string, value: string, link: string}> */
	private array $donations = [];

	private function __construct(
		private string $name,
	) {}

	public static function create(string $name): self {
		return new self($name);
	}

	public function name(string $name): self {
		$this->name = $name;

		return $this;
	}

	public function contact(string $name, string $link): self {
		$this->contacts[] = ['name' => $name, 'link' => $link];

		return $this;
	}

	/**
	 * @param   array<int, array{name: string, link: string}>  $contacts
	 */
	public function contacts(array $contacts): self {
		foreach($contacts as $contact) {
			if(is_array($contact)) {
				$this->contact((string) ($contact['name'] ?? ''), (string) ($contact['link'] ?? ''));
			}
		}

		return $this;
	}

	public function donation(string $name, string $value, string $link = ''): self {
		$this->donations[] = ['name' => $name, 'value' => $value, 'link' => $link];

		return $this;
	}

	/**
	 * @param   array<int, array{name: string, value: string, link?: string}>  $donations
	 */
	public function donations(array $donations): self {
		foreach($donations as $donation) {
			if(is_array($donation)) {
				$this->donation(
					(string) ($donation['name'] ?? ''),
					(string) ($donation['value'] ?? ''),
					(string) ($donation['link'] ?? ''),
				);
			}
		}

		return $this;
	}

	public function build(): Author {
		return new Author(
			name     : $this->name,
			contacts : $this->contacts,
			donations: $this->donations,
		);
	}

}
