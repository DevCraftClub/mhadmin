<?php

declare(strict_types=1);

/**
 * AJAX инициализации DevCraft без vendor: скачивание composer.phar и фоновый composer install.
 *
 * Вызывается из bootstrap.php, пока нет autoload.
 */

error_reporting(E_ALL ^ E_WARNING ^ E_DEPRECATED ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', dirname(__DIR__));
define('ENGINE_DIR', ROOT_DIR . '/engine');

require_once ENGINE_DIR . '/classes/plugins.class.php';
require_once DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php');
require_once DLEPlugins::Check(ROOT_DIR . '/devcraft/src/bootstrap/ajax-session.php');

header('Content-Type: application/json; charset=utf-8');

/**
 * @param array{currentStep:string,status:string,message:string,logExcerpt:string} $payload
 */
$dcBootstrapFail = static function (int $code, string $message) : void {
	http_response_code($code);
	echo json_encode([
		'currentStep' => 'auth',
		'status'      => 'failed',
		'message'     => $message,
		'logExcerpt'  => '',
	], JSON_UNESCAPED_UNICODE);
	exit;
};

if(!defined('LOGGED_IN') || LOGGED_IN !== true) {
	$dcBootstrapFail(403, 'Нужен вход в админку');
}

global $member_id, $user_group;

$groupId    = (int) ($member_id['user_group'] ?? 0);
$allowAdmin = (int) ($user_group[$groupId]['allow_admin'] ?? 0);

if($allowAdmin !== 1) {
	$dcBootstrapFail(403, 'Нет доступа к админке');
}

require_once DLEPlugins::Check(ROOT_DIR . '/devcraft/src/bootstrap/bootstrap-install.php');

$operation = (string) ($_POST['operation'] ?? 'status');

if(!in_array($operation, ['start', 'status', 'retry', 'next'], true)) {
	$operation = 'status';
}

try {
	dc_bootstrap_send_json(dc_bootstrap_build_response($operation));
} catch(Throwable $e) {
	http_response_code(500);
	dc_bootstrap_send_json([
		'currentStep' => 'install_defaults',
		'status'      => 'failed',
		'message'     => 'Ошибка bootstrap',
		'logExcerpt'  => $e->getMessage(),
	]);
}
