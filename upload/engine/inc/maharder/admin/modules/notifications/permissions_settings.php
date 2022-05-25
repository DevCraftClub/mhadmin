<?php

global $user_group, $db, $config;

$c_u_g = [];

foreach ($user_group as $uid => $group) {
	$user_count = $db->super_query("SELECT count(*) as count FROM " . USERPREFIX . "_users WHERE user_group = {$group['id']}");
	$group['users'] = $user_count['count'];
	$group['link'] = "{$config['http_home_url']}{$config['admin_path']}?mod=usergroup&action=edit&id={$group['id']}";
	$c_u_g[] = $group;
}

$modVars['title'] = 'Права групп';
$modVars['user_groups'] = $c_u_g;
$modVars['permissions_list'] = include(DLEPlugins::Check(MH_ADMIN . '/modules/notifications/admin/permissions.php'));

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => THIS_SELF.'?sites=permissions',
];

$htmlTemplate = 'modules/notifications/permissions.html';