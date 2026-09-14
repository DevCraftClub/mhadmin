<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Config\Paths;

/**
 * Лёгкая минификация CSS/JS без внешних зависимостей.
 */
final class PublicAssetMinify {

	public function css(string $css): string {
		$css = preg_replace('#/\*.*?\*/#s', '', $css) ?? $css;
		$css = preg_replace('/\s+/', ' ', $css) ?? $css;

		return trim(str_replace([' {', '{ ', ' }', '; '], ['{', '{', '}', ';'], $css));
	}

	public function js(string $js): string {
		// ponytail: потоковая минификация без AST; потолок — не трогаем строки с // внутри
		$out   = [];
		$lines = preg_split("/\r\n|\n|\r/", $js) ?: [];

		foreach($lines as $line) {
			$trim = trim($line);
			if($trim === '' || str_starts_with($trim, '//')) {
				continue;
			}

			$out[] = $trim;
		}

		return implode("\n", $out);
	}

}
