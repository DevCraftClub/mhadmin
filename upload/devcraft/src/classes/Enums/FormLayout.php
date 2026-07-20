<?php

namespace DevCraft\Core\Enums;

/**
 * Вариант раскладки декларативной формы настроек (`FormSchemaBuilder`).
 *
 * @package DevCraft
 * @since   200.4.0
 */
enum FormLayout {

	/** Секции формы отображаются вкладками Metro UI. */
	case TABS;

	/** Секции выводятся вертикальным стеком. */
	case STACK;

	/** Секции сворачиваются в аккордеон. */
	case ACCORDION;

}
