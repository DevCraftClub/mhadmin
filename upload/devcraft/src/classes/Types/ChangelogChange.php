<?php
//===============================================================
// Файл: ChangelogChange.php                                    =
// Путь: devcraft/src/classes/Types/ChangelogChange.php         =
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

use DevCraft\Core\Abstracts\AbstractType;
use DevCraft\Core\Enums\ChangelogChangeType;

/**
 * Одна запись изменения в журнале версий модуля.
 *
 * @package DevCraft
 * @subpackage Core.Types
 * @since 200.4.0
 *
 * @property ChangelogChangeType $type Тип изменения.
 * @property string              $text Текст записи.
 */
final class ChangelogChange extends AbstractType {

	/**
	 * Создаёт запись изменения.
	 *
	 * @since 200.4.0
	 *
	 * @param ChangelogChangeType $type Тип изменения.
	 * @param string              $text Текст записи.
	 *
	 * @example
	 *     $change = new ChangelogChange(ChangelogChangeType::FIXED, __('Исправлена ошибка сохранения'));
	 */
	public function __construct(
		public ChangelogChangeType $type,
		public string              $text,
	) {}

	/**
	 * Создаёт запись изменения из массива или legacy-строки.
	 *
	 * @since 200.4.0
	 *
	 * @param array<string, mixed>|string $data Структурированные данные или строка legacy-формата.
	 *
	 * @return static Новый экземпляр записи.
	 *
	 * @throws \InvalidArgumentException Если данные некорректны.
	 *
	 * @example
	 *     $change = ChangelogChange::fromArray(['type' => 'fixed', 'text' => 'Bug fix']);
	 */
	public static function fromArray(array|string $data): static {
		if(is_string($data)) {
			return self::fromLegacyString($data);
		}

		if(isset($data['text']) && is_string($data['text'])) {
			$type = isset($data['type'])
				? (is_string($data['type'])? ChangelogChangeType::fromKey($data['type']) : $data['type'])
				: ChangelogChangeType::CHANGED;

			if(!$type instanceof ChangelogChangeType) {
				$type = ChangelogChangeType::CHANGED;
			}

			return new self($type, $data['text']);
		}

		if(isset($data['type']) && is_string($data['type']) && isset($data['changes']) && is_array($data['changes'])) {
			$text = implode("\n", array_map('strval', $data['changes']));

			return new self(ChangelogChangeType::fromKey($data['type']), $text);
		}

		throw new \InvalidArgumentException(__('Некорректные данные записи changelog'));
	}

	/**
	 * Разбирает строку changelog в формате MHAdmin.
	 *
	 * @since 200.4.0
	 *
	 * @param string $line Строка с префиксом `[TAG]` или `FIX:`.
	 *
	 * @return self Запись изменения с определённым типом.
	 *
	 * @example
	 *     $change = ChangelogChange::fromLegacyString('[FIX] Исправлена ошибка');
	 */
	public static function fromLegacyString(string $line): self {
		$line = trim($line);

		if($line === '') {
			return new self(ChangelogChangeType::CHANGED, '');
		}

		if(preg_match('/^FIX:\s*/iu', $line) === 1) {
			return new self(
				ChangelogChangeType::FIXED,
				(string) preg_replace('/^FIX:\s*/iu', '', $line),
			);
		}

		if(preg_match('/^\[([A-Z]+)\]/u', $line, $matches) === 1) {
			$type = ChangelogChangeType::fromLegacyTag($matches[1]);
			$text = (string) preg_replace('/^(\[[^\]]+\])+\s*/u', '', $line);

			return new self($type, $text);
		}

		return new self(ChangelogChangeType::CHANGED, $line);
	}

	/**
	 * Преобразует запись изменения в ассоциативный массив для шаблонов.
	 *
	 * @since 200.4.0
	 *
	 * @return array{type: string, key: string, label: string, title: string, text: string} Сериализованная запись.
	 *
	 * @example
	 *     $item = $change->toArray();
	 */
	public function toArray(): array {
		return [
			'type'  => $this->type->name,
			'key'   => $this->type->key(),
			'label' => $this->type->label(),
			'title' => $this->type->title(),
			'text'  => $this->text,
		];
	}

}
