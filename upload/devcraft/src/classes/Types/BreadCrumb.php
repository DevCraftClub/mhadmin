<?php
//===============================================================
// Файл: BreadCrumb.php                                         =
// Путь: devcraft/src/classes/Types/BreadCrumb.php              =
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
 * Элемент навигационной цепочки «хлебных крошек» в админке.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string      $title Заголовок элемента.
 * @property string|null $url   URL ссылки или `null` для текущей страницы.
 */
final class BreadCrumb extends AbstractType {

	/**
	 * Создаёт элемент хлебных крошек.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $title  Заголовок элемента.
	 * @param   string|null  $url    URL ссылки или `null` для текущей страницы.
	 *
	 * @example
	 *     $crumb = new BreadCrumb(__('Настройки'), '?mod=devcraft&action=settings');
	 */
	public function __construct(
		public string  $title,
		public ?string $url = NULL,
	) {}

	/**
	 * Создаёт элемент из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные с ключами `title` и `url`.
	 *
	 * @return static Новый экземпляр элемента.
	 *
	 * @example
	 *     $crumb = BreadCrumb::fromArray(['title' => 'Home', 'url' => '/admin']);
	 */
	public static function fromArray(array $data): static {
		return new self(
			title: (string) ($data['title'] ?? ''),
			url  : isset($data['url'])? (string) $data['url'] : NULL,
		);
	}

	/**
	 * Преобразует элемент в массив с локализованным заголовком.
	 *
	 * @since 200.4.0
	 *
	 * @return array{title: string, url: ?string} Данные для шаблона.
	 *
	 * @example
	 *     $item = $crumb->toArray();
	 */
	public function toArray(): array {
		return [
			'title' => __($this->title),
			'url'   => $this->url,
		];
	}

}
