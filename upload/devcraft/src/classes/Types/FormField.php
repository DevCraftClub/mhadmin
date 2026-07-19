<?php
//===============================================================
// Файл: FormField.php                                          =
// Путь: devcraft/src/classes/Types/FormField.php               =
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
 * Описание одного поля декларативной формы DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                $id          Уникальный идентификатор поля.
 * @property string                $type        Тип виджета (`text`, `select` и т. д.).
 * @property string                $label       Подпись поля.
 * @property string|null           $description Пояснение под полем.
 * @property array<string, string> $options     Варианты для select/radio.
 * @property int|null              $filter      PHP-фильтр для санитизации значения.
 * @property mixed                 $default     Значение по умолчанию.
 * @property int|null              $columns     Ширина колонки в сетке Metro UI.
 * @property array<string, mixed>  $metro       Дополнительные атрибуты Metro UI и БД.
 */
final class FormField extends AbstractType {

	/**
	 * Создаёт описание поля формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                 $id           Уникальный идентификатор поля.
	 * @param   string                 $type         Тип виджета.
	 * @param   string                 $label        Подпись поля.
	 * @param   string|null            $description  Пояснение под полем.
	 * @param   array<string, string>  $options      Варианты для select/radio.
	 * @param   int|null               $filter       PHP-фильтр для санитизации.
	 * @param   mixed                  $default      Значение по умолчанию.
	 * @param   int|null               $columns      Ширина колонки в сетке.
	 * @param   array<string, mixed>   $metro        Атрибуты Metro UI и БД.
	 *
	 * @example
	 *     $field = new FormField('site_name', 'text', __('Название сайта'));
	 */
	public function __construct(
		public string  $id,
		public string  $type,
		public string  $label,
		public ?string $description = NULL,
		public array   $options = [],
		public ?int    $filter = NULL,
		public mixed   $default = NULL,
		public ?int    $columns = NULL,
		public array   $metro = [],
	) {}

	/**
	 * Создаёт описание поля из ассоциативного массива схемы.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Элемент схемы поля.
	 *
	 * @return static Новый экземпляр описания поля.
	 *
	 * @example
	 *     $field = FormField::fromArray(['id' => 'email', 'type' => 'email', 'label' => 'E-mail']);
	 */
	public static function fromArray(array $data): static {
		/** @var array<string, string> $options */
		$options = [];

		if(isset($data['options']) && is_array($data['options'])) {
			foreach($data['options'] as $key => $label) {
				$options[(string) $key] = (string) $label;
			}
		}

		/** @var array<string, mixed> $metro */
		$metro = isset($data['metro']) && is_array($data['metro'])? $data['metro'] : [];

		return new self(
			id         : (string) ($data['id'] ?? ''),
			type       : (string) ($data['type'] ?? 'text'),
			label      : (string) ($data['label'] ?? ''),
			description: isset($data['description'])? (string) $data['description'] : NULL,
			options    : $options,
			filter     : isset($data['filter'])? (int) $data['filter'] : NULL,
			default    : $data['default'] ?? NULL,
			columns    : isset($data['columns'])? (int) $data['columns'] : NULL,
			metro      : $metro,
		);
	}

	/**
	 * Преобразует описание поля в ассоциативный массив для шаблонов.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Сериализованное описание поля.
	 *
	 * @example
	 *     $data = $field->toArray();
	 */
	public function toArray(): array {
		$data = [
			'id'    => $this->id,
			'type'  => $this->type,
			'label' => $this->label,
		];

		if($this->description !== NULL) {
			$data['description'] = $this->description;
		}

		if($this->options !== []) {
			$data['options'] = $this->options;
		}

		if($this->filter !== NULL) {
			$data['filter'] = $this->filter;
		}

		if($this->default !== NULL) {
			$data['default'] = $this->default;
		}

		if($this->columns !== NULL) {
			$data['columns'] = $this->columns;
		}

		if($this->metro !== []) {
			$data['metro'] = $this->metro;
		}

		return $data;
	}

}
