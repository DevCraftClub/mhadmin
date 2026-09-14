<?php

declare(strict_types=1);

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Публичные ассеты оболочки сайта из секции `siteAssets` манифеста.
 *
 * @property list<string>                         $js
 * @property list<string>                         $css
 * @property list<array{name: string, content: string}> $meta
 */
final class ModuleSiteAssets extends AbstractType {

	/**
	 * @param   list<string>                          $js
	 * @param   list<string>                          $css
	 * @param   list<array{name: string, content: string}> $meta
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
			js  : self::normalizeList($data['js'] ?? []),
			css : self::normalizeList($data['css'] ?? []),
			meta: $meta,
		);
	}

	public function isEmpty(): bool {
		return $this->js === [] && $this->css === [] && $this->meta === [];
	}

	/**
	 * @param   mixed  $items
	 *
	 * @return list<string>
	 */
	private static function normalizeList(mixed $items): array {
		if(!is_array($items)) {
			return [];
		}

		$result = [];

		foreach($items as $item) {
			if(is_string($item) && $item !== '') {
				$result[] = $item;
			}
		}

		return $result;
	}

}
