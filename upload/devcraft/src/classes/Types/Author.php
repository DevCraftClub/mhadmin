<?php
//===============================================================
// Файл: Author.php                                             =
// Путь: devcraft/src/classes/Types/Author.php                  =
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

/**
 * Данные автора модуля для блока информации в админке.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                                                       $name      Имя автора.
 * @property array<int, array{name: string, link: string}>                $contacts  Список контактов.
 * @property array<int, array{name: string, value: string, link: string}> $donations Список способов поддержки.
 */
final class Author extends AbstractType {

	/**
	 * Создаёт описание автора модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                                                        $name       Имя автора.
	 * @param   array<int, array{name: string, link: string}>                 $contacts   Контакты автора.
	 * @param   array<int, array{name: string, value: string, link: string}>  $donations  Способы поддержки.
	 *
	 * @example
	 *     $author = new Author('Maxim Harder', contacts: [['name' => 'Telegram', 'link' => 'https://t.me/...']]);
	 */
	public function __construct(
		public string $name,
		public array  $contacts = [],
		public array  $donations = [],
	) {}

	/**
	 * Создаёт описание автора из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные с ключами `name`, `contacts`, `donations`.
	 *
	 * @return static Новый экземпляр описания автора.
	 *
	 * @example
	 *     $author = Author::fromArray(['name' => 'DevCraft', 'contacts' => []]);
	 */
	public static function fromArray(array $data): static {
		return new self(
			name     : (string) ($data['name'] ?? ''),
			contacts : self::normalizeContacts($data['contacts'] ?? []),
			donations: self::normalizeDonations($data['donations'] ?? []),
		);
	}

	/**
	 * Нормализует массив контактов автора.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $contacts  Исходный список контактов.
	 *
	 * @return array<int, array{name: string, link: string}> Отфильтрованный список контактов.
	 */
	private static function normalizeContacts(mixed $contacts): array {
		if(!is_array($contacts)) {
			return [];
		}

		$result = [];

		foreach($contacts as $contact) {
			if(!is_array($contact)) {
				continue;
			}

			$name = (string) ($contact['name'] ?? '');
			$link = (string) ($contact['link'] ?? '');

			if($name === '' && $link === '') {
				continue;
			}

			$result[] = ['name' => $name, 'link' => $link];
		}

		return $result;
	}

	/**
	 * Нормализует массив способов поддержки автора.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $donations  Исходный список способов поддержки.
	 *
	 * @return array<int, array{name: string, value: string, link: string}> Отфильтрованный список.
	 */
	private static function normalizeDonations(mixed $donations): array {
		if(!is_array($donations)) {
			return [];
		}

		$result = [];

		foreach($donations as $donation) {
			if(!is_array($donation)) {
				continue;
			}

			$name  = (string) ($donation['name'] ?? '');
			$value = (string) ($donation['value'] ?? '');
			$link  = (string) ($donation['link'] ?? '');

			if($name === '' && $value === '' && $link === '') {
				continue;
			}

			$result[] = ['name' => $name, 'value' => $value, 'link' => $link];
		}

		return $result;
	}

	/**
	 * Преобразует описание автора в ассоциативный массив.
	 *
	 * @since 200.4.0
	 *
	 * @return array{name: string, contacts: array<int, array{name: string, link: string}>, donations: array<int, array{name: string, value: string,
	 *                     link: string}>} Сериализованные данные.
	 *
	 * @example
	 *     $data = $author->toArray();
	 */
	public function toArray(): array {
		return [
			'name'      => $this->name,
			'contacts'  => $this->contacts,
			'donations' => $this->donations,
		];
	}

}
