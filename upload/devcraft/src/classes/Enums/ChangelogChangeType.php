<?php
//===============================================================
// Файл: ChangelogChangeType.php                                =
// Путь: devcraft/src/classes/Enums/ChangelogChangeType.php     =
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

namespace DevCraft\Core\Enums;

/**
 * Тип записи в журнале изменений (формат Keep a Changelog).
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Enums
 */
enum ChangelogChangeType {

	/**
	 * Новая функциональность.
	 *
	 * @since 200.4.0
	 */
	case ADDED;

	/**
	 * Изменение существующей функциональности.
	 *
	 * @since 200.4.0
	 */
	case CHANGED;

	/**
	 * Устаревшая функциональность.
	 *
	 * @since 200.4.0
	 */
	case DEPRECATED;

	/**
	 * Удалённая функциональность.
	 *
	 * @since 200.4.0
	 */
	case REMOVED;

	/**
	 * Исправление ошибки.
	 *
	 * @since 200.4.0
	 */
	case FIXED;

	/**
	 * Исправление уязвимости безопасности.
	 *
	 * @since 200.4.0
	 */
	case SECURITY;

	/**
	 * Возвращает все варианты перечисления в порядке отображения в UI.
	 *
	 * @since 200.4.0
	 *
	 * @return self[]
	 *
	 * @example
	 *     foreach (ChangelogChangeType::orderedCases() as $type) {
	 *         echo $type->title();
	 *     }
	 */
	public static function orderedCases(): array {
		return [
			self::ADDED,
			self::CHANGED,
			self::DEPRECATED,
			self::REMOVED,
			self::FIXED,
			self::SECURITY,
		];
	}

	/**
	 * Создаёт вариант перечисления по строковому ключу.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $key  Ключ типа (`added`, `fixed` и т. д.).
	 *
	 * @return self Соответствующий вариант перечисления.
	 *
	 * @throws \InvalidArgumentException Если ключ неизвестен.
	 *
	 * @example
	 *     $type = ChangelogChangeType::fromKey('fixed');
	 */
	public static function fromKey(string $key): self {
		return match (strtolower($key)) {
			'added'      => self::ADDED,
			'changed'    => self::CHANGED,
			'deprecated' => self::DEPRECATED,
			'removed'    => self::REMOVED,
			'fixed'      => self::FIXED,
			'security'   => self::SECURITY,
			default      => throw new \InvalidArgumentException(__('Неизвестный тип изменения: {key}', ['{key}' => $key])),
		};
	}

	/**
	 * Преобразует legacy-тег из старого формата changelog в вариант перечисления.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $tag  Тег в формате MHAdmin (`NEW`, `UPDATE`, `FIX` и т. д.).
	 *
	 * @return self Соответствующий вариант перечисления.
	 *
	 * @example
	 *     $type = ChangelogChangeType::fromLegacyTag('FIX');
	 */
	public static function fromLegacyTag(string $tag): self {
		return match (strtoupper($tag)) {
			'NEW'           => self::ADDED,
			'UPDATE'        => self::CHANGED,
			'FIX'           => self::FIXED,
			'DELETE', 'DEL' => self::REMOVED,
			'DEPRECATED'    => self::DEPRECATED,
			'SECURITY'      => self::SECURITY,
			default         => self::CHANGED,
		};
	}

	/**
	 * Возвращает машинный ключ типа для массивов и шаблонов.
	 *
	 * @since 200.4.0
	 *
	 * @return string Ключ в нижнем регистре (`added`, `fixed` и т. д.).
	 *
	 * @example
	 *     $key = ChangelogChangeType::FIXED->key();
	 */
	public function key(): string {
		return match ($this) {
			self::ADDED      => 'added',
			self::CHANGED    => 'changed',
			self::DEPRECATED => 'deprecated',
			self::REMOVED    => 'removed',
			self::FIXED      => 'fixed',
			self::SECURITY   => 'security',
		};
	}

	/**
	 * Возвращает англоязычную метку типа для внутреннего использования.
	 *
	 * @since 200.4.0
	 *
	 * @return string Метка на английском (`Added`, `Fixed` и т. д.).
	 *
	 * @example
	 *     $label = ChangelogChangeType::ADDED->label();
	 */
	public function label(): string {
		return match ($this) {
			self::ADDED      => 'Added',
			self::CHANGED    => 'Changed',
			self::DEPRECATED => 'Deprecated',
			self::REMOVED    => 'Removed',
			self::FIXED      => 'Fixed',
			self::SECURITY   => 'Security',
		};
	}

	/**
	 * Возвращает локализованный заголовок секции changelog.
	 *
	 * @since 200.4.0
	 *
	 * @return string Переведённый заголовок для отображения в админке.
	 *
	 * @example
	 *     echo ChangelogChangeType::SECURITY->title();
	 */
	public function title(): string {
		return match ($this) {
			self::ADDED      => __('Добавлено'),
			self::CHANGED    => __('Изменено'),
			self::DEPRECATED => __('Устарело'),
			self::REMOVED    => __('Удалено'),
			self::FIXED      => __('Исправлено'),
			self::SECURITY   => __('Безопасность'),
		};
	}

}
