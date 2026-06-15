<?php
//===============================================================
// Файл: FormSchema.php                                         =
// Путь: devcraft/src/classes/Types/FormSchema.php              =
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

use DevCraft\Core\Enums\FormLayout;
use DevCraft\Core\Abstracts\AbstractType;

/**
 * Полная декларативная схема формы настроек или фильтра.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string        $codename Уникальный код формы.
 * @property FormSection[] $sections Секции формы.
 * @property string        $layout   Режим компоновки (`stack`, `tabs`, `accordion`).
 */
final class FormSchema extends AbstractType {

	/**
	 * Создаёт схему формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string         $codename  Уникальный код формы.
	 * @param   FormSection[]  $sections  Секции формы.
	 * @param   FormLayout     $layout    Режим компоновки секций.
	 *
	 * @example
	 *     $schema = new FormSchema('settings', [$generalSection], layout: FormLayout::TABS);
	 */
	public function __construct(
		public string $codename,
		public array  $sections,
		public FormLayout $layout = FormLayout::STACK,
	) {}

	/**
	 * Создаёт схему формы из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходная схема формы.
	 *
	 * @return static Новый экземпляр схемы.
	 *
	 * @example
	 *     $schema = FormSchema::fromArray(require 'settings.schema.php');
	 */
	public static function fromArray(array $data): static {
		$sections = [];

		if(isset($data['sections']) && is_array($data['sections'])) {
			foreach($data['sections'] as $section) {
				if(is_array($section)) {
					$sections[] = FormSection::fromArray($section);
				}
			}
		}

		$layout = ($data['layout'] ?? FormLayout::STACK);

		if(!in_array($layout, FormLayout::cases(), true)) {
			$layout = FormLayout::STACK;
		}

		return new self(
			codename: (string) ($data['codename'] ?? ''),
			sections: $sections,
			layout  : $layout,
		);
	}

	/**
	 * Преобразует схему формы в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Сериализованная схема.
	 *
	 * @example
	 *     $data = $schema->toArray();
	 */
	public function toArray(): array {
		return [
			'codename' => $this->codename,
			'layout'   => $this->layout,
			'sections' => array_map(
				static fn(FormSection $section): array => $section->toArray(),
				$this->sections,
			),
		];
	}

	/**
	 * Возвращает плоский список всех полей схемы.
	 *
	 * @since 200.4.0
	 *
	 * @return FormField[] Все поля всех секций в порядке обхода.
	 *
	 * @example
	 *     foreach ($schema->allFields() as $field) {
	 *         echo $field->id;
	 *     }
	 */
	public function allFields(): array {
		$fields = [];

		foreach($this->sections as $section) {
			foreach($section->fields as $field) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

}
