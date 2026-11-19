<?php

declare(strict_types=1);

/**
 * ponytail: быстрый путь кэша схемы без schemaTablesExist на hit.
 * Запуск: php upload/devcraft/src/classes/Database/SchemaSyncService.selfcheck.php
 */

$src = (string) file_get_contents(__DIR__ . '/SchemaSyncService.php');

assert(str_contains($src, 'function tryTrustedCachedSchema'), 'trusted cache helper');
assert(str_contains($src, 'tryTrustedCachedSchema()'), 'ensureSchemaReady uses fast path');
assert(
	preg_match(
		'/if \(\$cached !== NULL && !\$needPending && !\$bootstrap && \$this->schemaTablesExist\(\$cached\)\)/',
		$src,
	) !== 1,
	'early cache return must not call schemaTablesExist',
);

fwrite(STDOUT, "SchemaSyncService.selfcheck: ok\n");
