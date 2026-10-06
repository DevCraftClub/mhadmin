<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use DevCraft\Core\Abstracts\AbstractEntity;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;

/**
 * Запись страницы публичных заголовков (meta name/content).
 */
#[Entity(
	role: 'dc_public_header',
	repository: PublicHeaderEntryRepository::class,
	table: 'dc_public_headers',
)]
#[Index(columns: ['sort_order'], name: 'idx_dc_pub_header_sort')]
#[Index(columns: ['module_code'], name: 'idx_dc_pub_header_module')]
class PublicHeaderEntry extends AbstractEntity {

	use PublicResourceDepsColumnsTrait;

	/** auto | manual */
	#[Column(type: 'string', size: 16)]
	public string $origin = 'manual';

	#[Column(type: 'string', size: 64, nullable: true, default: null)]
	public ?string $module_code = null;

	#[Column(type: 'string', size: 255)]
	public string $name = '';

	#[Column(type: 'text')]
	public string $content = '';

	#[Column(type: 'boolean', default: true)]
	public bool $active = true;

	#[Column(type: 'integer', unsigned: true, default: 0)]
	public int $sort_order = 0;

	public function __construct() {
		$this->createdAt = new \DateTimeImmutable();
	}

}
