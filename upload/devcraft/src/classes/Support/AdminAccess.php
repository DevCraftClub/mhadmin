<?php

declare(strict_types=1);

namespace DevCraft\Core\Support;

/**
 * Серверная проверка allow_groups из dle_admin_sections (F03).
 *
 * Одна глобальная Freigabe на раздел — без отдельной rights-матрицы.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Support
 */
final class AdminAccess {

	/**
	 * Имя секции DevCraft Admin в admin_sections.
	 */
	public const SECTION_DEVCRAFT = 'devcraft';

	/**
	 * @var array<string, string|null>
	 */
	private static array $allowGroupsCache = [];

	/**
	 * Доступ к панели DevCraft (секция name=devcraft).
	 *
	 * @since 200.4.0
	 */
	public static function allowsDevCraftAdmin(): bool {
		return self::allowsSection(self::SECTION_DEVCRAFT);
	}

	/**
	 * Доступ к разделу admin_sections по имени (группа 1 всегда допускается).
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $sectionName  Значение столбца name.
	 */
	public static function allowsSection(string $sectionName): bool {
		global $member_id;

		$gid = (int) ($member_id['user_group'] ?? 0);

		if($gid === 1) {
			return true;
		}

		if($gid <= 0 || $sectionName === '') {
			return false;
		}

		$raw = self::allowGroupsRaw($sectionName);

		if($raw === NULL) {
			return false;
		}

		$groups = preg_split('/\s*,\s*/', $raw) ?: [];

		foreach($groups as $g) {
			if((int) $g === $gid) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Для AJAX: секция = mod, при отсутствии строки — fallback на devcraft.
	 *
	 * @since 200.4.0
	 */
	public static function allowsAjaxMod(string $mod): bool {
		$mod = trim($mod);

		if($mod === '' || $mod === self::SECTION_DEVCRAFT) {
			return self::allowsDevCraftAdmin();
		}

		if(self::allowGroupsRaw($mod) !== NULL) {
			return self::allowsSection($mod);
		}

		return self::allowsDevCraftAdmin();
	}

	/**
	 * Сырое allow_groups из БД или null, если секции нет.
	 *
	 * @since 200.4.0
	 */
	private static function allowGroupsRaw(string $sectionName): ?string {
		if(array_key_exists($sectionName, self::$allowGroupsCache)) {
			return self::$allowGroupsCache[$sectionName];
		}

		global $db;

		if(!isset($db) || !is_object($db) || !defined('PREFIX')) {
			self::$allowGroupsCache[$sectionName] = NULL;

			return NULL;
		}

		$name = $db->safesql($sectionName);
		$row  = $db->super_query(
			'SELECT allow_groups FROM ' . PREFIX . "_admin_sections WHERE name = '{$name}' LIMIT 1",
		);

		if(!is_array($row) || !isset($row['allow_groups'])) {
			self::$allowGroupsCache[$sectionName] = NULL;

			return NULL;
		}

		self::$allowGroupsCache[$sectionName] = (string) $row['allow_groups'];

		return self::$allowGroupsCache[$sectionName];
	}

}
