<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Models;

use Cycle\Annotated\Annotation\Column;

/**
 * Колонки зависимостей и доступности для публичных ресурсов оболочки.
 *
 * TEXT в MySQL не может иметь ненулевой DEFAULT — пустой список = NULL / '' в БД, в PHP — [].
 */
trait PublicResourceDepsColumnsTrait {

	/** JSON-массив id записей той же категории; NULL/пусто = нет зависимостей. */
	#[Column(type: 'text', nullable: true, default: null)]
	public ?string $depends_on = null;

	/** JSON-массив ключей разделов; NULL/пусто = все разделы (кроме not_available). */
	#[Column(type: 'text', nullable: true, default: null)]
	public ?string $available = null;

	/** JSON-массив ключей разделов, где запись не показывается; NULL/пусто = без исключений. */
	#[Column(type: 'text', nullable: true, default: null)]
	public ?string $not_available = null;

	/**
	 * @return list<int>
	 */
	public function dependsOnIds(): array {
		return self::decodeIntList($this->depends_on);
	}

	/**
	 * @param   list<int>  $ids
	 */
	public function setDependsOnIds(array $ids): void {
		$encoded = self::encodeIntList($ids);
		$this->depends_on = $encoded === '[]' ? null : $encoded;
	}

	/**
	 * @return list<string>
	 */
	public function availableKeys(): array {
		return self::decodeStringList($this->available);
	}

	/**
	 * @param   list<string>  $keys
	 */
	public function setAvailableKeys(array $keys): void {
		$encoded = self::encodeStringList($keys);
		$this->available = $encoded === '[]' ? null : $encoded;
	}

	/**
	 * @return list<string>
	 */
	public function notAvailableKeys(): array {
		return self::decodeStringList($this->not_available);
	}

	/**
	 * @param   list<string>  $keys
	 */
	public function setNotAvailableKeys(array $keys): void {
		$encoded = self::encodeStringList($keys);
		$this->not_available = $encoded === '[]' ? null : $encoded;
	}

	/**
	 * Подходит ли запись для раздела сайта (whitelist + blacklist).
	 *
	 * Пустой `available` — все разделы; ключ из `not_available` всегда исключает.
	 */
	public function matchesSection(string $sectionKey): bool {
		$key = strtolower(trim($sectionKey));
		if($key === '') {
			$key = 'main';
		}

		$denied = $this->notAvailableKeys();
		if($denied !== [] && in_array($key, $denied, true)) {
			return false;
		}

		$allowed = $this->availableKeys();
		if($allowed !== [] && !in_array($key, $allowed, true)) {
			return false;
		}

		return true;
	}

	/**
	 * @return list<int>
	 */
	private static function decodeIntList(?string $json): array {
		if($json === null || trim($json) === '') {
			return [];
		}

		$decoded = json_decode($json, true);
		if(!is_array($decoded)) {
			return [];
		}

		$ids = [];

		foreach($decoded as $raw) {
			$id = (int) $raw;
			if($id > 0) {
				$ids[] = $id;
			}
		}

		return array_values(array_unique($ids));
	}

	/**
	 * @return list<string>
	 */
	private static function decodeStringList(?string $json): array {
		if($json === null || trim($json) === '') {
			return [];
		}

		$decoded = json_decode($json, true);
		if(!is_array($decoded)) {
			return [];
		}

		$keys = [];

		foreach($decoded as $raw) {
			$key = strtolower(trim((string) $raw));
			if($key !== '') {
				$keys[] = $key;
			}
		}

		return array_values(array_unique($keys));
	}

	/**
	 * @param   list<int>  $ids
	 */
	private static function encodeIntList(array $ids): string {
		$normalized = [];

		foreach($ids as $raw) {
			$id = (int) $raw;
			if($id > 0) {
				$normalized[] = $id;
			}
		}

		return (string) json_encode(array_values(array_unique($normalized)), JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param   list<string>  $keys
	 */
	private static function encodeStringList(array $keys): string {
		$normalized = [];

		foreach($keys as $raw) {
			$key = strtolower(trim((string) $raw));
			if($key !== '') {
				$normalized[] = $key;
			}
		}

		return (string) json_encode(array_values(array_unique($normalized)), JSON_UNESCAPED_UNICODE);
	}

}
