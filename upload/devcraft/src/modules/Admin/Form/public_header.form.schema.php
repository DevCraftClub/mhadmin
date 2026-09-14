<?php

declare(strict_types=1);

use DevCraft\Form\FormSchemaBuilder;

/**
 * Схема формы ручного добавления публичного meta-заголовка.
 *
 * @return \DevCraft\Types\FormSchema
 */
return FormSchemaBuilder::create('admin_public_header_form')
	->section(__('Новый заголовок'))
		->text('name', __('Имя (name)'))
			->description(__('Атрибут <code>name</code> тега meta.'))
			->metro(['required' => true])
			->default('')
		->text('content', __('Содержимое'))
			->description(__('Атрибут <code>content</code> тега meta.'))
			->metro(['required' => true])
			->default('')
	->build();
