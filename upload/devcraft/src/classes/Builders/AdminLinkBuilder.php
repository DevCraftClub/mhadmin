<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\AdminLink;

/**
 * Fluent-обёртка над фабриками AdminLink (page / hidden / divider).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class AdminLinkBuilder {

	/**
	 * Ссылка на страницу модуля.
	 *
	 * @param   class-string  $pageClass
	 */
	public static function page(
		string  $name,
		string  $action,
		string  $pageClass,
		?string $extra = NULL,
		string  $mod = 'devcraft',
	): AdminLink {
		return AdminLink::page($name, $action, $pageClass, $extra, $mod);
	}

	/**
	 * Страница без пункта меню.
	 *
	 * @param   class-string  $pageClass
	 */
	public static function hidden(string $action, string $pageClass): AdminLink {
		return AdminLink::hidden($action, $pageClass);
	}

	/**
	 * Разделитель меню.
	 */
	public static function divider(?string $header = NULL): AdminLink {
		return AdminLink::divider($header);
	}

}
