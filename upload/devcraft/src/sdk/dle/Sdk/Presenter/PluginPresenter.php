<?php

declare(strict_types=1);

namespace DevCraft\Dle\Sdk\Presenter;

use DevCraft\Dle\Sdk\Plugin\PluginInstallExporter;

/**
 * Презентер плагина (plugins).
 */
final class PluginPresenter extends AbstractTablePresenter {
	public function table(): string {
		return 'plugins';
	}

	public function withName(string $name): static {
		return $this->with('name', $name);
	}

	public function withDescription(string $description): static {
		return $this->with('description', $description);
	}

	public function withVersion(string $version): static {
		return $this->with('version', $version);
	}

	/** @param mixed $files TableBuilder|array|list → plugins_files */
	public function withFiles(mixed $files): static {
		return $this->withChild('plugins_files', $files);
	}

	/**
	 * Экспортирует установочный XML (или ZIP) плагина из БД по имени из withName().
	 *
	 * @param   string|null  $path        Каталог / полный путь / null → Paths::pluginExports()
	 * @param   bool         $archivate   true — ZIP с XML и файлами из plugins.filelist
	 */
	public function export(?string $path = null, bool $archivate = false): bool {
		$name = trim((string) ($this->attrs['name'] ?? ''));
		if($name === '') {
			return false;
		}

		return (new PluginInstallExporter())->export($name, $path, $archivate);
	}
}
