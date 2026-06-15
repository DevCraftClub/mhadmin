<?php
//===============================================================
// Файл: ChangelogPage.php                                      =
// Путь: devcraft/src/modules/Admin/Pages/ChangelogPage.php     =
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

namespace DevCraft\Modules\Admin\Pages;

use DevCraft\Core\Abstracts\AbstractPage;

/**
 * Страница истории изменений модуля DevCraft Admin.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class ChangelogPage extends AbstractPage {

	/**
	 * Формирует представление и данные страницы истории изменений.
	 *
	 * @since 200.4.0
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ шаблона и данные для Twig.
	 *
	 * @example
	 *     $result = (new ChangelogPage())->handle();
	 */
	public function handle(): array {
		$pageName = __('История изменений');

		$this->addBreadcrumb($pageName);

		return [
			'view' => 'admin/changelog.twig',
			'data' => [
				'page_title' => $pageName,
			],
		];
	}

}
