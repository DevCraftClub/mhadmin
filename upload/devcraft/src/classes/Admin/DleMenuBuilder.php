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

	/** Запасной класс иконки Metro, если mod неизвестен. */
	private const FALLBACK_ICON = 'mif-link';

	/**
	 * Карта DLE `mod` → класс иконки Core Pack (`icons.css`).
	 * Источник пунктов: `engine/inc/options.php` + новости в меню.
	 *
	 * @var array<string, string>
	 */
	private const MOD_ICONS = [
		// Новости (жёсткие пункты меню)
		'addnews'  => 'mif-news',
		'editnews' => 'mif-pencil',

		// Настройки (options['config'])
		'options'     => 'mif-cog',
		'friendlyurl' => 'mif-earth',
		'categories'  => 'mif-folder',
		'storage'     => 'mif-storage-ok',
		'xfields'     => 'mif-list',
		'dboption'    => 'mif-database',
		'question'    => 'mif-help',
		'videoconfig' => 'mif-video-camera',
		'oembed'      => 'mif-code',

		// Пользователи (options['user'])
		'editusers'  => 'mif-groups',
		'userfields' => 'mif-list',
		'usergroup'  => 'mif-groups',
		'social'     => 'mif-share',
		'blockip'    => 'mif-block',

		// Шаблоны (options['templates'])
		'templates' => 'mif-file-text',
		'email'     => 'mif-mail',

		// Фильтры / инструменты (options['filter'])
		'plugins'    => 'mif-cogs',
		'rebuild'    => 'mif-loop2',
		'wordfilter' => 'mif-filter-list',
		'iptools'    => 'mif-tools',
		'search'     => 'mif-search',
		'complaint'  => 'mif-warning',
		'metatags'   => 'mif-tags',
		'redirects'  => 'mif-link',
		'links'      => 'mif-link-on',
		'check'      => 'mif-checkmark',

		// Прочее (options['others'])
		'static'     => 'mif-file-empty',
		'clean'      => 'mif-cross',
		'newsletter' => 'mif-mail',
		'editvote'   => 'mif-checkmark',
		'files'      => 'mif-image',
		'banners'    => 'mif-image',
		'googlemap'  => 'mif-map2',
		'rss'        => 'mif-feed',
		'rssinform'  => 'mif-feed',
		'tagscloud'  => 'mif-tags',
		'logs'       => 'mif-file-text',

		// Частые разделы из admin_sections / соседние страницы DLE
		'comments'    => 'mif-bubbles',
		'cmoderation' => 'mif-bubbles',
		'main'        => 'mif-home',
	];

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

		$addNewsUrl  = '?mod=addnews&action=addnews';
		$editNewsUrl = '?mod=editnews&action=list';
		$allOptsUrl  = '?mod=options&action=options';

		$newsLinks = new AdminLink(
			name    : __('Новости'),
			type    : 'dropdown',
			children: [
				new AdminLink(
					name : $lang['add_news'] ?? __('Добавить новость'),
					link : $addNewsUrl,
					extra: $this->iconForUrl($addNewsUrl),
				),
				new AdminLink(
					name : $lang['edit_news'] ?? __('Редактировать новости'),
					link : $editNewsUrl,
					extra: $this->iconForUrl($editNewsUrl),
				),
			],
			extra: 'mif-news',
		);

		$divider  = AdminLink::divider();
		$children = [
			new AdminLink(
				name : $lang['header_all'] ?? __('Все настройки'),
				link : $allOptsUrl,
				extra: $this->iconForUrl($allOptsUrl),
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

				$sectionChildren[] = new AdminLink(
					name : $name,
					link : $url,
					extra: $this->iconForUrl($url),
				);
			}

			if($sectionChildren === []) {
				continue;
			}

			$children[] = new AdminLink(
				name    : $sectionLabel,
				type    : 'dropdown',
				children: $sectionChildren,
				extra: self::MOD_ICONS[$section] ?? self::FALLBACK_ICON,
			);
		}

		return new AdminLink(
			name    : __('Страницы DLE'),
			type    : 'dropdown',
			children: $children,
			extra: 'mif-layers-dots'
		);
	}

	/**
	 * Возвращает класс иконки Metro по `mod` из URL пункта меню.
	 *
	 * @since 200.4.1
	 */
	private function iconForUrl(string $url): string {
		$mod = $this->modFromUrl($url);

		if($mod === null || $mod === '') {
			return self::FALLBACK_ICON;
		}

		return self::MOD_ICONS[$mod] ?? self::FALLBACK_ICON;
	}

	/**
	 * Извлекает значение `mod` из query-строки или пути с `?`.
	 *
	 * @since 200.4.1
	 */
	private function modFromUrl(string $url): ?string {
		$trimmed = trim($url);

		if($trimmed === '') {
			return null;
		}

		$query = parse_url($trimmed, PHP_URL_QUERY);

		if(!is_string($query) || $query === '') {
			if(str_contains($trimmed, '?')) {
				$query = substr($trimmed, (int) strpos($trimmed, '?') + 1);
			} elseif(str_starts_with($trimmed, 'mod=')) {
				$query = $trimmed;
			} else {
				return null;
			}
		}

		parse_str($query, $params);
		$mod = $params['mod'] ?? null;

		return is_string($mod) && $mod !== '' ? $mod : null;
	}

}
