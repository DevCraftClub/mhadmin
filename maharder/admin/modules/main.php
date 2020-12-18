<?php

//===============================================================
// Mod: MaHarder_Admin
// File: main.php
// Path: Expected hash. name evaluated instead to freemarker.template.SimpleScalar on line 6, column 12 in Templates/Scripting/EmptyPHP.php.
// ============================================================ =
// Author: Maxim Harder <maxim_harder@jas.de> (c) 2020
// Website: https://www.jas.de
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!
//===============================================================

// Как добавить свои стили?
// 1. Вариант
// добавляем дополнительные стили в существующий массив: $variables['css'][] = htmlStatic("путь/к/стилям.css");
// тем самым добавив новый стиль к существующим
//
// 2. Вариант
// создаём новый массив с новыми стилями и перезаписываем переменную для рендера
// $variables['css'] = htmlStatic($newCssArray);

// Как добавить свои сkripty?
// 1. Вариант
// добавляем дополнительные скрипты в существующий массив: $variables['js'][] = htmlStatic("путь/к/скриптам.js", "html", "js");
// тем самым добавив новый скрипт к существующим
//
// 2. Вариант
// создаём новый массив с новыми скриптами и перезаписываем переменную для рендера
// $variables['js'] = htmlStatic($newJssArray, "js");

$modVars = [
	'title'				 => 'Basic Template',
	'module_name'		 => 'Testmodule',
	'module_version'	 => '1.0.0',
	'module_description' => 'Testmodule description',
	'module_icon'		 => 'fad fa-robot',
	'modindex'			 => $_SERVER['PHP_SELF'],
	'changelog'			 => $_SERVER['PHP_SELF'] . '?site=changelog',
	'site_link'	 => '',
	'docs_link'	 => '',
	'links'		 => [
		[
			'name'	 => 'Test 1',
			'href'	 => '#1',
			'type'	 => 'link',
		],
		[
			'name'	 => 'Test 6',
			'href'	 => '#6',
			'type'	 => 'link',
		],
		[
			'name'		 => 'Test 2',
			'href'		 => '#2',
			'type'		 => 'dropdown',
			'children'	 => [
				[
					'name'		 => 'Test 3',
					'href'		 => '#3',
					'type'		 => 'dropdown',
					'children'	 => [
						[
							'name'	 => 'Test 4',
							'href'	 => '#4',
							'type'	 => 'link'
						],
						[
							'name'	 => 'Test 5',
							'href'	 => '#5',
							'type'	 => 'link'
						],
					],
				],
				[
					'name'		 => 'Test 7',
					'href'		 => '#7',
					'type'		 => 'dropdown',
					'children'	 => [
						[
							'name'	 => 'Test 8',
							'href'	 => '#8',
							'type'	 => 'link'
						],
						[
							'name'	 => 'Test 9',
							'href'	 => '#9',
							'type'	 => 'link'
						],
					]
				]
			]
		],
		[
			'name'		 => 'Test 10',
			'href'		 => '#10',
			'type'		 => 'dropdown',
			'children'	 => [
				[
					'name'		 => 'Test 11',
					'href'		 => '#11',
					'type'		 => 'dropdown',
					'children'	 => [
						[
							'name'	 => 'Test 12',
							'href'	 => '#12',
							'type'	 => 'link'
						],
						[
							'name'	 => 'Test 13',
							'href'	 => '#13',
							'type'	 => 'link'
						],
					],
				],
				[
					'name'		 => 'Test 14',
					'href'		 => '#14',
					'type'		 => 'dropdown',
					'children'	 => [
						[
							'name'	 => 'Test 15',
							'href'	 => '#15',
							'type'	 => 'link'
						],
						[
							'name'	 => 'Test 16',
							'href'	 => '#16',
							'type'	 => 'link'
						],
					]
				]
			]
		],
	]
];

$htmlTemplate = 'modules/main.html';