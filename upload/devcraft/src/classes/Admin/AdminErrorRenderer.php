<?php
//===============================================================
// Файл: AdminErrorRenderer.php                                 =
// Путь: devcraft/src/classes/Admin/AdminErrorRenderer.php      =
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

namespace DevCraft\Core\Admin;

use DevCraft\Core\Application;
use DevCraft\Enums\AdminErrorKind;
use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Module\PluginContext;
use DevCraft\Core\Config\DevCraftConfig;

/**
 * Рендерит страницы ошибок админки (404, 500, общая) через Twig.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Admin
 */
final class AdminErrorRenderer {

	/**
	 * Выводит HTML-страницу ошибки и устанавливает HTTP-код ответа.
	 *
	 * @since 200.4.0
	 *
	 * @param   AdminErrorKind      $kind     Тип ошибки (404, 500 или общая).
	 * @param   string              $title    Заголовок страницы.
	 * @param   string              $message  Основное сообщение для пользователя.
	 * @param   int                 $status   HTTP-код ответа.
	 * @param   string|null         $detail   Дополнительные технические сведения.
	 * @param   PluginContext|null  $plugin   Контекст модуля для ссылки на dashboard.
	 *
	 * @example
	 *     (new AdminErrorRenderer())->render(
	 *         AdminErrorKind::NotFound,
	 *         __('Страница не найдена'),
	 *         __('Неизвестное действие.'),
	 *         404,
	 *     );
	 */
	public function render(
		AdminErrorKind $kind,
		string         $title,
		string         $message,
		int            $status = 200,
		?string        $detail = NULL,
		?PluginContext $plugin = NULL,
	): void {
		if(!headers_sent()) {
			http_response_code($status);
		}

		$mod = $plugin?->mod() ?? 'devcraft';

		$template = match ($kind) {
			AdminErrorKind::NotFound    => 'core/errors/404.twig',
			AdminErrorKind::ServerError => 'core/errors/500.twig',
			AdminErrorKind::Generic     => 'core/errors/generic.twig',
		};

		$code = match ($kind) {
			AdminErrorKind::NotFound    => '404',
			AdminErrorKind::ServerError => '500',
			AdminErrorKind::Generic     => '',
		};

		$assetBase = Application::instance()->public_asset_url();

		$locale = (string) DevCraftConfig::get('language', 'ru_RU');

		echo Application::instance()->twig()->render('core/errors/layout.twig', [
			'title'              => $title,
			'devcraft_html_lang' => Translation::loadLanguageMeta($locale)->iso2,
			'content'            => Application::instance()->twig()->render($template, [
				'title'         => $title,
				'message'       => $message,
				'detail'        => $detail,
				'code'          => $code,
				'dashboard_url' => '?mod=' . rawurlencode($mod) . '&action=dashboard',
			]),
			'asset_base'         => $assetBase,
			'css_urls'           => [
				$assetBase . 'css/metro.css',
				$assetBase . 'css/icons.css',
				$assetBase . 'css/devcraft.css',
			],
		]);
	}

}
