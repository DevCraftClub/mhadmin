<?php

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

const COMPOSER_DIRECTORY = ENGINE_DIR . '/inc/maharder/_includes/composer';

if (!file_exists(COMPOSER_DIRECTORY . '/vendor/autoload.php')) {
	DataManager::createDir(service : 'ComposerAction', permission : 0777, _path : COMPOSER_DIRECTORY);
	$composer_zip = COMPOSER_DIRECTORY . '/composer.zip';
	copy('https://assets.devcraft.club/composer.zip', $composer_zip);
	$zip = new ZipArchive();
	if ($zip->open($composer_zip) === true) {
		$zip->extractTo(COMPOSER_DIRECTORY);
		$zip->close();
	}
	unlink($composer_zip);
}

require COMPOSER_DIRECTORY . '/vendor/autoload.php';

putenv('COMPOSER_HOME=' . COMPOSER_DIRECTORY . '/vendor/bin/composer');

/**
 * @link https://stackoverflow.com/a/17244866
 */
abstract class ComposerAction {

	/**
	 * @var array
	 */
	private static array $packages = [];

	/**
	 * @throws JsonException
	 */
	public static function init() : void {
		self::$packages = json_decode(file_get_contents(dirname(COMPOSER_DIRECTORY, 2) . '/admin/composer.json'), true, 512, JSON_THROW_ON_ERROR);
		chdir(dirname(COMPOSER_DIRECTORY, 2) . '/admin');
	}
	/**
	 * Initializes the application and returns an instance of the Application class.
	 *
	 * @throws JsonException
	 * @return Application
	 */
	private static function application() : Application {
		// Call the init() method to initialize the application
		self::init();

		// Create a new instance of the Application class
		$application = new Application();

		// Set the auto exit option to false
		$application->setAutoExit(false);

		// Return the initialized application
		return $application;
	}

	/**
	 * @throws JsonException
	 */
	public static function install() : void {
		$app   = self::application();
		$input = new ArrayInput(['command' => 'install']);
		$app->run($input);
	}

	/**
	 * @throws JsonException
	 */
	public static function update() : void {
		$app   = self::application();
		$input = new ArrayInput(['command' => 'update']);
		$app->run($input);
	}

	/**
	 * @throws JsonException
	 */
	public static function require($name = null, $version = "*") : void {
		$app = self::application();
		if (is_null($name)) {
			foreach (self::$packages as $p => $v) {
				self::require($p, $v);
			}
		} else {
			$input = new ArrayInput(['command' => "require {update} \"{$version}\""]);
			$app->run($input);
		}
	}

	public static function destroy() : void {
		if (file_exists(ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php')) {
			array_map('unlink', glob("{COMPOSER_DIRECTORY}/*.*"));
			rmdir(COMPOSER_DIRECTORY);
			putenv('COMPOSER_HOME=' . ENGINE_DIR . '/inc/maharder/_includes/vendor/bin/composer');
		}
	}

}