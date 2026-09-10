<?php
//===============================================================
// Файл: LanguageData.php                                       =
// Путь: devcraft/src/classes/Types/LanguageData.php            =
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

namespace DevCraft\Types;

use DevCraft\Core\Abstracts\AbstractType;

/**
 * Метаданные языка локализации DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string $englishName  Англоязычное название языка.
 * @property string $originalName Название языка на языке оригинала.
 * @property string $iso2         Двухбуквенный ISO-код.
 * @property string $tag          BCP 47-тег локали (например, `ru_RU`).
 */
final class LanguageData extends AbstractType {

	/**
	 * Создаёт описание языка локализации.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $englishName   Англоязычное название языка.
	 * @param   string  $originalName  Название языка на языке оригинала.
	 * @param   string  $iso2          Двухбуквенный ISO-код.
	 * @param   string  $tag           BCP 47-тег локали.
	 *
	 * @example
	 *     $lang = new LanguageData('Russian', 'Русский', 'ru', 'ru_RU');
	 */
	public function __construct(
		public string $englishName,
		public string $originalName,
		public string $iso2,
		public string $tag,
	) {}

	/**
	 * Создаёт описание языка из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные.
	 *
	 * @return static Новый экземпляр описания языка.
	 *
	 * @example
	 *     $lang = LanguageData::fromArray(['englishName' => 'German', 'tag' => 'de_DE']);
	 */
	public static function fromArray(array $data): static {
		return new self(
			(string) ($data['englishName'] ?? ''),
			(string) ($data['originalName'] ?? ''),
			(string) ($data['iso2'] ?? ''),
			(string) ($data['tag'] ?? ''),
		);
	}

}
