<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Admin\SettingsFormService;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Modules\Admin\Models\PublicAssetEntry;
use DevCraft\Modules\Admin\Models\PublicHeaderEntry;
use DevCraft\Modules\Admin\Repositories\PublicAssetEntryRepository;
use DevCraft\Modules\Admin\Repositories\PublicHeaderEntryRepository;
use DevCraft\Types\FormField;
use DevCraft\Types\FormSchema;
use DLEPlugins;

/**
 * View-model форм публичных стилей/скриптов/заголовков через штатный SettingsFormService / FormSchema.
 */
final class PublicAssetAdminFormService {

	/**
	 * Форма добавления CSS/JS с подставленным kind.
	 *
	 * @param   string  $kind  css|js
	 *
	 * @return array<string, mixed>
	 */
	public function assetAddForm(string $kind): array {
		/** @var FormSchema $schema */
		$schema = require DLEPlugins::Check(
			Paths::modules() . '/Admin/Form/public_asset.form.schema.php',
		);
		$form = (new SettingsFormService())->buildViewModel(
			$schema,
			[],
			AdminIdentity::mod(),
		);

		foreach($form['sections'] as &$section) {
			foreach($section['fields'] as &$field) {
				if(($field['id'] ?? '') === 'kind') {
					$field['value'] = $kind;
				}
			}
			unset($field);
		}
		unset($section);

		return $form;
	}

	/**
	 * Форма добавления meta-заголовка.
	 *
	 * @return array<string, mixed>
	 */
	public function headerAddForm(): array {
		/** @var FormSchema $schema */
		$schema = require DLEPlugins::Check(
			Paths::modules() . '/Admin/Form/public_header.form.schema.php',
		);

		return (new SettingsFormService())->buildViewModel(
			$schema,
			[],
			AdminIdentity::mod(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function assetEditForm(PublicAssetEntry $entry): array {
		/** @var FormSchema $schema */
		$schema = require DLEPlugins::Check(
			Paths::modules() . '/Admin/Form/public_asset_edit.form.schema.php',
		);

		/** @var PublicAssetEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicAssetEntry::class);

		$dependsOptions = [];

		foreach($repo->listByKind($entry->kind) as $row) {
			if($row->id() === $entry->id()) {
				continue;
			}

			$dependsOptions[(string) $row->id()] = ($row->label ?: $row->local_path) . ' #' . $row->id();
		}

		$availableOptions = $this->sectionOptions();

		return $this->buildEditViewModel(
			$schema,
			[
				'id'         => $entry->id(),
				'kind'       => $entry->kind,
				'label'      => $entry->label ?? '',
				'local_path' => $entry->local_path,
				'source_url' => $entry->source_url ?? '',
				'active'     => $entry->active,
				'depends_on'    => $entry->dependsOnIds(),
				'available'     => $entry->availableKeys(),
				'not_available' => $entry->notAvailableKeys(),
			],
			[
				'depends_on'    => $dependsOptions,
				'available'     => $availableOptions,
				'not_available' => $availableOptions,
			],
			$entry->origin === 'auto',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function headerEditForm(PublicHeaderEntry $entry): array {
		/** @var FormSchema $schema */
		$schema = require DLEPlugins::Check(
			Paths::modules() . '/Admin/Form/public_header_edit.form.schema.php',
		);

		/** @var PublicHeaderEntryRepository $repo */
		$repo = Application::instance()->database()->repository(PublicHeaderEntry::class);

		$dependsOptions = [];

		foreach($repo->listAllOrdered() as $row) {
			if($row->id() === $entry->id()) {
				continue;
			}

			$dependsOptions[(string) $row->id()] = $row->name . ' #' . $row->id();
		}

		$sectionOptions = $this->sectionOptions();

		return $this->buildEditViewModel(
			$schema,
			[
				'id'            => $entry->id(),
				'name'          => $entry->name,
				'content'       => $entry->content,
				'active'        => $entry->active,
				'depends_on'    => $entry->dependsOnIds(),
				'available'     => $entry->availableKeys(),
				'not_available' => $entry->notAvailableKeys(),
			],
			[
				'depends_on'    => $dependsOptions,
				'available'     => $sectionOptions,
				'not_available' => $sectionOptions,
			],
			$entry->origin === 'auto',
		);
	}

	/**
	 * @param   array<string, mixed>              $values
	 * @param   array<string, array<string, string>>  $options
	 *
	 * @return array<string, mixed>
	 */
	private function buildEditViewModel(
		FormSchema $schema,
		array      $values,
		array      $options,
		bool       $isAuto,
	): array {
		$fields = [];

		foreach($schema->allFields() as $field) {
			$value = $values[$field->id] ?? $field->default ?? null;

			if($field->type === 'multi') {
				$value = is_array($value) ? array_map('strval', $value) : [];
			}

			if($field->type === 'checkbox') {
				$value = !empty($value);
			}

			$metro = $field->metro ?? [];

			if($isAuto && in_array($field->id, ['local_path', 'source_url', 'label', 'name', 'content'], true)) {
				$metro['readonly'] = true;
			}

			$fieldData = [
				'id'          => $field->id,
				'type'        => $field->type,
				'label'       => $field->label,
				'description' => $field->description,
				'value'       => $value,
				'columns'     => $field->columns ?? 12,
				'metro'       => $metro,
			];

			if($field->options !== [] || isset($options[$field->id])) {
				$fieldData['options'] = $options[$field->id] ?? $field->options;
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
			'is_auto'  => $isAuto,
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function sectionOptions(): array {
		$options = [];

		foreach(DleSiteSectionRegistry::instance()->all() as $row) {
			$options[$row['key']] = $row['label'];
		}

		return $options;
	}

}
