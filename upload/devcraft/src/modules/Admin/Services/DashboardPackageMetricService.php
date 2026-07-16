<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Composer\ComposerStateReader;
use DevCraft\Core\Composer\ManifestPackageReader;

/**
 * Считает отсутствующие обязательные пакеты и список пакетов для Dashboard.
 */
final class DashboardPackageMetricService {

	public function __construct(
		private readonly ComposerStateReader $stateReader = new ComposerStateReader(),
		private readonly ManifestPackageReader $manifestReader = new ManifestPackageReader(),
	) {}

	/**
	 * Количество отсутствующих hardRequired-пакетов.
	 *
	 * @param string|null $appCode Код приложения / plugin; null — все модули.
	 */
	public function missingRequiredCount(?string $appCode = NULL): int {
		$missing = 0;

		foreach($this->packagesForDashboard($appCode) as $package) {
			if(($package['isHardRequired'] ?? false) && !($package['installed'] ?? false)) {
				$missing++;
			}
		}

		return $missing;
	}

	/**
	 * Список пакетов для панели Composer на dashboard.
	 *
	 * @param string|null $appCode Код приложения / plugin; null — все модули.
	 *
	 * @return list<array{name: string, version: string, installed: bool, isHardRequired: bool}>
	 */
	public function packagesForDashboard(?string $appCode = NULL): array {
		$installed = $this->stateReader->installedPackages();
		$required  = $this->manifestReader->requiredPackages();
		$result    = [];

		foreach($required as $name => $rule) {
			if($appCode !== NULL && $appCode !== '') {
				$ruleApp    = (string) ($rule['appCode'] ?? '');
				$rulePlugin = (string) ($rule['plugin'] ?? '');

				if($ruleApp !== $appCode && $rulePlugin !== $appCode) {
					continue;
				}
			}

			$result[] = [
				'name'           => $name,
				'version'        => (string) ($rule['minVersion'] ?? ''),
				'installed'      => isset($installed[$name]),
				'isHardRequired' => (bool) ($rule['isHardRequired'] ?? false),
			];
		}

		return $result;
	}
}
