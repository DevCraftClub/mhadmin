<?php

declare(strict_types=1);

/**
 * ponytail: boot() не создаёт Twig; twig() — лениво.
 * Запуск: php upload/devcraft/src/classes/Application.selfcheck.php
 */

$src = (string) file_get_contents(__DIR__ . '/Application.php');

assert(
	preg_match('/function boot\(\): void \{[\s\S]*?EnvironmentFactory::create\(\)/', $src) !== 1,
	'boot() must not create Twig',
);
assert(
	str_contains($src, 'if($this->twig === NULL)')
	&& str_contains($src, '$this->twig = EnvironmentFactory::create()'),
	'twig() must lazy-create Environment',
);

fwrite(STDOUT, "Application.selfcheck: ok\n");
