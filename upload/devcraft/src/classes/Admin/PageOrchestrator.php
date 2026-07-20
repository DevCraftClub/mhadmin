<?php
//===============================================================
// Файл: PageOrchestrator.php                                   =
// Путь: devcraft/src/classes/Admin/PageOrchestrator.php        =
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

namespace DevCraft\Core\Admin;

use DevCraft\Types\Changelog;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Module\PluginContext;
use DevCraft\Core\Interfaces\PageInterface;
use DevCraft\Core\Interfaces\SettingsPageInterface;

/**
 * Подготавливает переменные Twig для страниц настроек и changelog.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class PageOrchestrator {

	/**
	 * @since 200.4.0
	 *
	 * @param   SettingsFormService  $formService  Сервис формы настроек.
	 */
	public function __construct(
		private readonly SettingsFormService $formService = new SettingsFormService(),
	) {}

	/**
	 * Собирает дополнительные переменные шаблона для текущей страницы.
	 *
	 * @since 200.4.0
	 *
	 * @param   PageInterface  $page    Обработчик страницы.
	 * @param   PluginContext  $plugin  Контекст модуля.
	 * @param   string         $action  Текущее действие админки.
	 *
	 * @return array<string, mixed> Переменные для передачи в Twig.
	 *
	 * @example
	 *     $vars = $orchestrator->prepare($page, $plugin, 'settings');
	 */
	public function prepare(PageInterface $page, PluginContext $plugin, string $action): array {
		$vars = [];

		if($page instanceof SettingsPageInterface) {
			$schema = $plugin->settingsSchema();

			if($schema === NULL) {
				$vars['schema_error'] = __('Схема настроек не найдена.');

				return $vars;
			}

			$vars['settings'] = DataManager::getConfig($schema->codename);
			$vars['form']     = $this->formService->buildViewModel(
				$schema,
				$page->supplementFormData(),
				$plugin->mod(),
				$plugin->ajaxController(),
			);
			$vars['modInfo']  = $plugin->meta();
		}

		if($action === 'changelog') {
			$vars['changelog'] = array_map(
				static fn(Changelog $entry): array => $entry->toArray(),
				$plugin->changelog(),
			);
		}

		return $vars;
	}

}
