<?php

global $lang, $config;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Extra\Cache\CacheRuntime;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\Inky\InkyExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\Cache\CacheExtension;
use Twig\Extra\String\StringExtension;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

if (!defined('DATALIFEENGINE')) {
	header("HTTP/1.1 403 Forbidden");
	header('Location: ../../../../');
	die("Hacking attempt!");
}

if (!file_exists(ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php')) {
	require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/classes/ComposerAction.php');
	try {
		try {
			if (!file_exists(ENGINE_DIR . '/inc/maharder/admin/composer.lock')) {
				ComposerAction::install();
			}
			ComposerAction::update();
		} catch (Exception $e) {
			ComposerAction::update();
		}
	} catch (Exception $e) {
		ComposerAction::require();
	}
}


define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('THIS_SELF', $_SERVER['PHP_SELF']);
define('URL', $config['http_home_url'] . 'engine/inc');

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/include/functions.inc.php');
require_once DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php');
require_once DLEPlugins::Check(ENGINE_DIR . '/data/config.php');

if (!defined('MH_INIT')) {
	require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');
}

$mh        = new Admin();

include_once DLEPlugins::Check(MH_MODULES . '/admin/module/links.php');

try {
	MhTranslation::convertXliffToJs();
} catch (JsonException|Throwable $e) {
	LogGenerator::generateLog('Admin/index', 'convertXliffToJs', $e->getMessage(), 'critical');
}

$loader = new FilesystemLoader(
	[
		MH_ADMIN . '/templates', // Папка с шаблонами админки и дополнения к ним
		MH_TEMPLATES, // Папка с шаблонами дополнений
	]
);

$debug = false;

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
