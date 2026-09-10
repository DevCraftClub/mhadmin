<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DateTimeImmutable;
use DevCraft\Core\Enums\ChangelogChangeType;
use DevCraft\Types\Changelog;
use DevCraft\Types\ChangelogChange;

/**
 * Fluent-строитель одной записи changelog.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class ChangelogBuilder {

	/** @var array<string, ChangelogChange[]> */
	private array $grouped = [];

	private ?DateTimeImmutable $date = NULL;

	private function __construct(
		private string $version,
	) {}

	public static function create(string $version): self {
		return new self($version);
	}

	public function date(string|DateTimeImmutable|null $date): self {
		if($date instanceof DateTimeImmutable) {
			$this->date = $date;
		} elseif(is_string($date) && $date !== '') {
			$this->date = new DateTimeImmutable($date);
		} else {
			$this->date = NULL;
		}

		return $this;
	}

	/**
	 * Добавляет пункты в группу (ключ типа: added, changed, fixed, …).
	 *
	 * @param   list<string>|string  $texts
	 */
	public function changes(string $typeKey, array|string $texts): self {
		$type = ChangelogChangeType::fromKey($typeKey);

		foreach((array) $texts as $text) {
			if(!is_string($text) || trim($text) === '') {
				continue;
			}

			$this->grouped[$type->key()][] = new ChangelogChange($type, $text);
		}

		return $this;
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function added(array|string $texts): self {
		return $this->changes('added', $texts);
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function changed(array|string $texts): self {
		return $this->changes('changed', $texts);
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function fixed(array|string $texts): self {
		return $this->changes('fixed', $texts);
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function removed(array|string $texts): self {
		return $this->changes('removed', $texts);
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function security(array|string $texts): self {
		return $this->changes('security', $texts);
	}

	/**
	 * @param   list<string>|string  $texts
	 */
	public function deprecated(array|string $texts): self {
		return $this->changes('deprecated', $texts);
	}

	public function build(): Changelog {
		return new Changelog($this->version, $this->date, $this->grouped);
	}

}
