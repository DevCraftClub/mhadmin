<?php

declare(strict_types=1);

namespace DevCraft\Core\Module;

use DevCraft\Core\Support\DataManager;
use DevCraft\Types\AdminLink;
use DevCraft\Types\FormField;
use DevCraft\Types\FormSchema;
use DevCraft\Types\FormSection;

/**
 * Встраивает сателлиты (`extends`) в host PluginContext: menu, ajax, settings.
 */
final class ModuleExtensionMerger {

	/**
	 * Подмешивает все модули с extends === host.mod в контекст host.
	 */
	public static function enrich(PluginContext $host, Registry $registry): void {
		$hostMod = $host->mod();

		foreach($registry->extensionsOf($hostMod) as $extension) {
			self::mergeInto($host, $extension);
		}
	}

	/**
	 * Разбивает валидные поля формы: без префикса → host, `{code}__{field}` → сателлит.
	 *
	 * @param   array<string, mixed>  $valid
	 *
	 * @return array{host: array<string, mixed>, extensions: array<string, array<string, mixed>>}
	 */
	public static function partitionValid(array $valid): array {
		$host       = [];
		$extensions = [];

		foreach($valid as $key => $value) {
			if(!is_string($key) || !str_contains($key, '__')) {
				$host[$key] = $value;

				continue;
			}

			[$code, $field] = explode('__', $key, 2);

			if($code === '' || $field === '') {
				$host[$key] = $value;

				continue;
			}

			$extensions[$code][$field] = $value;
		}

		return ['host' => $host, 'extensions' => $extensions];
	}

	/**
	 * Дописывает в массив settings значения сателлитов под префиксами полей схемы.
	 *
	 * @param   array<string, mixed>  $hostSettings
	 *
	 * @return array<string, mixed>
	 */
	public static function hydrateSettings(array $hostSettings, ?FormSchema $schema): array {
		if($schema === NULL) {
			return $hostSettings;
		}

		$out    = $hostSettings;
		$loaded = [];

		foreach($schema->allFields() as $field) {
			$id = $field->id;

			if(!str_contains($id, '__')) {
				continue;
			}

			[$code, $name] = explode('__', $id, 2);

			if($code === '' || $name === '') {
				continue;
			}

			if(!isset($loaded[$code])) {
				$loaded[$code] = DataManager::getConfig($code);
			}

			if(array_key_exists($name, $loaded[$code])) {
				$out[$id] = $loaded[$code][$name];
			} elseif(!array_key_exists($id, $out) && $field->default !== NULL) {
				$out[$id] = $field->default;
			}
		}

		return $out;
	}

	/**
	 * Сохраняет конфиги сателлитов из partition; возвращает поля host для дальнейшей записи.
	 *
	 * @param   array<string, mixed>  $valid
	 *
	 * @return array<string, mixed> Поля без префикса (для host JSON).
	 */
	public static function splitAndSaveExtensions(array $valid): array {
		$parts = self::partitionValid($valid);

		foreach($parts['extensions'] as $code => $fields) {
			if($fields === []) {
				continue;
			}

			$existing = DataManager::getConfig($code);
			DataManager::saveConfig($code, array_merge(is_array($existing) ? $existing : [], $fields));
		}

		return $parts['host'];
	}

	private static function mergeInto(PluginContext $host, PluginContext $extension): void {
		$hostMod = $host->mod();
		$meta    = $extension->meta();
		$title   = (string) ($meta['name'] ?? $extension->mod());

		foreach($extension->menu() as $link) {
			$host->appendMenuLink(self::rewriteLink($link, $hostMod));
		}

		$host->appendAjaxMethods($extension->ajaxMethods());

		$extSchema = $extension->settingsSchema();
		$hostSchema = $host->settingsSchema();

		if($extSchema === NULL || $hostSchema === NULL) {
			return;
		}

		$code = $extension->moduleData()->code ?? $extension->mod();
		$host->setSettingsSchema(self::mergeSchemas($hostSchema, $extSchema, $code, $title));
	}

	private static function rewriteLink(AdminLink $link, string $hostMod): AdminLink {
		if($link->action === NULL || $link->pageClass === NULL) {
			return $link;
		}

		if($link->type === 'hidden') {
			return AdminLink::hidden($link->action, $link->pageClass);
		}

		return AdminLink::page(
			$link->name !== '' ? $link->name : $link->action,
			$link->action,
			$link->pageClass,
			$link->extra,
			$hostMod,
		);
	}

	private static function mergeSchemas(
		FormSchema $host,
		FormSchema $extension,
		string $code,
		string $satelliteTitle,
	): FormSchema {
		$sections = $host->sections;

		foreach($extension->sections as $section) {
			$fields = [];

			foreach($section->fields as $field) {
				$fields[] = new FormField(
					id         : $code . '__' . $field->id,
					type       : $field->type,
					label      : $field->label,
					description: $field->description,
					options    : $field->options,
					filter     : $field->filter,
					default    : $field->default,
					columns    : $field->columns,
					metro      : $field->metro,
				);
			}

			$sections[] = new FormSection(
				title : $satelliteTitle . ': ' . $section->title,
				fields: $fields,
			);
		}

		return new FormSchema(
			codename: $host->codename,
			sections: $sections,
			layout  : $host->layout,
		);
	}

}
