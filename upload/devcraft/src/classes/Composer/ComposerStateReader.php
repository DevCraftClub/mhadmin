<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * Читает сведения об установленных пакетах из `devcraft/composer.lock`.
 *
 * @see ComposerDbSyncService Синхронизация статуса установки с БД.
 *
 * @example
 *     $installed = (new ComposerStateReader())->installedPackages();
 *     $entry = $installed['vendor/package'] ?? null;
 */
final class ComposerStateReader {

	/**
	 * @return array<string, array{name: string, installedVersion: string, isInstalled: bool, source: string}>
	 */
	public function installedPackages(): array {
		$lockPath = ROOT_DIR . '/devcraft/composer.lock';

		if(!is_file($lockPath)) {
			return [];
		}

		$raw = file_get_contents($lockPath);
		if($raw === false || $raw === '') {
			return [];
		}

		$decoded = json_decode($raw, true);
		if(!is_array($decoded)) {
			return [];
		}

		$packages = [];
		$list     = array_merge($decoded['packages'] ?? [], $decoded['packages-dev'] ?? []);

		foreach($list as $item) {
			if(!is_array($item)) {
				continue;
			}

			$name = (string) ($item['name'] ?? '');
			if($name === '') {
				continue;
			}

			$packages[$name] = [
				'name'             => $name,
				'installedVersion' => (string) ($item['version'] ?? ''),
				'isInstalled'      => true,
				'source'           => 'lock',
			];
		}

		ksort($packages);

		return $packages;
	}
}
