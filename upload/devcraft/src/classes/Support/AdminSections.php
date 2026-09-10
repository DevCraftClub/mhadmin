<?php

declare(strict_types=1);

namespace DevCraft\Core\Support;

/**
 * Работа с строками dle_admin_sections без install.xml.
 */
final class AdminSections {

	/**
	 * Удаляет раздел админки по имени (mod).
	 */
	public static function remove(string $name): void {
		global $db;

		$name = trim($name);

		if($name === '' || !isset($db) || !defined('PREFIX') || !is_object($db)) {
			return;
		}

		$safe = $db->safesql($name);
		$db->query('DELETE FROM ' . PREFIX . "_admin_sections WHERE name = '{$safe}'");
	}

	/**
	 * Есть ли раздел с данным именем.
	 */
	public static function exists(string $name): bool {
		global $db;

		$name = trim($name);

		if($name === '' || !isset($db) || !defined('PREFIX') || !is_object($db)) {
			return false;
		}

		$safe = $db->safesql($name);
		$row  = $db->super_query(
			'SELECT id FROM ' . PREFIX . "_admin_sections WHERE name = '{$safe}' LIMIT 1",
		);

		return !empty($row['id']);
	}

}
