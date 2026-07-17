<?php
/**
 * Заполнение тегов новости для ParseTemplateTags (логика show.custom / show.short).
 *
 * Ожидает в области видимости:
 * - \dle_template $tpl
 * - array $row (нормализованная)
 * - string $full_link
 * - string $mode ('short'|'full')
 *
 * @var \dle_template        $tpl
 * @var array<string, mixed> $row
 * @var string               $full_link
 * @var string               $mode
 */

declare(strict_types=1);

if(!defined('DATALIFEENGINE')) {
	return;
}

global $config, $cat_info, $lang, $member_id, $user_group, $category_id, $is_logged;

$cat_info              = is_array($cat_info ?? NULL)? $cat_info : [];
$member_id             = is_array($member_id ?? NULL)? $member_id : ['user_group' => 4, 'name' => '', 'user_id' => 0];
$user_group            = is_array($user_group ?? NULL)? $user_group : [];
$is_logged             = !empty($is_logged);
$short_news_cache      = false;
$allow_comments_in_cat = true;

$tpl->set_block("'{banner_(.*?)}'si", '');
$tpl->set_block("'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", '');

if(empty($row['category']) || $row['category'] === '0') {
	$my_cat      = '---';
	$my_cat_link = '---';
	$tpl->set('[not-has-category]', '');
	$tpl->set('[/not-has-category]', '');
	$tpl->set_block("'\\[has-category\\](.*?)\\[/has-category\\]'si", '');
} else {
	$my_cat      = [];
	$my_cat_link = [];
	$cat_list    = $row['cats'] = explode(',', (string) $row['category']);

	$tpl->set('[has-category]', '');
	$tpl->set('[/has-category]', '');
	$tpl->set_block("'\\[not-has-category\\](.*?)\\[/not-has-category\\]'si", '');

	if(count($cat_list) === 1) {
		$cid = $cat_list[0];

		if(!empty($cat_info[$cid]['id'])) {
			$my_cat[]    = $cat_info[$cid]['name'];
			$my_cat_link = function_exists('get_categories')
				? get_categories($cid, $config['category_separator'] ?? ' &raquo;')
				: $cat_info[$cid]['name'];

			if(!empty($cat_info[$cid]['disable_comments'])) {
				$allow_comments_in_cat = false;
			}
		} else {
			$my_cat_link = '---';
		}
	} else {
		foreach($cat_list as $element) {
			if($element && !empty($cat_info[$element]['id'])) {
				$my_cat[]      = $cat_info[$element]['name'];
				$my_cat_link[] = '<a href="' . \DLEUrl::BuildUrl('category', [
						'category' => function_exists('get_url')? get_url($element) : '',
					]) . '">' . $cat_info[$element]['name'] . '</a>';

				if(!empty($cat_info[$element]['disable_comments'])) {
					$allow_comments_in_cat = false;
				}
			}
		}
		$my_cat_link = count($my_cat_link)? implode($config['category_separator'] ?? ' &raquo;', $my_cat_link) : '---';
	}

	$my_cat = count($my_cat)? implode($config['category_separator'] ?? ' &raquo;', $my_cat) : '---';
}

$url_cat = $category_id ?? 0;

if(stripos($tpl->copy_template, '[category=') !== false && function_exists('check_category')) {
	$tpl->copy_template = preg_replace_callback("#\\[(category)=(.+?)\\](.*?)\\[/category\\]#is", 'check_category', $tpl->copy_template);
}

if(stripos($tpl->copy_template, '[not-category=') !== false && function_exists('check_category')) {
	$tpl->copy_template = preg_replace_callback("#\\[(not-category)=(.+?)\\](.*?)\\[/not-category\\]#is", 'check_category', $tpl->copy_template);
}

$category_id = $row['category'];

if(strpos($tpl->copy_template, '[catlist=') !== false && function_exists('check_category')) {
	$tpl->copy_template = preg_replace_callback("#\\[(catlist)=(.+?)\\](.*?)\\[/catlist\\]#is", 'check_category', $tpl->copy_template);
}

if(strpos($tpl->copy_template, '[not-catlist=') !== false && function_exists('check_category')) {
	$tpl->copy_template = preg_replace_callback("#\\[(not-catlist)=(.+?)\\](.*?)\\[/not-catlist\\]#is", 'check_category', $tpl->copy_template);
}

$temp_rating = $config['rating_type'] ?? 0;

if(function_exists('if_category_rating')) {
	$rt = if_category_rating($row['category']);

	if($rt !== false) {
		$config['rating_type'] = $rt;
	}
}

$category_id = $url_cat;

if(!empty($row['category']) && $row['category'] !== '0') {
	$tpl->set('{category-url}', \DLEUrl::BuildUrl('category', [
		'category' => function_exists('get_url')? get_url($row['category']) : '',
	]));
} else {
	$tpl->set('{category-url}', '#');
}

$row['category'] = (int) $row['category'];

if(!$allow_comments_in_cat) {
	$row['comm_num'] = 0;
}

$tpl->set('', [
	'{comments-num}'  => number_format((int) $row['comm_num'], 0, ',', ' '),
	'{views}'         => number_format((int) $row['news_read'], 0, ',', ' '),
	'{category}'      => $my_cat,
	'{link-category}' => $my_cat_link,
	'{news-id}'       => $row['id'],
	'{rssdate}'       => date('r', (int) $row['date']),
	'{rssauthor}'     => (string) $row['autor'],
	'{approve}'       => '',
	'{alt-name}'      => (string) ($row['alt_name'] ?? ''),
]);

// В public AJAX ($langdate часто не загружен) langdate() из functions.inc.php
// падает: strtr(..., string). Используем langdate только при валидном массиве.
$dcLangDateOk = isset($GLOBALS['langdate']) && is_array($GLOBALS['langdate']);
$dcFormatDate = static function(int $stamp, string $format, bool $servertime = false) use ($dcLangDateOk): string {
	if($dcLangDateOk && function_exists('langdate')) {
		return (string) langdate($format, $stamp, $servertime);
	}

	return date('d.m.Y H:i', $stamp);
};

$compare_date = function_exists('compare_days_date')? compare_days_date($row['date'], $short_news_cache) : 2;
$langHeute    = $lang['time_heute'] ?? '';
$langGestern  = $lang['time_gestern'] ?? '';

if(!$compare_date) {
	$tpl->set('{date}', $langHeute . $dcFormatDate((int) $row['date'], ', H:i', (bool) $short_news_cache));
} elseif($compare_date == 1) {
	$tpl->set('{date}', $langGestern . $dcFormatDate((int) $row['date'], ', H:i', (bool) $short_news_cache));
} else {
	$tpl->set('{date}', $dcFormatDate((int) $row['date'], (string) ($config['timestamp_active'] ?? 'j F Y H:i'), (bool) $short_news_cache));
}

$news_date = $row['date'];

if(str_contains((string) $tpl->copy_template, '{date=')) {
	if($dcLangDateOk && function_exists('formdate')) {
		$tpl->copy_template = preg_replace_callback('#\{date=(.+?)\}#i', 'formdate', $tpl->copy_template);
	} else {
		$tpl->copy_template = preg_replace_callback(
			'#\{date=(.+?)\}#i',
			static fn(array $m): string => date('d.m.Y H:i', (int) $news_date),
			(string) $tpl->copy_template,
		);
	}
}

if(strpos($tpl->copy_template, '[new]') !== false || strpos($tpl->copy_template, '[not-new]') !== false) {
	$isNew = !empty($config['post_new'])
	         && function_exists('compare_days_date')
	         && compare_days_date($row['date'], $short_news_cache, true) < $config['post_new'];

	if($isNew) {
		$tpl->set('[new]', '');
		$tpl->set('[/new]', '');
		$tpl->set_block("'\\[not-new\\](.*?)\\[/not-new\\]'si", '');
	} else {
		$tpl->set('[not-new]', '');
		$tpl->set('[/not-new]', '');
		$tpl->set_block("'\\[new\\](.*?)\\[/new\\]'si", '');
	}
}

if(strpos($tpl->copy_template, '[updated]') !== false || strpos($tpl->copy_template, '[not-updated]') !== false) {
	$isUpdated = !empty($config['post_updated'])
	             && !empty($row['editdate'])
	             && !empty($row['view_edit'])
	             && function_exists('compare_days_date')
	             && compare_days_date($row['date'], $short_news_cache, true) > ($config['post_new'] ?? 0)
	             && compare_days_date($row['editdate'], $short_news_cache, true) < $config['post_updated'];

	if($isUpdated) {
		$tpl->set('[updated]', '');
		$tpl->set('[/updated]', '');
		$tpl->set_block("'\\[not-updated\\](.*?)\\[/not-updated\\]'si", '');
	} else {
		$tpl->set('[not-updated]', '');
		$tpl->set('[/not-updated]', '');
		$tpl->set_block("'\\[updated\\](.*?)\\[/updated\\]'si", '');
	}
}

$tpl->set_block("'\\[not-news\\](.*?)\\[/not-news\\]'si", '');
$tpl->set_block("'\\[newscount=(.+?)\\](.*?)\\[/newscount\\]'si", '');
$tpl->set_block("'\\[not-newscount=(.+?)\\](.*?)\\[/not-newscount\\]'si", '');

if(!empty($row['fixed'])) {
	$tpl->set('[fixed]', '');
	$tpl->set('[/fixed]', '');
	$tpl->set_block("'\\[not-fixed\\](.*?)\\[/not-fixed\\]'si", '');
} else {
	$tpl->set('[not-fixed]', '');
	$tpl->set('[/not-fixed]', '');
	$tpl->set_block("'\\[fixed\\](.*?)\\[/fixed\\]'si", '');
}

if(!empty($row['comm_num'])) {
	$tpl->set('[comments]', '');
	$tpl->set('[/comments]', '');
	$tpl->set_block("'\\[not-comments\\](.*?)\\[/not-comments\\]'si", '');
} else {
	$tpl->set('[not-comments]', '');
	$tpl->set('[/not-comments]', '');
	$tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si", '');
}

if(!empty($row['votes'])) {
	$tpl->set('[poll]', '');
	$tpl->set('[/poll]', '');
	$tpl->set_block("'\\[not-poll\\](.*?)\\[/not-poll\\]'si", '');
	$tpl->set('{poll}', '');
} else {
	$tpl->set('[not-poll]', '');
	$tpl->set('[/not-poll]', '');
	$tpl->set_block("'\\[poll\\](.*?)\\[/poll\\]'si", '');
	$tpl->set('{poll}', '');
}

if(!empty($row['view_edit']) && !empty($row['editdate'])) {
	$compare_date = function_exists('compare_days_date')? compare_days_date($row['editdate'], $short_news_cache) : 2;

	if(!$compare_date) {
		$tpl->set('{edit-date}', $langHeute . $dcFormatDate((int) $row['editdate'], ', H:i', (bool) $short_news_cache));
	} elseif($compare_date == 1) {
		$tpl->set('{edit-date}', $langGestern . $dcFormatDate((int) $row['editdate'], ', H:i', (bool) $short_news_cache));
	} else {
		$tpl->set('{edit-date}', $dcFormatDate((int) $row['editdate'], (string) ($config['timestamp_active'] ?? 'j F Y H:i'), (bool) $short_news_cache));
	}

	$news_date = $row['editdate'];

	if(str_contains((string) $tpl->copy_template, '{edit-date=')) {
		if($dcLangDateOk && function_exists('formdate')) {
			$tpl->copy_template = preg_replace_callback('#\{edit-date=(.+?)\}#i', 'formdate', $tpl->copy_template);
		} else {
			$tpl->copy_template = preg_replace_callback(
				'#\{edit-date=(.+?)\}#i',
				static fn(array $m): string => date('d.m.Y H:i', (int) $news_date),
				(string) $tpl->copy_template,
			);
		}
	}

	$tpl->set('{editor}', (string) $row['editor']);
	$tpl->set('{edit-reason}', (string) $row['reason']);

	if(!empty($row['reason'])) {
		$tpl->set('[edit-reason]', '');
		$tpl->set('[/edit-reason]', '');
	} else {
		$tpl->set_block("'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", '');
	}

	$tpl->set('[edit-date]', '');
	$tpl->set('[/edit-date]', '');
} else {
	$tpl->set('{edit-date}', '');
	$tpl->set('{editor}', '');
	$tpl->set('{edit-reason}', '');
	$tpl->set_block("'\\[edit-date\\](.*?)\\[/edit-date\\]'si", '');
	$tpl->set_block("'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", '');
}

if(!empty($config['allow_tags']) && !empty($row['tags'])) {
	$tpl->set('[tags]', '');
	$tpl->set('[/tags]', '');
	$tags = [];

	foreach(explode(',', (string) $row['tags']) as $value) {
		$value = trim($value);

		if($value === '') {
			continue;
		}

		$url_tag = str_replace(['&#039;', '&quot;', '&amp;', '/'], ["'", '"', '&', '&frasl;'], $value);
		$tagEnc  = function_exists('dle_strtolower')? dle_strtolower($url_tag) : mb_strtolower($url_tag);
		$tags[]  = '<a href="' . \DLEUrl::BuildUrl('tags', ['tag' => rawurlencode($tagEnc)]) . '">' . $value . '</a>';
	}

	$tpl->set('{tags}', implode($config['tags_separator'] ?? ', ', $tags));
} else {
	$tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si", '');
	$tpl->set('{tags}', '');
}

if($row['category'] && !empty($cat_info[$row['category']]['icon'])) {
	$tpl->set('{category-icon}', $cat_info[$row['category']]['icon']);
	$tpl->set('[category-icon]', '');
	$tpl->set('[/category-icon]', '');
	$tpl->set_block("'\\[not-category-icon\\](.*?)\\[/not-category-icon\\]'si", '');
} else {
	$tpl->set('{category-icon}', '{THEME}/dleimages/no_icon.gif');
	$tpl->set('[not-category-icon]', '');
	$tpl->set('[/not-category-icon]', '');
	$tpl->set_block("'\\[category-icon\\](.*?)\\[/category-icon\\]'si", '');
}

$ratingType = (string) ($config['rating_type'] ?? '0');

if($ratingType === '1') {
	$tpl->set('[rating-type-2]', '');
	$tpl->set('[/rating-type-2]', '');
	$tpl->set_block("'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", '');
	$tpl->set_block("'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", '');
	$tpl->set_block("'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", '');
} elseif($ratingType === '2') {
	$tpl->set('[rating-type-3]', '');
	$tpl->set('[/rating-type-3]', '');
	$tpl->set_block("'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", '');
	$tpl->set_block("'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", '');
	$tpl->set_block("'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", '');
} elseif($ratingType === '3') {
	$tpl->set('[rating-type-4]', '');
	$tpl->set('[/rating-type-4]', '');
	$tpl->set_block("'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", '');
	$tpl->set_block("'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", '');
	$tpl->set_block("'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", '');
} else {
	$tpl->set('[rating-type-1]', '');
	$tpl->set('[/rating-type-1]', '');
	$tpl->set_block("'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", '');
	$tpl->set_block("'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", '');
	$tpl->set_block("'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", '');
}

if(!empty($row['allow_rate']) && function_exists('ShowRating')) {
	$ug          = $member_id['user_group'] ?? 4;
	$allowRating = !empty($user_group[$ug]['allow_rating']);
	$tpl->set('{rating}', ShowRating($row['id'], $row['rating'], $row['vote_num'], $allowRating? 1 : 0));
	$ratingscore = $row['vote_num']? str_replace(',', '.', (string) round($row['rating'] / $row['vote_num'], 1)) : '0';
	$dislikes    = ($row['vote_num'] - $row['rating']) / 2;
	$likes       = $row['vote_num'] - $dislikes;
	$tpl->set('{ratingscore}', $ratingscore);
	$tpl->set('{likes}', '<span data-likes-id="' . $row['id'] . '">' . $likes . '</span>');
	$tpl->set('{dislikes}', '<span data-dislikes-id="' . $row['id'] . '">' . $dislikes . '</span>');
	$tpl->set('{vote-num}', '<span data-vote-num-id="' . $row['id'] . '">' . $row['vote_num'] . '</span>');
	$tpl->set('[rating]', '');
	$tpl->set('[/rating]', '');
	$tpl->set_block("'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", '');
	$tpl->set_block("'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", '');
} else {
	$tpl->set('{rating}', '');
	$tpl->set('{ratingscore}', '');
	$tpl->set('{vote-num}', '');
	$tpl->set('{likes}', '');
	$tpl->set('{dislikes}', '');
	$tpl->set_block("'\\[rating\\](.*?)\\[/rating\\]'si", '');
	$tpl->set_block("'\\[rating-plus\\](.*?)\\[/rating-plus\\]'si", '');
	$tpl->set_block("'\\[rating-minus\\](.*?)\\[/rating-minus\\]'si", '');
}

$config['rating_type'] = $temp_rating;

$go_page = \DLEUrl::BuildUrl('user', ['user' => rawurlencode((string) $row['autor'])]);
$tpl->set('[day-news]',
	'<a href="' . \DLEUrl::BuildUrl('date.day', [
		'year'  => date('Y', (int) $row['date']),
		'month' => date('m', (int) $row['date']),
		'day'   => date('d', (int) $row['date']),
	]) . '">');
$tpl->set('[/day-news]', '</a>');
$tpl->set('[profile]', '<a href="' . $go_page . '">');
$tpl->set('[/profile]', '</a>');
$tpl->set('{login}', (string) $row['autor']);
$tpl->set('{author}', '<a href="' . $go_page . '">' . $row['autor'] . '</a>');

$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si", '');
$tpl->set_block("'\\[del\\](.*?)\\[/del\\]'si", '');
$tpl->set_block("'\\[complaint\\](.*?)\\[/complaint\\]'si", '');
$tpl->set('{favorites}', '');
$tpl->set_block("'\\[add-favorites\\](.*?)\\[/add-favorites\\]'si", '');
$tpl->set_block("'\\[del-favorites\\](.*?)\\[/del-favorites\\]'si", '');

if(empty($row['full_story']) && !empty($config['hide_full_link'])) {
	$tpl->set_block("'\\[full-link\\](.*?)\\[/full-link\\]'si", '');
} else {
	$tpl->set('[full-link]', '<a href="' . $full_link . '">');
	$tpl->set('[/full-link]', '</a>');
}

$tpl->set('{full-link}', $full_link);

if(!empty($row['allow_comm']) || (!empty($row['comm_num']))) {
	$tpl->set('[com-link]', '<a href="' . $full_link . '#comment">');
	$tpl->set('[/com-link]', '</a>');
} else {
	$tpl->set_block("'\\[com-link\\](.*?)\\[/com-link\\]'si", '');
}

$row['xfields']     = stripslashes((string) $row['xfields']);
$row['short_story'] = stripslashes((string) $row['short_story']);
$row['full_story']  = stripslashes((string) $row['full_story']);

$xfields_in_news = [];

if(class_exists('DLEXFields', false)) {
	\DLEXFields::Compile($row, $tpl, $xfields_in_news);
}

$row['title'] = stripslashes((string) $row['title']);
$tpl->set('{title}', str_replace('&amp;amp;', '&amp;', htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8')));

if(preg_match("#\\{title limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches) && function_exists('clear_content')) {
	$tpl->set($matches[0], clear_content($row['title'], $matches[1]));
}

if(stripos($tpl->copy_template, 'image-') !== false) {
	$images = [];
	preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $row['short_story'] . $row['xfields'] . $row['full_story'], $media);
	$data    = preg_replace('/(img|src)("|\'|="|=\')(.*)/i', '$3', $media[0]);
	$img_arr = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp', 'avif', 'svg'];

	foreach($data as $url) {
		$info = pathinfo($url);

		if(isset($info['extension'])) {
			if(($info['filename'] ?? '') === 'spoiler-plus' || ($info['filename'] ?? '') === 'spoiler-minus'
			   || str_contains((string) ($info['dirname'] ?? ''), 'public/emoticons')
			) {
				continue;
			}

			$ext = strtolower((string) $info['extension']);

			if(in_array($ext, $img_arr, true)) {
				$images[] = $url;
			}
		}
	}

	if(count($images)) {
		$i_count = 0;

		foreach($images as $url) {
			$i_count++;
			$tpl->copy_template = str_replace('{image-' . $i_count . '}', $url, $tpl->copy_template);
			$tpl->copy_template = str_replace('[image-' . $i_count . ']', '', $tpl->copy_template);
			$tpl->copy_template = str_replace('[/image-' . $i_count . ']', '', $tpl->copy_template);
			$tpl->copy_template =
				preg_replace("#\[not-image-{$i_count}\](.+?)\[/not-image-{$i_count}\]#is", '', $tpl->copy_template) ?? $tpl->copy_template;
		}
	}

	$tpl->copy_template = preg_replace('#\[image-(.+?)\](.+?)\[/image-(.+?)\]#is', '', $tpl->copy_template) ?? $tpl->copy_template;
	$tpl->copy_template = preg_replace('#\\{image-(.+?)\\}#i', '{THEME}/dleimages/no_image.jpg', $tpl->copy_template) ?? $tpl->copy_template;
	$tpl->copy_template = preg_replace('#\[not-image-(.+?)\]#i', '', $tpl->copy_template) ?? $tpl->copy_template;
	$tpl->copy_template = preg_replace('#\[/not-image-(.+?)\]#i', '', $tpl->copy_template) ?? $tpl->copy_template;
}

$tpl->set('{short-story}', $row['short_story']);

if(preg_match("#\\{short-story limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches) && function_exists('clear_content')) {
	$tpl->set($matches[0], clear_content($row['short_story'], $matches[1]));
}

if($mode === 'full' || str_contains($tpl->copy_template, '{full-story}')) {
	$fullStory = $row['full_story'] !== ''? $row['full_story'] : $row['short_story'];
	$tpl->set('{full-story}', $fullStory);

	if(preg_match("#\\{full-story limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches) && function_exists('clear_content')) {
		$tpl->set($matches[0], clear_content($fullStory, $matches[1]));
	}
} else {
	$tpl->set('{full-story}', '');
}

$tpl->compile('dc_parse', true, true);

if(count($xfields_in_news) && stripos((string) ($tpl->result['dc_parse'] ?? ''), '[xf') !== false) {
	foreach($xfields_in_news as $key => $value) {
		$tpl->result['dc_parse'] = str_replace($key, $value, $tpl->result['dc_parse']);
	}
}

if(stripos((string) ($tpl->result['dc_parse'] ?? ''), '[hide') !== false) {
	$ugId                    = $member_id['user_group'] ?? 4;
	$tpl->result['dc_parse'] = preg_replace_callback(
		'#\[hide(.*?)\](.+?)\[/hide\]#is',
		static function(array $matches) use ($member_id, $user_group, $lang, $ugId): string {
			$groupsAttr = str_replace(['=', ' '], '', $matches[1]);
			$inner      = $matches[2];

			if($groupsAttr !== '') {
				$groups = explode(',', $groupsAttr);

				if(in_array((string) $ugId, $groups, true) || (string) $ugId === '1') {
					return $inner;
				}

				return '<div class="quote dlehidden">' . ($lang['news_regus'] ?? '') . '</div>';
			}

			if(!empty($user_group[$ugId]['allow_hide'])) {
				return $inner;
			}

			return '<div class="quote dlehidden">' . ($lang['news_regus'] ?? '') . '</div>';
		},
		$tpl->result['dc_parse'],
	);
}

$tpl->result['dc_parse'] = str_ireplace('{PAGEBREAK}', '', (string) ($tpl->result['dc_parse'] ?? ''));
