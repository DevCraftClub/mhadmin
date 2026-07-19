<?php

declare(strict_types=1);

/**
 * Схема фильтрации и сортировки страницы Composer-пакетов DevCraft Admin.
 *
 * Гидрируется в `FilterSchema` через `FilterSchema::fromArray()` — сам файл
 * возвращает массив в форме, ожидаемой этим методом.
 *
 * @return array{
 *     sort: array{default: string, columns: array<string, string>},
 *     sections: list<array{title: string, fields: list<array{id: string, type: string, label: string, metro?: array<string, mixed>}>}>,
 * }
 */
return [
	'sort'     => [
		'default' => 'package',
		'columns' => [
			'package'   => __('Пакет'),
			'version'   => __('Версия'),
			'plugin'    => __('Модуль'),
			'required'  => __('Обязательный'),
			'installed' => __('Установлен'),
			'app_code'  => __('Код приложения'),
		],
	],
	'sections'     => [
		[
			'title'  => __('Фильтр Composer'),
			'fields' => [
				[
					'id'    => 'package',
					'type'  => 'text',
					'label' => __('Пакет'),
					'metro' => ['db_column' => 'package'],
				],
				[
					'id'    => 'app_code',
					'type'  => 'multi',
					'label' => __('Код приложения'),
					'metro' => ['db_column' => 'appCode'],
				],
				[
					'id'    => 'required',
					'type'  => 'multi',
					'label' => __('Обязательный пакет'),
					'metro' => ['db_column' => 'required'],
				],
				[
					'id'    => 'installed',
					'type'  => 'multi',
					'label' => __('Статус установки'),
					'metro' => ['db_column' => 'installed'],
				],
			],
		],
	],
];
