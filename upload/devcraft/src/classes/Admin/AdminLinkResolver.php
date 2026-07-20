<?php
//===============================================================
// Файл: AdminLinkResolver.php                                  =
// Путь: devcraft/src/classes/Admin/AdminLinkResolver.php       =
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

namespace DevCraft\Core\Admin;

use DevCraft\Types\AdminLink;
use InvalidArgumentException;

/**
 * Разрешает действия и классы страниц по дереву пунктов меню.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class AdminLinkResolver {

	/**
	 * Находит класс страницы по имени действия в дереве меню.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminLink[]  $menu    Дерево пунктов меню.
	 * @param   string       $action  Искомое действие.
	 *
	 * @return string|null FQCN обработчика страницы или null.
	 *
	 * @example
	 *     $class = AdminLinkResolver::resolvePageClass($menu, 'settings');
	 */
	public static function resolvePageClass(array $menu, string $action): ?string {
		foreach($menu as $link) {
			if($link->action === $action && $link->pageClass !== NULL) {
				return $link->pageClass;
			}

			if($link->children !== []) {
				$resolved = self::resolvePageClass($link->children, $action);

				if($resolved !== NULL) {
					return $resolved;
				}
			}
		}

		return NULL;
	}

	/**
	 * Возвращает пункты меню плагина без изменений (точка расширения).
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminLink[]  $menu  Дерево пунктов меню.
	 *
	 * @return AdminLink[] Тот же массив ссылок.
	 *
	 * @example
	 *     $links = AdminLinkResolver::pluginLinks($plugin->menu());
	 */
	public static function pluginLinks(array $menu): array {
		return $menu;
	}

	/**
	 * Определяет стартовое действие модуля по доступным пунктам меню.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminLink[]  $menu  Дерево пунктов меню.
	 *
	 * @return string|null Имя действия dashboard/index или null.
	 *
	 * @example
	 *     $action = AdminLinkResolver::defaultAction($menu);
	 */
	public static function defaultAction(array $menu): ?string {
		$actions = self::collectActions($menu);

		if(in_array('dashboard', $actions, true)) {
			return 'dashboard';
		}

		if(in_array('index', $actions, true)) {
			return 'dashboard';
		}

		return NULL;
	}

	/**
	 * Собирает все имена действий из дерева меню рекурсивно.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminLink[]  $menu  Дерево пунктов меню.
	 *
	 * @return string[] Список имён действий.
	 *
	 * @example
	 *     $actions = AdminLinkResolver::collectActions($menu);
	 */
	public static function collectActions(array $menu): array {
		$actions = [];

		foreach($menu as $link) {
			if($link->action !== NULL) {
				$actions[] = $link->action;
			}

			if($link->children !== []) {
				$actions = array_merge($actions, self::collectActions($link->children));
			}
		}

		return $actions;
	}

	/**
	 * Проверяет, что в меню не объявлены одновременно dashboard и index.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminLink[]  $menu  Дерево пунктов меню.
	 *
	 * @example
	 *     AdminLinkResolver::validateStartActions($menu);
	 */
	public static function validateStartActions(array $menu): void {
		$actions = self::collectActions($menu);

		if(in_array('dashboard', $actions, true) && in_array('index', $actions, true)) {
			throw new InvalidArgumentException(
				__('Меню манифеста не должно объявлять одновременно стартовые действия dashboard и index.'),
			);
		}
	}

}
