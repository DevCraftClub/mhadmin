<?php
//===============================================================
// Файл: ModuleGeneratorInput.php                               =
// Путь: devcraft/src/modules/Admin/Services/ModuleGeneratorInp…=
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

/**
 * DTO входных данных формы генератора DevCraft-модулей.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Modules.Admin
 */
final class ModuleGeneratorInput {

	/**
	 * Создаёт объект входных данных генератора модулей.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $name         Отображаемое имя модуля.
	 * @param   string  $translit     Латинский код модуля (пустой — транслитерация из имени).
	 * @param   string  $description  Краткое описание модуля.
	 * @param   string  $version      Версия модуля.
	 * @param   string  $icon         Иконка Metro UI для меню.
	 * @param   string  $pluginIcon   Путь к иконке плагина DLE.
	 * @param   string  $link         Ссылка на страницу плагина.
	 * @param   string  $docs         Ссылка на документацию.
	 * @param   bool    $db           Регистрировать модуль как плагин DLE.
	 * @param   bool    $override     Перезаписывать существующие файлы.
	 *
	 * @example
	 *     $input = new ModuleGeneratorInput('Мой модуль', 'mymodule', 'Описание', '200.4.0', '', '', '', '', true, false);
	 */
	public function __construct(
		public readonly string $name,
		public readonly string $translit,
		public readonly string $description,
		public readonly string $version,
		public readonly string $icon,
		public readonly string $pluginIcon,
		public readonly string $link,
		public readonly string $docs,
		public readonly bool   $db,
		public readonly bool   $override,
	) {}

	/**
	 * Создаёт DTO из массива данных AJAX-запроса или формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив полей формы.
	 *
	 * @return self Нормализованный объект входных данных.
	 *
	 * @example
	 *     $input = ModuleGeneratorInput::fromArray($request->data);
	 */
	public static function fromArray(array $data): self {
		return new self(
			name       : trim((string) ($data['name'] ?? '')),
			translit   : trim((string) ($data['translit'] ?? '')),
			description: trim((string) ($data['description'] ?? '')),
			version    : trim((string) ($data['version'] ?? '')),
			icon       : trim((string) ($data['icon'] ?? '')),
			pluginIcon : trim((string) ($data['plugin_icon'] ?? '')),
			link       : trim((string) ($data['link'] ?? '')),
			docs       : trim((string) ($data['docs'] ?? '')),
			db         : self::toBool($data['db'] ?? false),
			override   : self::toBool($data['override'] ?? false),
		);
	}

	/**
	 * Приводит произвольное значение к логическому типу PHP.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $value  Значение из запроса (`bool`, `int`, строка).
	 *
	 * @return bool Результат приведения.
	 */
	private static function toBool(mixed $value): bool {
		if(is_bool($value)) {
			return $value;
		}

		if(is_int($value)) {
			return $value === 1;
		}

		$normalized = strtolower(trim((string) $value));

		return in_array($normalized, ['1', 'true', 'on', 'yes'], true);
	}

}
