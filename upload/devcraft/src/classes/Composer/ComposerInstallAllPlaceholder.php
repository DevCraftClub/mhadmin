<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * Виртуальная строка Composer UI: одна кнопка ставит все пакеты из `composer.json` (`composer install`).
 *
 * Не пишется в БД — только в таблицу / панель, пока есть неустановленные зависимости.
 */
final class ComposerInstallAllPlaceholder {

	public const PACKAGE = 'devcraft/composer-install-all';

	public static function is(string $packageName): bool {
		return $packageName === self::PACKAGE;
	}

	/** Человекочитаемое имя в колонке «Пакет». */
	public static function label(): string {
		return __('Все пакеты из composer.json');
	}

}
