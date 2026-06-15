<?php
//===============================================================
// Файл: SettingsFormService.php                                =
// Путь: devcraft/src/classes/Admin/SettingsFormService.php     =
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

namespace DevCraft\Core\Admin;

use DevCraft\Types\FormField;
use DevCraft\Types\FormSchema;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Config\DevCraftConfig;

/**
 * Строит view-model и валидирует данные формы настроек модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class SettingsFormService {

	/**
	 * Формирует view-model формы настроек для Twig.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed>                   $config       Глобальная конфигурация DLE.
	 *
	 * @param   array<string, array<string, string>>  $supplements  Дополнительные options по id поля.
	 *
	 * @param   FormSchema                            $schema       Схема полей настроек.
	 *
	 * @return array<string, mixed> Данные формы: codename, layout, sections, save_url.
	 *
	 * @example
	 *     $form = (new SettingsFormService())->buildViewModel($schema);
	 */
	public function buildViewModel(FormSchema $schema, array $supplements = []): array {
		global $config;

		$settings = DataManager::getConfig($schema->codename);
		$fields   = [];

		foreach($schema->allFields() as $field) {
			$value = DevCraftConfig::resolveField($field, $settings[$field->id] ?? NULL);

			if(($value === NULL || $value === '') && $field->id === 'list_count') {
				$value = (int) ($config['news_number'] ?? 10);
			}

			if($field->type === 'multi') {
				$value = $this->multiValueToArray($value);
			}

			$description = $field->description;

			if($field->id === 'list_count' && $description !== NULL) {
				$newsNumber  = (int) ($config['news_number'] ?? 10);
				$description .= ' => <b>' . $newsNumber . '</b>.';
			}

			$fieldData = [
				'id'          => $field->id,
				'type'        => $field->type,
				'label'       => $field->label,
				'description' => $description !== NULL? $description : NULL,
				'value'       => $value,
				'columns'     => $field->columns ?? 12,
				'metro'       => $field->metro,
			];

			if($field->options !== [] || isset($supplements[$field->id])) {
				$fieldData['options'] = $supplements[$field->id] ?? $field->options;
			}

			$fields[] = $fieldData;
		}

		$sections = [];

		foreach($schema->sections as $section) {
			$sectionFields = array_values(array_filter(
				$fields,
				static fn(array $f): bool => in_array(
					$f['id'],
					array_map(static fn(FormField $field): string => $field->id, $section->fields),
					true,
				),
			));

			$sections[] = [
				'title'  => $section->title,
				'fields' => $sectionFields,
			];
		}

		return [
			'codename' => $schema->codename,
			'layout'   => strtolower($schema->layout->name),
			'sections' => $sections,
			'save_url' => Paths::ajaxUrl('settings'),
		];
	}

	/**
	 * Собирает карту PHP-фильтров для полей схемы.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormSchema  $schema  Схема полей настроек.
	 *
	 * @return array<string, int> id поля => константа filter_var.
	 *
	 * @example
	 *     $filters = (new SettingsFormService())->buildFilters($schema);
	 */
	public function buildFilters(FormSchema $schema): array {
		$filters = [];

		foreach($schema->allFields() as $field) {
			if($field->filter !== NULL) {
				$filters[$field->id] = $field->filter;
			}
		}

		return $filters;
	}

	/**
	 * Валидирует частичный ввод настроек по схеме.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $input   Входные данные формы.
	 * @param   FormSchema            $schema  Схема полей настроек.
	 *
	 * @return array{valid: array<string, mixed>, errors: array<string, string>} Валидные значения и ошибки по id.
	 *
	 * @example
	 *     $result = (new SettingsFormService())->validatePartial($_POST, $schema);
	 */
	public function validatePartial(array $input, FormSchema $schema): array {
		$valid  = [];
		$errors = [];

		foreach($schema->allFields() as $field) {
			if(!array_key_exists($field->id, $input)) {
				continue;
			}

			$raw = $input[$field->id];

			if($field->type === 'multi') {
				if(!is_array($raw)) {
					$errors[$field->id] = __('Недопустимое значение');

					continue;
				}

				$multi = [];

				foreach($raw as $item) {
					$key = (string) $item;

					if($field->options !== [] && !array_key_exists($key, $field->options)) {
						$errors[$field->id] = __('Недопустимый выбор');

						continue 2;
					}

					$multi[] = $key;
				}

				$valid[$field->id] = implode(' ', $multi);

				continue;
			}

			if($field->type === 'checkbox') {
				$valid[$field->id] = filter_var($raw, FILTER_VALIDATE_BOOLEAN);

				continue;
			}

			if($field->type === 'number' && ($raw === '' || $raw === NULL)) {
				$valid[$field->id] = '';

				continue;
			}

			$value = $this->filterFieldValue($field, $raw);

			if($value === false) {
				$errors[$field->id] = __('Недопустимое значение');

				continue;
			}

			if($field->type === 'select' && $field->options !== [] && !array_key_exists((string) $value, $field->options)) {
				$errors[$field->id] = __('Недопустимый выбор');

				continue;
			}

			$valid[$field->id] = $value;
		}

		return [
			'valid'  => $this->applyConfigDefaults($valid, $schema),
			'errors' => $errors,
		];
	}

	/**
	 * Подставляет значения по умолчанию и нормализует типы полей конфигурации.
	 *
	 * @since 200.4.0
	 *
	 * @global array<string, mixed>   $config  Глобальная конфигурация DLE.
	 *
	 * @param   FormSchema            $schema  Схема полей настроек.
	 *
	 * @param   array<string, mixed>  $valid   Уже прошедшие валидацию значения.
	 *
	 * @return array<string, mixed> Значения с учётом defaults DevCraftConfig.
	 *
	 * @example
	 *     $normalized = (new SettingsFormService())->applyConfigDefaults($valid, $schema);
	 */
	public function applyConfigDefaults(array $valid, FormSchema $schema): array {
		global $config;

		foreach($schema->allFields() as $field) {
			if(!array_key_exists($field->id, $valid)) {
				continue;
			}

			if($field->id === 'list_count' && DevCraftConfig::isEmptyValue($valid[$field->id])) {
				$valid[$field->id] = (int) ($config['news_number'] ?? 10);

				continue;
			}

			$valid[$field->id] = DevCraftConfig::resolveField($field, $valid[$field->id]);
		}

		return $valid;
	}

	/**
	 * Преобразует значение multi-поля в список строк.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $value  Сырые данные поля.
	 *
	 * @return list<string> Нормализованный список значений.
	 */
	private function multiValueToArray(mixed $value): array {
		if(is_array($value)) {
			return array_values(array_filter(
				array_map(static fn(mixed $item): string => (string) $item, $value),
				static fn(string $item): bool => $item !== '',
			));
		}

		if(!is_string($value) || trim($value) === '') {
			return [];
		}

		return array_values(array_filter(
			preg_split('/[\s,]+/', trim($value))? : [],
			static fn(string $item): bool => $item !== '',
		));
	}

	/**
	 * Применяет filter_var к значению поля согласно типу или явному фильтру.
	 *
	 * @since 200.4.0
	 *
	 * @param   FormField  $field  Объект поля схемы.
	 * @param   mixed      $raw    Сырое значение.
	 *
	 * @return mixed Отфильтрованное значение или false при ошибке.
	 */
	private function filterFieldValue(FormField $field, mixed $raw): mixed {
		$filter = $field->filter ?? match ($field->type) {
			'number'                     => FILTER_VALIDATE_INT,
			'text', 'textarea', 'hidden' => FILTER_UNSAFE_RAW,
			default                      => FILTER_UNSAFE_RAW,
		};

		if($field->type === 'textarea' || $field->type === 'text') {
			$value = filter_var((string) $raw, $filter);

			return is_string($value)? trim($value) : false;
		}

		return filter_var($raw, $filter);
	}

}
