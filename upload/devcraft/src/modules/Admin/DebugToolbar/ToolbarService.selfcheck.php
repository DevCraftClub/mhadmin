<?php

declare(strict_types=1);

/**
 * ponytail: панель есть; boot/render/inject; админ layout + публичный TagService.
 * Запуск: php upload/devcraft/src/modules/Admin/DebugToolbar/ToolbarService.selfcheck.php
 */

$root = dirname(__DIR__, 5); // .../upload — слишком глубоко; якоримся от __DIR__

$toolbar = (string) file_get_contents(__DIR__ . '/ToolbarService.php');
$registry = (string) file_get_contents(__DIR__ . '/QueryRegistry.php');
$layout = (string) file_get_contents(dirname(__DIR__, 3) . '/templates/core/layout.twig');
$partial = (string) file_get_contents(dirname(__DIR__, 3) . '/templates/core/includes/debug_toolbar.twig');
$tag = (string) file_get_contents(dirname(__DIR__) . '/Services/PublicAssetTagService.php');
$app = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Application.php');
$gateway = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Database/DatabaseGateway.php');
$router = (string) file_get_contents(dirname(__DIR__, 3) . '/classes/Admin/Router.php');

assert(str_contains($toolbar, 'function boot'), 'ToolbarService::boot');
assert(str_contains($toolbar, 'function renderHtml'), 'ToolbarService::renderHtml');
assert(str_contains($toolbar, 'function injectIntoHtml'), 'ToolbarService::injectIntoHtml');
assert(str_contains($toolbar, 'query_list'), 'DLE query_list snapshot');
assert(str_contains($registry, 'function record'), 'QueryRegistry::record');
assert(str_contains($partial, 'data-devcraft-debug-toolbar'), 'toolbar marker');
assert(str_contains($layout, 'debug_toolbar.twig'), 'admin layout include');
assert(str_contains($tag, 'ToolbarService::instance()->boot()'), 'public boot');
assert(str_contains($tag, 'register_shutdown_function'), 'public shutdown inject');
assert(str_contains($app, 'ToolbarService::instance()->boot()'), 'admin runAdmin boot');
assert(str_contains($router, 'viewModel()'), 'Router passes viewModel');
assert(str_contains($gateway, "QueryRegistry::record('cycle'"), 'Cycle query recording');

fwrite(STDOUT, "ToolbarService.selfcheck: ok\n");
