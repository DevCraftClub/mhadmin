<?php
//===============================================================
// Файл: DashboardPage.php                                      =
// Путь: devcraft/src/modules/Admin/Pages/DashboardPage.php     =
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

use DevCraft\Modules\Admin\AdminIdentity;

use DevCraft\Core\Application;
use DevCraft\Core\Module\PluginContext;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Composer\ComposerInstallAllPlaceholder;
use DevCraft\Modules\Admin\Services\DashboardPackageMetricService;
use DevCraft\Modules\Admin\Services\PublicAssetSeedService;

/**
 * Главная страница (панель) модуля DevCraft Admin.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class DashboardPage extends AbstractPage {

	/**
	 * Формирует представление и данные главной страницы админки.
	 *
	 * @since 200.4.0
	 *
	 * @return array{view: string, data: array<string, mixed>} Ключ шаблона и данные для Twig.
	 *
	 * @example
	 *     $result = (new DashboardPage())->handle();
	 */
	public function handle(): array {
		(new PublicAssetSeedService())->ensureRegistered();

		$registry = Application::instance()->registry();
		$plugin   = $registry->forMod(AdminIdentity::mod());
		$meta     = $plugin?->meta() ?? [];
		$context  = $this->adminContext();

		$changelog = $plugin?->changelog() ?? [];
		$latest    = isset($changelog[0])? $changelog[0]->toArray() : NULL;

		if($latest !== NULL) {
			$latest['teaser_items'] = $changelog[0]->teaserItems(3);
		}

		$mod         = $plugin?->mod() ?? AdminIdentity::mod();
		$menu        = [];
		$composerUrl = '?mod=' . $mod . '&action=composer';
		$metrics     = new DashboardPackageMetricService();

		foreach($context->menu() as $link) {
			if($link->type !== 'link' || $link->action === NULL || $link->action === 'dashboard') {
				continue;
			}

			$menu[] = [
				'name'   => $link->name,
				'link'   => $link->link,
				'icon'   => $link->extra,
				'action' => $link->action,
			];
		}

		return [
			'view' => 'pages/dashboard.twig',
			'data' => [
				'page_title' => (string) ($meta['name'] ?? 'DevCraft'),
				'dashboard'  => [
					'app'              => $meta,
					'author'           => $context->author()->toArray(),
					'lic_link'         => $context->licLink(),
					'menu'             => $menu,
					'changelog_latest' => $latest,
					'changelog_url'    => '?mod=' . $mod . '&action=changelog',
					'show_assets'      => true,
					'show_update'      => true,
					'composer'         => [
						'url'                 => $composerUrl,
						'missing_required'    => $metrics->missingRequiredCount(),
						'has_uninstalled'     => $metrics->hasUninstalledPackages(),
						'install_all_package' => ComposerInstallAllPlaceholder::PACKAGE,
						'packages'            => $metrics->packagesForDashboard(),
						'dump_autoload_url'   => 'dump_autoload',
					]
				],
			],
		];
	}

}
