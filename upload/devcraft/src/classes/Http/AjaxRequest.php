<?php
//===============================================================
// Файл: AjaxRequest.php                                        =
// Путь: devcraft/src/classes/Http/AjaxRequest.php              =
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

namespace DevCraft\Core\Http;

/**
 * DTO входящего AJAX-запроса DevCraft.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Http
 * @property string               $controller Имя контроллера или модуля-обработчика.
 * @property string               $method     Имя вызываемого метода.
 * @property array<string, mixed> $data       Полезная нагрузка запроса.
 * @property string               $mod        Идентификатор плагина DLE (mod).
 */
final readonly class AjaxRequest {

	/**
	 * Создаёт DTO из параметров маршрута и тела запроса.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $controller  Имя контроллера.
	 * @param   string                $method      Имя метода.
	 * @param   array<string, mixed>  $data        Данные запроса.
	 * @param   string                $mod         Идентификатор плагина.
	 *
	 * @example
	 *     $request = new AjaxRequest('admin', 'saveSettings', ['key' => 'value']);
	 */
	public function __construct(
		public string $controller,
		public string $method,
		public array  $data = [],
		public string $mod = 'devcraft',
	) {}

	/**
	 * Создаёт DTO из глобальных переменных $_REQUEST и $_POST.
	 *
	 * @since 200.4.0
	 *
	 * @return self Разобранный AJAX-запрос.
	 *
	 * @example
	 *     $request = AjaxRequest::fromGlobals();
	 */
	public static function fromGlobals(): self {
		$controller = (string) ($_REQUEST['controller'] ?? $_REQUEST['module'] ?? 'admin');
		$method     = (string) ($_REQUEST['method'] ?? $_REQUEST['action'] ?? '');
		$mod        = (string) ($_REQUEST['mod'] ?? 'devcraft');
		$data       = [];

		if(isset($_POST['data'])) {
			if(is_array($_POST['data'])) {
				$data = $_POST['data'];
			} elseif(is_string($_POST['data']) && $_POST['data'] !== '') {
				$decoded = json_decode($_POST['data'], true);

				if(is_array($decoded)) {
					$data = $decoded;
				}
			}
		} elseif($_POST !== []) {
			$data = $_POST;
		}

		return new self($controller, $method, $data, $mod);
	}

}
