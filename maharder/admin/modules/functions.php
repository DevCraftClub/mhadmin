<?php

// File: functions.php
// Path: /engine/inc/maharder/admin/modules/functions.php
// ============================================================ =
// Author: Maxim Harder <dev@devcraft.club> (c) 2020
// Website: https://www.devcraft.club
// Telegram: http://t.me/MaHarder
// ============================================================ =
// Do not change anything!
//===============================================================

function letsCurl($section, $request = 'GET', $header = [], $post = [])
{
	global $config;

	$curl = curl_init();

	$headerOptions = [
		'Content-Type: application/x-www-form-urlencoded',
		"x-api-key: {$config['api_key']}",
	];

	if (!empty($header)) {
		foreach ($header as $head) {
			$headerOptions[] = $head;
		}
	}

	$curlOptions = [
		CURLOPT_URL => "{$config['api_url']}/{$section}/",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_HTTPHEADER => $headerOptions,
		CURLOPT_CUSTOMREQUEST => $request,
	];
	if (!empty($post)) {
		$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($post);
	}
	curl_setopt_array($curl, $curlOptions);

	$response = curl_exec($curl);

	curl_close($curl);

	return $response;
}

function htmlStatic($data, $view = 'html', $type = 'css')
{
	$out = [];
	if ('html' == $view) {
		if (is_array($data)) {
			foreach ($data as $d) {
				if ('css' == $type) {
					$out[] = "<link rel='stylesheet' type='text/css' href='{$d}'>";
				} elseif ('js' == $type) {
					$out[] = "<script src='{$d}'></script>";
				}
			}
		} else {
			if ('css' == $type) {
				$out[] = "<link rel='stylesheet' type='text/css' href='{$data}'>";
			} elseif ('js' == $type) {
				$out[] = "<script src='{$data}'></script>";
			}
		}
	} elseif ('link' == $view) {
		if (is_array($data)) {
			foreach ($data as $d) {
				$out[] = $d;
			}
		} else {
			$out[] = $data;
		}
	}

	return $out;
}

function addInput($name, $value, $label, $chosen = false, $type = 'text')
{
	$placebo = $chosen ? 'class="chosen"' : '';
	$extra = '';
	if ('file' == $type) {
		if (!empty($value)) {
			$extra = "<br><div class='ui checkbox'>";
			$extra .= addCheckbox($name.'_check', '', 'hidden');
			$extra .= '<label for="'.$name.'_check">'.str_replace('%s', $label, translate('%s заменить?')).'</label></div>';
		}
		$placebo = 'class="icheck"';
	}

	return "<div class=\"field\"><input type=\"{$type}\" id=\"{$name}\" name=\"{$name}\" placeholder=\"{$label}\" value=\"{$value}\" {$placebo}>{$extra}</div>";
}

function addCheckbox($name, $selected, $class = 'switch')
{
	$selected = $selected ? 'checked' : '';

	return "<input id=\"{$name}\" class=\"{$class}\" tabindex='0' type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";
}

function saveButton($save = '', $name = 'save', $type = 'link')
{
	$save = $save ? translate($save) : translate('Сохранить');
	$btn = "<a role='button' class=\"fluid ui positive button {$name}\" ><i class=\"fas fa-save\"></i>&nbsp;&nbsp;{$save}</a>";
	if ('submit' == $type) {
		$btn = "<button type='submit' class=\"fluid ui positive button {$name}\" ><i class=\"fas fa-save\"></i>&nbsp;&nbsp;{$save}</button>";
	}

	return $btn;
}

function addTextarea($name, $value, $label, $editor = false, $params = ['id' => 0, 'name' => '', 'field' => ''])
{
	if ($editor) {
		global $config, $i18n_lang, $i18n, $mod_admin;
		$iso = $i18n_lang['active'][$i18n->getLocale()]['iso2'];
		if (1 == $config['allow_admin_wysiwyg'] || 2 == $config['allow_admin_wysiwyg']) {
			$mod_admin->setCssfiles([$config['http_home_url'].'engine/editor/jscripts/froala/css/editor.css']);
			$mod_admin->setJsfiles([
				$config['http_home_url'].'engine/skins/codemirror/js/code.js',
				$config['http_home_url'].'engine/editor/jscripts/froala/editor.js',
			]);
			if (file_exists(ROOT_DIR."/engine/editor/jscripts/froala/languages/{$iso}.js")) {
				$mod_admin->setJsfiles([$config['http_home_url']."engine/editor/jscripts/froala/languages/{$iso}.js"]);
			}
		} else {
			$mod_admin->setCssfiles([$config['http_home_url'].'engine/skins/maharder/css/theme/default/wbbtheme.css']);
			$mod_admin->setJsfiles([$config['http_home_url'].'engine/skins/maharder/js/jquery.wysibb.min.js']);
			if (file_exists(ROOT_DIR."/engine/skins/maharder/js/lang/{$iso}.js")) {
				$mod_admin->setJsfiles([$config['http_home_url']."engine/skins/maharder/js/lang/{$iso}.js"]);
			}
		}

		$textFields = [
			translate('Новое звено в списке'),
			translate('Скрытый текст'),
		];

		if ((1 == $config['allow_admin_wysiwyg'] || 2 == $config['allow_admin_wysiwyg'])) {
			include_once DLEPlugins::Check(ENGINE_DIR.'/classes/parse.class.php');

			$parse = new ParseFilter();
			$value = $parse->BB_Parse($value);
			require_once DLEPlugins::Check(MH_DIR.'/editor.php');
			$out = insertEditor($name, $iso, $params['id'], $params['name'], $params['field'], $value);
		} else {
			$out = "<div class=\"field\"><textarea id=\"{$name}\" name=\"{$name}\" placeholder=\"{$label}\">{$value}</textarea></div>";
			$out .= <<<HTML
			<script>
				var wbbOpt = {
					lang: '{$iso}',
					onlyBBmode: true,
					buttons: "bold,italic,underline,strike,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,listitem,|,spoiler,removeFormat",
					allButtons: {
						bold: {
							hotkey: "ctrl+b",
							transform: {
								'<b>{SELTEXT}</b>':'[b]{SELTEXT}[/b]'
							}
						},
						italic: {
							hotkey: "ctrl+i",
							transform: {
								'<i>{SELTEXT}</i>':'[i]{SELTEXT}[/i]'
							}
						},
						underline: {
							hotkey: "ctrl+u",
							transform: {
								'<u>{SELTEXT}</u>':'[u]{SELTEXT}[/u]'
							}
						},
						strike: {
							hotkey: "ctrl+s",
							transform: {
								'<s>{SELTEXT}</s>':'[s]{SELTEXT}[/s]'
							}
						},
						bullist : {
							transform : {
								'<ul class="ui bulleted list">{SELTEXT}</ul>':"[list]{SELTEXT}[/list]",
								'<li class="item">{SELTEXT}</li>':"[*]{SELTEXT}"
							}
						},
						numlist : {
							transform : {
								'<ol class="ui bulleted list">{SELTEXT}</ol>':"[list=1]{SELTEXT}[/list]",
								'<li class="item">{SELTEXT}</li>':"[*]{SELTEXT}"
							}
						},
						listitem: {
							title: '{$textFields[0]}',
							buttonText: 'Item',
							transform: {
								'<li class="item">{SELTEXT}</li>':"[*]{SELTEXT}"
							}
						},
						spoiler: {
							title: '{$textFields[1]}',
							buttonText: 'SPOILER',
							transform: {
								'[spoiler]{SELTEXT}[/spoiler]':"[spoiler]{SELTEXT}[/spoiler]"
							}
						}
					}
				};

				$(function() {
					$('#{$name}').wysibb(wbbOpt);
				});
			</script>
HTML;
		}
	} else {
		$out = "<div class=\"field\"><textarea id=\"{$name}\" name=\"{$name}\" placeholder=\"{$label}\">{$value}</textarea></div>";
	}

	return $out;
}

function addSelect($name, $value, $label, $selected)
{
	$output = "<div class=\"field\"><div class=\"ui search clearable selection dropdown\"><input type=\"hidden\" name=\"{$name}\" value=\"{$selected}\"><i class=\"dropdown icon\"></i><div class=\"default text\">{$label}</div><div class=\"menu\">";
	foreach ($value as $values => $description) {
		if (is_array($description)) {
			foreach ($description as $did => $dname) {
				$output .= "<div data-value=\"{$did}\" class=\"item";
				if ($selected == $did) {
					$output .= '  active selected';
				}
				$output .= "\">{$dname}</div>\n";
			}
		} else {
			$output .= "<div data-value=\"{$values}\" class=\"item";
			if ($selected == $values) {
				$output .= '  active selected';
			}
			$output .= "\">{$description}</div>\n";
		}
	}
	$output .= '</div></div></div>';

	return $output;
}

function addChosenSelect($name, $value, $selected)
{
	$tempList = [];
	$tempList2 = [];
	$tempList3 = [];
	$sels = explode(',', $selected);
	if (is_array($value)) {
		foreach ($value as $id => $data) {
			if (in_array($id, $sels, true)) {
				$tempList2[] = '<a class="ui label transition visible" data-value="'.$id.'" style="display: inline-block !important;">'.$data.'<i class="delete icon"></i></a>';
				$activ2 = ' active filtered';
				$active = ' selected';
			} else {
				$active = '';
				$activ2 = '';
			}
			$tempList[] = '<option value="'.$id.'" '.$active.' >'.$data.'</option>';
			$tempList3[] = '<div class="item'.$activ2.'" data-value="'.$id.'">'.$data.'</div>';
		}
	}
	$output = "<div class=\"inline field\"><div class=\"label ui selection clearable search fluid dropdown multiple\" tabindex=\"0\"><select name=\"{$name}\" multiple=\"\" class=\"\">";
	$output .= implode('', $tempList);
	$output .= "</select><input name='{$name}' type='hidden' value='{$selected}'><i class=\"dropdown icon\"></i>";
	$output .= implode('', $tempList2);
	$output .= '<div class="text"></div><div class="menu transition hidden" tabindex="-1">';
	$output .= implode('', $tempList3);
	$output .= '</div></div></div>';
	unset($tempList);

	return $output;
}

function segRow($name, $descr, $id, $variable, $type, $values = '', $extra = [])
{
	$act = '';
	if ('input' === $type) {
		$act = addInput($id, $variable[$id], $name);
	} elseif ('tag_input' === $type) {
		$act = addInput($id, $variable[$id], $name, true);
	} elseif ('number_input' === $type || 'number' === $type) {
		$act = addInput($id, $variable[$id], $name, false, 'number');
	} elseif ('hidden_input' === $type) {
		$act = addInput($id, $variable[$id], $name, false, 'hidden');
	} elseif ('input_file' === $type) {
		$act = addInput($id, $variable[$id], $name, false, 'file');
	} elseif ('select' === $type) {
		$act = addSelect($id, $values, $name, $variable[$id]);
	} elseif ('chosen_select' === $type) {
		$act = addChosenSelect($id, $values, $variable[$id]);
	} elseif ('textarea' === $type) {
		$act = addTextarea($id, $variable[$id], $name);
	} elseif ('editor_textarea' === $type) {
		$act = addTextarea($id, $variable[$id], $name, true, $extra);
	} elseif ('checkbox' === $type) {
		$act = addCheckbox($id, 1 == $variable[$id]);
	}

	$out = "<div class=\"two column row\"><div class=\"four wide column\"><label for=\"{$id}\">{$name}</label><br><small>{$descr}</small></div><div class=\"twelve wide column\">";
	$out .= $act;
	$out .= '</div></div>';

	return $out;
}

function getXfields($id, $type = 'post')
{
	global $db;
	if ('post' === $type) {
		$post = $db->super_query('SELECT xfields FROM '.PREFIX."_post WHERE id = '{$id}'");
	} elseif ('user' === $type) {
		$post = $db->super_query('SELECT xfields FROM '.PREFIX."_users WHERE user_id = '{$id}'");
	}

	if ($post) {
		$xfout = [];
		$fields = explode('||', $post['xfields']);
		foreach ($fields as $key => $value) {
			$xfout[$key] = $value;
		}
	} else {
		$xfout = false;
	}

	return $xfout;
}

function loadXfields($type = 'post')
{
	if ('post' === $type) {
		$xf_file = file(ENGINE_DIR.'/data/xfields.txt');
	} elseif (('user' === $type)) {
		$xf_file = file(ENGINE_DIR.'/data/xprofile.txt');
	}

	$xf_info = [];
	foreach ($xf_file as $line) {
		$info = explode('|', $line);
		$xf_info[$info[0]] = $info[1];
	}

	return $xf_info;
}

function addDocItem($icon, $header, $subheader, $content)
{
	$out = "<h2 class=\"ui header\"><i class=\"{$icon}\"></i><div class=\"content\">{$header}";
	if (isset($subheader)) {
		$out .= "<div class=\"sub header\">{$subheader}</div>";
	}
	$out .= '</div></h2>';
	$out .= $content;

	return $out;
}

function stepByStep($items, $type = 'ol')
{
	if ('ol' == $type) {
		$list = 'ol';
	} elseif ('li' == $type) {
		$list = 'li';
	}
	$out = "<{$list}>";
	foreach ($items as $item) {
		$out .= "<li>{$item}</li>";
	}
	$out .= "</{$list}>";

	return $out;
}

function docMenu($items)
{
	global $mh_lang;
	$i = 0;
	foreach ($items as $name => $keys) {
		if (0 == $i) {
			$active = ' active';
		} else {
			$active = '';
		}
		$out[] = '<a href="#" class="item'.$active.'" data-tab="'.$name.'"><i class="'.$keys['icon'].'"></i> '.$keys['name'].'</a>';
		++$i;
	}
	$out[] = "<a class=\"item\" data-tab=\"help\"><i class=\"user circle icon\"></i> {$mh_lang['functions_01']}</a>";

	return implode('', $out);
}

function docBoxes($name, $items, $first = false)
{
	if ($first) {
		$active = ' active';
	} else {
		$active = '';
	}
	$input = implode('', $items);

	return '<div class="ui segment tab'.$active.'" data-tab="'.$name.'">'.$input.'</div>';
}

function docPage($columnOne, $columnTwo, $helplink = '', $sitelink = '')
{
	global $mh_lang;

	$st_link = str_replace('\helplink', $helplink, $mh_lang['functions_07']);
	$st_link = str_replace('\sitelink', $sitelink, $mh_lang['functions_07']);
	$out = <<<HTML
<div class="ui fluid container">
<div class="ui equal width stackable divided grid">
		<div class="three wide column sticky">
			<div class="ui vertical fluid tabular menu docMenu">
				{$columnOne}
			</div>
		</div>
		<div class="column content docContent">
			{$columnTwo}
			<div class="ui segment tab" data-tab="help">
				<h2 class="ui header">
					<i class="user circle icon"></i>
					<div class="content">
					   {$mh_lang['functions_01']}
						<div class="sub header">{$mh_lang['functions_02']} <a href="{$helplink}" target="_blank">{$mh_lang['functions_03']}<i class="fas fa-external-link-alt"></i></a></div>
					</div>
				</h2>
				<p>{$mh_lang['functions_04']}</p>
				<p>{$mh_lang['functions_05']}</p>
				<p>{$mh_lang['functions_06']}</p>
				<ul>
					<li>{$st_link}</li>
					<li>{$mh_lang['functions_08']}</li>
					<li>{$mh_lang['functions_09']}</li>
					<li>{$mh_lang['functions_10']}</li>
					<li>{$mh_lang['functions_11']}</li>
					<li>{$mh_lang['functions_12']}</li>
				</ul>
				<p>{$mh_lang['functions_13']}</p>
				<ul>
					<li>{$mh_lang['functions_14']}</li>
					<li>{$mh_lang['functions_15']}</li>
					<li>{$mh_lang['functions_16']}</li>
					<li>{$mh_lang['functions_17']}</li>
					<li>{$mh_lang['functions_18']}</li>
					<li>{$mh_lang['functions_19']}</li>
				</ul>
				<p>{$mh_lang['functions_20']}</p>
				<ul>
					<li>{$mh_lang['functions_21']} Maxim Harder</li>
					<li>{$mh_lang['functions_22']} <a href="https://t.me/MaHarder" target="_blank" rel="noopener">MaHarder</a></li>
				</ul>
				<p>{$mh_lang['functions_23']}</p>
				<ul>
					<li><strong>Webmoney (RU)</strong>:&nbsp;R127552376453</li>
					<li><strong>Webmoney (USD)</strong>:&nbsp;Z139685140004</li>
					<li><strong>Webmoney (EU)</strong>:&nbsp;E275336355586</li>
					<li><strong>PayPal</strong>:&nbsp;<a href="https://paypal.me/MaximH" target="_blank" rel="noopener">paypal.me/MaximH</a></li>
			</ul>
		</div>
	</div>
</div>
</div>
HTML;

	echo $out;
}

function getUsers()
{
	global $db;

	$db->query('SELECT * FROM '.PREFIX."_users WHERE restricted = '0'");
	$user_ar = [];

	while ($build = $db->get_array()) {
		$user_ar[$build['user_id']] = $build['name'];
	}

	$db->free();

	return $user_ar;
}

function getCategories($news_id, $link = false)
{
	global $db, $config, $PHP_SELF, $languages_config;
	$langCode = strtolower(substr($languages_config['use_language'], 0, 1));
	$catName = 'name';
	if ($languages_config['use_language'] !== $languages_config['default_language']) {
		$catName .= '_'.$langCode;
	}

	$cat_name = [];
	$cats = $db->super_query('SELECT category FROM '.PREFIX."_post WHERE id = '{$news_id}'");
	$cat = explode(',', $cats['category']);
	foreach ($cat as $category) {
		$temp_cat = $db->super_query('SELECT * FROM '.PREFIX."_category WHERE id = '{$category}'");
		if ($link) {
			if ($config['allow_alt_url']) {
				$pid = $temp_cat['parentid'];
				$url = '&lt;a href="'.$config['http_home_url'];
				$parent_list = [];
				if (isset($pid) && 0 != $pid) {
					while (0 != $pid) {
						$par_id = $db->super_query('SELECT * FROM '.PREFIX."_category WHERE id = '{$pid}'");
						$parent_list[] = $par_id['alt_name'];
						$pid = $par_id['parentid'];
					}
				}
				rsort($parent_list);
				$parent_list[] = $temp_cat['alt_name'];
				$url .= implode('/', $parent_list)."/\" &gt;{$temp_cat[$catName]}&lt;/a&gt;";
				$cat_name[] = $url;
			} else {
				$cat_name[] = "&lt;a href=\"{$PHP_SELF}?do=cat&amp;category={$temp_cat['alt_name']}\"&gt;{$temp_cat[$catName]}&lt;/a&gt;";
			}
		} else {
			$cat_name[] = $temp_cat[$catName];
		}
	}

	return implode($config['category_separator'].' ', $cat_name);
}

function getCats()
{
	global $db, $languages_config;
	$langCode = strtolower(substr($languages_config['use_language'], 0, 1));
	$catName = 'name';
	if ($languages_config['use_language'] !== $languages_config['default_language']) {
		$catName .= '_'.$langCode;
	}
	$cats = $db->query("SELECT id, {$catName} FROM ".PREFIX."_category ORDER by {$catName}");
	$categories = [];
	while ($entry = $db->get_array($cats)) {
		$categories[$entry['id']] = $entry[$catName];
	}
	unset($cats);

	return $categories;
}

if (!function_exists('mkdir_p')) {
	function mkdir_p($dir)
	{
		// WPF Function
		if (!function_exists('wp_is_stream')) {
			function wp_is_stream($path)
			{
				$scheme_separator = strpos($path, '://');

				if (false === $scheme_separator) {
					return false;
				}

				$stream = substr($path, 0, $scheme_separator);

				return in_array($stream, stream_get_wrappers(), true);
			}
		}

		$wrapper = null;

		if (wp_is_stream($dir)) {
			list($wrapper, $target) = explode('://', $dir, 2);
		}

		$dir = str_replace('//', '/', $dir);

		if (null !== $wrapper) {
			$dir = $wrapper.'://'.$dir;
		}

		$dir = rtrim($dir, '/');
		if (empty($target)) {
			$dir = '/';
		}

		if (file_exists($dir)) {
			return @is_dir($dir);
		}

		$dir_parent = dirname($dir);
		while ('.' !== $dir_parent && !is_dir($dir_parent)) {
			$dir_parent = dirname($dir_parent);
		}

		if ($stat = @stat($dir_parent)) {
			$dir_perms = $stat['mode'] & 0007777;
		} else {
			$dir_perms = 0777;
		}

		if (mkdir($dir, $dir_perms, true) || is_dir($dir)) {
			if ($dir_perms != ($dir_perms & ~umask())) {
				$folder_parts = explode('/', substr($dir, strlen($dir_parent) + 1));
				for ($i = 1, $iMax = count($folder_parts); $i <= $iMax; ++$i) {
					@chmod($dir_parent.'/'.implode('/', array_slice($folder_parts, 0, $i)), $dir_perms);
				}
			}

			return true;
		}

		return false;
	}
}