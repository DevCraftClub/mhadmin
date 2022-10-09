<?php


require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

class MhAjax {
	use DataLoader;
	use AssetsChecker;
	use UpdatesChecker;

	public function __construct() {
		$mh_settings = self::getConfig('maharder');
		LogGenerator::setLogs(isset($mh_settings['logs']));
		LogGenerator::setTelegramType($mh_settings["logs_telegram_type"]);
		LogGenerator::setTelegramBot($mh_settings["logs_telegram_api"]);
		LogGenerator::setTelegramChannel($mh_settings["logs_telegram_channel"]);
		LogGenerator::setTelegramSend(isset($mh_settings["logs_telegram"]));
		LogGenerator::setConsoleLogger(isset($mh_settings["logs_console"]));
		LogGenerator::setChromeLogger(isset($mh_settings["logs_chrome"]));
		LogGenerator::setFirephp(isset($mh_settings["logs_firephp"]));
		LogGenerator::setDbLogs(isset($mh_settings["logs_db"]));
	}
}