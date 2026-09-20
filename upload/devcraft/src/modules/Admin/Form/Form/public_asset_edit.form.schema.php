<?php

declare(strict_types=1);

use DevCraft\Form\FormSchemaBuilder;

/**
 * Схема страницы правки публичного CSS/JS.
 *
 * @return \DevCraft\Types\FormSchema
 */
return FormSchemaBuilder::create('admin_public_asset_edit_form')
	->section(__('Запись'))
		->hidden('id', '')
		->hidden('kind', '')
		->text('label', __('Метка'))
			->default('')
		->text('local_path', __('Локальный путь'))
			->description(__('Путь от корня сайта.'))
			->default('')
		->text('source_url', __('Или HTTPS URL'))
			->default('')
		->checkbox('active', __('Активен'))
			->default(true)
	->section(__('Зависимости и показ'))
		->multi('depends_on', __('Зависит от'))
			->description(__('Записи той же категории, которые должны идти раньше.'))
		->multi('available', __('Разделы показа'))
			->description(__('Пустой список — на всех разделах сайта.'))
	->build();
