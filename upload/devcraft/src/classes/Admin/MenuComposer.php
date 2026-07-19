<?php
//===============================================================
// Файл: MenuComposer.php                                       =
// Путь: devcraft/src/classes/Admin/MenuComposer.php            =
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

use DevCraft\Types\AdminLink;
use DevCraft\Core\Module\PluginContext;

/**
 * Объединяет меню DLE и пункты манифеста плагина в единую структуру.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class MenuComposer {

	/**
	 * Формирует итоговое меню админки DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @param   DleMenuBuilder                                   $dle      Построитель меню DLE.
	 * @param   PluginContext                                    $plugin   Контекст модуля.
	 * @param   array<string, array<int, array<string, mixed>>>  $options  Разделы меню DLE.
	 * @param   array<string, string>                            $lang     Языковые строки DLE.
	 *
	 * @return AdminLink[] Список корневых пунктов меню.
	 *
	 * @example
	 *     $menu = (new MenuComposer())->compose($dleBuilder, $plugin, $options, $lang);
	 */
	public function compose(
		DleMenuBuilder $dle,
		PluginContext  $plugin,
		array          $options,
		array          $lang,
	): array {
		$menu = [$dle->buildDleSeiten($options, $lang)];

		$pluginLinks = $plugin->menu();
		$pluginMeta  = $plugin->meta();
		$pluginName  = trim((string) ($pluginMeta['name'] ?? ''));

		if($pluginLinks !== []) {
			$menu[] = AdminLink::divider($pluginName !== ''? $pluginName : 'DevCraft');
		}

		foreach($pluginLinks as $link) {
			$menu[] = $link;
		}

		if($plugin->mod() !== 'devcraft' && $pluginName !== '') {
			$query = http_build_query([
				'mod'          => 'devcraft',
				'action'       => 'logs',
				'order'        => 'time',
				'sort'         => 'DESC',
				'filter_rules' => [[
					'field' => 'plugin',
					'type'  => 'multi',
					'value' => [$pluginName],
				]],
			]);
			$menu[] = new AdminLink(
				name: __('Логи'),
				link: '?' . $query,
				type: 'link',
				extra: 'mif-list',
			);
		}

		return $menu;
	}

}
