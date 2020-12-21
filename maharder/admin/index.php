<?php

define('MH_DIR', dirname(__FILE__));
define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('THIS_SELF', $_SERVER['PHP_SELF']);
define('URL', (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http').'://'.THIS_HOST);

$config = [
	'api_url' => 'http://dle141.local/api/v1',
	'api_key' => 'ef35-db49-fa41-ab6a-74a3',
];

require_once MH_DIR.'/vendor/autoload.php';
require_once MH_DIR.'/modules/functions.php';

$cssArr = [
	URL.'/maharder/admin/assets/css/base.css',
	URL.'/maharder/admin/assets/css/icons.css',
	URL.'/maharder/admin/assets/css/tokens.css',
	URL.'/maharder/admin/assets/css/prettify.css',
	URL.'/maharder/admin/assets/css/theme.css',
];

$jsArr = [
	URL.'/maharder/admin/assets/js/jquery.js',
	URL.'/maharder/admin/assets/js/base.js',
	URL.'/maharder/admin/assets/js/autosize.min.js',
	URL.'/maharder/admin/assets/js/mask.js',
	URL.'/maharder/admin/assets/js/tokens.js',
	URL.'/maharder/admin/assets/js/theme.js',
];

$variables = [
	'css_dir' => URL.'/maharder/admin/assets/css',
	'js_dir' => URL.'/maharder/admin/assets/js',
	'css' => htmlStatic($cssArr),
	'js' => htmlStatic($jsArr, 'html', 'js'),
	'author' => [
		'name' => 'Maxim Harder',
		'contacts' => [
			[
				'name' => 'E-Mail',
				'link' => 'mailto:dev@devcraft.club',
			],
			[
				'name' => 'Telegram',
				'link' => 'https://t.me/@MaHarder',
			],
			[
				'name' => 'Вебсайт',
				'link' => 'https://devcraft.club/misc/contact',
			],
		],
		'donate' => [
			[
				'name' => 'WME (Webmoney (EU))',
				'value' => 'E275336355586',
				'link' => '',
			],
			[
				'name' => 'WMZ (Webmoney (USD))',
				'value' => 'Z139685140004',
				'link' => '',
			],
			[
				'name' => 'WMR (Webmoney (RU))',
				'value' => 'R127552376453',
				'link' => '',
			],
			[
				'name' => 'PayPal',
				'value' => 'paypal.me/MaximH',
				'link' => 'https://paypal.me/MaximH',
			],
			[
				'name' => 'Ko-Fi',
				'value' => 'ko-fi.com/devcraft',
				'link' => 'https://ko-fi.com/J3J118N1C',
			],
			[
				'name' => 'Yandex.Money',
				'value' => '41001454367103',
				'link' => 'https://sobe.ru/na/devcraftclub',
			],
			[
				'name' => 'DonationAlerts',
				'value' => '/r/maharder',
				'link' => 'https://www.donationalerts.com/r/maharder',
			],
		],
	],
];

$links = [
	[
		'name' => 'Главная',
		'href' => THIS_SELF,
		'type' => 'link', // divider, link, dropdown
		'children' => [],
	],
];

$breadcrumbs = [
	[
		'name' => 'Главная',
		'url' => THIS_SELF,
	],
];

$loader = new \Twig\Loader\FilesystemLoader(MH_DIR.'/templates');
$mh_admin = new \Twig\Environment($loader, [
	'cache' => MH_DIR.'/cache',
	'debug' => true,
]);