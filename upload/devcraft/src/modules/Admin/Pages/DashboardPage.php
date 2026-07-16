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

use DevCraft\Core\Application;
use DevCraft\Core\Abstracts\AbstractPage;
use DevCraft\Core\Module\PluginContext;
use DevCraft\Modules\Admin\Services\DashboardPackageMetricService;

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
		$registry = Application::instance()->registry();
		$plugin   = $registry->forMod('devcraft');
		$meta     = $plugin?->meta() ?? [];
		$context  = $this->adminContext();

		$changelog = $plugin?->changelog() ?? [];
		$latest    = isset($changelog[0])? $changelog[0]->toArray() : NULL;

		if($latest !== NULL) {
			$latest['teaser_items'] = $changelog[0]->teaserItems(3);
		}

		$menu = [];
		$composerUrl = '?mod=devcraft&action=composer';
		$crowdin     = $this->buildCrowdinDisplay($plugin);

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
			'view' => 'admin/dashboard.twig',
			'data' => [
				'page_title' => (string) ($meta['name'] ?? 'DevCraft'),
				'dashboard'  => [
					'app'              => [
						'name'        => (string) ($meta['name'] ?? 'DevCraft'),
						'version'     => (string) ($meta['version'] ?? '0.0.0'),
						'description' => (string) ($meta['description'] ?? ''),
						'icon'        => (string) ($meta['icon'] ?? ''),
						'docs_link'   => (string) ($meta['docsLink'] ?? ''),
						'site_link'   => (string) ($meta['siteLink'] ?? ''),
						'site_id'     => (int) ($meta['siteId'] ?? 0),
						'code'        => (string) ($meta['module_code'] ?? 'devcraft'),
					],
					'author'           => $context->author()->toArray(),
					'lic_link'         => $context->licLink(),
					'menu'             => $menu,
					'changelog_latest' => $latest,
					'changelog_url'    => '?mod=devcraft&action=changelog',
					'composer'         => [
						'url'                => $composerUrl,
						'missing_required'   => (new DashboardPackageMetricService())->missingRequiredCount(),
						'dump_autoload_url'  => 'dump_autoload',
					],
					'crowdin'          => $crowdin,
				],
			],
		];
	}

	/**
	 * Формирует данные для статического Crowdin-бейджа на панели.
	 *
	 * @since 200.4.0
	 *
	 * @param   PluginContext|null  $plugin  Контекст модуля из реестра.
	 *
	 * @return  array<string, string>|null  URL и идентификаторы или null без конфигурации.
	 */
	private function buildCrowdinDisplay(?PluginContext $plugin): ?array {
		$data   = $plugin?->moduleData();
		$name   = $data?->crowdinName !== NULL? trim($data->crowdinName) : '';
		$statId = $data?->crowdinStatId !== NULL? trim($data->crowdinStatId) : '';

		if($name === '' || $statId === '') {
			return NULL;
		}

		return [
			'name'        => $name,
			'stat_id'     => $statId,
			'badge_url'   => 'https://d322cqt584bo4o.cloudfront.net/' . rawurlencode($statId) . '.png',
			'project_url' => 'https://crowdin.com/project/' . rawurlencode($name),
		];
	}

}
