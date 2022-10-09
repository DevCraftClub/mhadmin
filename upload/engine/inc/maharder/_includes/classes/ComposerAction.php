<?php

	use Composer\Console\Application;
	use Symfony\Component\Console\Input\ArrayInput;

	const COMPOSER_DIRECTORY = ENGINE_DIR.'/inc/maharder/_includes/composer';

	if ( !file_exists(COMPOSER_DIRECTORY.'/vendor/autoload.php')) {
		@mkdir(COMPOSER_DIRECTORY, 0755, true);
		$composer_zip = COMPOSER_DIRECTORY.'/composer.zip';
		copy('https://assets.devcraft.club/composer.zip', $composer_zip);
		$zip = new ZipArchive();
		if ($zip->open($composer_zip) === true) {
			$zip->extractTo(COMPOSER_DIRECTORY);
			$zip->close();
		}
		unlink($composer_zip);
	}

	require COMPOSER_DIRECTORY.'/vendor/autoload.php';

	putenv('COMPOSER_HOME='.COMPOSER_DIRECTORY.'/vendor/bin/composer');

	/**
	 * @link https://stackoverflow.com/a/17244866
	 */
	abstract class ComposerAction {

		/**
		 * @var array
		 */
		private static $packages = [];

		public static function init() {
			self::$packages = json_decode(file_get_contents(dirname(COMPOSER_DIRECTORY, 2).'/admin/composer.json'), true);
			chdir(dirname(COMPOSER_DIRECTORY, 2).'/admin');
		}

		private static function application() {
			self::init();
			$application = new Application();
			$application->setAutoExit(false);

			return $application;
		}

		public static function install() {
			$app = self::application();
			$input = new ArrayInput(['command' => 'install']);
			$app->run($input);
		}

		public static function update() {
			$app = self::application();
			$input = new ArrayInput(['command' => 'update']);
			$app->run($input);
		}

		public static function require($name = null, $version = "*") {
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

		public static function destroy() {
			if (file_exists(ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php')) {
				DLEFiles::DeleteDirectory(COMPOSER_DIRECTORY);
				putenv('COMPOSER_HOME='. ENGINE_DIR . '/inc/maharder/_includes/vendor/bin/composer');
			}
		}

	}