<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * Хранилище default-политик Composer в JSON-конфиге `devcraft/config/composer_admin.json`.
 *
 * @see PackagePolicyService Проверка действий по сохранённым политикам.
 */
final class DefaultPackagePolicyStore {

	private const CONFIG_FILE = ROOT_DIR . '/devcraft/config/composer_admin.json';

	/**
	 * @return array<string, array{name: string, minAllowedVersion: string, downgradeBlocked: bool, removeBlocked: bool, origin: string}>
	 */
	public function all(): array {
		$config = $this->readConfig();
		$items  = $config['default_policies'] ?? [];
		if(!is_array($items)) {
			return [];
		}

		$result = [];
		foreach($items as $item) {
			if(!is_array($item)) {
				continue;
			}

			$name = (string) ($item['name'] ?? '');
			if($name === '') {
				continue;
			}

			$result[$name] = [
				'name'              => $name,
				'minAllowedVersion' => (string) ($item['minAllowedVersion'] ?? ''),
				'downgradeBlocked'  => (bool) ($item['downgradeBlocked'] ?? true),
				'removeBlocked'     => (bool) ($item['removeBlocked'] ?? true),
				'origin'            => (string) ($item['origin'] ?? 'manual'),
			];
		}

		return $result;
	}

	public function upsert(array $policy): void {
		$name = (string) ($policy['name'] ?? '');
		if($name === '') {
			return;
		}

		$all                        = $this->all();
		$all[$name]                 = [
			'name'              => $name,
			'minAllowedVersion' => (string) ($policy['minAllowedVersion'] ?? ''),
			'downgradeBlocked'  => (bool) ($policy['downgradeBlocked'] ?? true),
			'removeBlocked'     => (bool) ($policy['removeBlocked'] ?? true),
			'origin'            => (string) ($policy['origin'] ?? 'manual'),
		];
		$config                     = $this->readConfig();
		$config['default_policies'] = array_values($all);
		$this->writeConfig($config);
	}

	public function remove(string $name): void {
		$all = $this->all();
		unset($all[$name]);
		$config                     = $this->readConfig();
		$config['default_policies'] = array_values($all);
		$this->writeConfig($config);
	}

	public function isSeeded(): bool {
		$config = $this->readConfig();

		return (bool) ($config['seeded_from_composer_json'] ?? false);
	}

	public function markSeeded(): void {
		$config                              = $this->readConfig();
		$config['seeded_from_composer_json'] = true;
		$this->writeConfig($config);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function readConfig(): array {
		if(!is_file(self::CONFIG_FILE)) {
			return [
				'default_policies'          => [],
				'seeded_from_composer_json' => false,
			];
		}

		$raw = file_get_contents(self::CONFIG_FILE);
		if($raw === false || $raw === '') {
			return [];
		}

		$decoded = json_decode($raw, true);

		return is_array($decoded)? $decoded : [];
	}

	/**
	 * @param   array<string, mixed>  $config
	 */
	private function writeConfig(array $config): void {
		file_put_contents(
			self::CONFIG_FILE,
			json_encode($config, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL,
		);
	}

}
