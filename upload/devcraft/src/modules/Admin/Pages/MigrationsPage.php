<?php
//===============================================================
// Файл: MigrationsPage.php                                     =
// Путь: devcraft/src/modules/Admin/Pages/MigrationsPage.php    =
// Последнее изменение: 2026-10-28 12:00:00                     =
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
use DevCraft\Modules\Admin\Services\MigrationWorkbenchService;

/**
 * Страница миграций Cycle всех модулей: статусы и действия без консоли.
 *
 * @package    DevCraft
 * @since      200.4.2
 * @subpackage Modules.Admin
 */
final class MigrationsPage extends AbstractPage {

	public function handle(): array {
		$rows   = MigrationWorkbenchService::create()->rows();
		$counts = array_count_values(array_column($rows, 'status'));
		$this->addBreadcrumb(__('Миграции'));

		return [
			'view' => 'admin/migrations.twig',
			'data' => [
				'page_title' => __('Миграции'),
				'rows'       => $rows,
				'pending'    => $counts[MigrationWorkbenchService::STATUS_PENDING] ?? 0,
				'executed'   => $counts[MigrationWorkbenchService::STATUS_EXECUTED] ?? 0,
				'orphans'    => $counts[MigrationWorkbenchService::STATUS_ORPHAN] ?? 0,
			],
		];
	}

}
