<?php

declare(strict_types=1);

/**
 * Схема фильтрации списков публичных CSS/JS.
 *
 * @return array{
 *     sort: array{default: string, columns: array<string, string>},
 *     sections: list<array{title: string, fields: list<array{id: string, type: string, label: string, metro?: array<string, mixed>}>}>,
 * }
 */
return [
	'sort'     => [
		'default' => 'sort_order',
		'columns' => [
			'sort_order'  => __('Порядок'),
			'label'       => __('Метка'),
			'local_path'  => __('Путь'),
			'module_code' => __('Модуль'),
			'origin'      => __('Источник'),
		],
	],
	'sections' => [
		[
			'title'  => __('Фильтр'),
			'fields' => [
				[
					'id'    => 'label',
					'type'  => 'text',
					'label' => __('Метка'),
					'metro' => ['db_column' => 'label'],
				],
				[
					'id'    => 'local_path',
					'type'  => 'text',
					'label' => __('Путь'),
					'metro' => ['db_column' => 'local_path'],
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
		],
	],
];
