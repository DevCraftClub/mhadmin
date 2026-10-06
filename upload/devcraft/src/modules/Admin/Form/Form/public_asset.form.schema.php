<?php

declare(strict_types=1);

use DevCraft\Form\FormSchemaBuilder;

/**
 * Схема формы ручного добавления публичного CSS/JS.
 *
 * @return \DevCraft\Types\FormSchema
 */
return FormSchemaBuilder::create('admin_public_asset_form')
	->section(__('Новая запись'))
		->hidden('kind', '')
			->default('')
		->text('label', __('Метка'))
			->description(__('Короткое имя для списка в админке.'))
			->default('')
		->text('local_path', __('Локальный путь'))
			->description(__('Путь от корня сайта, например <code>devcraft/src/...</code>.'))
			->metro(['placeholder' => 'devcraft/...'])
			->default('')
		->text('source_url', __('Или HTTPS URL'))
			->description(__('Внешний файл будет скачан в кэш (HTTPS, CSS/JS).'))
			->metro(['placeholder' => 'https://...', 'type' => 'url'])
			->default('')
	->build();
