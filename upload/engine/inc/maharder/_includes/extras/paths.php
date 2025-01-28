<?php
//===============================================================
// Файл: paths.php                                              =
// Путь: engine/inc/maharder/_includes/extras/paths.php         =
// Дата создания: 2024-04-15 07:27:46                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

if (!defined('DATALIFEENGINE')) {
	header('HTTP/1.1 403 Forbidden');
	header('Location: ../../../../../');

	exit('Hacking attempt!');
}

/**
 * Определяет текущую директорию проекта, если ранее не была задана константа `ROOT_DIR`.
 *
 * Если константа `ROOT_DIR` не определена, то текущая директория вычисляется с использованием скрипта:
 * - Если путь текущей директории содержит поддиректорию `cache`, то устанавливается директория, расположенная на 5
 * уровней выше.
 * - В противном случае используется директория на 6 уровней выше.
 *
 * Если `ROOT_DIR` уже задана, то используется её значение.
 *
 * @global string $current_dir Текущая директория проекта. Автоматически определяется, если `ROOT_DIR` не задана.
 * @see ROOT_DIR
 */
if (!defined('ROOT_DIR')) {
	$current_dir = __DIR__;
	if (preg_grep('/(cache)/', explode("\n", $current_dir))) $current_dir = dirname(
		__FILE__,
		5
	); else $current_dir = dirname(__FILE__, 6);
} else {
	$current_dir = ROOT_DIR;
}

if (!defined('MH_INIT')) {
	/**
	 * Определяет константу, используемую для инициализации системы.
	 *
	 * Константа используется для указания того, что система была
	 * успешно инициализирована.
	 *
	 * @global bool MH_INIT Указывает, что инициализация выполнена.
	 */
	define("MH_INIT", true);
}

if (!defined('ROOT')) {
	/**
	 * Определяет константу `ROOT`, если она ещё не определена.
	 *
	 * Константа `ROOT` указывает на текущую директорию проекта, определяемую в переменной `$current_dir`.
	 * Если путь содержит подкаталог `cache`, то используется директория выше на пять уровней.
	 * В противном случае используется директория выше на шесть уровней.
	 *
	 * Константа используется для упрощённого доступа к корневому каталогу при работе
	 * с файловой системой.
	 *
	 * @global string $current_dir Содержит путь к текущей директории исполнения скрипта.
	 */
	define("ROOT", $current_dir);
}
if (!defined('MH_ROOT')) {
	/**
	 * Определяет константу `MH_ROOT`, если она ещё не определена.
	 *
	 * Константа `MH_ROOT` указывает на путь к директории `engine/inc/maharder` в структуре проекта.
	 * Этот путь формируется на основе значения константы `ROOT`, определяющей корневую директорию проекта.
	 *
	 * Константа используется для упрощённого доступа к базовой директории функционала Maharder.
	 *
	 * @see ROOT
	 */
	define("MH_ROOT", ROOT . '/engine/inc/maharder');
}
if (!defined('MH_ADMIN')) {
	/**
	 * Константа MH_ADMIN определяет путь к административной директории.
	 * Формируется путем объединения константы MH_ROOT с поддиректорией '/admin'.
	 *
	 * @see MH_ROOT
	 */
	define("MH_ADMIN", MH_ROOT . '/admin');
}
if (!defined('MH_LOCALES')) {
	/**
	 * Константа, задающая путь к локализациям.
	 *
	 * Константа `MH_LOCALES` определяет абсолютный путь к директории,
	 * в которой хранятся файлы локализаций. Основывается на значении
	 * константы `MH_ROOT` с добавлением директории `/_locales`.
	 *
	 * @see MH_ROOT
	 */
	define("MH_LOCALES", MH_ROOT . '/_locales');
}
if (!defined('MH_MODULES')) {
	/**
	 * Определяет константу MH_MODULES, указывающую на путь к папке модулей в корневой директории.
	 *
	 * Константа используется для задания базового пути к директории с модулями,
	 * относительно константы MH_ROOT, которая должна быть определена ранее.
	 *
	 * @see MH_ROOT
	 */
	define("MH_MODULES", MH_ROOT . '/_modules');
}
if (!defined('MH_INCLUDES')) {
	/**
	 * Определяет константу `MH_INCLUDES`, содержащую путь к директории `_includes` внутри корневой директории.
	 *
	 * Константа используется для централизованного указания пути к дополнительным включаемым файлам проекта.
	 * Значение формируется путем объединения значения константы `MH_ROOT` и строки `/_includes`.
	 *
	 * @see MH_ROOT
	 */
	define("MH_INCLUDES", MH_ROOT . '/_includes');
}
if (!defined('MH_CONFIG')) {
	/**
	 * Определяет путь к папке конфигурации приложения.
	 *
	 * Константа `MH_CONFIG` содержит полный путь к директории `_config`,
	 * основываясь на значении корневой директории `MH_ROOT`.
	 *
	 * @see MH_ROOT
	 */
	define("MH_CONFIG", MH_ROOT . '/_config');
}
if (!defined('MH_TEMPLATES')) {
	/**
	 * Определяет константу MH_TEMPLATES, которая содержит путь
	 * к директории с шаблонами проекта.
	 *
	 * Значение пути формируется на основе константы MH_ROOT
	 * с добавлением поддиректории '_templates'.
	 *
	 * @see MH_ROOT
	 */
	define("MH_TEMPLATES", MH_ROOT . '/_templates');
}
if (!defined('COMPOSER_DIR')) {
	/**
	 * Определяет константу COMPOSER_DIR, которая содержит путь до библиотеки копмозера
	 */
	define("COMPOSER_DIR", MH_INCLUDES . '/composer');
}

$mh_models_paths = [
	MH_MODULES . '/admin/models',
	// Custom models //
];

$mh_loader_paths = [
	MH_INCLUDES . '/classes',
	MH_INCLUDES . '/database',
	MH_INCLUDES . '/responses',
	MH_INCLUDES . '/traits',
	MH_INCLUDES . '/types',
	MH_INCLUDES . '/twigExtensions',
	MH_MODULES . '/admin/repositories',
	// Custom paths //
];
$mh_loader_paths = array_filter(array_merge($mh_loader_paths, $mh_models_paths));

include_once DLEPlugins::Check(MH_INCLUDES . '/extras/functions.php');
include_once DLEPlugins::Check(MH_INCLUDES . '/extras/mhLoader.php');

if (!file_exists(MH_INCLUDES . '/vendor/autoload.php')) {
	putenv('COMPOSER_HOME=' . COMPOSER_DIR . '/vendor/bin/composer');
	DataManager::createDir(service: 'ComposerAction', permission: 0777, _path: COMPOSER_DIR);
	$composer = COMPOSER_DIR . '/composer.phar';
	if (!file_exists($composer)) {
		copy('https://getcomposer.org/download/latest-stable/composer.phar', $composer);
	}
	try {
		$phar = new Phar($composer);
		$phar->extractTo(COMPOSER_DIR);
	} catch (Exception $e) {}
	unlink($composer);


	ComposerAction::install();
}

include_once MH_INCLUDES . '/vendor/autoload.php';

$MHDB = new MhDB();

ComposerAction::destroy();
