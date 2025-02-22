<?php

global $lang, $config;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Cache\CacheExtension;
use Twig\Extra\Cache\CacheRuntime;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFilter;

if (!defined('DATALIFEENGINE')) {
	header("HTTP/1.1 403 Forbidden");
	header('Location: ../../../../');
	die("Hacking attempt!");
}


define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('THIS_SELF', $_SERVER['PHP_SELF']);
define('DLE_URL', $config['http_home_url']);
define('URL', DLE_URL . 'engine/inc');

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php');
require_once DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php');
require_once DLEPlugins::Check(ENGINE_DIR . '/data/config.php');

if (!defined('MH_INIT')) {
	require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');
}

$mh = new Admin();

if(!file_exists(MH_INCLUDES . '/assets.json')) {
	$mh->parseAssets(true);
}

$links = include DLEPlugins::Check(MH_MODULES . '/admin/module/links.php');

try {
	MhTranslation::convertXliffToJs();
} catch (JsonException|Throwable $e) {
	LogGenerator::generateLog('Admin/index.php', 'convertXliffToJs', $e->getMessage(), 'critical');
}

$loader = new FilesystemLoader(
	[
		MH_ADMIN . '/templates', // Папка с шаблонами админки и дополнения к ним
		MH_TEMPLATES, // Папка с шаблонами дополнений
	]
);

$debug = true;

$twigConfigDebug = [
	'cache'       => false,
	'debug'       => true,
	'auto_reload' => true
];
$twigConfig      = ['cache' => MH_ROOT . '/_cache'];

if ($debug) $twigConfig = array_merge($twigConfig, $twigConfigDebug);

$mh_template = new Environment($loader, $twigConfig);

$mh_template->addFilter(new TwigFilter('htmlentities', 'htmlentities'));
$mh_template->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));

$mh_template->addExtension(new MobileDetectExtension());
$mh_template->addExtension(new DeclineExtension());
$mh_template->addExtension(new AdminUrlExtension());
$mh_template->addExtension(new MarkdownExtension());
$mh_template->addExtension(new CacheExtension());
$mh_template->addExtension(new IntlExtension());
$mh_template->addExtension(new CssInlinerExtension());
$mh_template->addExtension(new StringExtension());
$mh_template->addExtension(new HtmlExtension());
$mh_template->addExtension(new InkyExtension());
$mh_template->addExtension(new TextLimiter());
$mh_template->addExtension(new DateTimeFormatter());
if ($debug) $mh_template->addExtension(new DebugExtension());
$mh_template->addRuntimeLoader(
	new class implements RuntimeLoaderInterface {
		public function load($class) {
			if (MarkdownRuntime::class === $class) {
				return new MarkdownRuntime(new DefaultMarkdown());
			}
		}
	}
);
$mh_template->addRuntimeLoader(
	new class implements RuntimeLoaderInterface {
		public function load($class) {
			if (CacheRuntime::class === $class) {
				return new CacheRuntime(new TagAwareAdapter(new FilesystemAdapter()));
			}
		}
	}
);


$mh_config = DataManager::getConfig('maharder');
$mh->setBreadcrumb(new BreadCrumb($mh->getLinkName('index'), $mh->getLinkUrl('index')));
