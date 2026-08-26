<?php

declare(strict_types=1);

namespace DevCraft\Builders;

use DevCraft\Types\ModuleAjaxConfig;

/**
 * Fluent-строитель секции `ajax` манифеста модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Builders
 */
final class ModuleAjaxConfigBuilder {

	/** @var array<string, class-string> */
	private array $methods = [];

	/** @var array<string, array{handler: class-string, allow_guest: bool}> */
	private array $public = [];

	private function __construct(
		private string $controller = 'admin',
	) {}

	/**
	 * Создаёт строитель AJAX-конфига.
	 */
	public static function create(string $controller = 'admin'): self {
		return new self($controller);
	}

	/**
	 * Задаёт идентификатор контроллера.
	 */
	public function controller(string $controller): self {
		$this->controller = $controller !== ''? $controller : 'admin';

		return $this;
	}

	/**
	 * Регистрирует admin AJAX-метод.
	 *
	 * @param   class-string  $handler
	 */
	public function method(string $name, string $handler): self {
		if($name !== '' && $handler !== '') {
			$this->methods[$name] = $handler;
		}

		return $this;
	}

	/**
	 * Задаёт карту admin-методов целиком.
	 *
	 * @param   array<string, class-string>  $methods
	 */
	public function methods(array $methods): self {
		foreach($methods as $name => $handler) {
			if(is_string($name) && is_string($handler)) {
				$this->method($name, $handler);
			}
		}

		return $this;
	}

	/**
	 * Регистрирует публичный AJAX-метод (controller=public).
	 *
	 * @param   class-string  $handler
	 */
	public function publicMethod(string $name, string $handler, bool $allowGuest = false): self {
		if($name !== '' && $handler !== '') {
			$this->public[$name] = [
				'handler'     => $handler,
				'allow_guest' => $allowGuest,
			];
		}

		return $this;
	}

	/**
	 * Собирает ModuleAjaxConfig.
	 */
	public function build(): ModuleAjaxConfig {
		return new ModuleAjaxConfig(
			controller: $this->controller,
			methods   : $this->methods,
			public    : $this->public,
		);
	}

}
