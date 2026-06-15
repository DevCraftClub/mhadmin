<?php
//===============================================================
// Файл: SettingsPage.php                                       =
// Путь: devcraft/src/modules/Admin/Pages/SettingsPage.php      =
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

use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Interfaces\SettingsPageInterface;

/**
 * Страница настроек модуля DevCraft Admin.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class SettingsPage extends AbstractPage implements SettingsPageInterface {

	/**
	 * Формирует представление и данные страницы настроек.
	 *
	 * @since 200.4.0
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ шаблона и данные для Twig.
	 *
	 * @example
	 *     $result = (new SettingsPage())->handle();
	 */
	public function handle(): array {
		$this->addBreadcrumb(__('Настройки'));

		return [
			'view' => 'admin/settings.twig',
			'data' => [
				'page_title' => __('Настройки'),
			],
		];
	}

	public function supplementFormData(): array {
		return [];
	}

}
