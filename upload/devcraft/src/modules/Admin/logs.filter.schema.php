<?php
//===============================================================
// Файл: logs.filter.schema.php                                 =
// Путь: devcraft/src/modules/Admin/logs.filter.schema.php      =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

/**
 * Схема фильтрации и сортировки страницы журнала DevCraft Admin.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
return [
	'sort'     => [
		'default' => 'time',
		'columns' => [
			'id'       => '#',
			'log_type' => __('Тип'),
			'plugin'   => __('Плагин'),
			'fn_name'  => __('Функция'),
			'time'     => __('Время'),
			'message'  => __('Сообщение'),
		],
	],
	'sections' => [
		[
			'title'  => __('Фильтр'),
			'fields' => [
				[
					'id'    => 'plugin',
					'type'  => 'multi',
					'label' => __('Плагин'),
					'metro' => ['db_column' => 'plugin'],
				],
				[
					'id'    => 'log_type',
					'type'  => 'multi',
					'label' => __('Тип'),
					'metro' => ['db_column' => 'log_type'],
				],
				[
					'id'    => 'fn_name',
					'type'  => 'multi',
					'label' => __('Функция'),
					'metro' => ['db_column' => 'fn_name'],
				],
				[
					'id'    => 'message',
					'type'  => 'text',
					'label' => __('Сообщение'),
					'metro' => ['db_column' => 'message'],
				],
				[
					'id'    => 'time',
					'type'  => 'daterange',
					'label' => __('Время'),
					'metro' => ['db_column' => 'time'],
				],
				[
					'id'    => 'id',
					'type'  => 'range',
					'label' => '#',
					'metro' => ['db_column' => 'id'],
				],
			],
		],
	],
];
