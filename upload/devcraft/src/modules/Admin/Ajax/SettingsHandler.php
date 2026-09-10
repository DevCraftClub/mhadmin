<?php
//===============================================================
// Файл: SettingsHandler.php                                    =
// Путь: devcraft/src/modules/Admin/Ajax/SettingsHandler.php    =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Ajax;

use DevCraft\Modules\Admin\AdminIdentity;

use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Http\JsonResponse;
use DevCraft\Types\FormSchema;
use DevCraft\Core\Abstracts\AbstractSettingsHandler;

/**
 * AJAX-обработчик частичного сохранения настроек ядра DevCraft Admin.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class SettingsHandler extends AbstractSettingsHandler {
	protected function configName(): ?string {
		return AdminIdentity::code();
	}

	/**
	 * @param   array<string, mixed>  $existing
	 * @param   array<string, mixed>  $valid
	 *
	 * @return array<string, mixed>
	 */
	protected function prepareConfig(array $existing, array $valid, FormSchema $schema): array|JsonResponse {
		$merged = array_merge($existing, $valid);
		unset($merged['debug_filter_daterange']);

		return $merged;
	}

	protected function afterSave(array $saved, FormSchema $schema): void {
		Translation::reset();
	}

}
