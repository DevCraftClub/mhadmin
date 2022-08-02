<?php

namespace MaHarder\classes;

use Mobile_Detect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use DLEPlugins;

/**
 * @link https://github.com/serbanghita/Mobile-Detect/
 */
require_once DLEPlugins::Check(ENGINE_DIR . '/classes/mobiledetect.class.php');

/**
 * Inspired by bes89
 *
 * @link https://github.com/bes89/mobiledetect-twig-extension
 */
class MobileDetectExtension extends AbstractExtension {
	protected ?Mobile_Detect $detector = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->detector = new Mobile_Detect();
	}

	/**
	 * Twig functions
	 *
	 * @return array
	 */
	public function getFunctions(): array {
		$functions = array(
			new TwigFunction('get_available_devices', array($this, 'getAvailableDevices')),
			new TwigFunction('is_mobile', array($this, 'isMobile')),
			new TwigFunction('is_tablet', array($this, 'isTablet'))
		);

		foreach ($this->getAvailableDevices() as $device => $fixedName) {
			$methodName = 'is' . $device;
			$twigFunctionName = 'is_' . $fixedName;
			$functions[] = new TwigFunction($twigFunctionName, array($this, $methodName));
		}

		return $functions;
	}

	/**
	 * Returns an array of all available devices
	 *
	 * @return array
	 */
	public function getAvailableDevices(): array {
		$availableDevices = array();
		$rules = array();
		$rules = array_unique(array_merge(array_change_key_case($this->detector::getPhoneDevices()), $rules));
		$rules = array_unique(array_merge(array_change_key_case($this->detector::getTabletDevices()), $rules));
		$rules = array_unique(array_merge(array_change_key_case($this->detector::getOperatingSystems()), $rules));
		$rules = array_unique(array_merge(array_change_key_case($this->detector::getBrowsers()), $rules));
		$rules = array_unique(array_merge(array_change_key_case($this->detector::getUtilities()), $rules));

		foreach ($rules as $device => $rule) {
			$availableDevices[$device] = static::fromCamelCase($device);
		}

		return $availableDevices;
	}

	/**
	 * Pass through calls of undefined methods to the mobile detect library
	 *
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->detector, $name), $arguments);
	}

	/**
	 * Converts a string to camel case
	 *
	 * @param $string
	 * @return array|string|string[]|null
	 */
	protected static function toCamelCase($string) {
		return preg_replace('~\s+~', '', lcfirst(str_replace('_', ' ', $string)));
	}

	/**
	 * Converts a string from camel case
	 *
	 * @param        $string
	 * @param string $separator
	 * @return string
	 */
	protected static function fromCamelCase($string, $separator = '_') {
		return strtolower(preg_replace('/(?!^)[[:upper:]]+/', $separator . '$0', $string));
	}

	/**
	 * The extension name
	 *
	 * @return string
	 */
	public function getName() {
		return 'mobile_detect.twig.extension';
	}
}