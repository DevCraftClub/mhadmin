<?php

global $lang, $options, $modInfo;

$dle_links_header = [
	'config'         => $lang['opt_hopt'],
	'user'           => $lang['opt_s_acc'],
	'templates'      => $lang['opt_s_tem'],
	'filter'         => $lang['opt_s_fil'],
	'others'         => $lang['opt_s_oth'],
	'admin_sections' => $lang['admin_other_section'],
];

$admin_links = [
	[
		'name'     => $lang['header_all'],
		'href'     => '?mod=options&action=options',
		'type'     => 'link',
		'children' => []
	],
	[
		'name'     => '',
		'href'     => '',
		'type'     => 'divider',
		'children' => []
	],
	[
		'name'     => __('mhadmin','Новости'),
		'href'     => '',
		'type'     => 'dropdown',
		'children' => [
			[
				'name'     => $lang['add_news'],
				'href'     => '?mod=addnews&action=addnews',
				'type'     => 'link',
				'children' => []
			],
			[
				'name'     => $lang['edit_news'],
				'href'     => '?mod=editnews&action=list',
				'type'     => 'link',
				'children' => []
			],
		]
	],
	[
		'name'     => '',
		'href'     => '',
		'type'     => 'divider',
		'children' => []
	],
];

foreach ($options as $o => $a) {
	$opt_children = [];

	foreach ($a as $c) $opt_children[] = [
		'name'     => $c['name'],
		'href'     => $c['url'],
		'type'     => 'link',
		'children' => []
	];

	$admin_links[] = [
		'name'     => $dle_links_header[$o],
		'href'     => '',
		'type'     => 'dropdown',
		'children' => $opt_children
	];
}

// Добавляем новую ссылку в меню
// Новая ссылка должна быть массивом
// В массиве должны быть указаны "Название (name)", "Ссылка (href)", "Тип ссылки (type)" и "Подссылки (children)"
// Тип ссылок может быть одним из "link (просто ссылка)", "divider (разделитель)" или "dropdown (выпадающее меню - настроенно до второго уровня)"
// Подссылки имеют тот же формат, что и сами ссылки
$links = [
	'dle'       => [
		'name'     => __('mhadmin','Страницы DLE'),
		'href'     => '',
		'type'     => 'dropdown',
		'children' => $admin_links,
	],
	'index'     => [
		'name'     => __('mhadmin','Главная'),
		'href'     => THIS_SELF . '?mod=' . $modInfo['module_code'],
		'type'     => 'link',
		'children' => [],
	],
	'changelog' => [
		'name'     => __('mhadmin','История изменений'),
		'href'     => THIS_SELF . '?mod=' . $modInfo['module_code'] . '&sites=changelog',
		'type'     => 'link',
		'children' => [],
	],
];
