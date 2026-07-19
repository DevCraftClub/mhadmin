<?php
//===============================================================
// Файл: PageInterface.php                                      =
// Путь: devcraft/src/classes/Interfaces/PageInterface.php      =
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
 * Контракт обработчика административной страницы модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Interfaces
 */
interface PageInterface {

	/**
	 * Обрабатывает запрос страницы и возвращает ключ представления с данными.
	 *
	 * @since 200.4.0
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ Twig-шаблона и данные для рендера.
	 *
	 * @example
	 *     $result = $page->handle();
	 *     echo $twig->render($result['view'], $result['data']);
	 */
	public function handle(): array;

}
