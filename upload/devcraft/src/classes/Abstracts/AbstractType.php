<?php
//===============================================================
// Файл: AbstractType.php                                       =
// Путь: devcraft/src/classes/Abstracts/AbstractType.php        =
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

namespace DevCraft\Core\Abstracts;

use Devcraft\Abstracts\AbstractReflection;

/**
 * Базовый compatibility-layer для DTO DevCraft поверх Reflection mapper.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractType extends AbstractReflection {}
