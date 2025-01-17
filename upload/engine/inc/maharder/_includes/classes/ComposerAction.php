<?php

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

const COMPOSER_DIRECTORY = ENGINE_DIR . '/inc/maharder/_includes/composer';

if (!file_exists(COMPOSER_DIRECTORY . '/vendor/autoload.php')) {
	DataManager::createDir(service: 'ComposerAction', permission: 0777, _path: COMPOSER_DIRECTORY);
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
	public static function init(): void {
		self::$packages = json_decode(
			file_get_contents(dirname(COMPOSER_DIRECTORY, 2) . '/admin/composer.json'),
			true,
			512,
			JSON_THROW_ON_ERROR
		);
		chdir(dirname(COMPOSER_DIRECTORY, 2) . '/admin');
	}

	/**
	 * Инициализирует приложение и возвращает экземпляр класса Application.
	 *
	 * @return Application Экземпляр инициализированного приложения.
	 * @throws JsonException Исключение может быть выброшено, если возникнет ошибка обработки JSON при инициализации.
	 */
	private static function application(): Application {
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
	 * Устанавливает зависимости проекта через Composer.
	 *
	 * Метод инициализирует приложение Composer с помощью метода `application()`,
	 * создает объект `ArrayInput` для команды `install` и выполняет её с помощью
	 * метода `run()` экземпляра приложения.
	 *
	 * Используется для выполнения командной строки Composer в коде PHP.
	 *
	 * @return void
	 * @throws JsonException Может быть вызвано при ошибке обработки JSON в методе `application()`.
	 *
	 */
	public static function install(): void {
		$app   = self::application();
		$input = new ArrayInput(['command' => 'install']);
		$app->run($input);
	}

	/**
	 * Выполняет обновление зависимостей проекта через Composer.
	 *
	 * Метод создает экземпляр приложения Composer, используя метод `application()`,
	 * передает команду `update` через объект `ArrayInput` и выполняет ее с помощью `run()`.
	 *
	 * @throws JsonException Может быть выброшено при обработке JSON в методе `application()`.
	 */
	public static function update(): void {
		$app   = self::application();
		$input = new ArrayInput(['command' => 'update']);
		$app->run($input);
	}

	/**
	 * Добавляет указанный пакет или все зарегистрированные пакеты в зависимости от переданных параметров.
	 *
	 * Если имя пакета (`$name`) не указано, метод проходит по всем зарегистрированным
	 * в `$packages` пакетам и вызывает себя для каждого из них.
	 *
	 * В противном случае создается экземпляр `ArrayInput`, передающий команду для
	 * установки указанного пакета с версией `$version`. После этого команда
	 * выполняется с помощью метода `run()` приложения Composer.
	 *
	 * @param string|null $name    Название пакета, который нужно установить. Если значение `null` — обрабатываются все
	 *                             пакеты.
	 * @param string      $version Версия пакета, которую необходимо установить. По умолчанию `"*"`.
	 *
	 * @return void
	 * @throws JsonException Может выбросить исключение при ошибке обработки JSON (например, в методе `application`).
	 */
	public static function require($name = null, $version = "*"): void {
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

	/**
	 * Удаляет содержащиеся в директории файлы и настройки, связанные с Composer.
	 *
	 * Метод проверяет, существует ли файл автозагрузчика Composer по заданному пути.
	 * Если файл существует:
	 * - Удаляются все файлы в указанной директории Composer с помощью функции `array_map` и `unlink`.
	 * - Удаляется сама директория Composer с помощью функции `rmdir`.
	 * - Устанавливается окружение Composer через `putenv`, указывая путь к исполняемому файлу Composer.
	 *
	 * Данный метод не возвращает значения и предназначен для очистки и удаления данных Composer.
	 *
	 * @return void
	 */
	public static function destroy(): void {
		if (file_exists(ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php')) {
			array_map('unlink', glob("{COMPOSER_DIRECTORY}/*.*"));
			rmdir(COMPOSER_DIRECTORY);
			putenv('COMPOSER_HOME=' . ENGINE_DIR . '/inc/maharder/_includes/vendor/bin/composer');
		}
	}

}