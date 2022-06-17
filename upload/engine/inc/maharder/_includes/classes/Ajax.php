<?php


require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DataLoader.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/AssetsChecker.php');

class Ajax {
	use LogGenerator;
	use DataLoader;
	use AssetsChecker;

	public function __construct() {
		$mh_settings = $this->getConfig('maharder');
		$this->setLogs(isset($mh_settings['logs']));
		$this->setTelegramType($mh_settings["logs_telegram_type"]);
		$this->setTelegramBot($mh_settings["logs_telegram_api"]);
		$this->setTelegramChannel($mh_settings["logs_telegram_channel"]);
		$this->setTelegramSend(isset($mh_settings["logs_telegram"]));
	}
}