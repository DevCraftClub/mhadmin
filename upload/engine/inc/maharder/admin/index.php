<?php

use Twig\TwigFilter;
use jblond\TwigTrans\Translation;
use Twig\Extension\DebugExtension;
use MaHarder\Classes\DeclineExtension;
use MaHarder\Classes\AdminUrlExtension;
use MaHarder\Classes\MobileDetectExtension;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Twig\Environment;
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

if( !defined('DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../../' );
	die( "Hacking attempt!" );
}

global $lang;

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');
define('THIS_HOST', $_SERVER['HTTP_HOST']);
define('THIS_SELF', $_SERVER['PHP_SELF']);
define('URL', (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http').'://'.THIS_HOST.'/engine/inc');

require_once DLEPlugins::Check(MH_ROOT.'/_includes/vendor/autoload.php');
require_once DLEPlugins::Check(MH_ROOT.'/_includes/classes/Admin.php');
require_once DLEPlugins::Check(MH_ROOT.'/_includes/classes/DeclineExtension.php');
require_once DLEPlugins::Check(MH_ROOT.'/_includes/classes/MobileDetectExtension.php');
require_once DLEPlugins::Check(MH_ROOT.'/_includes/classes/AdminUrlExtension.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/inc/include/functions.inc.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/skins/default.skin.php');
require_once DLEPlugins::Check(ENGINE_DIR.'/data/config.php');

$loader = new FilesystemLoader(MH_ADMIN.'/templates');

$langCode = 'ru_RU';
putenv("LC_ALL=$langCode.UTF-8");
if (setlocale(LC_ALL, "$langCode.UTF-8", $langCode, 'ru') === false) {
	echo sprintf('Языковой код %s не найден', $langCode);
}

$localDir = MH_ADMIN . '/_locales';
if(!mkdir($localDir . '/' . $langCode, 0777, true) && !is_dir($localDir)) {
	throw new \RuntimeException(sprintf('Папка "%s" не могла быть создана', $localDir));
}

bindtextdomain("MHAdmin", $localDir);
textdomain("MHAdmin");

$debug = false;

$twigConfigDebug = [
	'cache' => false,
	'debug' => true,
	'auto_reload' => true
];
$twigConfig = ['cache' => MH_ADMIN.'/_cache'];

if($debug) $twigConfig = array_merge($twigConfig, $twigConfigDebug);

$mh_template = new Environment($loader, $twigConfig);

$filter = new TwigFilter(
	'trans',
	function ($context, $string) {
		return Translation::transGetText($string, $context);
	},
	['needs_context' => true]
);
$mh_template->addFilter($filter);

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
$mh_template->addExtension(new Translation());
if($debug) $mh_template->addExtension(new DebugExtension());
$mh_template->addRuntimeLoader(new class implements RuntimeLoaderInterface {
	public function load($class) {
		if (MarkdownRuntime::class === $class) {
			return new MarkdownRuntime(new DefaultMarkdown());
		}
	}
});
$mh_template->addRuntimeLoader(new class implements RuntimeLoaderInterface {
	public function load($class) {
		if (CacheRuntime::class === $class) {
			return new CacheRuntime(new TagAwareAdapter(new FilesystemAdapter()));
		}
	}
});

$dle_links_header = [
	'config' => $lang['opt_hopt'],
	'user' => $lang['opt_s_acc'],
	'templates' => $lang['opt_s_tem'],
	'filter' => $lang['opt_s_fil'],
	'others' => $lang['opt_s_oth'],
	'admin_sections' => $lang['admin_other_section'],
];

$admin_links = [
	[
		'name' => $lang['header_all'],
		'href' => '?mod=options&action=options',
		'type' => 'link',
		'children' => []
	],
	[
		'name' => '',
		'href' => '',
		'type' => 'divider',
		'children' => []
	],
	[
		'name' => _('Новости'),
		'href' => '',
		'type' => 'dropdown',
		'children' => [
			[
				'name' => $lang['add_news'],
				'href' => '?mod=addnews&action=addnews',
				'type' => 'link',
				'children' => []
			],
			[
				'name' => $lang['edit_news'],
				'href' => '?mod=editnews&action=list',
				'type' => 'link',
				'children' => []
			],
		]
	],
	[
		'name' => '',
		'href' => '',
		'type' => 'divider',
		'children' => []
	],
];

foreach($options as $o => $a) {
	$opt_children = [];

	foreach($a as $c) $opt_children[] = [
		'name' => $c['name'],
		'href' => $c['url'],
		'type' => 'link',
		'children' => []
	];

	$admin_links[] = [
		'name' => $dle_links_header[$o],
		'href' => '',
		'type' => 'dropdown',
		'children' => $opt_children
	];
}

// Добавляем новую ссылку в меню
// Новая ссылка должна быть массивом
// В массиве должны быть указаны "Название (name)", "Ссылка (href)", "Тип ссылки (type)" и "Подссылки (children)"
// Тип ссылок может быть одним из "link (просто ссылка)", "divider (разделитель)" или "dropdown (выпадающее меню - настроенно до второго уровня)"
// Подссылки имеют тот же формат, что и сами ссылки
$links = [
	'dle' => [
		'name' => _('Страницы DLE'),
		'href' => '',
		'type' => 'dropdown',
		'children' => $admin_links,
	],
	'index' => [
		'name' => _('Главная'),
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'],
		'type' => 'link',
		'children' => [],
	],
	'changelog' => [
		'name' => _('История изменений'),
		'href' => THIS_SELF.'?mod='.$modInfo['module_code'].'&sites=changelog',
		'type' => 'link',
		'children' => [],
	],
];

$breadcrumbs = [
	[
		'name' => $links['index']['name'],
		'url' => $links['index']['href'],
	],
];

