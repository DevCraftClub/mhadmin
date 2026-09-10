<?php
//===============================================================
// Файл: ModuleIdentity.php                                     =
// Путь: devcraft/src/classes/Interfaces/ModuleIdentity.php     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Interfaces;

/**
 * Контракт identity модуля: DLE `mod` и внутренний `code` (конфиг / codename).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface ModuleIdentity {

	/**
	 * DLE-ключ модуля (`?mod=…`, AdminLink, registry).
	 *
	 * @since 200.4.0
	 */
	public static function mod(): string;

	/**
	 * Внутренний код модуля (config JSON, FormSchema codename, legacy confName).
	 *
	 * @since 200.4.0
	 */
	public static function code(): string;

}
