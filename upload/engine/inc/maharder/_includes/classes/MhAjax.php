<?php



class MhAjax {
	use AssetsChecker;
	use UpdatesChecker;
	use DataLoader;
	use DleData;

	public function __construct() {
	}

	public function getDleUrl() {
		global $config;

		return "{$config['http_home_url']}{$config['admin_path']}";
	}
}