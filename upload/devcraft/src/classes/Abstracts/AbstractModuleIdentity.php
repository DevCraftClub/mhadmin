<?php
//===============================================================
// Файл: AbstractModuleIdentity.php                             =
// Путь: devcraft/src/classes/Abstracts/AbstractModuleIdentity.php
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

declare(strict_types=1);

namespace DevCraft\Core\Abstracts;

use DevCraft\Core\Interfaces\ModuleIdentity;

/**
 * Базовый identity модуля: константы MODULE/CODE у наследника и статические accessors.
 *
 * Наследник обязан объявить `public const string MODULE` и `public const string CODE`.
 * `public const string VERSION` и `public const string NAME` у наследника опциональны.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @version    200.4.2
 * @subpackage Core.Abstracts
 */
abstract class AbstractModuleIdentity implements ModuleIdentity {

	public const string DEFAULT_LIC_LINK = 'https://devcraft.club/pages/licence-agreement/';

	/**
	 * @since 200.4.0
	 */
	public static function mod(): string {
		return static::MODULE;
	}

	/**
	 * @since 200.4.0
	 */
	public static function code(): string {
		return static::CODE;
	}

	/**
	 * Возвращает версию модуля строкой.
	 *
	 * Берёт собственную константу `VERSION` либо собирает номер на основе `VERSIONID` движка DLE.
	 *
	 * @since      200.4.2
	 *
	 * @return string Номер версии.
	 */
	public static function version(): string {
		return static::VERSION ?? str_replace('.', '', VERSIONID) . '.1.0';
	}

	/**
	 * Возвращает название модуля строкой.
	 *
	 * Берёт значение собственной константы `NAME` либо название класса, если константа не задана.
	 *
	 * @since      200.4.2
	 * @return string Название модуля.
	 */
	public static function name(): string {
		return self::mod() . ' -> ' . (new \ReflectionClass(static::class))->getShortName();
	}

	public static function licenceAgreement(): string {
		return self::DEFAULT_LIC_LINK;
	}

	public static function icon(): string {
		return 'mif-sidebar';
	}

	/**
	 * Автоматически генерируем ссылку на документацию, если она не указана в манифесте
	 */
	public static function docsUrl(): string {
		return 'https://readme.devcraft.club/dev/dle/' . self::code() . '/' . self::version() . '/getting_started';
	}

	/**
	 * Автоматически генерируем ID сайта, если он не указан в манифесте
	 */
	public static function siteId(): int {
		if (is_callable([static::class, 'siteUrl'])) {
			$siteUrl = static::siteUrl();

			if (!empty($siteUrl)) {
				$pattern = '/devcraft\.club\/downloads\/[^\/]+\.(\d+)\/?/';

				if (preg_match($pattern, $siteUrl, $matches)) {
					return (int) $matches[1];
				}
			}
		}

		return 0;
	}

	/**
	 * @return string|null
	 */
	public static function description(): string|null {
		return NULL;
	}

	/**
	 * @return string|null
	 */
	public static function crowdInName(): string|null {
		return NULL;
	}

	/**
	 * @return string|null
	 */
	public static function crowdInStats(): string|null {
		return NULL;
	}

	/**
	 * @return string|null
	 */
	public static function siteUrl(): string|null {
		return NULL;
	}

}
