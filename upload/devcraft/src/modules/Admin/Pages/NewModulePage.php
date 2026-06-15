<?php
//===============================================================
// Файл: NewModulePage.php                                      =
// Путь: devcraft/src/modules/Admin/Pages/NewModulePage.php     =
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
use DevCraft\Modules\Admin\Services\ModuleGeneratorService;

/**
 * Страница генератора новых DevCraft-модулей.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class NewModulePage extends AbstractPage {

	/**
	 * Формирует представление и начальные данные формы генератора модулей.
	 *
	 * @since 200.4.0
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ шаблона и данные для Twig.
	 *
	 * @example
	 *     $result = (new NewModulePage())->handle();
	 */
	public function handle(): array {
		$this->addBreadcrumb(__('Генератор модулей'));

		return [
			'view' => 'admin/new_module.twig',
			'data' => [
				'page_title'      => __('Генератор модулей'),
				'default_version' => ModuleGeneratorService::defaultVersion(),
				'variable'        => [
					'version'  => ModuleGeneratorService::defaultVersion(),
					'db'       => true,
					'override' => false,
				],
			],
		];
	}

}
