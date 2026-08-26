<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\BreadCrumb;

/**
 * Fluent-строитель BreadCrumb.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class BreadCrumbBuilder {

	private string $title = '';

	private ?string $url = NULL;

	public static function create(string $title = ''): self {
		$self        = new self();
		$self->title = $title;

		return $self;
	}

	public function title(string $title): self {
		$this->title = $title;

		return $this;
	}

	public function url(?string $url): self {
		$this->url = $url;

		return $this;
	}

	public function build(): BreadCrumb {
		return new BreadCrumb($this->title, $this->url);
	}

}
