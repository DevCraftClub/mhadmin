<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Composer\DefaultPackagePolicyStore;

/**
 * Инициализирует default-политики из composer.json.
 */
final class DefaultPackageSeedService {

	public function __construct(
		private readonly DefaultPackagePolicyStore $store = new DefaultPackagePolicyStore(),
	) {}

	public function seedIfNeeded(): void {
		if($this->store->isSeeded()) {
			return;
		}

		$composerJson = ROOT_DIR . '/devcraft/composer.json';
		if(!is_file($composerJson)) {
			return;
		}

		$raw = file_get_contents($composerJson);
		if($raw === false || $raw === '') {
			return;
		}

		$decoded = json_decode($raw, true);
		$require = is_array($decoded)? ($decoded['require'] ?? []) : [];
		if(!is_array($require)) {
			return;
		}

		foreach($require as $name => $version) {
			$package = (string) $name;
			if($package === '' || str_starts_with($package, 'php') || str_starts_with($package, 'ext-')) {
				continue;
			}

			$this->store->upsert([
				'name'              => $package,
				'minAllowedVersion' => ltrim((string) $version, '^~>=< '),
				'downgradeBlocked'  => true,
				'removeBlocked'     => true,
				'origin'            => 'composer_json_seed',
			]);
		}

		$this->store->markSeeded();
	}

}
