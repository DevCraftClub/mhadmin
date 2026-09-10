<?php
//===============================================================
// Файл: AbstractModuleIdentity.php                             =
// Путь: devcraft/src/classes/Abstracts/AbstractModuleIdentity.php
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Abstracts;

use DevCraft\Core\Interfaces\ModuleIdentity;

/**
 * Базовый identity модуля: константы MODULE/CODE у наследника и статические accessors.
 *
 * Наследник обязан объявить `public const string MODULE` и `public const string CODE`.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractModuleIdentity implements ModuleIdentity {

	/**
	 * @since 200.4.0
	 */
	public static function mod(): string {
		return static::MODULE;
	}

	/**
	 * @since 200.4.0
	 */
	public static function code(): string {
		return static::CODE;
	}

}
