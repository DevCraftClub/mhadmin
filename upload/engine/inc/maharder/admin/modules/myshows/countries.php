<?php

global $mh, $mh_config, $modInfo;

require_once DLEPlugins::Check(MH_ROOT . '/mystatus/Models/Country.php');

$countries = new Country();

$cur_page = $modInfo['_get']['page'] ?? 1;
$total_pages = @ceil($countries->count() / $mh_config['list_count']);
$start = isset($modInfo['_get']['page']) ? ($modInfo['_get']['page'] * $mh_config['list_count']) + 1 : 0;
$end = isset($modInfo['_get']['page']) ? (($modInfo['_get']['page'] + 1) * $mh_config['list_count']) : $mh_config['list_count'];

$modVars = [
	'title' => 'Настройка стран',
	'countries' => $countries->getAll(['limit' => "{$start},{$end}"]),
	'page' => $cur_page,
	'total_pages' => $total_pages,
	'search_fields' => [
		'sfields' => $mh->generate_link(
			_('Где искать?'), '#', 'dropdown', [
							$mh->generate_link(_('Везде'), '#', 'data', [], 'all'),
							$mh->generate_link(_('Оригинальное название'), '#', 'data', [], 'original'),
							$mh->generate_link(_('Аббревиатура'), '#', 'data', [], 'abbr'),
							$mh->generate_link(_('Перевод'), '#', 'data', [], 'translate'),
		                ], 'search'
		)
	]
];

$breadcrumbs[] = [
	'name' => $modVars['title'],
	'url' => $links['countries']['href'],
];

$htmlTemplate = 'modules/myshows/countries.html';