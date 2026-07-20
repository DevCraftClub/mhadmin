<?php
//===============================================================
// Файл: FormFieldBuilder.php                                   =
// Путь: devcraft/src/classes/Form/FormFieldBuilder.php         =
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

namespace DevCraft\Form;

use DevCraft\Types\FormField;
use InvalidArgumentException;
use DevCraft\Types\FormSchema;

/**
 * Fluent-строитель одного поля формы в цепочке FormSchemaBuilder.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Form
 */
final class FormFieldBuilder {

	/**
	 * Дополнительные атрибуты Metro UI для поля.
	 *
	 * @since 200.4.0
	 *
	 * @var array<string, mixed>
	 */
	private array $metro = [];

	/**
	 * Создаёт строитель поля в контексте секции и схемы.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormSectionBuilder     $sectionBuilder  Родительская секция.
	 * @param   FormSchemaBuilder      $schemaBuilder   Корневая схема.
	 * @param   string                 $id              Идентификатор поля.
	 * @param   string                 $type            Тип поля.
	 * @param   string                 $label           Подпись поля.
	 * @param   string|null            $description     Описание поля.
	 * @param   array<string, string>  $options         Варианты для select/multi.
	 * @param   int|null               $filter          Битовая маска фильтра DLE.
	 * @param   mixed                  $default         Значение по умолчанию.
	 * @param   int|null               $columns         Число колонок сетки.
	 *
	 * @example
	 *     $field = $section->text('site_name', 'Название сайта');
	 */
	public function __construct(
		private readonly FormSectionBuilder $sectionBuilder,
		private readonly FormSchemaBuilder  $schemaBuilder,
		private string                      $id,
		private string                      $type,
		private string                      $label,
		private ?string                     $description = NULL,
		private array                       $options = [],
		private ?int                        $filter = NULL,
		private mixed                       $default = NULL,
		private ?int                        $columns = NULL,
	) {}

	/**
	 * Задаёт описание поля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $description  Текст подсказки.
	 *
	 * @return self Текущий строитель для цепочки.
	 *
	 * @example
	 *     $field->description('Отображается в заголовке вкладки');
	 */
	public function description(string $description): self {
		$this->description = $description;

		return $this;
	}

	/**
	 * Задаёт варианты выбора для select или multi.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, string>  $options  Ключ => подпись.
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $field->options(['ru_RU' => 'Русский', 'en_US' => 'English']);
	 */
	public function options(array $options): self {
		$this->options = $options;

		return $this;
	}

	/**
	 * Задаёт битовую маску фильтра DLE для поля.
	 *
	 * @since 200.4.0
	 *
	 * @param   int  $filter  Маска фильтра.
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $field->filter(1);
	 */
	public function filter(int $filter): self {
		$this->filter = $filter;

		return $this;
	}

	/**
	 * Задаёт значение по умолчанию.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $default  Значение по умолчанию.
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $field->default('ru_RU');
	 */
	public function default(mixed $default): self {
		$this->default = $default;

		return $this;
	}

	/**
	 * Задаёт ширину поля в колонках сетки Metro.
	 *
	 * @since 200.4.0
	 *
	 * @param   int  $columns  Число колонок (1–12).
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $field->columns(6);
	 */
	public function columns(int $columns): self {
		$this->columns = $columns;

		return $this;
	}

	/**
	 * Задаёт дополнительные атрибуты Metro UI.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $metro  Пары атрибут => значение.
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $field->metro(['data-role' => 'input']);
	 */
	public function metro(array $metro): self {
		$this->metro = $metro;

		return $this;
	}

	/**
	 * Начинает описание текстового поля в текущей секции.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель text-поля.
	 *
	 * @example
	 *     $field->text('api_key', 'API-ключ')->description('Секретный ключ');
	 */
	public function text(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'text', $label);
	}

	/**
	 * Начинает описание числового поля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель number-поля.
	 *
	 * @example
	 *     $field->number('timeout', 'Таймаут (сек)');
	 */
	public function number(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'number', $label);
	}

	/**
	 * Начинает описание поля select.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель select-поля.
	 *
	 * @example
	 *     $field->select('language', 'Язык')->options(['ru_RU' => 'Русский']);
	 */
	public function select(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'select', $label);
	}

	/**
	 * Начинает описание поля множественного выбора.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель multi-поля.
	 *
	 * @example
	 *     $field->multi('groups', 'Группы');
	 */
	public function multi(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'multi', $label);
	}

	/**
	 * Начинает описание чекбокса.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель checkbox-поля.
	 *
	 * @example
	 *     $field->checkbox('logs', 'Включить логи');
	 */
	public function checkbox(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'checkbox', $label);
	}

	/**
	 * Начинает описание многострочного поля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return self Новый строитель textarea-поля.
	 *
	 * @example
	 *     $field->textarea('footer', 'Текст подвала');
	 */
	public function textarea(string $id, string $label): self {
		return $this->sectionBuilder->beginField($id, 'textarea', $label);
	}

	/**
	 * Начинает описание скрытого поля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись (может быть пустой).
	 *
	 * @return self Новый строитель hidden-поля.
	 *
	 * @example
	 *     $field->hidden('mod', 'devcraft');
	 */
	public function hidden(string $id, string $label = ''): self {
		return $this->sectionBuilder->beginField($id, 'hidden', $label);
	}

	/**
	 * Фиксирует текущее поле и открывает новую секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $title  Заголовок новой секции.
	 *
	 * @return FormSectionBuilder Строитель новой секции.
	 *
	 * @example
	 *     $section = $field->section('Дополнительно');
	 */
	public function section(string $title): FormSectionBuilder {
		$this->sectionBuilder->commitField($this->toField());
		$this->schemaBuilder->commitSection($this->sectionBuilder);

		return $this->schemaBuilder->section($title);
	}

	/**
	 * Фиксирует поле и возвращает готовую схему формы.
	 *
	 * @since 200.4.0
	 *
	 * @return \DevCraft\Types\FormSchema Собранная схема.
	 *
	 * @example
	 *     $schema = $field->default(true)->build();
	 */
	public function build(): FormSchema {
		$this->sectionBuilder->commitField($this->toField());
		$this->schemaBuilder->commitSection($this->sectionBuilder);

		return $this->schemaBuilder->build();
	}

	/**
	 * Преобразует накопленные параметры в объект FormField.
	 *
	 * @since 200.4.0
	 *
	 * @return FormField Immutable-описание поля.
	 *
	 * @example
	 *     $formField = $builder->toField();
	 */
	public function toField(): FormField {
		if($this->id === '') {
			throw new InvalidArgumentException(__('Идентификатор поля не может быть пустым'));
		}

		return new FormField(
			id         : $this->id,
			type       : $this->type,
			label      : $this->label,
			description: $this->description,
			options    : $this->options,
			filter     : $this->filter,
			default    : $this->default,
			columns    : $this->columns,
			metro      : $this->metro,
		);
	}

}
