<?php
//===============================================================
// Файл: DevCraftException.php                                  =
// Путь: devcraft/src/classes/Exception/DevCraftException.php   =
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

namespace DevCraft\Core\Exception;

/**
 * Базовое исключение DevCraft для ошибок конфигурации и бизнес-логики плагина.
 *
 * Используется там, где требуется перехват на уровне Application::runAdmin()
 * с отображением сообщения пользователю админки.
 *
 * @package DevCraft
 * @subpackage Core.Exception
 * @since 200.4.0
 */
class DevCraftException extends \RuntimeException {}
