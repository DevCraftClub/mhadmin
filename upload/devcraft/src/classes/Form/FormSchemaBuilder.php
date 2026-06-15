<?php
//===============================================================
// Файл: FormSchemaBuilder.php                                  =
// Путь: devcraft/src/classes/Form/FormSchemaBuilder.php        =
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

use InvalidArgumentException;
use DevCraft\Types\FormSchema;
use DevCraft\Types\FormSection;
use DevCraft\Core\Enums\FormLayout;

/**
 * Fluent-строитель декларативной схемы формы настроек или фильтра.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Form
 */
final class FormSchemaBuilder {

	/**
	 * Зафиксированные секции схемы.
	 *
	 * @since 200.4.0
	 *
	 * @var FormSection[]
	 */
	private array $sections = [];

	/**
	 * Секция, находящаяся в процессе заполнения.
	 *
	 * @since 200.4.0
	 *
	 * @var FormSectionBuilder|null
	 */
	private ?FormSectionBuilder $currentSection = NULL;

	/**
	 * Создаёт строитель схемы с заданным codename.
	 *
	 * @since 200.4.0
	 *
	 * @param   string      $codename  Уникальный код формы.
	 * @param   FormLayout  $layout    Начальный layout (stack по умолчанию).
	 */
	private function __construct(
		private readonly string $codename,
		private FormLayout      $layout = FormLayout::STACK,
	) {}

	/**
	 * Создаёт новый строитель схемы формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $codename  Уникальный код формы.
	 *
	 * @return self Экземпляр строителя.
	 *
	 * @example
	 *     $builder = FormSchemaBuilder::create('settings')->layoutTabs();
	 */
	public static function create(string $codename): self {
		if($codename === '') {
			throw new InvalidArgumentException(__('Codename схемы не может быть пустым'));
		}

		return new self($codename);
	}

	/**
	 * Задаёт режим компоновки секций.
	 *
	 * @since 200.4.0
	 *
	 * @param   \DevCraft\Core\Enums\FormLayout  $layout  stack, tabs или accordion.
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $builder->layout('accordion');
	 */
	public function layout(FormLayout $layout): self {

		if(!in_array($layout, FormLayout::cases(), true)) {
			throw new InvalidArgumentException(
				__('Недопустимый layout «{layout}». Допустимо: {variants}', ['{layout}' => $layout, '{variants}' => implode(', ', FormLayout::cases())]),
			);
		}

		$this->layout = $layout;

		return $this;
	}

	/**
	 * Устанавливает layout stack (вертикальный стек секций).
	 *
	 * @since 200.4.0
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $builder->layoutStack();
	 */
	public function layoutStack(): self {
		return $this->layout(FormLayout::STACK);
	}

	/**
	 * Устанавливает layout tabs (вкладки).
	 *
	 * @since 200.4.0
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $builder->layoutTabs();
	 */
	public function layoutTabs(): self {
		return $this->layout(FormLayout::TABS);
	}

	/**
	 * Устанавливает layout accordion (аккордеон).
	 *
	 * @since 200.4.0
	 *
	 * @return self Текущий строитель.
	 *
	 * @example
	 *     $builder->layoutAccordion();
	 */
	public function layoutAccordion(): self {
		return $this->layout(FormLayout::ACCORDION);
	}

	/**
	 * Открывает новую секцию формы.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $title  Заголовок секции.
	 *
	 * @return FormSectionBuilder Строитель секции.
	 *
	 * @example
	 *     $section = $builder->section('Основные');
	 */
	public function section(string $title): FormSectionBuilder {
		$this->commitCurrentSection();

		$this->currentSection = new FormSectionBuilder($this, $title);

		return $this->currentSection;
	}

	/**
	 * Фиксирует секцию, построенную FormSectionBuilder.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormSectionBuilder  $sectionBuilder  Завершённый строитель секции.
	 */
	public function commitSection(FormSectionBuilder $sectionBuilder): void {
		$this->sections[]     = $sectionBuilder->toSection();
		$this->currentSection = NULL;
	}

	/**
	 * Собирает immutable-объект FormSchema.
	 *
	 * @since 200.4.0
	 *
	 * @return FormSchema Готовая схема формы.
	 *
	 * @example
	 *     $schema = $builder->section('Общие')->text('name', 'Имя')->build();
	 */
	public function build(): FormSchema {
		$this->commitCurrentSection();

		if($this->sections === []) {
			throw new InvalidArgumentException(__('Схема должна содержать хотя бы одну секцию'));
		}

		return new FormSchema(
			codename: $this->codename,
			sections: $this->sections,
			layout  : $this->layout,
		);
	}

	/**
	 * Фиксирует текущую незавершённую секцию, если она есть.
	 *
	 * @since 200.4.0
	 */
	private function commitCurrentSection(): void {
		if($this->currentSection !== NULL) {
			$this->commitSection($this->currentSection);
		}
	}

}
