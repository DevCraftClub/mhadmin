<?php

global $lang, $options, $modInfo, $mh;

$dle_links_header = [
	'config'         => $lang['opt_hopt'],
	'user'           => $lang['opt_s_acc'],
	'templates'      => $lang['opt_s_tem'],
	'filter'         => $lang['opt_s_fil'],
	'others'         => $lang['opt_s_oth'],
	'admin_sections' => $lang['admin_other_section'],
];

$newsLinks = new AdminLink(name: __('Новости'), type: 'dropdown');
$newsLinks->addChild(new AdminLink(name: $lang['add_news'], link: '?mod=addnews&action=addnews'));
$newsLinks->addChild(new AdminLink(name: $lang['edit_news'], link: '?mod=editnews&action=list'));

$dividerLink = new AdminLink(type: 'divider');

$admin_links = new AdminLink(name: __('Страницы DLE'), type: 'dropdown');
$admin_links->addChild(new AdminLink(name: $lang['header_all'], link: '?mod=options&action=options'));
$admin_links->addChild($dividerLink);
$admin_links->addChild($newsLinks);
$admin_links->addChild($dividerLink);

foreach ($options as $o => $a) {
	$opt_children = [];

	foreach ($a as $c) $opt_children[] = new AdminLink($o, name: $c['name'], link: $c['url']);

	$admin_links->addChild(new AdminLink(name: $dle_links_header[$o], type: 'dropdown', children: $opt_children));
}

// Добавляем новую ссылку в меню
// Новая ссылка должна быть массивом
// В массиве должны быть указаны "Название (name)", "Ссылка (href)", "Тип ссылки (type)" и "Подссылки (children)"
// Тип ссылок может быть одним из "link (просто ссылка)", "divider (разделитель)" или "dropdown (выпадающее меню - настроенно до второго уровня)"
// Подссылки имеют тот же формат, что и сами ссылки

$mh->setLink($admin_links, 'dle');
$mh->setLink(new AdminLink('index', name: __('Главная'), link: '?mod=' . $modInfo['module_code']), 'index');
$mh->setLink(
	new AdminLink('changelog', name: __('Журнал изменений'), link: '?mod=' . $modInfo['module_code'] . '&sites=changelog'),
	'changelog'
);

