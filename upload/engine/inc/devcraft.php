<?php

declare(strict_types=1);

/*
=====================================================
 DevCraft — DLE 20.0 admin entry (thin)
=====================================================
*/

if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
    header('HTTP/1.1 403 Forbidden');
    header('Location: ../../');

    exit('Hacking attempt!');
}

$dcRoot   = rtrim((string) ROOT_DIR, '/\\') . '/devcraft';
$dcVendor = $dcRoot . '/vendor/autoload.php';

require_once $dcRoot . '/src/bootstrap/composer_marker.php';

// Не DLEPlugins::Check: копия init.php в кэше плагина ищет vendor рядом с собой и не видит пакеты.
// Готово, если есть autoload и composer.lock либо .composer_initialized.
if (!is_file($dcVendor) || !dc_composer_initialized()) {
    require_once $dcRoot . '/bootstrap.php';

    return;
}

dc_composer_ensure_stamp();

if (!defined('DEVCRAFT_BOOTSTRAPPED')) {
    require_once $dcRoot . '/init.php';
}

if (!defined('DEVCRAFT_BOOTSTRAPPED')) {
    require_once $dcVendor;
    require_once $dcRoot . '/src/sdk/dle/bootstrap.php';
    define('DEVCRAFT_BOOTSTRAPPED', true);
    DevCraft\Core\Config\Paths::register();
    DevCraft\Core\Application::instance()->boot();
}

if (!DevCraft\Core\Support\AdminAccess::allowsDevCraftAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';

    return;
}

DevCraft\Core\Application::instance()->runAdmin(moduleDir: 'Admin');
