<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../../' );
	die( "Hacking attempt!" );
}

const MH_ROOT = ENGINE_DIR . '/inc/maharder';
const MH_ADMIN = MH_ROOT . '/admin';
const ROOT = ROOT_DIR;
define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('THIS_SELF', $_SERVER['PHP_SELF']);
define('URL', (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http').'://'.THIS_HOST.'/engine/inc');

require_once DLEPlugins::Check(MH_ROOT.'/functions/maharder.class.php');
require_once DLEPlugins::Check(MH_ROOT.'/functions/vendor/autoload.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/inc/include/functions.inc.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/skins/default.skin.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/data/config.php');

$loader = new \Twig\Loader\FilesystemLoader(MH_ADMIN.'/templates');

$mh_template = new \Twig\Environment($loader, [
	'cache' => MH_ADMIN.'/cache',
	'debug' => true,
]);

$mh_template->addExtension(new Bes\Twig\Extension\MobileDetectExtension());

$dle_links_header = [
	'config' => $lang['opt_hopt'],
	'user' => $lang['opt_s_acc'],
	'templates' => $lang['opt_s_tem'],
	'filter' => $lang['opt_s_fil'],
	'others' => $lang['opt_s_oth'],
	'admin_sections' => $lang['admin_other_section'],
];

$admin_links = [
	[
		'name' => $lang['header_all'],
		'href' => '?mod=options&action=options',
		'type' => 'link',
		'children' => []
	],
	[
		'name' => '',
		'href' => '',
		'type' => 'divider',
		'children' => []
	],
	[
		'name' => 'Новости',
		'href' => '',
		'type' => 'dropdown',
		'children' => [
			[
				'name' => $lang['add_news'],
				'href' => '?mod=addnews&action=addnews',
				'type' => 'link',
				'children' => []
			],
			[
				'name' => $lang['edit_news'],
				'href' => '?mod=editnews&action=list',
				'type' => 'link',
				'children' => []
			],
		]
	],
	[
		'name' => '',
		'href' => '',
		'type' => 'divider',
		'children' => []
	],
];

foreach($options as $o => $a) {
	$opt_children = [];

	foreach($a as $c) $opt_children[] = [
		'name' => $c['name'],
		'href' => $c['url'],
		'type' => 'link',
		'children' => []
	];

	$admin_links[] = [
		'name' => $dle_links_header[$o],
		'href' => '',
		'type' => 'dropdown',
		'children' => $opt_children
	];
}

// Добавляем новую ссылку в меню
// Новая ссылка должна быть массивом
// В массиве должны быть указаны "Название (name)", "Ссылка (href)", "Тип ссылки (type)" и "Подссылки (children)"
// Тип ссылок может быть одним из "link (просто ссылка)", "divider (разделитель)" или "dropdown (выпадающее меню - настроенно до второго уровня)"
// Подссылки имеют тот же формат, что и сами ссылки
$links = [
	[
		'name' => 'Страницы DLE',
		'href' => '',
		'type' => 'dropdown',
		'children' => $admin_links,
	],
	[
		'name' => 'Главная',
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'],
		'type' => 'link',
		'children' => [],
	],
	[
		'name' => 'История изменений',
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'].'&sites=changelog',
		'type' => 'link',
		'children' => [],
	],
];

$breadcrumbs = [
	[
		'name' => $links[1]['name'],
		'url' => $links[1]['href'],
	],
];
