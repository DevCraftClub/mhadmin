<?php
//===============================================================
// Файл: ContextLogger.php                                      =
// Путь: devcraft/src/classes/Logging/ContextLogger.php         =
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

namespace DevCraft\Core\Logging;

/**
 * Контекстный логгер с привязкой к имени модуля.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Core.Logging
 */
final class ContextLogger {

	/**
	 * Создаёт логгер для указанного модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $module  Имя модуля или сервиса.
	 *
	 * @example
	 *     $logger = new ContextLogger('Translation');
	 */
	public function __construct(
		private readonly string $module,
	) {}

	/**
	 * Записывает отладочное сообщение через LogGenerator::debug().
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed                 $message  Текст или структура сообщения.
	 * @param   array<string, mixed>  $context  Дополнительный контекст.
	 *
	 * @example
	 *     $logger->debug('Загрузка словаря', ['locale' => 'ru_RU']);
	 */
	public function debug(mixed $message, array $context = []): void {
		LogGenerator::debug($this->module, $message, $context);
	}

	/**
	 * Записывает событие в каналы логирования через LogGenerator::dispatchLog().
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed   $message  Сообщение о событии.
	 * @param   string  $type     Тип события (error, warn, info и т. д.).
	 *
	 * @example
	 *     $logger->log('Файл перевода не найден', 'warn');
	 */
	public function log(mixed $message, string $type = 'error'): void {
		LogGenerator::dispatchLog(
			$this->module,
			LogGenerator::resolveCallerContext(),
			$message,
			$type,
		);
	}

}
