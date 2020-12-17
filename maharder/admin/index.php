<?php

define('MH_DIR', dirname(__FILE__));
define('THIS_HOST', $_SERVER['HOST']);
define('URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . THIS_HOST);

$config = [
    'api_url' => 'http://dle141.local/api/v1',
    'api_key' => 'ef35-db49-fa41-ab6a-74a3'
];

require_once MH_DIR . '/vendor/autoload.php';
require_once MH_DIR . '/modules/functions.php';


$variables = [
	'css_dir' => URL . '/assets/css',
	'js_dir' => URL . '/assets/js',
	
];

?>