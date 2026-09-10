<?php
//===============================================================
// Файл: ModuleAjaxConfig.php                                   =
// Путь: devcraft/src/classes/Types/ModuleAjaxConfig.php        =
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
 * Конфигурация AJAX-эндпоинтов модуля из секции `ajax` manifest.php.
 *
 * @package    DevCraft
 * @since      200.4.0
 *
 * @subpackage Core.Types
 * @property string                                                         $controller Идентификатор AJAX-контроллера.
 * @property array<string, class-string>                                    $methods    Карта method → FQCN обработчика.
 * @property array<string, array{handler: class-string, allow_guest: bool}> $public     Публичные методы (controller=public).
 */
final class ModuleAjaxConfig extends AbstractType {

	/**
	 * Создаёт конфигурацию AJAX-эндпоинтов модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                                                          $controller  Идентификатор AJAX-контроллера.
	 * @param   array<string, class-string>                                     $methods     Карта method → FQCN обработчика.
	 * @param   array<string, array{handler: class-string, allow_guest: bool}>  $public      Публичные AJAX-методы.
	 *
	 * @example
	 *     $ajax = new ModuleAjaxConfig('admin', ['settings' => SettingsHandler::class]);
	 */
	public function __construct(
		public string $controller = 'admin',
		public array  $methods = [],
		public array  $public = [],
	) {}

	/**
	 * Создаёт конфигурацию AJAX из ассоциативного массива.
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $data  Секция `ajax` из manifest.php.
	 *
	 * @return static Новый экземпляр конфигурации.
	 *
	 * @example
	 *     $ajax = ModuleAjaxConfig::fromArray($manifest['ajax']);
	 */
	public static function fromArray(array $data): static {
		$methods = [];

		if(isset($data['methods']) && is_array($data['methods'])) {
			foreach($data['methods'] as $method => $handler) {
				if(is_string($method) && is_string($handler) && $handler !== '') {
					$methods[$method] = $handler;
				}
			}
		}

		return new self(
			controller: (string) ($data['controller'] ?? 'admin'),
			methods   : $methods,
			public    : self::normalizePublic($data['public'] ?? []),
		);
	}

	/**
	 * Нормализует секцию ajax.public.
	 *
	 * @param   mixed  $raw  Сырые данные.
	 *
	 * @return array<string, array{handler: class-string, allow_guest: bool}>
	 */
	public static function normalizePublic(mixed $raw): array {
		if(!is_array($raw)) {
			return [];
		}

		$methods = [];

		foreach($raw as $method => $handler) {
			if(!is_string($method) || $method === '') {
				continue;
			}

			if(is_string($handler) && $handler !== '') {
				$methods[$method] = [
					'handler'     => $handler,
					'allow_guest' => false,
				];

				continue;
			}

			if(is_array($handler)) {
				$class = (string) ($handler['handler'] ?? $handler['class'] ?? '');

				if($class === '') {
					continue;
				}

				$methods[$method] = [
					'handler'     => $class,
					'allow_guest' => !empty($handler['allow_guest']),
				];
			}
		}

		return $methods;
	}

}
