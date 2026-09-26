<?php

/**
 * Самопроверка заглушки «установить все» (без bootstrap DLE).
 *
 * Запуск из каталога файла:
 * php composer_install_all.selfcheck.php
 */

declare(strict_types=1);

require __DIR__ . '/ComposerInstallAllPlaceholder.php';

use DevCraft\Core\Composer\ComposerInstallAllPlaceholder;

assert(ComposerInstallAllPlaceholder::PACKAGE === 'devcraft/composer-install-all');
assert(ComposerInstallAllPlaceholder::is(ComposerInstallAllPlaceholder::PACKAGE));
assert(!ComposerInstallAllPlaceholder::is('devcraftclub/dev-tools'));

$adapterSrc = file_get_contents(__DIR__ . '/ComposerRuntimeAdapter.php');
assert(is_string($adapterSrc) && str_contains($adapterSrc, 'function installAllFromComposerJson'));
assert(is_string($adapterSrc) && str_contains($adapterSrc, "['install']"));

$handlerSrc = file_get_contents(dirname(__DIR__, 2) . '/modules/Admin/Ajax/ComposerActionHandler.php');
assert(is_string($handlerSrc) && str_contains($handlerSrc, 'install_all'));
assert(is_string($handlerSrc) && str_contains($handlerSrc, 'installAllFromComposerJson'));

fwrite(STDOUT, "composer_install_all.selfcheck: ok\n");
