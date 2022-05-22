<?php


require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/LogGenerator.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/DataLoader.php');
require_once DLEPlugins::Check(MH_ROOT . '/_includes/traits/AssetsChecker.php');

class Ajax {
	use LogGenerator;
	use DataLoader;
	use AssetsChecker;
}