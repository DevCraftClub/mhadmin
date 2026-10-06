<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use DevCraft\Core\Abstracts\AbstractEntity;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;

/**
 * Запись списка публичных стилей или скриптов оболочки сайта.
 */
#[Entity(
	role: 'dc_public_asset',
	repository: PublicAssetEntryRepository::class,
	table: 'dc_public_assets',
)]
#[Index(columns: ['kind', 'sort_order'], name: 'idx_dc_pub_asset_kind_sort')]
#[Index(columns: ['module_code'], name: 'idx_dc_pub_asset_module')]
class PublicAssetEntry extends AbstractEntity {

	use PublicResourceDepsColumnsTrait;

	/** css | js */
	#[Column(type: 'string', size: 8)]
	public string $kind = 'js';

	/** auto | manual */
	#[Column(type: 'string', size: 16)]
	public string $origin = 'manual';

	#[Column(type: 'string', size: 64, nullable: true, default: null)]
	public ?string $module_code = null;

	#[Column(type: 'string', size: 255, nullable: true, default: null)]
	public ?string $label = null;

	#[Column(type: 'string', size: 512, nullable: true, default: null)]
	public ?string $source_path = null;

	#[Column(type: 'string', size: 1024, nullable: true, default: null)]
	public ?string $source_url = null;

	/** Не индексировать: utf8mb4 × 1024 превышает лимит ключа MySQL (3072 байта). */
	#[Column(type: 'string', size: 1024)]
	public string $local_path = '';

	#[Column(type: 'boolean', default: true)]
	public bool $active = true;

	#[Column(type: 'integer', unsigned: true, default: 0)]
	public int $sort_order = 0;

	public function __construct() {
		$this->createdAt = new \DateTimeImmutable();
	}

}
