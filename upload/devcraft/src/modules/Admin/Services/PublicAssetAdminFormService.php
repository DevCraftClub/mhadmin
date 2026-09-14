<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Admin\SettingsFormService;
use DevCraft\Core\Config\Paths;
use DevCraft\Modules\Admin\AdminIdentity;
use DevCraft\Types\FormSchema;
use DLEPlugins;

/**
 * View-model форм публичных ассетов через штатный SettingsFormService / FormSchema.
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

}
