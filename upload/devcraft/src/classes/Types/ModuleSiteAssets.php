<?php

declare(strict_types=1);

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Публичные ассеты оболочки сайта из секции `siteAssets` манифеста.
 *
 * @property list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}> $js
 * @property list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}> $css
 * @property list<array{name: string, content: string}>                                                                            $meta
 */
final class ModuleSiteAssets extends AbstractType {

	/**
	 * @param   list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}>  $js
	 * @param   list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}>  $css
	 * @param   list<array{name: string, content: string}>                                                                             $meta
	 */
	public function __construct(
		public array $js = [],
		public array $css = [],
		public array $meta = [],
	) {}

	/**
	 * @param   array<string, mixed>  $data
	 */
	public static function fromArray(array $data): static {
		$meta = [];

		foreach(($data['meta'] ?? []) as $item) {
			if(!is_array($item)) {
				continue;
			}

			$name    = trim((string) ($item['name'] ?? ''));
			$content = (string) ($item['content'] ?? '');

			if($name === '' || $content === '') {
				continue;
			}

			$meta[] = ['name' => $name, 'content' => $content];
		}

		return new self(
			js  : self::normalizeFileList($data['js'] ?? []),
			css : self::normalizeFileList($data['css'] ?? []),
			meta: $meta,
		);
	}

	public function isEmpty(): bool {
		return $this->js === [] && $this->css === [] && $this->meta === [];
	}

	/**
	 * @param   mixed  $item
	 *
	 * @return array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}|null
	 */
	public static function normalizeFile(mixed $item): ?array {
		if(is_string($item)) {
			$file = trim($item);

			return $file === '' ? null : [
				'file'         => $file,
				'dependsOn'    => [],
				'available'    => [],
				'notAvailable' => [],
				'active'       => true,
			];
		}

		if(!is_array($item)) {
			return null;
		}

		$file = trim((string) ($item['file'] ?? $item['path'] ?? ''));
		if($file === '') {
			return null;
		}

		$active = $item['active'] ?? true;

		return [
			'file'         => $file,
			'dependsOn'    => self::normalizePathList($item['dependsOn'] ?? $item['depends_on'] ?? []),
			'available'    => self::normalizeStringList($item['available'] ?? []),
			'notAvailable' => self::normalizeStringList($item['notAvailable'] ?? $item['not_available'] ?? []),
			'active'       => filter_var($active, FILTER_VALIDATE_BOOLEAN),
		];
	}

	/**
	 * @param   mixed  $items
	 *
	 * @return list<array{file: string, dependsOn: list<string>, available: list<string>, notAvailable: list<string>, active: bool}>
	 */
	private static function normalizeFileList(mixed $items): array {
		if(!is_array($items)) {
			return [];
		}

		$result = [];

		foreach($items as $item) {
			$normalized = self::normalizeFile($item);
			if($normalized !== null) {
				$result[] = $normalized;
			}
		}

		return $result;
	}

	/**
	 * @return list<string>
	 */
	private static function normalizePathList(mixed $raw): array {
		if(is_string($raw)) {
			$raw = [$raw];
		}

		if(!is_array($raw)) {
			return [];
		}

		$paths = [];

		foreach($raw as $item) {
			if(!is_scalar($item)) {
				continue;
			}

			$path = trim((string) $item);
			if($path !== '' && !str_contains($path, '..')) {
				$paths[] = $path;
			}
		}

		return array_values(array_unique($paths));
	}

	/**
	 * @return list<string>
	 */
	private static function normalizeStringList(mixed $raw): array {
		if(is_string($raw)) {
			$raw = [$raw];
		}

		if(!is_array($raw)) {
			return [];
		}

		$keys = [];

		foreach($raw as $item) {
			if(!is_scalar($item)) {
				continue;
			}

			$key = strtolower(trim((string) $item));
			if($key !== '') {
				$keys[] = $key;
			}
		}

		return array_values(array_unique($keys));
	}

}
