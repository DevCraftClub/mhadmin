<?php

declare(strict_types=1);

use DevCraft\Form\FormSchemaBuilder;

/**
 * Схема страницы правки публичного meta-заголовка.
 *
 * @return \DevCraft\Types\FormSchema
 */
return FormSchemaBuilder::create('admin_public_header_edit_form')
	->section(__('Заголовок'))
		->hidden('id', '')
		->text('name', __('Имя (name)'))
			->default('')
		->textarea('content', __('Содержимое'))
			->default('')
		->checkbox('active', __('Активен'))
			->default(true)
	->section(__('Зависимости и показ'))
		->multi('depends_on', __('Зависит от'))
		->multi('available', __('Разделы показа'))
			->description(__('Пустой список — на всех разделах сайта.'))
	->build();
