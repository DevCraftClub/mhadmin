<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

/**
 * Реестр ключей разделов показа публичных ресурсов на сайте DLE.
 *
 * Сателлиты регистрируют дополнительные ключи при загрузке манифеста или init:
 *
 * ```php
 * DleSiteSectionRegistry::instance()->register('my_module_page', __('Моя страница'));
 * ```
 *
 * Сопоставление с текущей страницей — только через глобальный `$do` DLE (см. currentKey()).
 */
final class DleSiteSectionRegistry {

	private static ?self $instance = null;

	/** @var array<string, string> key => label */
	private array $sections = [];

	private function __construct() {
		// Стандартные значения $do ядра DLE (пустой do → main в currentKey()).
		$this->sections = [
			'main'          => __('Главная'),
			'date'          => __('Новости за дату'),
			'cat'           => __('Категория'),
			'showfull'      => __('Полная новость'),
			'search'        => __('Поиск'),
			'xfsearch'      => __('Поиск по доп. полю'),
			'userinfo'      => __('Профиль пользователя'),
			'register'      => __('Регистрация'),
			'stats'         => __('Статистика'),
			'pm'            => __('Личные сообщения'),
			'feedback'      => __('Обратная связь'),
			'favorites'     => __('Закладки'),
			'newposts'      => __('Новые сообщения'),
			'addnews'       => __('Добавление новости'),
			'lastnews'      => __('Последние новости'),
			'lastcomments'  => __('Последние комментарии'),
			'lostpassword'  => __('Восстановление пароля'),
			'static'        => __('Статическая страница'),
			'catalog'       => __('Каталог страниц'),
			'alltags'       => __('Облако тегов'),
			'tags'          => __('Новости по тегу'),
			'allnews'       => __('Все новости'),
		];
	}

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Регистрирует или переопределяет подпись ключа раздела.
	 */
	public function register(string $key, string $label): void {
		$key = strtolower(trim($key));
		if($key === '') {
			return;
		}

		$this->sections[$key] = $label;
	}

	/**
	 * @return list<array{key: string, label: string}>
	 */
	public function all(): array {
		$result = [];

		foreach($this->sections as $key => $label) {
			$result[] = [
				'key'   => $key,
				'label' => $label,
			];
		}

		return $result;
	}

	public function label(string $key): string {
		$key = strtolower(trim($key));

		return $this->sections[$key] ?? $key;
	}

	/**
	 * Текущий ключ раздела по контексту DLE (`$do`).
	 */
	public function currentKey(): string {
		global $do;

		$raw = strtolower(trim((string) ($do ?? '')));

		return $raw === '' ? 'main' : $raw;
	}

}
