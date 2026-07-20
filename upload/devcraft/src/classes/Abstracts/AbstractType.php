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

/**
 * Базовый тип данных с сериализацией в массив и обратно.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractType {

	/**
	 * Создаёт экземпляр типа из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные.
	 *
	 * @return static Новый экземпляр типа.
	 */
	abstract public static function fromArray(array $data): static;

	/**
	 * Преобразует экземпляр типа в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Сериализованное представление.
	 */
	abstract public function toArray(): array;

}
