<?php

declare(strict_types=1);

/**
 * ponytail: публичный hot path не вызывает seed; sync только через syncIfChanged в debug.
 * Запуск: php upload/devcraft/src/modules/Admin/Services/PublicAssetTagService.selfcheck.php
 */

$tagPath  = __DIR__ . '/PublicAssetTagService.php';
$syncPath = __DIR__ . '/PublicAssetManifestSyncService.php';
$listPath = __DIR__ . '/PublicAssetListPageService.php';
$dashPath = __DIR__ . '/../Pages/DashboardPage.php';

$tag  = (string) file_get_contents($tagPath);
$sync = (string) file_get_contents($syncPath);
$list = (string) file_get_contents($listPath);
$dash = (string) file_get_contents($dashPath);

assert(
	!preg_match('/applyToTemplate[\s\S]*?ensureRegistered/', $tag)
	&& !str_contains($tag, '(new PublicAssetSeedService())->ensureRegistered()'),
	'applyToTemplate must not call ensureRegistered',
);
assert(
	str_contains($tag, 'LogGenerator::isDebugEnabled()')
	&& str_contains($tag, 'syncIfChanged()'),
	'applyToTemplate must syncIfChanged only when debug is on',
);
assert(
	str_contains($sync, 'function syncIfChanged')
	&& str_contains($sync, 'function manifestsFingerprint')
	&& str_contains($sync, 'manifest_sync.stamp'),
	'syncIfChanged + stamp fingerprint required',
);
assert(
	str_contains($dash, 'ensureRegistered()')
	&& str_contains($list, 'ensureRegistered()'),
	'Dashboard and Public Assets must keep full seed',
);

fwrite(STDOUT, "PublicAssetTagService.selfcheck: ok\n");
