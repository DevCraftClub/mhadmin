<?php

declare(strict_types=1);

namespace DevCraft\Core\Debug;

use DevCraft\Core\Application;

/**
 * Мост к панели отладки Admin без жёсткой зависимости Core → Modules\Admin.
 *
 * Если класса панели нет — вызовы ничего не делают.
 */
final class DebugHooks {

	private const string TOOLBAR = 'DevCraft\\Modules\\Admin\\DebugToolbar\\ToolbarService';

	/**
	 * Метка времени для таймлайна панели (no-op без Admin).
	 */
	public static function mark(string $name): void {
		$class = self::toolbarClass();

		if($class === null) {
			return;
		}

		$class::mark($name);
	}

	/**
	 * Запуск сбора панели на поверхности admin|public.
	 */
	public static function boot(string $surface): void {
		$class = self::toolbarClass();

		if($class === null) {
			return;
		}

		$class::instance()->boot($surface);
	}

	/**
	 * Ранний хук native admin.php.
	 */
	public static function hookDleAdmin(): void {
		$class = self::toolbarClass();

		if($class === null) {
			return;
		}

		$class::instance()->hookDleAdmin();
	}

	public static function isBooted(): bool {
		$class = self::toolbarClass();

		return $class !== null && $class::instance()->isBooted();
	}

	/**
	 * Подключить Cycle SQL-tap, если шлюз БД уже создан.
	 */
	public static function refreshCycleSqlTap(): void {
		try {
			Application::instance()->notifyDebugSqlTap();
		} catch(\Throwable) {
			// Boot панели не должен требовать ORM.
		}
	}

	/**
	 * @return class-string|null
	 */
	private static function toolbarClass(): ?string {
		$class = self::TOOLBAR;

		return class_exists($class) ? $class : null;
	}

}
