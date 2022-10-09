<?php

	if (!class_exists('Analog\Analog') || !class_exists('DataLoader')) {
		require_once DLEPlugins::Check(ENGINE_DIR.'/inc/maharder/_includes/extras/paths.php');
	}

	abstract class LogGenerator {

		/**
		 * Регулятор логирования системы
		 *
		 * @var bool
		 */
		protected static $logs;

		/**
		 * Регулятор отправки логов в телеграм канал
		 * По умолчанию - выключен
		 *
		 * @var bool
		 */
		protected static $telegram_send = false;
		/**
		 * ID канала, куда будут отправляться логи
		 *
		 * @var int|string|null
		 */
		protected static $telegram_channel = null;
		/**
		 * API телеграм бота, который будет отправлять логи
		 *
		 * @var string|null
		 */
		protected static $telegram_bot = null;
		/**
		 * Тип логов, которые будут отправлены в телеграм
		 *
		 * @var string|null
		 */
		protected static $telegram_type = null;
		/**
		 * Сохранять логи в базе данных
		 *
		 * @var bool
		 */
		protected static $db_logs;
		/**
		 * @var bool
		 */
		protected static $firephp;
		/**
		 * @var bool
		 */
		protected static $chrome_logger;
		/**
		 * @var bool
		 */
		protected static $console_logger;

		/**
		 * Генерация лог-файлов, если по какой-то причине произошла ошибка во время исполнения функционала
		 *
		 * @version 2.0.9
		 *
		 * @param    string    $function_name
		 * @param              $message
		 * @param    string    $type
		 * @param    string    $service
		 */
		public static function generate_log(string $service, string $function_name, $message, string $type = 'error'): void {
			if (self::getLogs()) {
				$root_dir            = dirname(__DIR__, 2);
				$date                = date('Y-m-d d.m.Y H:i');
				$concurrentDirectory = $root_dir.'/_logs/'.$service.'/'.$function_name;

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
				$file = $concurrentDirectory.'/'.$type.'.log';

				$log_message = [
					'plugin'        => $service,
					'function_name' => $function_name,
					'datetime'      => $date,
					'message'       => $message,
				];

				switch ($type) {
					default:
					case 'error':
						$log_level = Analog::ERROR;
						break;

					case 'info':
						$log_level = Analog::INFO;
						break;

					case 'notice':
						$log_level = Analog::NOTICE;
						break;

					case 'warn':
					case 'warning':
						$log_level = Analog::WARNING;
						break;

					case 'crit':
					case 'critical':
						$log_level = Analog::CRITICAL;
						break;

					case 'alert':
						$log_level = Analog::ALERT;
						break;

					case 'urgent':
					case 'emergency':
						$log_level = Analog::URGENT;
						break;

					case 'Debug':
					case 'debug':
						$log_level = Analog::DEBUG;
						break;
				}

				self::fileLog($file, $log_message, $log_level);
				self::chromeLog($log_message, $log_level);
				self::fireLog($log_message, $log_level);
				self::consoleLog($log_message, $log_level);
				self::telegramLog($log_message, $type);
				self::dbLog($log_message, $type);
			}
		}

		/**
		 * @param $file
		 * @param $message
		 * @param $level
		 *
		 * @return void
		 */
		private static function fileLog($file, $message, $level) {
			if(self::getLogs()) {
				Analog::handler(Analog\Handler\File::init($file));
				Analog::log(serialize($message), $level);
			}
		}

		/**
		 * @param $message
		 * @param $level
		 *
		 * @return void
		 */
		private static function chromeLog($message, $level) {
			if (self::isChromeLogger()) {
				Analog::handler(Analog\Handler\ChromeLogger::init());
				Analog::log($message, $level);
			}
		}

		/**
		 * @param $message
		 * @param $level
		 *
		 * @return void
		 */
		private static function fireLog($message, $level) {
			if(self::isFirephp()) {
				Analog::handler(Analog\Handler\FirePHP::init());
				Analog::log(serialize($message), $level);
			}
		}

		/**
		 * @param $message
		 * @param $level
		 *
		 * @return void
		 */
		private static function consoleLog($message, $level) {
			if (self::isConsoleLogger()) {
				Analog::handler(Analog\Handler\EchoConsole::init());
				Analog::log(serialize($message), $level);
			}
		}

		/**
		 * @version 2.0.9
		 * @since   2.0.9
		 *
		 * @param    array     $message
		 * @param    string    $type
		 *
		 * @return void
		 */
		private static function telegramLog(array $message, string $type) {
			if (self::isTelegramSend()) {
				$t_type = explode(' ', self::getTelegramType());
				$send   = false;
				if (is_array($t_type)) {
					if (in_array('all', $t_type)) {
						$send = true;
					} elseif (in_array($type, $t_type)) {
						$send = true;
					}
				}

				if ($send) {
					$type_descr = self::getAllowedType($type);

					$tg_message = <<<HTML
<b>Тип</b>: {$type_descr}<br>
<b>Время</b>: {$message['datetime']}<br>
<b>Плагин</b>: {$message['plugin']}<br>
<b>Функция</b>: {$message['function_name']}<br>
<b>Описание</b>: <code>{$message['message']}</code>
HTML;
					$bot        = self::getTelegramBot();
					$channel    = str_replace('chat_id=%40', 'chat_id=@', self::getTelegramChannel());
					$tg_message = urlencode($tg_message);
					$link       = "https://api.telegram.org/bot/{$bot}/sendMessage?parse_mode=HTML&chat_id={$channel}&text={$tg_message}";

					if (!file_get_contents($link)) {
						$send_to_tg = self::isTelegramSend();
						self::setTelegramSend(false);
						LogGenerator::generate_log('LogGenerator', 'telegramLog', [file_get_contents($link), $link]);
						self::setTelegramSend($send_to_tg);
					}
				}
			}
		}

		/**
		 * Отправка сообщений логов в базу данных
		 *
		 * @param    array     $message Массив с данными сообщения
		 * @param    string    $type Тип логов
		 *
		 * @return void
		 */
		private static function dbLog(array $message, string $type) {
			if (self::isDbLogs()) {
				$log = new MhLog();

				$m = '';

				if (is_array($message['message'])) {
					foreach ($message['message'] as $n => $v) {
						if (isset($message['message'][$n])) {
							$m .= "<b>{$n}</b>: {$v}";
						} else {
							$m .= "{$v}";
						}
						$m .= '<br />';
					}
				} else {
					$m .= $message['message'];
				}

				$log->create([
					'type' => $type,
					'plugin' => $message['plugin'],
					'fn_name' => $message['function_name'],
					'time' => $message['datetime'],
					'message' => br2nl(htmlspecialchars($m))
				]);
			}
		}

		/**
		 * @return bool
		 */
		public static function getLogs(): bool {
			if (self::$logs === null) {
				$config = DataLoader::getConfig('maharder');
				self::setLogs(isset($config['logs']) ? $config['logs'] : false);
			}

			return self::$logs;
		}

		/**
		 * @param    bool|int    $logs
		 */
		public static function setLogs($logs): void {
			self::$logs = (bool)$logs;
		}

		/**
		 * Устанавливает регулятор для отправки
		 *
		 * @param    bool    $telegram_send
		 */
		public static function setTelegramSend(bool $telegram_send = false): void {
			self::$telegram_send = $telegram_send;
		}

		/**
		 * @return bool
		 */
		public static function isTelegramSend(): bool {
			if (!self::$telegram_send && self::getLogs()) {
				$config = DataLoader::getConfig('maharder');
				self::setTelegramSend(isset($config['logs_telegram']) ? $config['logs_telegram'] : false);
			} else self::setTelegramSend(false);
			return self::$telegram_send;
		}

		/**
		 * @param    int|null|string    $telegram_channel
		 */
		public static function setTelegramChannel($telegram_channel): void {
			self::$telegram_channel = $telegram_channel;
		}

		/**
		 * @return int|null
		 */
		public static function getTelegramChannel(): int {
			return self::$telegram_channel;
		}

		/**
		 * @param    string|null    $telegram_bot
		 */
		public static function setTelegramBot(?string $telegram_bot): void {
			self::$telegram_bot = $telegram_bot;
		}

		/**
		 * @return string|null
		 */
		public static function getTelegramBot(): string {
			return self::$telegram_bot;
		}

		/**
		 * @param    string|null    $telegram_type
		 */
		public static function setTelegramType(?string $telegram_type): void {
			self::$telegram_type = $telegram_type;
		}

		/**
		 * @return string|null
		 */
		public static function getTelegramType(): string {
			return self::$telegram_type ?: 'all';
		}

		/**
		 * @version 2.0.9
		 * @since   2.0.9
		 * @return array
		 */
		public static function getAllowedTypes(): array {
			return [
				'all'     => __('mhadmin', 'Все типы ошибок'),
				'error'     => __('mhadmin', 'Ошибка'),
				'info'      => __('mhadmin', 'Информация'),
				'notice'    => __('mhadmin', 'Уведомление / К справке'),
				'warning'   => __('mhadmin', 'Предупреждение'),
				'critical'  => __('mhadmin', 'Критическая ошибка'),
				'debug'     => __('mhadmin', 'Отладка'),
				'warn'      => self::getAllowedType('warning'),
				'crit'      => self::getAllowedType('critical'),
				'urgent'    => __('mhadmin', 'Требует срочного решения'),
				'emergency' => self::getAllowedType('urgent'),
			];
		}

		/**
		 * @since 2.0.9
		 *
		 * @param $type
		 *
		 * @return string
		 */
		public static function getAllowedType($type): string {
			return self::getAllowedTypes()[$type];
		}

		/**
		 * @param    bool    $db_logs
		 */
		public static function setDbLogs(bool $db_logs): void {
			self::$db_logs = $db_logs;
		}

		/**
		 * @return bool
		 */
		public static function isDbLogs(): bool {
			if (!self::$db_logs && self::getLogs()) {
				$config = DataLoader::getConfig('maharder');
				self::setDbLogs(isset($config['logs_db']) ? $config['logs_db'] : false);
			} else self::setDbLogs(false);
			return self::$db_logs;
		}

		/**
		 * @param    bool    $firephp
		 */
		public static function setFirephp(bool $firephp): void {
			self::$firephp = $firephp;
		}

		/**
		 * @return bool
		 */
		public static function isFirephp(): bool {
			if (!self::$firephp && self::getLogs()) {
				$config = DataLoader::getConfig('maharder');
				self::setFirephp(isset($config['logs_firephp']) ? $config['logs_firephp'] : false);
			} else self::setFirephp(false);

			return self::$firephp;
		}

		/**
		 * @param    bool    $chrome_logger
		 */
		public static function setChromeLogger(bool $chrome_logger): void {
			self::$chrome_logger = $chrome_logger;
		}

		/**
		 * @return bool
		 */
		public static function isChromeLogger(): bool {
			if (!self::$chrome_logger && self::getLogs()) {
				$config = DataLoader::getConfig('maharder');
				self::setChromeLogger(isset($config['logs_chrome']) ? $config['logs_chrome'] : false);
			} else self::setChromeLogger(false);
			return self::$chrome_logger;
		}

		/**
		 * @param    bool    $console_logger
		 */
		public static function setConsoleLogger(bool $console_logger): void {
			self::$console_logger = $console_logger;
		}

		/**
		 * @return bool
		 */
		public static function isConsoleLogger(): bool {
			if (!self::$console_logger && self::getLogs()) {
				$config = DataLoader::getConfig('maharder');
				self::setConsoleLogger(isset($config['logs_console']) ? $config['logs_console'] : false);
			} else self::setConsoleLogger(false);
			return self::$console_logger;
		}

	}