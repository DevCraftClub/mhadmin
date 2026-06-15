<?php
//===============================================================
// Файл: ModuleData.php                                         =
// Путь: devcraft/src/classes/Types/ModuleData.php              =
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

/**
 * Метаданные зарегистрированного модуля DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                      $id            Уникальный идентификатор модуля.
 * @property string                      $name          Отображаемое имя модуля.
 * @property string                      $version       Версия модуля.
 * @property string                      $namespace     PHP-пространство имён модуля.
 * @property string                      $path          Абсолютный путь к каталогу модуля.
 * @property array<string, class-string> $pages         Карта action → класс страницы.
 * @property string|null                 $description   Описание модуля.
 * @property string|null                 $code          Код модуля для DLE.
 * @property int|null                    $siteId        Идентификатор сайта.
 * @property string|null                 $icon          Иконка модуля.
 * @property string|null                 $siteLink      Ссылка на сайт модуля.
 * @property string|null                 $docsLink      Ссылка на документацию.
 * @property string|null                 $crowdinName   Имя проекта в Crowdin.
 * @property string|null                 $crowdinStatId Идентификатор статистики Crowdin.
 * @property Changelog[]                 $changelog     Записи журнала изменений.
 */
final readonly class ModuleData {

	/**
	 * Создаёт метаданные модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                       $id             Уникальный идентификатор модуля.
	 * @param   string                       $name           Отображаемое имя модуля.
	 * @param   string                       $version        Версия модуля.
	 * @param   string                       $namespace      PHP-пространство имён модуля.
	 * @param   string                       $path           Абсолютный путь к каталогу модуля.
	 * @param   array<string, class-string>  $pages          Карта action → класс страницы.
	 * @param   string|null                  $description    Описание модуля.
	 * @param   string|null                  $code           Код модуля для DLE.
	 * @param   int|null                     $siteId         Идентификатор сайта.
	 * @param   string|null                  $icon           Иконка модуля.
	 * @param   string|null                  $siteLink       Ссылка на сайт модуля.
	 * @param   string|null                  $docsLink       Ссылка на документацию.
	 * @param   string|null                  $crowdinName    Имя проекта в Crowdin.
	 * @param   string|null                  $crowdinStatId  Идентификатор статистики Crowdin.
	 * @param   Changelog[]                  $changelog      Записи журнала изменений.
	 *
	 * @example
	 *     $module = new ModuleData('devcraft', 'DevCraft', '200.4.0', 'DevCraft\\Modules\\Admin', '/path/to/module');
	 */
	public function __construct(
		public string  $id,
		public string  $name,
		public string  $version,
		public string  $namespace,
		public string  $path,
		public array   $pages = [],
		public ?string $description = NULL,
		public ?string $code = NULL,
		public ?int    $siteId = NULL,
		public ?string $icon = NULL,
		public ?string $siteLink = NULL,
		public ?string $docsLink = NULL,
		public ?string $crowdinName = NULL,
		public ?string $crowdinStatId = NULL,
		public array   $changelog = [],
	) {}

	/**
	 * Создаёт метаданные модуля из массива манифеста.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $mod         Код модуля.
	 * @param   array<string, mixed>  $manifest    Данные `manifest.php`.
	 * @param   string                $modulePath  Абсолютный путь к каталогу модуля.
	 *
	 * @return self Метаданные модуля.
	 *
	 * @example
	 *     $module = ModuleData::fromManifest('devcraft', $manifest, DC_MODULES . '/Admin');
	 */
	public static function fromManifest(string $mod, array $manifest, string $modulePath): self {
		/** @var array<string, mixed> $meta */
		$meta  = is_array($manifest['meta'] ?? NULL)? $manifest['meta'] : [];
		$pages = [];

		foreach($manifest['menu'] ?? [] as $item) {
			if(!$item instanceof AdminLink) {
				continue;
			}

			if($item->action === NULL || $item->pageClass === NULL) {
				continue;
			}

			$pages[$item->action] = $item->pageClass;
		}

		$changelogRaw = $manifest['changelog'] ?? [];
		$changelog    = is_array($changelogRaw)? Changelog::listFromManifest($changelogRaw) : [];
		$dirName      = basename(rtrim($modulePath, '/\\'));

		return new self(
			id         : $mod,
			name       : (string) ($meta['name'] ?? $mod),
			version    : (string) ($meta['version'] ?? '0.0.0'),
			namespace  : 'DevCraft\\Modules\\' . $dirName,
			path       : $modulePath,
			pages      : $pages,
			description: isset($meta['description'])? (string) $meta['description'] : NULL,
			code       : isset($manifest['code'])? (string) $manifest['code'] : NULL,
			siteId     : isset($meta['siteId'])? (int) $meta['siteId'] : NULL,
			icon       : isset($meta['icon'])? (string) $meta['icon'] : NULL,
			siteLink   : isset($meta['siteLink'])? (string) $meta['siteLink'] : NULL,
			docsLink   : isset($meta['docsLink'])? (string) $meta['docsLink'] : NULL,
			changelog  : $changelog,
		);
	}

	/**
	 * Создаёт метаданные модуля из конфигурационного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $id      Идентификатор модуля.
	 * @param   array<string, mixed>  $config  Конфигурация модуля.
	 *
	 * @return self Метаданные модуля.
	 *
	 * @example
	 *     $module = ModuleData::fromArray('devcraft', $registryEntry);
	 */
	public static function fromArray(string $id, array $config): self {
		/** @var array<string, class-string> $pages */
		$pages = $config['pages'] ?? [];

		return new self(
			id           : $id,
			name         : (string) ($config['name'] ?? $id),
			version      : (string) ($config['version'] ?? '0.0.0'),
			namespace    : (string) ($config['namespace'] ?? ''),
			path         : (string) ($config['path'] ?? ''),
			pages        : $pages,
			description  : isset($config['description'])? (string) $config['description'] : NULL,
			code         : isset($config['code'])? (string) $config['code'] : NULL,
			siteId       : isset($config['siteId'])? (int) $config['siteId'] : NULL,
			icon         : isset($config['icon'])? (string) $config['icon'] : NULL,
			siteLink     : isset($config['siteLink'])? (string) $config['siteLink'] : NULL,
			docsLink     : isset($config['docsLink'])? (string) $config['docsLink'] : NULL,
			crowdinName  : isset($config['crowdinName'])? (string) $config['crowdinName'] : NULL,
			crowdinStatId: isset($config['crowdinStatId'])? (string) $config['crowdinStatId'] : NULL,
		);
	}

	/**
	 * Преобразует метаданные модуля в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Сериализованные метаданные.
	 *
	 * @example
	 *     $data = $module->toArray();
	 */
	public function toArray(): array {
		return [
			'id'            => $this->id,
			'name'          => $this->name,
			'version'       => $this->version,
			'namespace'     => $this->namespace,
			'path'          => $this->path,
			'pages'         => $this->pages,
			'description'   => $this->description,
			'code'          => $this->code,
			'siteId'        => $this->siteId,
			'icon'          => $this->icon,
			'siteLink'      => $this->siteLink,
			'docsLink'      => $this->docsLink,
			'crowdinName'   => $this->crowdinName,
			'crowdinStatId' => $this->crowdinStatId,
			'changelog'     => array_map(
				static fn(Changelog $entry): array => $entry->toArray(),
				$this->changelog,
			),
		];
	}

}
