<?php

declare(strict_types=1);

namespace DevCraft\Dle\Sdk\Plugin;

use DevCraft\Core\Config\Paths;
use DevCraft\Core\Support\DataManager;
use DevCraft\Dle\Schema\PluginsFilesSchema;
use DevCraft\Dle\Schema\PluginsSchema;
use ZipArchive;

/**
 * Экспорт установочного XML / ZIP плагина из таблиц plugins + plugins_files.
 */
final class PluginInstallExporter {

	/**
	 * Экспортирует плагин по имени в XML или ZIP.
	 *
	 * @param   string       $pluginName  Значение plugins.name
	 * @param   string|null  $path        Каталог, полный путь к файлу или null → Paths::pluginExports()
	 * @param   bool         $archivate   true — ZIP с XML и filelist
	 */
	public function export(string $pluginName, ?string $path = null, bool $archivate = false): bool {
		$pluginName = trim($pluginName);
		if($pluginName === '') {
			return false;
		}

		$plugins = PluginsSchema::filter(['name' => $pluginName], [], ['id' => 'ASC'], 1, 0);
		if($plugins === []) {
			return false;
		}

		$row = $plugins[0]->asArray();
		$id  = (int) ($row['id'] ?? 0);
		if($id < 1) {
			return false;
		}

		$fileRows = PluginsFilesSchema::filter(
			['plugin_id' => $id],
			[],
			['id' => 'ASC'],
			10000,
			0,
		);
		$files = [];
		foreach($fileRows as $fileSchema) {
			$files[] = $fileSchema->asArray();
		}

		$xml      = $this->buildXml($row, $files);
		$basename = $this->buildBasename((string) ($row['name'] ?? $pluginName), (string) ($row['version'] ?? '0'));
		$target   = $this->resolveTargetPath($path, $basename, $archivate);
		if($target === null) {
			return false;
		}

		$dir = dirname($target);
		if(!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
			return false;
		}

		if(!$archivate) {
			return file_put_contents($target, $xml) !== false;
		}

		return $this->writeZip($target, $basename . '.xml', $xml, (string) ($row['filelist'] ?? ''));
	}

	/**
	 * @param   array<string, mixed>        $row
	 * @param   list<array<string, mixed>>  $fileRows
	 */
	public function buildXml(array $row, array $fileRows): string {
		$versionCompare = $this->mapVersionCompare((string) ($row['versioncompare'] ?? ''));
		$filesXml       = $this->buildFileXml($fileRows);

		return '<?xml version="1.0" encoding="utf-8"?>' . "\n"
			. '<dleplugin>' . "\n"
			. "\t<name>" . $this->escape((string) ($row['name'] ?? '')) . '</name>' . "\n"
			. "\t<description>" . $this->escape((string) ($row['description'] ?? '')) . '</description>' . "\n"
			. "\t<icon>" . $this->escape((string) ($row['icon'] ?? '')) . '</icon>' . "\n"
			. "\t<version>" . $this->escape((string) ($row['version'] ?? '')) . '</version>' . "\n"
			. "\t<dleversion>" . $this->escape((string) ($row['dleversion'] ?? '')) . '</dleversion>' . "\n"
			. "\t<versioncompare>" . $this->escape($versionCompare) . '</versioncompare>' . "\n"
			. "\t<upgradeurl>" . $this->escape((string) ($row['upgradeurl'] ?? '')) . '</upgradeurl>' . "\n"
			. "\t<filedelete>" . $this->escape((string) ($row['filedelete'] ?? '0')) . '</filedelete>' . "\n"
			. "\t<needplugin>" . $this->escape((string) ($row['needplugin'] ?? '')) . '</needplugin>' . "\n"
			. "\t<mnotice>" . $this->escape((string) ($row['mnotice'] ?? '0')) . '</mnotice>' . "\n"
			. "\t<mysqlinstall>" . $this->cdata((string) ($row['mysqlinstall'] ?? '')) . '</mysqlinstall>' . "\n"
			. "\t<mysqlupgrade>" . $this->cdata((string) ($row['mysqlupgrade'] ?? '')) . '</mysqlupgrade>' . "\n"
			. "\t<mysqlenable>" . $this->cdata((string) ($row['mysqlenable'] ?? '')) . '</mysqlenable>' . "\n"
			. "\t<mysqldisable>" . $this->cdata((string) ($row['mysqldisable'] ?? '')) . '</mysqldisable>' . "\n"
			. "\t<mysqldelete>" . $this->cdata((string) ($row['mysqldelete'] ?? '')) . '</mysqldelete>' . "\n"
			. "\t<phpinstall>" . $this->cdata((string) ($row['phpinstall'] ?? '')) . '</phpinstall>' . "\n"
			. "\t<phpupgrade>" . $this->cdata((string) ($row['phpupgrade'] ?? '')) . '</phpupgrade>' . "\n"
			. "\t<phpenable>" . $this->cdata((string) ($row['phpenable'] ?? '')) . '</phpenable>' . "\n"
			. "\t<phpdisable>" . $this->cdata((string) ($row['phpdisable'] ?? '')) . '</phpdisable>' . "\n"
			. "\t<phpdelete>" . $this->cdata((string) ($row['phpdelete'] ?? '')) . '</phpdelete>' . "\n"
			. "\t<notice>" . $this->cdata((string) ($row['notice'] ?? '')) . '</notice>'
			. $filesXml . "\n"
			. '</dleplugin>' . "\n";
	}

	/**
	 * @param   list<array<string, mixed>>  $fileRows
	 */
	private function buildFileXml(array $fileRows): string {
		if($fileRows === []) {
			return '';
		}

		$grouped = [];
		foreach($fileRows as $fileRow) {
			$fileName = (string) ($fileRow['file'] ?? '');
			if($fileName === '') {
				continue;
			}
			$safeName = htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8');
			$grouped[$safeName][] = [
				'action'             => (string) ($fileRow['action'] ?? ''),
				'searchcode'         => (string) ($fileRow['searchcode'] ?? ''),
				'replacecode'        => (string) ($fileRow['replacecode'] ?? ''),
				'searchcount'        => (int) ($fileRow['searchcount'] ?? 0),
				'replacecount'       => (int) ($fileRow['replacecount'] ?? 0),
				'filedisable'        => (int) ($fileRow['filedisable'] ?? 0),
				'filedleversion'     => htmlspecialchars((string) ($fileRow['filedleversion'] ?? ''), ENT_QUOTES, 'UTF-8'),
				'fileversioncompare' => $this->mapVersionCompare((string) ($fileRow['fileversioncompare'] ?? '')),
			];
		}

		$xml = '';
		foreach($grouped as $fileName => $operations) {
			$xml .= "\n\t<file name=\"{$fileName}\">";
			foreach($operations as $operation) {
				$xml .= "\n\t\t<operation action=\"{$operation['action']}\">";
				if($operation['searchcode'] !== '') {
					$xml .= "\n\t\t\t<searchcode>" . $this->cdata($operation['searchcode']) . '</searchcode>';
				}
				if($operation['replacecode'] !== '') {
					$xml .= "\n\t\t\t<replacecode>" . $this->cdata($operation['replacecode']) . '</replacecode>';
				}
				if($operation['searchcount'] > 0) {
					$xml .= "\n\t\t\t<searchcount>{$operation['searchcount']}</searchcount>";
				}
				if($operation['replacecount'] > 0) {
					$xml .= "\n\t\t\t<replacecount>{$operation['replacecount']}</replacecount>";
				}
				$xml .= "\n\t\t\t<enabled>{$operation['filedisable']}</enabled>";
				if($operation['filedleversion'] !== '') {
					$xml .= "\n\t\t\t<dleversion>{$operation['filedleversion']}</dleversion>";
					$xml .= "\n\t\t\t<versioncompare>{$operation['fileversioncompare']}</versioncompare>";
				}
				$xml .= "\n\t\t</operation>";
			}
			$xml .= "\n\t</file>";
		}

		return $xml;
	}

	public function buildBasename(string $name, string $version): string {
		$slug = DataManager::toTranslit($name);
		$slug = str_replace('_', '-', $slug);
		$slug = (string) preg_replace('/-+/', '-', $slug);
		$slug = trim($slug, '-');
		if($slug === '') {
			$slug = 'plugin';
		}

		$version = trim(str_replace([' ', "\t"], '', $version));
		if($version === '') {
			$version = '0';
		}

		return $slug . '_v' . $version;
	}

	private function resolveTargetPath(?string $path, string $basename, bool $archivate): ?string {
		$ext      = $archivate ? 'zip' : 'xml';
		$autoName = $basename . '.' . $ext;

		if($path === null || $path === '') {
			return DataManager::normalizePath(Paths::pluginExports() . '/' . $autoName);
		}

		$path = DataManager::normalizePath($path);
		$lower = strtolower($path);

		if(str_ends_with($lower, '.xml') || str_ends_with($lower, '.zip')) {
			$wantZip = $archivate;
			$isZip   = str_ends_with($lower, '.zip');
			if($wantZip !== $isZip) {
				return null;
			}

			return $path;
		}

		return DataManager::normalizePath(rtrim($path, '/\\') . '/' . $autoName);
	}

	private function writeZip(string $target, string $xmlEntryName, string $xml, string $filelist): bool {
		$entries = [];
		$filelist = trim($filelist);
		if($filelist !== '') {
			foreach(explode(',', $filelist) as $relative) {
				$relative = trim(str_replace('\\', '/', $relative));
				$relative = ltrim($relative, '/');
				if($relative === '' || str_contains($relative, '..')) {
					return false;
				}
				$absolute = DataManager::normalizePath(ROOT_DIR . '/' . $relative);
				if(!is_file($absolute)) {
					return false;
				}
				$entries[$relative] = $absolute;
			}
		}

		$zip = new ZipArchive();
		if($zip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			return false;
		}

		if($zip->addFromString($xmlEntryName, $xml) === false) {
			$zip->close();
			@unlink($target);

			return false;
		}

		foreach($entries as $relative => $absolute) {
			if($zip->addFile($absolute, $relative) === false) {
				$zip->close();
				@unlink($target);

				return false;
			}
		}

		return $zip->close();
	}

	private function mapVersionCompare(string $compare): string {
		return match ($compare) {
			'>=' => 'greater',
			'<=' => 'less',
			default => $compare,
		};
	}

	private function cdata(string $value): string {
		return '<![CDATA[' . $value . ']]>';
	}

	private function escape(string $value): string {
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}

}
