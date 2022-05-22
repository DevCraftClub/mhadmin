<?php

namespace MaHarder\classes;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AdminUrlExtension extends AbstractExtension implements GlobalsInterface {

	protected static function getServerData()
	: array {
		return $_SERVER;
	}

	protected static function getThisSelf() {
		return $_SERVER['PHP_SELF'];
	}

	protected static function getThisHost() {
		return $_SERVER['HTTP_HOST'];
	}

	protected static function getThisRoot() {
		return $_SERVER['DOCUMENT_ROOT'];
	}

	protected static function getAssetsUrl() {
		return (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http') . '://' . self::getThisHost()
		       . '/engine/inc';
	}

	protected static function getModulesUrl() {
		return (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER']
			: ((!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI']
				: self::getThisSelf() . "?{$_SERVER['QUERY_STRING']}");
	}

	public function getGlobals()
	: array {

		return [
			'assets_url' => self::getAssetsUrl(), '_server' => self::getServerData(),
			'this_self'  => self::getThisSelf(), 'this_host' => self::getThisHost(),
			'plugin_url'  => self::getModulesUrl(),
			'document_root'  => self::getThisRoot(),
		];
	}
}