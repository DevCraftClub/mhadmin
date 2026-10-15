<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin;

use DevCraft\Core\Abstracts\AbstractModuleIdentity;

/**
 * NotificationsPushIdentity модуля DevCraft Admin.
 */
final class AdminIdentity extends AbstractModuleIdentity {

	public const string MODULE = 'devcraft';

	public const string CODE = 'devcraft';

	public const string VERSION = '200.4.2';

	public static function name(): string {
		return __('DevCraft: Админ панель');
	}

	public static function description(): string {
		return __('DevCraft — админ-оболочка для плагинов DLE');
	}

	public static function icon(): string {
		return 'mif-construction';
	}

	public static function crowdInName(): string|null {
		return 'mhadmin';
	}

	public static function crowdInStats(): string|null {
		return '16830581-755131';
	}

	public static function siteUrl(): string {
		return 'https://devcraft.club/downloads/devcraft-admin-panel.4/';
	}

	public static function docsUrl(): string {
		return 'https://readme.devcraft.club/dev/dle/devcraft_admin/200.4.2/getting_started';
	}

}
