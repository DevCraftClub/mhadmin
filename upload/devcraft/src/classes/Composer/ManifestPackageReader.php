<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

use DevCraft\Core\Application;

/**
 * Агрегирует `composer_required` из всех модулей реестра DevCraft.
 *
 * @see ComposerDbSyncService Использует правила для атрибуции модулей в БД.
 *
 * @example
 *     $rules = (new ManifestPackageReader())->requiredPackages();
 *     $rule = $rules['vendor/package'] ?? null;
 */
final class ManifestPackageReader {

	/**
	 * @return array<string, array{name: string, requiredBy: string, minVersion: string, isHardRequired: bool, plugin: string, appCode: string}>
	 */
	public function requiredPackages(): array {
		$result = [];

		foreach(Application::instance()->registry()->modules() as $moduleId => $module) {
			$dirName = basename(rtrim($module->path, '/\\'));
			$appCode = $module->code ?? $module->id;

			foreach($module->composerRequired as $rule) {
				$name = trim($rule->package);
				if($name === '') {
					continue;
				}

				if(!isset($result[$name])) {
					$result[$name] = [
						'name'           => $name,
						'requiredBy'     => $moduleId,
						'minVersion'     => $rule->version,
						'isHardRequired' => $rule->requires,
						'plugin'         => $dirName,
						'appCode'        => $appCode,
					];
					continue;
				}

				if($rule->requires) {
					$result[$name]['isHardRequired'] = true;
				}
			}
		}

		return $result;
	}
}
