<?php

declare(strict_types=1);

/**
 * Схема фильтрации списка публичных meta-заголовков.
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
			'name'        => __('Имя'),
			'content'     => __('Содержимое'),
			'module_code' => __('Модуль'),
			'origin'      => __('Источник'),
		],
	],
	'sections' => [
		[
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
		],
	],
];
