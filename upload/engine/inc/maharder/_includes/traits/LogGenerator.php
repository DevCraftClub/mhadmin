<?php

if ( ! class_exists('Monolog\Logger')) {
	include_once ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php';
}

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

trait LogGenerator {

	/**
	 * Регулятор логирования системы
	 * @var bool|int
	 */
	protected int $logs = 0;

	/**
	 * Генерация лог-файлов, если по какой-то прочине произошла ошибка во время исполнения функционала
	 *
	 * @param        $service
	 * @param        $function_name
	 * @param        $message
	 * @param        $type
	 */
	public function generate_log($service, $function_name, $message, $type = 'error'): void {
		if ($this->getLogs()) {
			$root_dir = dirname(__DIR__, 2);
			$date = date('[Y-m-d] d.m.Y, H:i');
			$concurrentDirectory = $root_dir . '/_logs/' . $service . '/' . $function_name;

			if (!mkdir($concurrentDirectory, 0777, true) && !is_dir($concurrentDirectory)) {
				echo "<b>Уведомление</b>:{$type}<br>";
				echo "<b>Модуль</b>:{$service}<br>";
				echo "<b>Функция</b>:{$function_name}<br>";
				echo "<b>Дата и время</b>:{$date}<br>";
				echo "<b>Ошибка</b>:<br>";
				echo "Directory \"{$concurrentDirectory}\" was not created";
				echo "<br><br><b>Ошибка</b>:<br>";
				echo $message;
			}
			$file = $concurrentDirectory . '/' . $type . '.log';

			$logger = new Logger($service);

			switch ($type) {
				case 'error':
					$log_level = Logger::ERROR;
					break;

				case 'info':
					$log_level = Logger::INFO;
					break;

				case 'notice':
					$log_level = Logger::NOTICE;
					break;

				case 'warn':
				case 'warning':
					$log_level = Logger::WARNING;
					break;

				case 'crit':
				case 'critical':
					$log_level = Logger::CRITICAL;
					break;

				case 'alert':
					$log_level = Logger::ALERT;
					break;

				case 'Debug':
				case 'debug':
				default:
					$log_level = Logger::DEBUG;
			}

			$logger->pushHandler(new StreamHandler($file, $log_level));
			$logger->pushHandler(new FirePHPHandler($log_level));
			$logger->pushHandler(new ChromePHPHandler($log_level));
			$logger->pushHandler(new BrowserConsoleHandler($log_level));

			// You can now use your logger
			$logger->info($type, [
				'plugin'   => $service,
				'function' => $function_name,
				'datetime' => $date,
				'message'  => $message
			]);

		}
	}

	/**
	 * @return bool
	 */
	public function getLogs()
	: bool {
		return (bool) $this->logs;
	}

	/**
	 * @param bool|int $logs
	 */
	public function setLogs($logs): void {
		$this->logs = $logs;
	}


}