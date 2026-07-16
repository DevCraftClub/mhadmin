<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DevCraft\Core\Abstracts\AbstractEntity;
use DevCraft\Core\Composer\Repositories\ComposerDataRepository;

/**
 * Сущность пакета Composer в таблице `devcraft_composer_data`.
 *
 * @see ComposerDataRepository Репозиторий CRUD и синхронизации снимка.
 * @see \DevCraft\Core\Composer\ComposerDbSyncService Заполнение из json/lock/manifest.
 */
#[Entity(role: 'composer_data', repository: ComposerDataRepository::class, table: 'devcraft_composer_data')]
class ComposerData extends AbstractEntity {

	#[Column(type: 'string')]
	public string $plugin;

	#[Column(type: 'string')]
	public string $package;

	#[Column(type: 'string')]
	public string $version;

	#[Column(type: 'string')]
	public string $appCode;

	#[Column(type: 'boolean')]
	public bool $installed;

	#[Column(type: 'boolean')]
	public bool $required;

	/**
	 * Создаёт сущность ComposerData из доменного снимка пакета.
	 *
	 * @param array{name:string,version:string,installed:bool,required:bool,plugin?:string,appCode?:string} $payload
	 */
	public static function fromArray(array $payload): self {
		$entity            = new self();
		$entity->plugin    = (string) ($payload['plugin'] ?? 'devcraft_admin');
		$entity->package   = (string) ($payload['name'] ?? '');
		$entity->version   = (string) ($payload['version'] ?? '');
		$entity->appCode   = (string) ($payload['appCode'] ?? 'devcraft_admin');
		$entity->installed = (bool) ($payload['installed'] ?? false);
		$entity->required  = (bool) ($payload['required'] ?? false);

		return $entity;
	}

	/**
	 * @return array{name:string,version:string,installed:bool,required:bool,plugin:string,appCode:string,id:int}
	 */
	public function toArray(): array {
		return [
			'id'        => $this->id(),
			'name'      => $this->package,
			'version'   => $this->version,
			'installed' => $this->installed,
			'required'  => $this->required,
			'plugin'    => $this->plugin,
			'appCode'   => $this->appCode,
		];
	}

	public function getColumnVal(string $name): mixed {
		return match ($name) {
			'id'               => $this->id(),
			'package', 'name'  => $this->package,
			'version'          => $this->version,
			'installed'        => $this->installed ? '1' : '0',
			'required'         => $this->required ? '1' : '0',
			'plugin'           => $this->plugin,
			'appCode'          => $this->appCode,
			default            => NULL,
		};
	}
}
