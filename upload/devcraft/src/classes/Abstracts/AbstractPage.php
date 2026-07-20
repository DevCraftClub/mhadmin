<?php
//===============================================================
// Файл: AbstractPage.php                                       =
// Путь: devcraft/src/classes/Abstracts/AbstractPage.php        =
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

namespace DevCraft\Core\Abstracts;

use Twig\Environment;
use DevCraft\Core\Application;
use DevCraft\Core\Admin\AdminContext;
use DevCraft\Core\Interfaces\PageInterface;
use DevCraft\Core\Exception\DevCraftException;

/**
 * Базовый класс административной страницы с доступом к Twig и контексту админки.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Abstracts
 */
abstract class AbstractPage implements PageInterface {

	/**
	 * Привязанный контекст административной панели.
	 *
	 * @since 200.4.0
	 * @var AdminContext|null
	 */
	private ?AdminContext $adminContext = NULL;

	/**
	 * Нормализует ключ представления для загрузчика Twig.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $view  Относительный или абсолютный ключ шаблона.
	 *
	 * @return string Ключ, понятный загрузчику шаблонов DevCraft.
	 *
	 * @example
	 *     $key = AbstractPage::resolveViewKey('admin/logs.twig');
	 */
	public static function resolveViewKey(string $view): string {
		if(str_starts_with($view, '@') || str_starts_with($view, 'core/') || str_starts_with($view, 'pages/')) {
			return $view;
		}

		if(str_contains($view, '/')) {
			return '@' . $view;
		}

		return $view;
	}

	/**
	 * Привязывает контекст админки к экземпляру страницы.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminContext  $adminContext  Контекст текущего запроса админки.
	 *
	 * @example
	 *     $page->bindAdminContext($adminContext);
	 */
	public function bindAdminContext(AdminContext $adminContext): void {
		$this->adminContext = $adminContext;
	}

	/**
	 * Возвращает привязанный контекст админки.
	 *
	 * @since 200.4.0
	 *
	 * @return AdminContext Контекст текущего запроса.
	 *
	 * @throws DevCraftException Если контекст не был привязан.
	 */
	protected function adminContext(): AdminContext {
		if($this->adminContext === NULL) {
			throw new DevCraftException(__('AdminContext не привязан.'));
		}

		return $this->adminContext;
	}

	/**
	 * Добавляет элемент хлебных крошек в контекст админки.
	 *
	 * @since 200.4.0
	 *
	 * @param   string       $title  Заголовок элемента.
	 * @param   string|null  $url    URL элемента или `null` для текущей страницы.
	 */
	protected function addBreadcrumb(string $title, ?string $url = NULL): void {
		$this->adminContext()->addBreadcrumb($title, $url);
	}

	/**
	 * Возвращает экземпляр Twig из приложения.
	 *
	 * @since 200.4.0
	 *
	 * @return Environment Настроенный движок шаблонов.
	 */
	protected function twig(): Environment {
		return Application::instance()->twig();
	}

	/**
	 * Рендерит Twig-шаблон с переданными данными.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $view  Ключ или путь шаблона.
	 * @param   array<string, mixed>  $data  Данные для шаблона.
	 *
	 * @return string Сгенерированная HTML-разметка.
	 */
	protected function render(string $view, array $data = []): string {
		return $this->twig()->render(self::resolveViewKey($view), $data);
	}

}
