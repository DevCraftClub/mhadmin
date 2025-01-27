<?php

//namespace MaHarder\classes;

use Detection\MobileDetect;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @link https://github.com/serbanghita/Mobile-Detect/
 */
require_once DLEPlugins::Check(ENGINE_DIR . '/classes/mobiledetect.class.php');

/**
 * Расширение Twig, основанное на MobileDetect.
 *
 * Это расширение предоставляет функции, которые позволяют определять устройства,
 * такие как мобильные телефоны, планшеты, операционные системы и браузеры, через Twig.
 *
 * @link https://github.com/bes89/mobiledetect-twig-extension Инспирация для разработки.
 * @link https://github.com/serbanghita/Mobile-Detect Библиотека Mobile-Detect.
 */
class MobileDetectExtension extends AbstractExtension {

	/**
	 * @var MobileDetect|null
	 */
	protected ?MobileDetect $detector = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->detector = new MobileDetect();
	}

	/**
	 * Twig functions
	 *
	 * @return array
	 */
	public function getFunctions() : array {
		$functions = array(
			new TwigFunction('get_available_devices', array($this, 'getAvailableDevices')),
			new TwigFunction('is_mobile', array($this, 'isMobile')),
			new TwigFunction('is_tablet', array($this, 'isTablet'))
		);

		foreach ($this->getAvailableDevices() as $device => $fixedName) {
			$methodName       = 'is' . $device;
			$twigFunctionName = 'is_' . $fixedName;
			$functions[]      = new TwigFunction($twigFunctionName, array($this, $methodName));
		}

		return $functions;
	}

	/**
	 * Returns an array of all available devices
	 *
	 * @return array
	 */
	public function getAvailableDevices() : array {
		$availableDevices = array();
		$rules            = array();
		$rules            = array_unique(array_merge(array_change_key_case($this->detector::getPhoneDevices()), $rules));
		$rules            = array_unique(array_merge(array_change_key_case($this->detector::getTabletDevices()), $rules));
		$rules            = array_unique(array_merge(array_change_key_case($this->detector::getOperatingSystems()), $rules));
		$rules            = array_unique(array_merge(array_change_key_case($this->detector::getBrowsers()), $rules));

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
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->detector, $name), $arguments);
	}

	/**
	 * Converts a string to camel case
	 *
	 * @param    string    $string
	 *
	 * @return array|string|string[]|null
	 */
	protected static function toCamelCase(string $string) : array|string|null {
		return preg_replace('~\s+~', '', lcfirst(str_replace('_', ' ', $string)));
	}

	/**
	 * Converts a string from camel case
	 *
	 * @param    string    $string
	 * @param    string    $separator
	 *
	 * @return string
	 */
	protected static function fromCamelCase(string $string, string $separator = '_') : string {
		return strtolower(preg_replace('/(?!^)[[:upper:]]+/', $separator . '$0', $string));
	}

	/**
	 * The extension name
	 *
	 * @return string
	 */
	public function getName() : string {
		return 'mobile_detect.twig.extension';
	}
}