<?php

declare(strict_types=1);

namespace DevCraft\Core\Config;

use DevCraft\Core\Support\DataManager;

/**
 * Чтение и запись bag `third_party[{mod}]` в конфиге хост-панели Admin.
 *
 * @package    DevCraft
 * @since      200.4.1
 * @subpackage Core.Config
 */
final class ThirdPartyHostSettings {

	/** Codename JSON настроек Admin (`devcraft.json`). */
	public const HOST_CODENAME = 'devcraft';

	/** Корневой ключ сторонних модулей в конфиге хоста. */
	public const BAG_KEY = 'third_party';

	/**
	 * Возвращает bag настроек модуля (пустой массив, если нет).
	 *
	 * @return array<string, mixed>
	 *
	 * @since 200.4.1
	 */
	public static function get(string $mod): array {
		$mod = trim($mod);

		if($mod === '') {
			return [];
		}

		$host = DataManager::getConfig(self::HOST_CODENAME);
		$root = $host[self::BAG_KEY] ?? [];

		if(!is_array($root)) {
			return [];
		}

		$bag = $root[$mod] ?? [];

		return is_array($bag)? $bag : [];
	}

	/**
	 * Возвращает одно значение из bag модуля.
	 *
	 * @since 200.4.1
	 */
	public static function getValue(string $mod, string $key, mixed $default = NULL): mixed {
		$bag = self::get($mod);

		return array_key_exists($key, $bag)? $bag[$key] : $default;
	}

	/**
	 * Сливает поля в `third_party[{mod}]` без затрагивания чужих ключей хоста.
	 *
	 * @param   array<string, mixed>  $values
	 *
	 * @since 200.4.1
	 */
	public static function merge(string $mod, array $values): bool {
		$mod = trim($mod);

		if($mod === '') {
			return false;
		}

		$host = DataManager::getConfig(self::HOST_CODENAME);
		$root = $host[self::BAG_KEY] ?? [];

		if(!is_array($root)) {
			$root = [];
		}

		$existing = $root[$mod] ?? [];

		if(!is_array($existing)) {
			$existing = [];
		}

		$root[$mod]            = array_merge($existing, $values);
		$host[self::BAG_KEY]   = $root;

		if(!DataManager::saveConfig(self::HOST_CODENAME, $host)) {
			return false;
		}

		DevCraftConfig::resetCache();

		return true;
	}

}
