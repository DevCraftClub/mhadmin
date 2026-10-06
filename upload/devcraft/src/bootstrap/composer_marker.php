<?php

declare(strict_types=1);

/**
 * Признак, что Composer в корне DevCraft уже отрабатывал.
 *
 * Достаточно `composer.lock` или файла `.composer_initialized` с датой и временем.
 */

if(!function_exists('dc_composer_root')) {
	function dc_composer_root(): string {
		if(defined('ROOT_DIR')) {
			return rtrim((string) ROOT_DIR, '/\\') . '/devcraft';
		}

		return dirname(__DIR__, 2);
	}

	function dc_composer_lock_path(): string {
		return dc_composer_root() . '/composer.lock';
	}

	function dc_composer_stamp_path(): string {
		return dc_composer_root() . '/.composer_initialized';
	}

	function dc_composer_initialized(): bool {
		return is_file(dc_composer_lock_path()) || is_file(dc_composer_stamp_path());
	}

	/**
	 * Пишет дату и время. Повторно — только если $refresh.
	 */
	function dc_composer_mark_initialized(bool $refresh = false): void {
		$path = dc_composer_stamp_path();

		if(!$refresh && is_file($path)) {
			return;
		}

		file_put_contents($path, date('Y-m-d H:i:s') . "\n");
	}

	/**
	 * Если пакеты уже ставились (`composer.lock`), а метки ещё нет — записать её один раз.
	 */
	function dc_composer_ensure_stamp(): void {
		if(!is_file(dc_composer_lock_path()) && !is_file(dc_composer_stamp_path())) {
			return;
		}

		dc_composer_mark_initialized(false);
	}
}
