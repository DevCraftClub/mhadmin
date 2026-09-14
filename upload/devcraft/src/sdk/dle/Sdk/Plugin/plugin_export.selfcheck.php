<?php

declare(strict_types=1);

/**
 * ponytail: без БД — XML basename/kebab и сборка XML; с ZipArchive — архив только с XML.
 * Запуск: php devcraft/src/sdk/dle/Sdk/Plugin/plugin_export.selfcheck.php
 */

function __(string $phrase, array $params = [], int $count = 0): string {
	return $params === [] ? $phrase : strtr($phrase, $params);
}

require_once dirname(__DIR__, 5) . '/vendor/autoload.php';

use DevCraft\Core\Config\Paths;
use DevCraft\Dle\Sdk\Plugin\PluginInstallExporter;

Paths::register();

$exporter = new PluginInstallExporter();

assert($exporter->buildBasename('DLE API', '200.1.1') === 'dle-api_v200.1.1');
assert($exporter->buildBasename('Custom UserTags', '200.3.1') === 'custom-usertags_v200.3.1');

$row = [
	'name'            => 'Demo Plugin',
	'description'     => 'Test',
	'icon'            => 'mif-cog',
	'version'         => '1.0.0',
	'dleversion'      => '20.0',
	'versioncompare'  => '>=',
	'upgradeurl'      => '',
	'filedelete'      => '1',
	'needplugin'      => 'DevCraft Admin',
	'mnotice'         => '1',
	'mysqlinstall'    => 'SELECT 1;',
	'mysqlupgrade'    => '',
	'mysqlenable'     => "INSERT INTO {prefix}_admin_sections (name) VALUES ('demo')",
	'mysqldisable'    => '',
	'mysqldelete'     => '',
	'phpinstall'      => '',
	'phpupgrade'      => '',
	'phpenable'       => '',
	'phpdisable'      => '',
	'phpdelete'       => '',
	'notice'          => 'docs',
];

$files = [
	[
		'file'               => 'engine/inc/email.php',
		'action'             => 'after',
		'searchcode'         => 'search',
		'replacecode'        => 'replace',
		'searchcount'        => 0,
		'replacecount'       => 0,
		'filedisable'        => 1,
		'filedleversion'     => '',
		'fileversioncompare' => '',
	],
];

$xml = $exporter->buildXml($row, $files);
assert(str_contains($xml, '<name>Demo Plugin</name>'));
assert(str_contains($xml, '<version>1.0.0</version>'));
assert(str_contains($xml, '<versioncompare>greater</versioncompare>'));
assert(str_contains($xml, '<file name="engine/inc/email.php">'));
assert(str_contains($xml, '<![CDATA[SELECT 1;]]>'));

$tmpDir = sys_get_temp_dir() . '/dc_plugin_export_selfcheck_' . getmypid();
@mkdir($tmpDir, 0755, true);
$xmlPath = $tmpDir . '/demo-plugin_v1.0.0.xml';
assert(file_put_contents($xmlPath, $xml) !== false);

$zipPath = $tmpDir . '/demo-plugin_v1.0.0.zip';
$zip     = new ZipArchive();
assert($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);
assert($zip->addFromString('demo-plugin_v1.0.0.xml', $xml) === true);
assert($zip->close());

$check = new ZipArchive();
assert($check->open($zipPath) === true);
assert($check->locateName('demo-plugin_v1.0.0.xml') !== false);
$check->close();

@unlink($xmlPath);
@unlink($zipPath);
@rmdir($tmpDir);

fwrite(STDOUT, "plugin_export.selfcheck OK\n");
