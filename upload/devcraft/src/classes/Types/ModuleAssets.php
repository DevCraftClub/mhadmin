<?php
//===============================================================
// Файл: ModuleAssets.php                                       =
// Путь: devcraft/src/classes/Types/ModuleAssets.php            =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Публичные ассеты модуля из секции `assets` manifest.php.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property list<string> $js  Имена JS-файлов относительно Public/.
 * @property list<string> $css Имена CSS-файлов относительно Public/.
 */
final class ModuleAssets extends AbstractType {

	/**
	 * Создаёт описание публичных ассетов модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<string>  $js   Имена JS-файлов.
	 * @param   list<string>  $css  Имена CSS-файлов.
	 *
	 * @example
	 *     $assets = new ModuleAssets(js: ['admin.js']);
	 */
	public function __construct(
		public array $js = [],
		public array $css = [],
	) {}

	/**
	 * Создаёт описание ассетов из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Секция `assets` из manifest.php.
	 *
	 * @return static Новый экземпляр описания ассетов.
	 *
	 * @example
	 *     $assets = ModuleAssets::fromArray($manifest['assets']);
	 */
	public static function fromArray(array $data): static {
		return new self(
			js : self::normalizeList($data['js'] ?? []),
			css: self::normalizeList($data['css'] ?? []),
		);
	}

	/**
	 * Нормализует список имён файлов ассетов.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $items  Исходный список.
	 *
	 * @return list<string> Отфильтрованный список непустых строк.
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

	/**
	 * Преобразует описание ассетов в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array{js: list<string>, css: list<string>} Сериализованные данные.
	 *
	 * @example
	 *     $data = $assets->toArray();
	 */
	public function toArray(): array {
		return [
			'js'  => $this->js,
			'css' => $this->css,
		];
	}

}
