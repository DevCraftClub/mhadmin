<?php

namespace MaHarder\classes;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AdminUrlExtension extends AbstractExtension implements GlobalsInterface {

	protected static function getServerData()
	: array {
		return $_SERVER;
	}

	protected static function getUserHash()
	: string {
		global $dle_login_hash;

		return $dle_login_hash;
	}

	protected static function getDleConfig()
	: array {
		global $config;

		return $config;
	}

	protected static function getGetParams()
	: ?array {
		return filter_input_array(INPUT_GET);
	}

	protected static function getPostParams()
	: ?array {
		return filter_input_array(INPUT_POST);
	}

	protected static function getThisSelf() {
		return self::getServerData()['PHP_SELF'];
	}

	protected static function getThisHost() {
		return self::getServerData()['HTTP_HOST'];
	}

	protected static function getThisRoot() {
		return self::getServerData()['DOCUMENT_ROOT'];
	}

	protected static function getAssetsUrl() {
		return (isset(self::getServerData()['HTTPS']) && 'on' === self::getServerData()['HTTPS'] ? 'https' : 'http')
		       . '://' . self::getThisHost() . '/engine/inc';
	}

	protected static function getModulesUrl() {
		return (!empty(self::getServerData()['HTTP_REFERER']))
			? self::getServerData()['HTTP_REFERER']
			: ((!empty(self::getServerData()['REQUEST_URI'])) ? self::getServerData()['REQUEST_URI']
				: self::getThisSelf() . "?{self::getServerData()['QUERY_STRING']}");
	}

	public function getGlobals()
	: array {

		return [
			'assets_url'     => self::getAssetsUrl(), 'plugin_url' => self::getModulesUrl(),
			'dle_login_hash' => self::getUserHash(), 'dle_config' => self::getDleConfig(),
			'_server'        => self::getServerData(), '_get' => self::getGetParams(), '_post' => self::getPostParams(),
		];
	}
}