<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

use DevCraft\Core\Application;
use DevCraft\Core\Composer\Models\ComposerData;
use DevCraft\Core\Composer\Repositories\ComposerDataRepository;

/**
 * Синхронизирует данные Composer между runtime-снимком (json/lock/manifest) и БД.
 *
 * @see ComposerStateReader Статус установки из composer.lock.
 * @see ManifestPackageReader Обязательные пакеты модулей.
 *
 * @example
 *     (new ComposerDbSyncService())->syncFromRuntimeSnapshot();
 *     (new ComposerDbSyncService())->applySuccessfulAction('install', 'vendor/package');
 */
final class ComposerDbSyncService {

	public function __construct(
		private readonly ComposerStateReader   $stateReader = new ComposerStateReader(),
		private readonly ManifestPackageReader $manifestReader = new ManifestPackageReader(),
	) {}

	/**
	 * Выполняет полный ручной sync из composer.json, manifest и composer.lock.
	 */
	public function syncFromRuntimeSnapshot(): void {
		$required     = $this->manifestReader->requiredPackages();
		$installed    = $this->stateReader->installedPackages();
		$composerDeps = $this->readComposerJsonDependencies();
		$allNames     = array_unique(array_merge(array_keys($composerDeps), array_keys($required)));
		sort($allNames);

		$snapshot = [];

		foreach($allNames as $name) {
			$lockEntry   = $installed[$name] ?? NULL;
			$isInstalled = $lockEntry !== NULL && (bool) ($lockEntry['isInstalled'] ?? false);
			$rule        = $required[$name] ?? NULL;

			if($isInstalled) {
				$version = (string) ($lockEntry['installedVersion'] ?? '');
			} elseif(isset($composerDeps[$name])) {
				$version = $composerDeps[$name];
			} else {
				$version = (string) ($rule['minVersion'] ?? '');
			}

			$plugin     = (string) ($rule['plugin'] ?? 'Admin');
			$appCode    = (string) ($rule['appCode'] ?? 'devcraft');
			$isRequired = (bool) ($rule['isHardRequired'] ?? false);

			$snapshot[] = [
				'name'      => $name,
				'version'   => $version,
				'installed' => $isInstalled,
				'required'  => $isRequired,
				'plugin'    => $plugin,
				'appCode'   => $appCode,
			];
		}

		/** @var ComposerDataRepository $repository */
		$repository = Application::instance()->database()->repository(ComposerData::class);
		$repository->replaceFromSnapshot($snapshot);
	}

	/**
	 * Обновляет БД после успешного Composer-действия с повторным чтением lock.
	 */
	public function applySuccessfulAction(string $actionType, string $packageName, ?string $targetVersion = NULL): void {
		/** @var ComposerDataRepository $repository */
		$repository = Application::instance()->database()->repository(ComposerData::class);
		$existing   = $repository->findByPackage($packageName);

		$required    = $existing?->required ?? false;
		$appCode     = $existing?->appCode ?? 'devcraft';
		$plugin      = $existing?->plugin ?? 'Admin';
		$installed   = $this->stateReader->installedPackages();
		$lockEntry   = $installed[$packageName] ?? NULL;
		$isInstalled = $lockEntry !== NULL && (bool) ($lockEntry['isInstalled'] ?? false);

		if($isInstalled) {
			$version = (string) ($lockEntry['installedVersion'] ?? ($existing?->version ?? ''));
		} elseif($existing !== NULL) {
			$version = $existing->version;
		} else {
			$version = $targetVersion ?? '';
		}

		if(!$isInstalled) {
			$version = $actionType === 'remove'? '' : $version;
		}

		$repository->upsertByPackage([
			'name'      => $packageName,
			'version'   => $version,
			'installed' => $isInstalled,
			'required'  => $required,
			'appCode'   => $appCode,
			'plugin'    => $plugin,
		]);
	}

	/**
	 * @return array<string, string>
	 */
	private function readComposerJsonDependencies(): array {
		$composerJsonPath = ROOT_DIR . '/devcraft/composer.json';
		if(!is_file($composerJsonPath)) {
			return [];
		}

		$raw = file_get_contents($composerJsonPath);
		if($raw === false || $raw === '') {
			return [];
		}

		$decoded = json_decode($raw, true);
		if(!is_array($decoded)) {
			return [];
		}

		$require = $decoded['require'] ?? [];
		if(!is_array($require)) {
			return [];
		}

		$deps = [];
		foreach($require as $name => $version) {
			$package = (string) $name;
			if($package === '' || str_starts_with($package, 'php') || str_starts_with($package, 'ext-')) {
				continue;
			}

			$deps[$package] = (string) $version;
		}

		ksort($deps);

		return $deps;
	}

}
