<?php


require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(__DIR__ . '/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/AssetsChecker.php');

class Ajax {
	use DataLoader;
	use AssetsChecker;

	public function __construct() {
		$mh_settings = self::getConfig('maharder');
		LogGenerator::setLogs(isset($mh_settings['logs']));
		LogGenerator::setTelegramType($mh_settings["logs_telegram_type"]);
		LogGenerator::setTelegramBot($mh_settings["logs_telegram_api"]);
		LogGenerator::setTelegramChannel($mh_settings["logs_telegram_channel"]);
		LogGenerator::setTelegramSend(isset($mh_settings["logs_telegram"]));
	}
}