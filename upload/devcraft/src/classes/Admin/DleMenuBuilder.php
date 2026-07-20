<?php
//===============================================================
// Файл: DleMenuBuilder.php                                     =
// Путь: devcraft/src/classes/Admin/DleMenuBuilder.php          =
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

/**
 * Строит выпадающее меню «Страницы DLE» из опций и языковых строк DLE.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class DleMenuBuilder {

	/**
	 * Формирует корневой пункт меню со всеми разделами DLE.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, array<int, array<string, mixed>>>  $options  Разделы меню DLE.
	 * @param   array<string, string>                            $lang     Языковые строки DLE.
	 *
	 * @return AdminLink Корневой dropdown «Страницы DLE».
	 *
	 * @example
	 *     $link = (new DleMenuBuilder())->buildDleSeiten($options, $lang);
	 */
	public function buildDleSeiten(array $options, array $lang): AdminLink {
		$headers = [
			'config'         => $lang['opt_hopt'] ?? __('Настройки'),
			'user'           => $lang['opt_s_acc'] ?? __('Пользователи'),
			'templates'      => $lang['opt_s_tem'] ?? __('Шаблоны'),
			'filter'         => $lang['opt_s_fil'] ?? __('Фильтры'),
			'others'         => $lang['opt_s_oth'] ?? __('Прочее'),
			'admin_sections' => $lang['admin_other_section'] ?? __('Разделы'),
		];

		$newsLinks = new AdminLink(
			name    : __('Новости'),
			type    : 'dropdown',
			children: [
				new AdminLink(
					name: $lang['add_news'] ?? __('Добавить новость'),
					link: '?mod=addnews&action=addnews',
				),
				new AdminLink(
					name: $lang['edit_news'] ?? __('Редактировать новости'),
					link: '?mod=editnews&action=list',
				),
			],
		);

		$divider  = AdminLink::divider();
		$children = [
			new AdminLink(
				name: $lang['header_all'] ?? __('Все настройки'),
				link: '?mod=options&action=options',
			),
			$divider,
			$newsLinks,
			$divider,
		];

		foreach($options as $section => $items) {
			if(!is_string($section) || !is_array($items) || $items === []) {
				continue;
			}

			$sectionLabel    = $headers[$section] ?? $section;
			$sectionChildren = [];

			foreach($items as $item) {
				if(!is_array($item)) {
					continue;
				}

				$name = (string) ($item['name'] ?? '');
				$url  = (string) ($item['url'] ?? '');

				if($name === '' || $url === '') {
					continue;
				}

				$sectionChildren[] = new AdminLink(name: $name, link: $url);
			}

			if($sectionChildren === []) {
				continue;
			}

			$children[] = new AdminLink(
				name    : $sectionLabel,
				type    : 'dropdown',
				children: $sectionChildren,
			);
		}

		return new AdminLink(
			name    : __('Страницы DLE'),
			type    : 'dropdown',
			children: $children,
		);
	}

}
