<?php

declare(strict_types=1);

/**
 * ponytail: панель есть; boot/render/inject; доступ по настройкам; админ layout + публичный TagService.
 * Запуск: php upload/devcraft/src/modules/Admin/DebugToolbar/ToolbarService.selfcheck.php
 */

$toolbar = (string) file_get_contents(__DIR__ . '/ToolbarService.php');
$registry = (string) file_get_contents(__DIR__ . '/QueryRegistry.php');
$layout = (string) file_get_contents(dirname(__DIR__, 3) . '/templates/core/layout.twig');
$partial = (string) file_get_contents(dirname(__DIR__, 3) . '/templates/core/includes/debug_toolbar.twig');
$tag = (string) file_get_contents(dirname(__DIR__) . '/Services/PublicAssetTagService.php');
$app = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Application.php');
$gateway = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Database/DatabaseGateway.php');
$router = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Admin/Router.php');
$schema = (string) file_get_contents(dirname(__DIR__) . '/settings.schema.php');

assert(str_contains($toolbar, 'function boot'), 'ToolbarService::boot');
assert(str_contains($toolbar, 'function isBooted'), 'ToolbarService::isBooted');
assert(str_contains($toolbar, 'function isActive'), 'ToolbarService::isActive');
assert(str_contains($toolbar, 'function canCollect'), 'ToolbarService::canCollect');
assert(str_contains($toolbar, 'function hookDleAdmin'), 'ToolbarService::hookDleAdmin');
assert(str_contains($toolbar, 'function withFreshTiming'), 'ToolbarService::withFreshTiming');
assert(str_contains($toolbar, 'function renderHtml'), 'ToolbarService::renderHtml');
assert(str_contains($toolbar, 'function injectIntoHtml'), 'ToolbarService::injectIntoHtml');
assert(str_contains($toolbar, '$Timer'), 'prefer DLE microTimer');
assert(str_contains($toolbar, 'debug_toolbar_groups'), 'groups gate');
assert(str_contains($toolbar, 'debug_toolbar_panels'), 'panels gate');
assert(str_contains($toolbar, 'query_list'), 'DLE query_list snapshot');
assert(str_contains($registry, 'function record'), 'QueryRegistry::record');
assert(str_contains($partial, 'data-devcraft-debug-toolbar'), 'toolbar marker');
assert(str_contains($partial, 'appendChild'), 'reparent toolbar to body');
assert(str_contains($partial, 'data-toolbar-browser-ms'), 'browser timing slot');
assert(str_contains($partial, 'sql_ms_label'), 'SQL total in bar');
assert(str_contains($partial, 'Куда ушло время'), 'timing breakdown panel');
assert(str_contains($partial, 'Этапы генерации'), 'stages list');
assert(str_contains($partial, 'Самые долгие запросы'), 'slowest queries');
assert(str_contains($toolbar, 'function sortByTimeDesc'), 'sort queries by duration');
assert(str_contains($toolbar, 'function buildStages'), 'buildStages');
assert(str_contains($toolbar, 'function mark'), 'Timing marks');
assert(str_contains($app, 'DebugHooks'), 'Application uses DebugHooks not hard ToolbarService use');
assert(str_contains($app, "mark('devcraft_boot_start')") || str_contains($app, "DebugHooks::mark('devcraft_boot_start')"), 'boot start mark');
assert(str_contains($toolbar, 'currentUserAllowed'), 'boot waits for allowed group');
assert(str_contains($toolbar, "member_id"), 'boot needs member_id before collect');
assert(str_contains($gateway, 'enableCycleSqlTap') || str_contains($gateway, 'attachCycleSqlLoggerIfNeeded') || str_contains($gateway, 'DebugHooks::isBooted'), 'Cycle logger gated');
assert(str_contains($schema, '->default(false)'), 'toolbar defaults off');
assert(str_contains($toolbar, 'Генерация страницы'), 'page-work stage label');
assert(str_contains($toolbar, "'kind'  => 'timing'"), 'time section kind');
assert(str_contains($toolbar, "'kind'  => 'vars'"), 'vars section kind');
assert(str_contains($toolbar, 'function buildRequestVars'), 'buildRequestVars');
assert(str_contains($toolbar, 'function isSensitiveKey'), 'redact secrets');
assert(str_contains($partial, 'Переменные запроса'), 'vars panel');
assert(str_contains($partial, 'data-toolbar-localstorage'), 'localStorage slot');
assert(str_contains($partial, 'data-toolbar-sessionstorage'), 'sessionStorage slot');
assert(str_contains($layout, 'debug_toolbar.twig'), 'admin layout include');
assert(str_contains($layout, 'debug_toolbar is defined and debug_toolbar'), 'admin layout shows toolbar without dc_debug');
assert(!str_contains($toolbar, 'isDebugEnabled()'), 'toolbar not gated by dc_debug');
assert(str_contains($registry, 'isBooted()'), 'QueryRegistry records when toolbar booted');
assert(str_contains($tag, "boot('public')"), 'public boot');
assert(str_contains($tag, 'register_shutdown_function'), 'public shutdown inject');
assert(str_contains($app, "boot('admin')"), 'admin runAdmin boot');
assert(str_contains($app, 'hookDleAdmin()'), 'Application boot hooks native admin.php');
assert(str_contains((string) file_get_contents(dirname(__DIR__, 5) . '/install.xml'), 'echofooter'), 'install.xml patches echofooter');
assert(str_contains($app, "\$_SESSION['dc_debug']"), 'dc_debug early in runAdmin');
assert(str_contains($router, 'viewModel()'), 'Router passes viewModel');
assert(str_contains($router, 'isBooted()'), 'Router does not reboot toolbar');
assert(str_contains($router, "isActive('admin')"), 'Router checks isActive');
assert(str_contains($gateway, 'CycleQueryLogger'), 'Cycle driver logger');
assert(str_contains($gateway, 'setLogger'), 'DatabaseManager::setLogger');
assert(is_file(__DIR__ . '/CycleQueryLogger.php'), 'CycleQueryLogger file');
assert(str_contains($schema, "checkbox('debug_toolbar'"), 'settings: debug_toolbar');
assert(str_contains($schema, "multi('debug_toolbar_groups'"), 'settings: groups');
assert(str_contains($schema, "multi('debug_toolbar_panels'"), 'settings: panels');
assert(str_contains($schema, "checkbox('debug_toolbar_admin'"), 'settings: admin surface');
assert(str_contains($schema, "checkbox('debug_toolbar_public'"), 'settings: public surface');

fwrite(STDOUT, "ToolbarService.selfcheck: ok\n");
