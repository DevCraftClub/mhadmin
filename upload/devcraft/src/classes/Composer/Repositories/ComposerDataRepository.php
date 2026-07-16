<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer\Repositories;

use DevCraft\Core\Abstracts\AbstractRepository;
use DevCraft\Core\Application;
use DevCraft\Core\Composer\Models\ComposerData;

/**
 * Репозиторий пакетов Composer в таблице `devcraft_composer_data`.
 *
 * @see ComposerData Сущность ORM.
 * @see \DevCraft\Core\Composer\ComposerDbSyncService Полная и точечная синхронизация.
 */
class ComposerDataRepository extends AbstractRepository {

	public function findByPackage(string $package): ?ComposerData {
		/** @var ComposerData|null $entity */
		$entity = $this->select()->where('package', $package)->fetchOne();

		return $entity;
	}

	/**
	 * @param array{name:string,version:string,installed:bool,required:bool,plugin?:string,appCode?:string} $payload
	 */
	public function upsertByPackage(array $payload): ComposerData {
		$package = (string) ($payload['name'] ?? '');
		$entity  = $this->findByPackage($package);

		if($entity === null) {
			$entity = ComposerData::fromArray($payload);
		} else {
			$entity->plugin    = (string) ($payload['plugin'] ?? $entity->plugin);
			$entity->package   = $package;
			$entity->version   = (string) ($payload['version'] ?? $entity->version);
			$entity->appCode   = (string) ($payload['appCode'] ?? $entity->appCode);
			$entity->installed = (bool) ($payload['installed'] ?? $entity->installed);
			$entity->required  = (bool) ($payload['required'] ?? $entity->required);
		}

		Application::instance()->database()->getManager()->persist($entity)->run();

		return $entity;
	}

	/**
	 * @param list<array{name:string,version:string,installed:bool,required:bool,plugin?:string,appCode?:string}> $snapshot
	 */
	public function replaceFromSnapshot(array $snapshot): void {
		$manager = Application::instance()->database()->getManager();
		/** @var list<ComposerData> $existing */
		$existing = $this->select()->fetchAll();

		foreach($existing as $entity) {
			$manager->delete($entity);
		}

		foreach($snapshot as $payload) {
			$manager->persist(ComposerData::fromArray($payload));
		}

		$manager->run();
	}
}
