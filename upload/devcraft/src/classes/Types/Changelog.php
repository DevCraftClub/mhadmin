<?php
//===============================================================
// Файл: Changelog.php                                          =
// Путь: devcraft/src/classes/Types/Changelog.php               =
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

use DateTimeImmutable;
use DevCraft\Core\Abstracts\AbstractType;
use DevCraft\Core\Enums\ChangelogChangeType;

/**
 * Запись версии в журнале изменений модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                           $version        Номер версии или «Неопубликованное».
 * @property DateTimeImmutable|null           $date           Дата релиза.
 * @property array<string, ChangelogChange[]> $groupedChanges Изменения, сгруппированные по типу.
 */
final class Changelog extends AbstractType {

	/**
	 * Создаёт запись версии changelog.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                            $version         Номер версии.
	 * @param   DateTimeImmutable|null            $date            Дата релиза.
	 * @param   array<string, ChangelogChange[]>  $groupedChanges  Изменения по ключам типа.
	 *
	 * @example
	 *     $entry = new Changelog('200.4.0', new DateTimeImmutable('2026-06-13'));
	 */
	public function __construct(
		public string             $version,
		public ?DateTimeImmutable $date = NULL,
		public array              $groupedChanges = [],
	) {}

	/**
	 * Создаёт запись версии из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные версии.
	 *
	 * @return static Новый экземпляр записи.
	 *
	 * @throws \DateMalformedStringException Если дата имеет неверный формат.
	 *
	 * @example
	 *     $entry = Changelog::fromArray(['version' => '1.0.0', 'items' => ['[NEW] Feature']]);
	 */
	public static function fromArray(array $data): static {
		$version = (string) ($data['version'] ?? self::unreleasedLabel());
		$date    = NULL;

		if(isset($data['date']) && is_string($data['date']) && $data['date'] !== '') {
			$date = new DateTimeImmutable($data['date']);
		}

		$grouped = [];

		if(isset($data['changes']) && is_array($data['changes'])) {
			foreach($data['changes'] as $key => $items) {
				if(!is_string($key) || !is_array($items)) {
					continue;
				}

				$type = ChangelogChangeType::fromKey($key);

				foreach($items as $item) {
					if(!is_string($item) || trim($item) === '') {
						continue;
					}

					$grouped[$type->key()][] = ChangelogChange::fromLegacyString($item);
				}
			}
		} elseif(isset($data['items']) && is_array($data['items'])) {
			foreach($data['items'] as $item) {
				if(!is_string($item) || trim($item) === '') {
					continue;
				}

				$change                          = ChangelogChange::fromLegacyString($item);
				$grouped[$change->type->key()][] = $change;
			}
		}

		return new self($version, $date, $grouped);
	}

	/**
	 * Создаёт список записей из массива манифеста модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<int, array<string, mixed>>  $entries  Элементы `changelog` из manifest.php.
	 *
	 * @return self[] Список записей changelog.
	 *
	 * @example
	 *     $changelog = Changelog::listFromManifest($manifest['changelog']);
	 */
	public static function listFromManifest(array $entries): array {
		$result = [];

		foreach($entries as $entry) {
			if(!is_array($entry)) {
				continue;
			}

			$result[] = self::fromArray($entry);
		}

		return $result;
	}

	/**
	 * Проверяет, является ли версия неопубликованной.
	 *
	 * @since 200.4.0
	 *
	 * @return bool `true`, если версия соответствует строке «Неопубликованное».
	 *
	 * @example
	 *     $unreleased = $entry->isUnreleased();
	 */
	public function isUnreleased(): bool {
		return strcasecmp($this->version, self::unreleasedLabel()) === 0;
	}

	/**
	 * Преобразует запись версии в ассоциативный массив для шаблонов.
	 *
	 * @since 200.4.0
	 *
	 * @return array{version: string, date: ?string, sections: array<int, array{key: string, label: string, title: string, items: array<int,
	 *                        array<string, string>>}>} Сериализованная запись.
	 *
	 * @example
	 *     $data = $entry->toArray();
	 */
	public function toArray(): array {
		$sections = [];

		foreach(ChangelogChangeType::orderedCases() as $type) {
			$key   = $type->key();
			$items = $this->groupedChanges[$key] ?? [];

			if($items === []) {
				continue;
			}

			$sections[] = [
				'key'   => $key,
				'label' => $type->label(),
				'title' => $type->title(),
				'items' => array_map(
					static fn(ChangelogChange $change): array => $change->toArray(),
					$items,
				),
			];
		}

		return [
			'version'  => $this->version,
			'date'     => $this->date?->format('Y-m-d'),
			'sections' => $sections,
		];
	}

	/**
	 * Возвращает первые записи для тизера на панели управления.
	 *
	 * @since 200.4.0
	 *
	 * @param   int  $limit  Максимальное количество элементов (по умолчанию 3).
	 *
	 * @return array<int, array<string, string>> Список сериализованных изменений.
	 *
	 * @example
	 *     $teaser = $entry->teaserItems(5);
	 */
	public function teaserItems(int $limit = 3): array {
		$items = [];

		foreach(ChangelogChangeType::orderedCases() as $type) {
			foreach($this->groupedChanges[$type->key()] ?? [] as $change) {
				$items[] = $change->toArray();

				if(count($items) >= $limit) {
					return $items;
				}
			}
		}

		return $items;
	}

	public static function unreleasedLabel(): string {
		return __('Неопубликованное');
	}

}
