<?php

define('MH_DIR', dirname(__FILE__));
define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . THIS_HOST);

$config = [
    'api_url' => 'http://dle141.local/api/v1',
    'api_key' => 'ef35-db49-fa41-ab6a-74a3'
];

require_once MH_DIR . '/vendor/autoload.php';
require_once MH_DIR . '/modules/functions.php';

$cssArr = [
	URL . '/maharder/admin/assets/css/base.css',
	URL . '/maharder/admin/assets/css/icons.css',
	URL . '/maharder/admin/assets/css/theme.css',

];

$jsArr = [
	URL . '/maharder/admin/assets/js/jquery.js',
	URL . '/maharder/admin/assets/js/base.js',
	URL . '/maharder/admin/assets/js/theme.js',

];

$variables = [
	'css_dir' => URL . '/maharder/admin/assets/css',
	'js_dir' => URL . '/maharder/admin/assets/js',
	'css' => htmlStatic($cssArr ),
	'js' => htmlStatic($jsArr, 'html', 'js'),
	'title' => 'Basis HTML'
];

$loader = new \Twig\Loader\FilesystemLoader(MH_DIR . '/templates');
$mh_admin = new \Twig\Environment($loader, [
    'cache' => MH_DIR . 'cache',
	'debug' => true
]);

?>