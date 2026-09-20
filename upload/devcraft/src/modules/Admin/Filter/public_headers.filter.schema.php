<?php

declare(strict_types=1);

use DevCraft\Builders\FilterSchemaBuilder;
use DevCraft\Types\FormSection;

/**
 * Схема фильтрации списка публичных meta-заголовков.
 */
return FilterSchemaBuilder::create()
	->defaultOrder('sort_order')
	->sortColumns([
		'sort_order'  => __('Порядок'),
		'name'        => __('Имя'),
		'content'     => __('Содержимое'),
		'module_code' => __('Модуль'),
		'origin'      => __('Источник'),
	])
	->addSection(FormSection::fromArray([
		'title'  => __('Фильтр'),
		'fields' => [
			[
				'id'    => 'name',
				'type'  => 'text',
				'label' => __('Имя (name)'),
				'metro' => ['db_column' => 'name'],
			],
			[
				'id'    => 'content',
				'type'  => 'text',
				'label' => __('Содержимое'),
				'metro' => ['db_column' => 'content'],
			],
			[
				'id'    => 'module_code',
				'type'  => 'text',
				'label' => __('Модуль'),
				'metro' => ['db_column' => 'module_code'],
			],
			[
				'id'    => 'origin',
				'type'  => 'multi',
				'label' => __('Источник'),
				'metro' => ['db_column' => 'origin'],
			],
		],
	]))
	->build();
