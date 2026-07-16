<?php
//===============================================================
// Файл: manifest.php                                           =
// Путь: devcraft/src/modules/Admin/manifest.php                =
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

use DevCraft\Types\AdminLink;
use DevCraft\Modules\Admin\Pages\LogsPage;
use DevCraft\Modules\Admin\Pages\SettingsPage;
use DevCraft\Modules\Admin\Pages\ChangelogPage;
use DevCraft\Modules\Admin\Pages\DashboardPage;
use DevCraft\Modules\Admin\Pages\ComposerPage;
use DevCraft\Modules\Admin\Pages\NewModulePage;
use DevCraft\Modules\Admin\Ajax\DumpAutoloadHandler;
use DevCraft\Modules\Admin\Ajax\SettingsHandler;
use DevCraft\Modules\Admin\Ajax\DeleteLogHandler;
use DevCraft\Modules\Admin\Ajax\NewModuleHandler;
use DevCraft\Modules\Admin\Ajax\SaveAssetHandler;
use DevCraft\Modules\Admin\Ajax\LogsTableHandler;
use DevCraft\Modules\Admin\Ajax\ComposerTableHandler;
use DevCraft\Modules\Admin\Ajax\ComposerActionHandler;
use DevCraft\Modules\Admin\Ajax\ComposerPolicyHandler;
use DevCraft\Modules\Admin\Ajax\ComposerSyncHandler;
use DevCraft\Modules\Admin\Ajax\SyncAssetsHandler;
use DevCraft\Modules\Admin\Ajax\CheckAssetsHandler;
use DevCraft\Modules\Admin\Ajax\CheckUpdateHandler;

/**
 * Манифест модуля DevCraft Admin: метаданные, меню, AJAX и ресурсы.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
return [
	'mod'       => 'devcraft',
	'code'      => 'devcraft',
	'crowdinName'   => 'mhadmin',
	'crowdinStatId' => '16830581-755131',
	'meta'      => [
		'name'        => 'DevCraft Admin',
		'version'     => '200.4.0',
		'description' => __('DevCraft — админ-оболочка для плагинов DLE'),
		'icon'        => 'mif-construction',
		'docsLink'    => 'https://readme.devcraft.club/latest/dev/devcraft_admin/install/',
		'siteLink'    => 'https://devcraft.club/',
		'siteId'      => 4,
		'licLink'     => 'https://devcraft.club/pages/licence-agreement/',
		'author'      => [
			'name'      => 'Maxim Harder',
			'contacts'  => [
				['name' => __('E-Mail'), 'link' => 'mailto:dev@devcraft.club'],
				['name' => __('Telegram'), 'link' => 'https://t.me/MaHarder'],
				['name' => __('Website'), 'link' => 'https://devcraft.club/misc/contact'],
			],
			'donations' => [
				['name' => 'PayPal', 'value' => 'paypal.me/MaximH', 'link' => 'https://paypal.me/MaximH'],
				['name' => 'Ko-Fi', 'value' => 'ko-fi.com/devcraft', 'link' => 'https://ko-fi.com/J3J118N1C'],
				['name' => 'YooMoney', 'value' => '41001454367103', 'link' => 'https://yoomoney.ru/to/41001454367103'],
			],
		],
	],
	'menu'      => [
		AdminLink::page(__('Главная'), 'dashboard', DashboardPage::class, 'mif-home'),
		AdminLink::page(__('Настройки'), 'settings', SettingsPage::class, 'mif-cog'),
		AdminLink::page(__('Вывод логов'), 'logs', LogsPage::class, 'mif-list'),
		AdminLink::page(__('Composer'), 'composer', ComposerPage::class, 'mif-tools'),
		AdminLink::page(__('История изменений'), 'changelog', ChangelogPage::class, 'mif-library'),
		AdminLink::page(__('Генератор модулей'), 'generator', NewModulePage::class, 'mif-plus'),
	],
	'ajax'      => [
		'controller' => 'admin',
		'methods'    => [
			'settings'     => SettingsHandler::class,
			'delete_log'   => DeleteLogHandler::class,
			'check_assets' => CheckAssetsHandler::class,
			'sync_assets'  => SyncAssetsHandler::class,
			'save_asset'   => SaveAssetHandler::class,
			'check_update' => CheckUpdateHandler::class,
			'logs_table'   => LogsTableHandler::class,
			'new_module'   => NewModuleHandler::class,
			'composer_table' => ComposerTableHandler::class,
			'composer_action' => ComposerActionHandler::class,
			'composer_policy' => ComposerPolicyHandler::class,
			'composer_sync'   => ComposerSyncHandler::class,
			'dump_autoload'   => DumpAutoloadHandler::class,
		],
	],
	'composer_required' => [
		['name' => 'twig/twig', 'minVersion' => '3.14', 'hardRequired' => true],
		['name' => 'cycle/orm', 'minVersion' => '2.9', 'hardRequired' => true],
		['name' => 'symfony/translation', 'minVersion' => '7.4', 'hardRequired' => true],
	],
	/** Подключает данные истории изменений модуля Admin. */
	'changelog' => require DLEPlugins::Check(__DIR__ . '/changelog.data.php'),
	'assets'    => [
		'js' => ['admin.js'],
	],
];
