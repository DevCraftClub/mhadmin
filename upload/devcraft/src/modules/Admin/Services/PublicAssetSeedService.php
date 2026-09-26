<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Application;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;

/**
 * Однократный сид auto-записи Admin для `dc_public.js` и демо-ключа раздела quickstart.
 */
final class PublicAssetSeedService {

	private const string DC_PUBLIC_JS = 'devcraft/src/templates/core/assets/js/dc_public.js';

	/** Ключ раздела для сценария SC-004 в quickstart (только локальная проверка). */
	public const string QUICKSTART_SECTION_KEY = 'dc_demo_section';

	/**
	 * Создаёт строку `dc_public.js` и подставляет siteAssets в списки Admin.
	 */
	public function ensureRegistered(): void {
		$this->ensureAdminDcPublicJs();
		(new PublicAssetManifestSyncService())->syncAll();
	}

	public function ensureAdminDcPublicJs(): void {
		try {
			DleSiteSectionRegistry::instance()->register(
				self::QUICKSTART_SECTION_KEY,
				__('Демо-раздел quickstart'),
			);

			/** @var PublicAssetEntryRepository $repo */
			$repo = Application::instance()->database()->repository(PublicAssetEntry::class);
			$existing = $repo->findByKindAndLocalPath('js', self::DC_PUBLIC_JS);

			if($existing !== null) {
				return;
			}

			$entry               = new PublicAssetEntry();
			$entry->kind         = 'js';
			$entry->origin       = 'auto';
			$entry->module_code  = AdminIdentity::code();
			$entry->label        = 'dc_public.js';
			$entry->source_path  = self::DC_PUBLIC_JS;
			$entry->local_path   = self::DC_PUBLIC_JS;
			$entry->active       = true;
			$entry->sort_order   = 0;
			$repo->saveEntity($entry);
		} catch(\Throwable) {
			// Схема ещё не готова — сид повторится при следующем вызове.
		}
	}

}
