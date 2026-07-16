<?php

declare(strict_types=1);

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
