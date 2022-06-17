<?php

if(!class_exists('Monolog\Logger')) {
	include_once ENGINE_DIR . '/inc/maharder/_includes/vendor/autoload.php';
}

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\TelegramBotHandler;

trait LogGenerator {

	/**
	 * Регулятор логирования системы
	 *
	 * @var bool|int
	 */
	protected int $logs = 0;

	/**
	 * Регулятор отправки логов в телеграм канал
	 * По умолчанию - выключен
	 *
	 * @var bool
	 */
	protected bool $telegram_send = false;
	/**
	 * ID канала, куда будут отправляться логи
	 *
	 * @var int|string|null
	 */
	protected $telegram_channel = null;
	/**
	 * API телеграм бота, который будет отправлять логи
	 *
	 * @var string|null
	 */
	protected ?string $telegram_bot = null;
	/**
	 * Тип логов, которые будут отправлены в телеграм
	 *
	 * @var string|null
	 */
	protected ?string $telegram_type = null;

	/**
	 * Генерация лог-файлов, если по какой-то причине произошла ошибка во время исполнения функционала
	 *
	 * @param string $service
	 * @param string $function_name
	 * @param        $message
	 * @param string $type
	 *
	 * @throws \Monolog\Handler\MissingExtensionException
	 */
	public function generate_log(string $service, string $function_name, $message, string $type = 'error')
	: void {
		if($this->getLogs()) {
			$root_dir = dirname(__DIR__, 2);
			$date = date('Y-m-d d.m.Y H:i');
			$concurrentDirectory = $root_dir . '/_logs/' . $service . '/' . $function_name;

			if(!mkdir($concurrentDirectory, 0777, true) && !is_dir($concurrentDirectory)) {
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

			switch($type) {
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

				case 'emergency':
					$log_level = Logger::EMERGENCY;
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

			$log_message = [
				'plugin' => $service, 'function_name' => $function_name, 'datetime' => $date, 'message' => $message
			];

			$telegram_send = false;

			if($this->isTelegramSend()) {
				$telegramLogger = new TelegramBotHandler($this->getTelegramBot(), $this->getTelegramChannel(), $log_level);
				$telegramLogger->setParseMode('HTML');

				$t_type = explode(' ', $this->getTelegramType());
				if(is_array($t_type)) {
					if(in_array('all', $t_type)) {
						$logger->pushHandler($telegramLogger);
						$telegram_send = true;
					} elseif(in_array($type, $t_type)) {
						$logger->pushHandler($telegramLogger);
						$telegram_send = true;
					}
				}
			}

			switch($type) {
				case 'error':
					$logger->error($function_name, $log_message);
					break;

				case 'info':
					$logger->info($function_name, $log_message);
					break;

				case 'notice':
					$logger->notice($function_name, $log_message);
					break;

				case 'warn':
				case 'warning':
					$logger->warning($function_name, $log_message);
					break;

				case 'crit':
				case 'critical':
					$logger->critical($function_name, $log_message);
					break;

				case 'alert':
					$logger->alert($function_name, $log_message);
					break;

				case 'emergency':
					$logger->emergency($function_name, $log_message);
					break;

				case 'Debug':
				case 'debug':
				default:
					$logger->debug($function_name, $log_message);
			}

		}
	}

	/**
	 * @return bool
	 */
	public function getLogs()
	: bool {
		return (bool)$this->logs;
	}

	/**
	 * @param bool|int $logs
	 */
	public function setLogs(?bool $logs)
	: void {
		$this->logs = (bool)$logs;
	}

	/**
	 * Устанавливает регулятор для отправки
	 *
	 * @param bool $telegram_send
	 */
	public function setTelegramSend(bool $telegram_send = false)
	: void {
		$this->telegram_send = $telegram_send;
	}

	/**
	 * @return bool
	 */
	public function isTelegramSend()
	: bool {
		return $this->telegram_send;
	}

	/**
	 * @param int|null|string  $telegram_channel
	 */
	public function setTelegramChannel($telegram_channel)
	: void {
		$this->telegram_channel = $telegram_channel;
	}

	/**
	 * @return int|null
	 */
	public function getTelegramChannel()
	: ?int {
		return $this->telegram_channel;
	}

	/**
	 * @param string|null $telegram_bot
	 */
	public function setTelegramBot(?string $telegram_bot)
	: void {
		$this->telegram_bot = $telegram_bot;
	}

	/**
	 * @return string|null
	 */
	public function getTelegramBot()
	: ?string {
		return $this->telegram_bot;
	}

	/**
	 * @param string|null $telegram_type
	 */
	public function setTelegramType(?string $telegram_type)
	: void {
		$this->telegram_type = $telegram_type;
	}

	/**
	 * @return string|null
	 */
	public function getTelegramType()
	: ?string {
		return $this->telegram_type;
	}



}