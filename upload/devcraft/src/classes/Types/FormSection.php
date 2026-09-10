<?php
//===============================================================
// Файл: FormSection.php                                        =
// Путь: devcraft/src/classes/Types/FormSection.php             =
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
 * Секция декларативной формы с заголовком и набором полей.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string      $title  Заголовок секции.
 * @property FormField[] $fields Список полей секции.
 */
final class FormSection extends AbstractType {

	/**
	 * Создаёт секцию формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $title   Заголовок секции.
	 * @param   FormField[]  $fields  Список полей секции.
	 *
	 * @example
	 *     $section = new FormSection(__('Общие'), [$fieldA, $fieldB]);
	 */
	public function __construct(
		public string $title,
		public array  $fields,
	) {}

	/**
	 * Создаёт секцию формы из ассоциативного массива схемы.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Элемент схемы секции.
	 *
	 * @return static Новый экземпляр секции.
	 *
	 * @example
	 *     $section = FormSection::fromArray(['title' => 'General', 'fields' => []]);
	 */
	public static function fromArray(array $data): static {
		$fields = [];

		if(isset($data['fields']) && is_array($data['fields'])) {
			foreach($data['fields'] as $field) {
				if(is_array($field)) {
					$fields[] = FormField::fromArray($field);
				}
			}
		}

		return new self(
			title : (string) ($data['title'] ?? ''),
			fields: $fields,
		);
	}

}
