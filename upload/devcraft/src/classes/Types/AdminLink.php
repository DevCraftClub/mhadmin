<?php
//===============================================================
// Файл: AdminLink.php                                          =
// Путь: devcraft/src/classes/Types/AdminLink.php               =
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
 * Элемент навигационного меню административной панели.
 *
 * Портирован из MHAdmin `AdminLink` с поддержкой иерархии, типов и привязки страниц.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string      $name      Отображаемое имя пункта меню.
 * @property string|null $link      URL или query-string ссылки.
 * @property string      $type      Тип элемента (`link`, `dropdown`, `divider`, `data`).
 * @property string|null $parent    Идентификатор родительского элемента.
 * @property string|null $extra     Дополнительные атрибуты или data-значение.
 * @property AdminLink[] $children  Вложенные пункты меню.
 * @property string|null $action    Ключ action страницы модуля.
 * @property string|null $pageClass Класс обработчика страницы модуля.
 */
final class AdminLink extends AbstractType {

	/**
	 * Создаёт элемент меню с заданными параметрами.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $name       Отображаемое имя пункта меню.
	 * @param   string|null  $link       URL или query-string ссылки.
	 * @param   string       $type       Тип элемента (`link`, `dropdown`, `divider`, `data`).
	 * @param   string|null  $parent     Идентификатор родительского элемента.
	 * @param   string|null  $extra      Дополнительные атрибуты или data-значение.
	 * @param   AdminLink[]  $children   Вложенные пункты меню.
	 * @param   string|null  $action     Ключ action страницы модуля.
	 * @param   string|null  $pageClass  Класс обработчика страницы модуля.
	 *
	 * @example
	 *     $link = new AdminLink(name: __('Настройки'), link: '?mod=devcraft&action=settings');
	 */
	public function __construct(
		public string  $name,
		public ?string $link = NULL,
		public string  $type = 'link',
		public ?string $parent = NULL,
		public ?string $extra = NULL,
		public array   $children = [],
		public ?string $action = NULL,
		public ?string $pageClass = NULL,
	) {}

	/**
	 * Создаёт ссылку на страницу модуля DevCraft.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $name       Отображаемое имя пункта меню.
	 * @param   string       $action     Ключ action страницы.
	 * @param   string       $pageClass  Полное имя класса страницы.
	 * @param   string|null  $extra      Дополнительное data-значение.
	 * @param   string       $mod        Код модуля DLE (по умолчанию `devcraft`).
	 *
	 * @return self Элемент меню типа `link`.
	 *
	 * @example
	 *     $link = AdminLink::page(__('Логи'), 'logs', LogsPage::class);
	 */
	public static function page(
		string  $name,
		string  $action,
		string  $pageClass,
		?string $extra = NULL,
		string  $mod = 'devcraft',
	): self {
		return new self(
			name     : $name,
			link     : '?mod=' . rawurlencode($mod) . '&action=' . rawurlencode($action),
			type     : 'link',
			extra    : $extra,
			action   : $action,
			pageClass: $pageClass,
		);
	}

	/**
	 * Создаёт разделитель или заголовок группы в меню.
	 *
	 * @since 200.4.0
	 *
	 * @param   string|null  $header  Необязательный заголовок раздела.
	 *
	 * @return self Элемент меню типа `divider`.
	 *
	 * @example
	 *     $divider = AdminLink::divider(__('Модули'));
	 */
	public static function divider(?string $header = NULL): self {
		return new self(name: $header ?? '', type: 'divider');
	}

	/**
	 * Создаёт элемент меню из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Исходные данные (в т. ч. вложенные `children`).
	 *
	 * @return static Новый экземпляр элемента меню.
	 *
	 * @example
	 *     $link = AdminLink::fromArray(['name' => 'Home', 'link' => '?mod=devcraft', 'type' => 'link']);
	 */
	public static function fromArray(array $data): static {
		$children = [];

		if(isset($data['children']) && is_array($data['children'])) {
			foreach($data['children'] as $child) {
				if(is_array($child)) {
					$children[] = self::fromArray($child);
				}
			}
		}

		return new self(
			name     : (string) ($data['name'] ?? ''),
			link     : isset($data['link'])? (string) $data['link'] : NULL,
			type     : (string) ($data['type'] ?? 'link'),
			parent   : isset($data['parent'])? (string) $data['parent'] : NULL,
			extra    : isset($data['extra'])? (string) $data['extra'] : NULL,
			children : $children,
			action   : isset($data['action'])? (string) $data['action'] : NULL,
			pageClass: isset($data['pageClass'])? (string) $data['pageClass'] : NULL,
		);
	}

	/**
	 * Преобразует элемент меню в массив для шаблонов и сериализации.
	 *
	 * @since 200.4.0
	 *
	 * @return array<string, mixed> Данные пункта меню (имя уже локализовано в manifest).
	 *
	 * @example
	 *     $menuItem = $link->toArray();
	 */
	public function toArray(): array {
		$data = [
			'name'     => $this->name,
			'link'     => $this->link,
			'type'     => $this->type,
			'parent'   => $this->parent,
			'extra'    => $this->extra,
			'children' => array_map(
				static fn(self $child): array => $child->toArray(),
				$this->children,
			),
		];

		if($this->action !== NULL) {
			$data['action'] = $this->action;
		}

		if($this->pageClass !== NULL) {
			$data['pageClass'] = $this->pageClass;
		}

		return $data;
	}

}
