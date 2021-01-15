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