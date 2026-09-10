<?php

declare(strict_types=1);

namespace DevCraft\Core\Support;

/**
 * Публичные ассеты DevCraft (общие для сателлитов на сайте).
 */
final class PublicAssets {

	/**
	 * Подключает dc_public.js один раз за запрос и пропускает, если скрипт уже есть на странице.
	 */
	public static function emitDcPublicScript(string $src): void {
		if($src === '') {
			return;
		}

		if(!empty($GLOBALS['devcraft_dc_public_js_emitted'])) {
			return;
		}

		$GLOBALS['devcraft_dc_public_js_emitted'] = true;

		$jsonSrc = json_encode($src, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		if($jsonSrc === false) {
			return;
		}

		echo '<script>(function(s){if(window.DevCraftPublic)return;'
			. 'if(document.querySelector(\'script[src*="dc_public.js"]\'))return;'
			. 'document.write(\'<script src="\'+s+\'"><\\/script>\');'
			. '})(' . $jsonSrc . ');</script>' . "\n";
	}

}
