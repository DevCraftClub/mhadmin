<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * Проверяет ограничения default-политик перед пакетными действиями Composer.
 *
 * @see DefaultPackagePolicyStore Источник политик.
 * @see ComposerRuntimeAdapter Выполнение действий после успешной проверки.
 *
 * @example
 *     $blocked = (new PackagePolicyService())->validateAction('remove', 'vendor/package');
 *     if($blocked !== null) { /* политика запрещает действие *\/ }
 */
final class PackagePolicyService {

	public function __construct(
		private readonly DefaultPackagePolicyStore $store = new DefaultPackagePolicyStore(),
	) {}

	public function validateAction(string $actionType, string $packageName, ?string $targetVersion = null): ?ComposerActionResult {
		$policies = $this->store->all();
		$policy   = $policies[$packageName] ?? null;

		if($policy === null) {
			return null;
		}

		if($actionType === 'remove' && (bool) ($policy['removeBlocked'] ?? false)) {
			return ComposerActionResult::error('Удаление пакета запрещено политикой по умолчанию');
		}

		if(
			$actionType === 'update'
			&& (bool) ($policy['downgradeBlocked'] ?? false)
			&& $targetVersion !== null
			&& $targetVersion !== ''
		) {
			$minAllowedVersion = (string) ($policy['minAllowedVersion'] ?? '');
			if($minAllowedVersion !== '' && version_compare($targetVersion, $minAllowedVersion, '<')) {
				return ComposerActionResult::error('Понижение версии ниже минимального порога запрещено');
			}
		}

		return null;
	}
}
