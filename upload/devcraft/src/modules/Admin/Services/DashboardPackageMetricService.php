<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Composer\ComposerStateReader;
use DevCraft\Core\Composer\ManifestPackageReader;

/**
 * Считает количество отсутствующих обязательных пакетов для Dashboard.
 */
final class DashboardPackageMetricService {

	public function __construct(
		private readonly ComposerStateReader $stateReader = new ComposerStateReader(),
		private readonly ManifestPackageReader $manifestReader = new ManifestPackageReader(),
	) {}

	public function missingRequiredCount(): int {
		$installed = $this->stateReader->installedPackages();
		$required  = $this->manifestReader->requiredPackages();
		$missing   = 0;

		foreach($required as $name => $rule) {
			if(!($rule['isHardRequired'] ?? false)) {
				continue;
			}

			if(!isset($installed[$name])) {
				$missing++;
			}
		}

		return $missing;
	}
}
