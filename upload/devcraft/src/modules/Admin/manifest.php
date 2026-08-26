<?php
//===============================================================
// Файл: manifest.php                                           =
// Путь: devcraft/src/modules/Admin/manifest.php                =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

use DevCraft\Modules\Admin\AdminIdentity;

use DevCraft\Types\AdminLink;
use DevCraft\Types\ModuleManifest;
use DevCraft\Builders\ModuleManifestBuilder;
use DevCraft\Builders\ModuleAjaxConfigBuilder;
use DevCraft\Builders\ModuleAssetsBuilder;
use DevCraft\Builders\ComposerTypeBuilder;
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
 * Манифест модуля DevCraft Admin (fluent ModuleManifestBuilder).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 *
 * @return ModuleManifest
 */
return ModuleManifestBuilder::create()
	->mod(AdminIdentity::mod())
	->code(AdminIdentity::code())
	->crowdinName('mhadmin')
	->crowdinStatId('16830581-755131')
	->name('DevCraft Admin')
	->version('200.4.1')
	->description(__('DevCraft — админ-оболочка для плагинов DLE'))
	->icon('mif-construction')
	->docsLink('https://readme.devcraft.club/latest/dev/devcraft_admin/install/')
	->siteLink('https://devcraft.club/')
	->siteId(4)
	->menu([
		AdminLink::page(__('Главная'), 'dashboard', DashboardPage::class, 'mif-home'),
		AdminLink::page(__('Настройки'), 'settings', SettingsPage::class, 'mif-cog'),
		AdminLink::page(__('Вывод логов'), 'logs', LogsPage::class, 'mif-list'),
		AdminLink::page(__('Composer'), 'composer', ComposerPage::class, 'mif-tools'),
		AdminLink::page(__('История изменений'), 'changelog', ChangelogPage::class, 'mif-library'),
		AdminLink::page(__('Генератор модулей'), 'generator', NewModulePage::class, 'mif-plus'),
	])
	->ajax(
		ModuleAjaxConfigBuilder::create('admin')
			->methods([
				'settings'          => SettingsHandler::class,
				'delete_log'        => DeleteLogHandler::class,
				'check_assets'      => CheckAssetsHandler::class,
				'sync_assets'       => SyncAssetsHandler::class,
				'save_asset'        => SaveAssetHandler::class,
				'check_update'      => CheckUpdateHandler::class,
				'logs_table'        => LogsTableHandler::class,
				'new_module'        => NewModuleHandler::class,
				'composer_table'    => ComposerTableHandler::class,
				'composer_action'   => ComposerActionHandler::class,
				'composer_policy'   => ComposerPolicyHandler::class,
				'composer_sync'     => ComposerSyncHandler::class,
				'dump_autoload'     => DumpAutoloadHandler::class,
			])
	)
	->composerRequired([
		ComposerTypeBuilder::create('devcraftclub/dev-tools')->minVersion('^1.0')->hardRequired()->build(),
		ComposerTypeBuilder::create('twig/twig')->minVersion('3.14')->hardRequired()->build(),
		ComposerTypeBuilder::create('cycle/orm')->minVersion('2.9')->hardRequired()->build(),
		ComposerTypeBuilder::create('symfony/translation')->minVersion('7.4')->hardRequired()->build(),
	])
	->changelog(require DLEPlugins::Check(__DIR__ . '/changelog.data.php'))
	->assets(ModuleAssetsBuilder::create()->js('admin.js'))
	->build(__DIR__);
