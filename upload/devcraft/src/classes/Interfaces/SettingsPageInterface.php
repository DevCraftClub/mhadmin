<?php
//===============================================================
// Файл: SettingsPageInterface.php                              =
// Путь: devcraft/src/classes/Interfaces/SettingsPageInterface.…=
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

namespace DevCraft\Core\Interfaces;

/**
 * Контракт страницы настроек модуля с дополнительными данными формы.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface SettingsPageInterface {

	/**
	 * Дополняет данные формы настроек значениями, специфичными для страницы.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, array<string, string>> Карта секций и полей с дополнительными значениями.
	 *
	 * @example
	 *     $extra = $settingsPage->supplementFormData();
	 *     $formData = array_merge($baseData, $extra);
	 */
	public function supplementFormData(): array;

}
