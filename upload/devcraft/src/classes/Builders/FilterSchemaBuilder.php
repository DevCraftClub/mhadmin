<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\FilterSchema;
use DevCraft\Types\FormSection;

/**
 * Fluent-строитель FilterSchema.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class FilterSchemaBuilder {

	/** @var FormSection[] */
	private array $sections = [];

	private string $defaultOrder = 'time';

	/** @var array<string, string> */
	private array $sortColumns = [];

	public static function create(): self {
		return new self();
	}

	/**
	 * @param   list<FormSection>  $sections
	 */
	public function sections(array $sections): self {
		foreach($sections as $section) {
			if($section instanceof FormSection) {
				$this->sections[] = $section;
			}
		}

		return $this;
	}

	public function addSection(FormSection $section): self {
		$this->sections[] = $section;

		return $this;
	}

	public function defaultOrder(string $column): self {
		$this->defaultOrder = $column;

		return $this;
	}

	/**
	 * @param   array<string, string>  $columns
	 */
	public function sortColumns(array $columns): self {
		$this->sortColumns = $columns;

		return $this;
	}

	public function build(): FilterSchema {
		return new FilterSchema(
			sections    : $this->sections,
			defaultOrder: $this->defaultOrder,
			sortColumns : $this->sortColumns,
		);
	}

}
