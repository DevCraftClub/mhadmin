<?php

	//===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===
	// Mod: MaHarder_Admin
	// File: main.php
	// Path: Expected hash. name evaluated instead to freemarker.template.SimpleScalar on line 6, column 12 in Templates/Scripting/EmptyPHP.php.
	// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  =
	// Author: Maxim Harder <maxim_harder@jas.de> ( c ) 2020
	// Website: https://www.jas.de
	// Telegram: http://t.me/MaHarder
	// ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  =
	// Do not change anything!
	//===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===

	// Как добавить свои стили?
	// 1. Вариант
	// добавляем дополнительные стили в существующий массив: $variables['css'][] = htmlStatic( 'путь/к/стилям.css' );
	// тем самым добавив новый стиль к существующим
	//
	// 2. Вариант
	// создаём новый массив с новыми стилями и перезаписываем переменную для рендера
	// $variables['css'] = htmlStatic( $newCssArray );

	// Как добавить свои скрипты?
	// 1. Вариант
	// добавляем дополнительные скрипты в существующий массив: $variables['js'][] = htmlStatic( 'путь/к/скриптам.js', 'html', 'js' );
	// тем самым добавив новый скрипт к существующим
	//
	// 2. Вариант
	// создаём новый массив с новыми скриптами и перезаписываем переменную для рендера
	// $variables['js'] = htmlStatic( $newJssArray, 'js' );
	$langs_data = [];

	foreach(MhTranslation::getFormattedLanguageList() as $t => $s) {
		$langs_data[$s['tag']] = $s['name'];
	}

	$modVars = [
		'title' => __('Настройки модуля'),
		'lang_data' => $langs_data
	];

	$htmlTemplate = 'admin/main.html';
