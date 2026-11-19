<?php

declare(strict_types=1);

/**
 * ponytail: проверяем только форму настройки Twig; это не замена запуску сайта.
 * Запуск: php upload/devcraft/src/classes/Twig/EnvironmentFactory.selfcheck.php
 */

$source = (string) file_get_contents(__DIR__ . '/EnvironmentFactory.php');

assert(str_contains($source, "'cache'      => self::cachePath(),"), 'Кэш Twig должен идти через путь на диске');
assert(str_contains($source, "'auto_reload' => \$useDebug,"), 'В отладке должен быть включён auto_reload');
assert(str_contains($source, 'private static function cachePath(): string'), 'cachePath должен возвращать путь');
assert(!str_contains($source, 'string|false'), 'cachePath не должен отключать кэш через false');

fwrite(STDOUT, "EnvironmentFactory.selfcheck: ok\n");
