<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Abstracts\AbstractEntity;
use DevCraft\Core\Cache\CacheControl;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use ReflectionProperty;

/**
 * Кэш списков публичных CSS/JS/meta через CacheControl (TTL = cache_timer).
 *
 * Сущности в pool не кладём — только массивы полей; на чтении собираем объекты без SQL.
 */
final class PublicAssetCatalogCache {

	public const string TYPE = 'public_assets';

	/**
	 * @param   callable(): list<PublicAssetEntry>  $loader
	 *
	 * @return list<PublicAssetEntry>
	 */
	public static function assets(string $kind, callable $loader): array {
		$kind = $kind === 'css' ? 'css' : 'js';
		$name = 'list_' . $kind;
		$hit  = CacheControl::getCache(self::TYPE, $name);

		if(is_array($hit)) {
			/** @var list<PublicAssetEntry> */
			return array_values(array_filter(
				array_map(self::hydrateAsset(...), $hit),
				static fn(mixed $row): bool => $row instanceof PublicAssetEntry,
			));
		}

		$entries = $loader();
		$rows    = [];

		foreach($entries as $entry) {
			if($entry instanceof PublicAssetEntry) {
				$rows[] = self::exportAsset($entry);
			}
		}

		CacheControl::setCache(self::TYPE, $name, $rows);

		return $entries;
	}

	/**
	 * @param   callable(): list<PublicHeaderEntry>  $loader
	 *
	 * @return list<PublicHeaderEntry>
	 */
	public static function headers(callable $loader): array {
		$hit = CacheControl::getCache(self::TYPE, 'list_meta');

		if(is_array($hit)) {
			/** @var list<PublicHeaderEntry> */
			return array_values(array_filter(
				array_map(self::hydrateHeader(...), $hit),
				static fn(mixed $row): bool => $row instanceof PublicHeaderEntry,
			));
		}

		$entries = $loader();
		$rows    = [];

		foreach($entries as $entry) {
			if($entry instanceof PublicHeaderEntry) {
				$rows[] = self::exportHeader($entry);
			}
		}

		CacheControl::setCache(self::TYPE, 'list_meta', $rows);

		return $entries;
	}

	/**
	 * Сброс списков в pool (после правки в админке / sync манифестов).
	 */
	public static function invalidate(): void {
		CacheControl::clearCache(self::TYPE);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function exportAsset(PublicAssetEntry $entry): array {
		return [
			'id'            => $entry->id(),
			'kind'          => $entry->kind,
			'origin'        => $entry->origin,
			'module_code'   => $entry->module_code,
			'label'         => $entry->label,
			'source_path'   => $entry->source_path,
			'source_url'    => $entry->source_url,
			'local_path'    => $entry->local_path,
			'active'        => $entry->active,
			'sort_order'    => $entry->sort_order,
			'depends_on'    => $entry->depends_on,
			'available'     => $entry->available,
			'not_available' => $entry->not_available,
		];
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function exportHeader(PublicHeaderEntry $entry): array {
		return [
			'id'            => $entry->id(),
			'origin'        => $entry->origin,
			'module_code'   => $entry->module_code,
			'name'          => $entry->name,
			'content'       => $entry->content,
			'active'        => $entry->active,
			'sort_order'    => $entry->sort_order,
			'depends_on'    => $entry->depends_on,
			'available'     => $entry->available,
			'not_available' => $entry->not_available,
		];
	}

	/**
	 * @param   array<string, mixed>  $row
	 */
	private static function hydrateAsset(mixed $row): ?PublicAssetEntry {
		if(!is_array($row)) {
			return null;
		}

		$entry              = new PublicAssetEntry();
		$entry->kind        = (string) ($row['kind'] ?? 'js');
		$entry->origin      = (string) ($row['origin'] ?? 'manual');
		$entry->module_code = isset($row['module_code']) ? (string) $row['module_code'] : null;
		$entry->label       = isset($row['label']) ? (string) $row['label'] : null;
		$entry->source_path = isset($row['source_path']) ? (string) $row['source_path'] : null;
		$entry->source_url  = isset($row['source_url']) ? (string) $row['source_url'] : null;
		$entry->local_path  = (string) ($row['local_path'] ?? '');
		$entry->active      = (bool) ($row['active'] ?? true);
		$entry->sort_order  = (int) ($row['sort_order'] ?? 0);
		$entry->depends_on  = isset($row['depends_on']) ? (string) $row['depends_on'] : null;
		$entry->available   = isset($row['available']) ? (string) $row['available'] : null;
		$entry->not_available = isset($row['not_available']) ? (string) $row['not_available'] : null;
		self::assignId($entry, (int) ($row['id'] ?? 0));

		return $entry;
	}

	/**
	 * @param   array<string, mixed>  $row
	 */
	private static function hydrateHeader(mixed $row): ?PublicHeaderEntry {
		if(!is_array($row)) {
			return null;
		}

		$entry              = new PublicHeaderEntry();
		$entry->origin      = (string) ($row['origin'] ?? 'manual');
		$entry->module_code = isset($row['module_code']) ? (string) $row['module_code'] : null;
		$entry->name        = (string) ($row['name'] ?? '');
		$entry->content     = (string) ($row['content'] ?? '');
		$entry->active      = (bool) ($row['active'] ?? true);
		$entry->sort_order  = (int) ($row['sort_order'] ?? 0);
		$entry->depends_on  = isset($row['depends_on']) ? (string) $row['depends_on'] : null;
		$entry->available   = isset($row['available']) ? (string) $row['available'] : null;
		$entry->not_available = isset($row['not_available']) ? (string) $row['not_available'] : null;
		self::assignId($entry, (int) ($row['id'] ?? 0));

		return $entry;
	}

	private static function assignId(AbstractEntity $entity, int $id): void {
		if($id <= 0) {
			return;
		}

		$ref = new ReflectionProperty(AbstractEntity::class, 'id');
		$ref->setValue($entity, $id);
	}

}
