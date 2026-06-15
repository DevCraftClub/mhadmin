<?php
//===============================================================
// Файл: ModuleGeneratorService.php                             =
// Путь: devcraft/src/modules/Admin/Services/ModuleGeneratorSer…=
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use Throwable;
use DLEPlugins;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Сервис генерации каркаса нового DevCraft-модуля из шаблонов.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class ModuleGeneratorService {

	/**
	 * Относительный каталог шаблонов генератора в `devcraft/config/`.
	 *
	 * @since 200.4.0
	 * @var string
	 */
	private const STUB_DIR = 'scaffold/module';

	/**
	 * Возвращает версию по умолчанию для нового модуля на основе VERSIONID DLE.
	 *
	 * @since 200.4.0
	 *
	 * @return string Строка версии вида `200.40.1.0` или `200.1.0`.
	 *
	 * @example
	 *     $version = ModuleGeneratorService::defaultVersion();
	 */
	public static function defaultVersion(): string {
		if(defined('VERSIONID')) {
			return str_replace('.', '', (string) VERSIONID) . '.1.0';
		}

		return '200.1.0';
	}

	/**
	 * Создаёт каталоги, файлы и при необходимости регистрирует плагин DLE.
	 *
	 * @since 200.4.0
	 *
	 * @param   ModuleGeneratorInput  $input  Нормализованные данные формы генератора.
	 *
	 * @return array{
	 *     success: bool,
	 *     error?: string,
	 *     invalid_fields?: string[],
	 *     dirs: array{success: string[], fails: array<int, array{path: string, message: string}>},
	 *     files: array{success: string[], fails: array<int, array{path: string, message: string}>},
	 *     plugin: array{success: array<int, array<string, string>>, fails: array<int, array{message: string}>}
	 * } Отчёт о созданных путях, ошибках и статусе регистрации плагина.
	 *
	 * @example
	 *     $report = (new ModuleGeneratorService())->generate($input);
	 */
	public function generate(ModuleGeneratorInput $input): array {
		$report = [
			'success' => false,
			'dirs'    => ['success' => [], 'fails' => []],
			'files'   => ['success' => [], 'fails' => []],
			'plugin'  => ['success' => [], 'fails' => []],
		];

		$normalized = $this->normalize($input);

		$invalid = [];

		if($normalized['name'] === '') {
			$invalid[] = 'name';
		}

		if($normalized['description'] === '') {
			$invalid[] = 'description';
		}

		if($normalized['version'] === '') {
			$invalid[] = 'version';
		}

		if($normalized['latin'] === '') {
			$invalid[] = 'translit';
		}

		if($invalid !== []) {
			$report['invalid_fields'] = $invalid;
			$report['error']          = __('Нужные данные не были заполнены');

			return $report;
		}

		$latin   = $normalized['latin'];
		$dirName = $normalized['dir'];
		$replace = $this->replacements($normalized);

		$dirs = [
			DEVCRAFT_MODULES . '/' . $dirName,
			DEVCRAFT_MODULES . '/' . $dirName . '/Pages',
			DEVCRAFT_MODULES . '/' . $dirName . '/Ajax',
			DEVCRAFT_MODULES . '/' . $dirName . '/assets',
			DEVCRAFT_MODULES . '/' . $dirName . '/templates',
			DEVCRAFT_LOCALES,
		];

		foreach(Translation::getLanguages() as $locale) {
			$dirs[] = DEVCRAFT_LOCALES . '/' . $locale->tag;
		}

		foreach($dirs as $dir) {
			if(is_dir($dir)) {
				$report['dirs']['success'][] = $dir;

				continue;
			}

			if(!DataManager::createDir($dir)) {
				$report['dirs']['fails'][] = [
					'path'    => $dir,
					'message' => __('Ошибка во время создания папки'),
				];
				LogGenerator::for(self::class)->log(__('Путь не был создан: {dir}', ['{dir}' => $dir]));

				continue;
			}

			$report['dirs']['success'][] = $dir;
		}

		$fileMap = [
			'engine_inc.php.stub'      => ROOT_DIR . '/engine/inc/' . $latin . '.php',
			'manifest.php.stub'        => DEVCRAFT_MODULES . '/' . $dirName . '/manifest.php',
			'DashboardPage.php.stub'   => DEVCRAFT_MODULES . '/' . $dirName . '/Pages/DashboardPage.php',
			'ChangelogPage.php.stub'   => DEVCRAFT_MODULES . '/' . $dirName . '/Pages/ChangelogPage.php',
			'SettingsPage.php.stub'    => DEVCRAFT_MODULES . '/' . $dirName . '/Pages/SettingsPage.php',
			'SettingsHandler.php.stub' => DEVCRAFT_MODULES . '/' . $dirName . '/Ajax/SettingsHandler.php',
			'settings.schema.php.stub' => DEVCRAFT_MODULES . '/' . $dirName . '/settings.schema.php',
			'changelog.data.php.stub'  => DEVCRAFT_MODULES . '/' . $dirName . '/changelog.data.php',
			'dashboard.twig.stub'      => DEVCRAFT_MODULES . '/' . $dirName . '/templates/dashboard.twig',
			'settings.twig.stub'       => DEVCRAFT_MODULES . '/' . $dirName . '/templates/settings.twig',
			'changelog.twig.stub'      => DEVCRAFT_MODULES . '/' . $dirName . '/templates/changelog.twig',
			'assets_htaccess.stub'     => DEVCRAFT_MODULES . '/' . $dirName . '/templates/.htaccess',
		];

		foreach($fileMap as $stub => $target) {
			$this->writeFromStub($stub, $target, $replace, $normalized['override'], $report);
		}

		$this->writeLocales($latin, $normalized['override'], $report);

		if($normalized['db']) {
			$this->registerPlugin($normalized, $report);
		}

		if(function_exists('clear_cache')) {
			clear_cache();
		}

		$report['success'] = $report['files']['fails'] === []
		                     && $report['dirs']['fails'] === []
		                     && ($normalized['db'] === false || $report['plugin']['fails'] === []);

		return $report;
	}

	/**
	 * Нормализует и дополняет входные данные генератора модулей.
	 *
	 * @since 200.4.0
	 *
	 * @param   ModuleGeneratorInput  $input  Исходные данные формы.
	 *
	 * @return array{
	 *     name: string,
	 *     latin: string,
	 *     dir: string,
	 *     description: string,
	 *     version: string,
	 *     icon: string,
	 *     plugin_icon: string,
	 *     link: string,
	 *     docs: string,
	 *     db: bool,
	 *     override: bool
	 * } Подготовленный набор значений для подстановки в шаблоны.
	 */
	private function normalize(ModuleGeneratorInput $input): array {
		$name  = $input->name;
		$latin = $input->translit;

		if($latin === '') {
			$latin = $name;
		}

		$latin = DataManager::toTranslit($latin);

		$icon       = $input->icon !== ''? $input->icon : 'mif-cog';
		$pluginIcon = $input->pluginIcon !== ''? $input->pluginIcon : 'engine/skins/images/default_module.png';
		$version    = $input->version !== ''? $input->version : self::defaultVersion();

		return [
			'name'        => $name,
			'latin'       => $latin,
			'dir'         => ucfirst($latin),
			'description' => $input->description,
			'version'     => $version,
			'icon'        => $icon,
			'plugin_icon' => $pluginIcon,
			'link'        => $input->link,
			'docs'        => $input->docs,
			'db'          => $input->db,
			'override'    => $input->override,
		];
	}

	/**
	 * Формирует карту плейсхолдеров для подстановки в stub-файлы.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, string>  $normalized  Нормализованные данные модуля.
	 *
	 * @return array<string, string> Карта `%ключ%` → значение.
	 */
	private function replacements(array $normalized): array {
		return [
			'%latin%'       => $normalized['latin'],
			'%dir%'         => $normalized['dir'],
			'%name%'        => $normalized['name'],
			'%version%'     => $normalized['version'],
			'%description%' => $normalized['description'],
			'%icon%'        => $normalized['icon'],
			'%link%'        => $normalized['link'],
			'%docs%'        => $normalized['docs'],
			'%year%'        => date('Y'),
		];
	}

	/**
	 * Записывает целевой файл из stub-шаблона с подстановкой плейсхолдеров.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                 $stubFile    Имя stub-файла в каталоге шаблонов.
	 * @param   string                 $targetFile  Абсолютный путь создаваемого файла.
	 * @param   array<string, string>  $replace     Карта плейсхолдеров для подстановки.
	 * @param   bool                   $override    Перезаписывать существующие файлы.
	 * @param   array<string, mixed>   $report      Ссылка на накапливаемый отчёт генерации.
	 */
	private function writeFromStub(
		string $stubFile,
		string $targetFile,
		array  $replace,
		bool   $override,
		array  &$report,
	): void {
		$stubPath = $this->stubPath($stubFile);

		if(!is_readable($stubPath)) {
			$report['files']['fails'][] = [
				'path'    => $targetFile,
				'message' => __('Шаблон не найден: {file}', ['{file}' => $stubFile]),
			];

			return;
		}

		if(is_file($targetFile) && !$override) {
			$report['files']['fails'][] = [
				'path'    => $targetFile,
				'message' => __('Данный файл ({file}) уже существует.', ['{file}' => $targetFile]),
			];

			return;
		}

		$replace['%path%'] = ltrim(str_replace('\\', '/', str_replace(ROOT_DIR, '', $targetFile)), '/');

		$content = str_replace(
			array_keys($replace),
			array_values($replace),
			(string) file_get_contents($stubPath),
		);

		$directory = dirname($targetFile);

		if(!is_dir($directory) && !DataManager::createDir($directory)) {
			$report['files']['fails'][] = [
				'path'    => $targetFile,
				'message' => __('Не удалось создать каталог'),
			];

			return;
		}

		try {
			if(file_put_contents($targetFile, $content, LOCK_EX) === false) {
				$report['files']['fails'][] = [
					'path'    => $targetFile,
					'message' => __('Невозможно записать файл'),
				];

				return;
			}

			chmod($targetFile, 0755);
			$report['files']['success'][] = $targetFile;
		} catch(Throwable $throwable) {
			$report['files']['fails'][] = [
				'path'    => $targetFile,
				'message' => $throwable->getMessage(),
			];
			LogGenerator::for(self::class)->log($throwable->getMessage());
		}
	}

	/**
	 * Создаёт XLIFF-файлы локализации для нового модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $latin     Латинский код модуля (имя каталога локали).
	 * @param   bool                  $override  Перезаписывать существующие файлы локали.
	 * @param   array<string, mixed>  $report    Ссылка на накапливаемый отчёт генерации.
	 */
	private function writeLocales(string $latin, bool $override, array &$report): void {
		$stubPath = $this->stubPath('locale.xliff.stub');

		if(!is_readable($stubPath)) {
			return;
		}

		$template = (string) file_get_contents($stubPath);
		$locales  = Translation::getLanguages();

		foreach($locales as $lang) {
			$localePath = DEVCRAFT_LOCALES . '/' . $lang->tag . '/' . $latin . '.xliff';

			if(is_file($localePath) && !$override) {
				$report['files']['fails'][] = [
					'path'    => $localePath,
					'message' => __('Данный файл ({file}) уже существует.', ['{file}' => $localePath]),
				];

				continue;
			}

			$langPath = DEVCRAFT_LOCALES . '/' . $lang->tag . '/' . $latin . '.xliff';

			$content = str_replace(
				['%lang_path%', '%source_lang%', '%target_lang%'],
				[$langPath, $lang->iso2, explode('_', $lang->tag)[0]],
				$template,
			);

			if(file_put_contents($localePath, $content, LOCK_EX) === false) {
				$report['files']['fails'][] = [
					'path'    => $localePath,
					'message' => __('Невозможно записать файл локали ({file}).', ['{file}' => $localePath]),
				];

				continue;
			}

			chmod($localePath, 0755);
			$report['files']['success'][] = $localePath;
		}
	}

	/**
	 * Регистрирует модуль как плагин DLE в таблице `_plugins`.
	 *
	 * @since 200.4.0
	 *
	 * @global object                 $db          Объект базы данных DLE.
	 * @global array<string,mixed>    $config      Конфигурация DLE.
	 * @global array<string,mixed>    $member_id   Данные текущего администратора.
	 * @global string|int             $_TIME       Метка времени запроса DLE.
	 * @global string                 $_IP         IP-адрес текущего запроса.
	 *
	 * @param   array<string, mixed>  $normalized  Нормализованные данные модуля.
	 * @param   array<string, mixed>  $report      Ссылка на накапливаемый отчёт генерации.
	 */
	private function registerPlugin(array $normalized, array &$report): void {
		global $db, $config, $member_id, $_TIME, $_IP;

		if(!isset($db) || !is_object($db)) {
			$report['plugin']['fails'][] = ['message' => __('База данных недоступна')];

			return;
		}

		$latin       = $normalized['latin'];
		$name        = $db->safesql(htmlspecialchars(trim($normalized['name']), ENT_QUOTES, $config['charset'] ?? 'utf-8'));
		$description = $db->safesql(htmlspecialchars(trim($normalized['description']), ENT_QUOTES, $config['charset'] ?? 'utf-8'));
		$version     = $db->safesql(htmlspecialchars(trim($normalized['version']), ENT_QUOTES, $config['charset'] ?? 'utf-8'));
		$dleVersion  = $db->safesql(htmlspecialchars(trim((string) ($config['version_id'] ?? VERSIONID)), ENT_QUOTES, $config['charset'] ?? 'utf-8'));

		$icon = $db->safesql(
			function_exists('clearfilepath')
				? clearfilepath(htmlspecialchars(trim($normalized['plugin_icon']), ENT_QUOTES, $config['charset'] ?? 'utf-8'),
				['gif', 'jpg', 'jpeg', 'png', 'webp'])
				: htmlspecialchars(trim($normalized['plugin_icon']), ENT_QUOTES, $config['charset'] ?? 'utf-8'),
		);

		if(!class_exists('DLE_API')) {
			/** Подключает класс DLE API для регистрации админ-модуля. */
			require_once DLEPlugins::Check(ENGINE_DIR . '/api/api.class.php');
		}

		$dleApi             = new \DLE_API();
		$dleApi->db         = $db;
		$dleApi->dle_config = $config;

		$dleApi->install_admin_module(
			$latin,
			"{$normalized['name']} v{$normalized['version']}",
			$normalized['description'],
			$normalized['plugin_icon'],
			'1,2',
		);

		$linkHtml = $normalized['link'] !== ''
			? '<li><b>Ссылка на плагин</b>: <a href="' . htmlspecialchars($normalized['link'], ENT_QUOTES) . '" target="_blank">Перейти</a></li>'
			: '';
		$docsHtml = $normalized['docs'] !== ''
			? '<li><b>Документация</b>: <a href="' . htmlspecialchars($normalized['docs'], ENT_QUOTES) . '" target="_blank">Перейти</a></li>'
			: '';
		$notice   = $db->safesql('<ul>' . $linkHtml . $docsHtml . '</ul>');

		$mysqlUpgrade =
			"INSERT INTO {prefix}_admin_sections (name, title, descr, icon, allow_groups) VALUES ('{$latin}', '{$name} v{$version}', '{$description}', '{$icon}', '1, 2') ON DUPLICATE KEY UPDATE title = VALUE('title');";
		$mysqlEnable  = $mysqlUpgrade;
		$mysqlDisable = "DELETE FROM {prefix}_admin_sections WHERE name = '{$latin}';";
		$mysqlDelete  = $mysqlDisable;

		try {
			$plugin = $db->query('SELECT * FROM ' . PREFIX . "_plugins WHERE name = '{$name}'");

			if($plugin && $db->num_rows() > 0) {
				$report['plugin']['fails'][] = ['message' => 'Плагин уже существует'];

				return;
			}

			$prefix    = PREFIX;
			$sqlInsert = <<<SQL
INSERT INTO {$prefix}_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, filelist, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) VALUES ('{$name}', '{$description}', '{$icon}', "{$version}", "{$dleVersion}", ">=", 1, '', "{$mysqlUpgrade}", "{$mysqlEnable}", "{$mysqlDisable}", "{$mysqlDelete}", 1, '', '', '', '', '', '', '', '', '{$notice}', 1);
SQL;

			$db->query($sqlInsert);
			$pluginId = (int) $db->insert_id();

			if(isset($member_id['name'])) {
				$adminName = $db->safesql((string) $member_id['name']);
				$time      = (string) ($_TIME ?? time());
				$ip        = $db->safesql((string) ($_IP ?? ''));
				$db->query(
					"INSERT INTO " . USERPREFIX .
					"_admin_logs (name, date, ip, action, extras) VALUES ('{$adminName}', '{$time}', '{$ip}', '116', '{$name}')",
				);
			}

			if(function_exists('execute_query') && $pluginId > 0) {
				execute_query($pluginId, $mysqlEnable);
			}

			$adminPath = (string) ($config['admin_path'] ?? 'admin.php');
			$homeUrl   = (string) ($config['http_home_url'] ?? '/');

			$report['plugin']['success'][] = [
				'link' => $homeUrl . $adminPath . '?mod=plugins&action=edit&id=' . $pluginId,
				'name' => "{$normalized['name']} v{$normalized['version']}",
			];
		} catch(Throwable $throwable) {
			$report['plugin']['fails'][] = ['message' => $throwable->getMessage()];
			LogGenerator::for(self::class)->log($throwable->getMessage());
		}
	}

	/**
	 * Возвращает абсолютный путь к stub-файлу генератора модулей.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $file  Имя stub-файла в каталоге `scaffold/module`.
	 *
	 * @return string Нормализованный абсолютный путь к шаблону.
	 */
	private function stubPath(string $file): string {
		return DataManager::normalizePath(Paths::config() . '/' . self::STUB_DIR . '/' . $file);
	}

}
