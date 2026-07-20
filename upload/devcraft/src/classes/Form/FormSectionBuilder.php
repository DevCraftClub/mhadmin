<?php
//===============================================================
// Файл: FormSectionBuilder.php                                 =
// Путь: devcraft/src/classes/Form/FormSectionBuilder.php       =
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
use DevCraft\Types\FormSchema;
use DevCraft\Types\FormSection;

/**
 * Fluent-строитель секции формы с набором полей.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Form
 */
final class FormSectionBuilder {

	/**
	 * Зафиксированные поля секции.
	 *
	 * @since 200.4.0
	 *
	 * @var FormField[]
	 */
	private array $fields = [];

	/**
	 * Поле, находящееся в процессе конфигурации.
	 *
	 * @since 200.4.0
	 *
	 * @var FormFieldBuilder|null
	 */
	private ?FormFieldBuilder $pending = NULL;

	/**
	 * Создаёт строитель секции.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormSchemaBuilder  $schemaBuilder  Родительская схема.
	 * @param   string             $title          Заголовок секции.
	 *
	 * @example
	 *     $section = new FormSectionBuilder($builder, 'Основные');
	 */
	public function __construct(
		private readonly FormSchemaBuilder $schemaBuilder,
		private readonly string            $title,
	) {}

	/**
	 * Добавляет текстовое поле в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->text('site_url', 'URL сайта');
	 */
	public function text(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'text', $label);
	}

	/**
	 * Добавляет числовое поле в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->number('cache_ttl', 'TTL кэша');
	 */
	public function number(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'number', $label);
	}

	/**
	 * Добавляет поле select в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->select('theme', 'Тема');
	 */
	public function select(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'select', $label);
	}

	/**
	 * Добавляет поле множественного выбора в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->multi('roles', 'Роли');
	 */
	public function multi(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'multi', $label);
	}

	/**
	 * Добавляет чекбокс в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->checkbox('debug', 'Режим отладки');
	 */
	public function checkbox(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'checkbox', $label);
	}

	/**
	 * Добавляет textarea в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->textarea('notice', 'Уведомление');
	 */
	public function textarea(string $id, string $label): FormFieldBuilder {
		return $this->beginField($id, 'textarea', $label);
	}

	/**
	 * Добавляет скрытое поле в секцию.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $label  Подпись (опционально).
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $section->hidden('action', 'save');
	 */
	public function hidden(string $id, string $label = ''): FormFieldBuilder {
		return $this->beginField($id, 'hidden', $label);
	}

	/**
	 * Фиксирует секцию и открывает следующую в схеме.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $title  Заголовок новой секции.
	 *
	 * @return FormSectionBuilder Строитель новой секции.
	 *
	 * @example
	 *     $next = $section->section('Расширенные');
	 */
	public function section(string $title): FormSectionBuilder {
		$this->commitPending();
		$this->schemaBuilder->commitSection($this);

		return $this->schemaBuilder->section($title);
	}

	/**
	 * Фиксирует секцию и возвращает готовую схему формы.
	 *
	 * @since 200.4.0
	 *
	 * @return \DevCraft\Types\FormSchema Собранная схема.
	 *
	 * @example
	 *     $schema = $section->text('name', 'Имя')->build();
	 */
	public function build(): FormSchema {
		$this->commitPending();
		$this->schemaBuilder->commitSection($this);

		return $this->schemaBuilder->build();
	}

	/**
	 * Начинает конфигурацию поля указанного типа.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $id     Идентификатор поля.
	 * @param   string  $type   Тип поля.
	 * @param   string  $label  Подпись.
	 *
	 * @return FormFieldBuilder Строитель поля.
	 *
	 * @example
	 *     $field = $section->beginField('token', 'text', 'Токен');
	 */
	public function beginField(string $id, string $type, string $label): FormFieldBuilder {
		$this->commitPending();

		$this->pending = new FormFieldBuilder(
			sectionBuilder: $this,
			schemaBuilder : $this->schemaBuilder,
			id            : $id,
			type          : $type,
			label         : $label,
		);

		return $this->pending;
	}

	/**
	 * Добавляет готовое поле в секцию с проверкой уникальности id.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormField  $field  Описание поля.
	 */
	public function commitField(FormField $field): void {
		foreach($this->fields as $existing) {
			if($existing->id === $field->id) {
				throw new \InvalidArgumentException(__('Поле с идентификатором «{id}» уже определено', ['{id}' => $field->id]));
			}
		}

		$this->fields[] = $field;
		$this->pending  = NULL;
	}

	/**
	 * Преобразует накопленные поля в объект FormSection.
	 *
	 * @since 200.4.0
	 *
	 * @return FormSection Immutable-секция.
	 *
	 * @example
	 *     $formSection = $sectionBuilder->toSection();
	 */
	public function toSection(): FormSection {
		$this->commitPending();

		return new FormSection(
			title : $this->title,
			fields: $this->fields,
		);
	}

	/**
	 * Фиксирует незавершённое поле pending, если оно есть.
	 *
	 * @since 200.4.0
	 */
	private function commitPending(): void {
		if($this->pending !== NULL) {
			$this->commitField($this->pending->toField());
		}
	}

}
