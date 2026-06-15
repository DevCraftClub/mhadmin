<?php
//===============================================================
// Файл: LogGenerator.php                                       =
// Путь: devcraft/src/classes/Logging/LogGenerator.php          =
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

use Analog;
use Throwable;
use DateTimeImmutable;
use Analog\Handler\File;
use InvalidArgumentException;
use DevCraft\Core\Application;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Config\DevCraftConfig;
use DevCraft\Modules\Admin\Models\LogRecord;

/**
 * Центральный генератор логов: файлы, Telegram и база данных.
 *
 * Портировано из LogGenerator (mhadmin).
 *
 * @package    DevCraft
 * @since      170.2.10
 * @subpackage Core.Logging
 */
final class LogGenerator {

	/**
	 * Флаг завершённой инициализации настроек логирования.
	 *
	 * @since 173.3.0
	 *
	 * @var bool
	 */
	private static bool $initialized = false;

	/**
	 * Регулятор включения логирования системы.
	 *
	 * @since 173.3.0
	 *
	 * @var bool
	 */
	private static bool $logs = false;

	/**
	 * Регулятор отправки логов в Telegram-канал.
	 *
	 * @since 173.3.0
	 *
	 * @var bool
	 */
	private static bool $telegram_send = false;

	/**
	 * Идентификатор или имя Telegram-канала для логов.
	 *
	 * @since 173.3.0
	 *
	 * @var int|string|null
	 */
	private static string|int|null $telegram_channel = NULL;

	/**
	 * API-токен Telegram-бота для отправки логов.
	 *
	 * @since 173.3.0
	 *
	 * @var string|null
	 */
	private static ?string $telegram_bot = NULL;

	/**
	 * Типы логов, допустимые для отправки в Telegram.
	 *
	 * @since 173.3.0
	 *
	 * @var string|null
	 */
	private static ?string $telegram_type = NULL;

	/**
	 * Флаг сохранения логов в базе данных.
	 *
	 * @since 173.3.0
	 *
	 * @var bool
	 */
	private static bool $db_logs = false;

	/**
	 * Инициализирует настройки логирования из конфигурации DevCraft.
	 *
	 * @since 173.3.0
	 *
	 * @example
	 *     LogGenerator::init();
	 */
	public static function init(): void {
		if(self::$initialized) {
			return;
		}

		$settings = DevCraftConfig::raw();
		self::setLogs(array_key_exists('logs', $settings) && $settings['logs']);
		self::setTelegramType($settings['logs_telegram_type'] ?? NULL);
		self::setTelegramBot($settings['logs_telegram_api'] ?? NULL);
		self::setTelegramChannel($settings['logs_telegram_channel'] ?? NULL);
		self::setTelegramSend(array_key_exists('logs_telegram', $settings) && $settings['logs_telegram']);
		self::setDbLogs(array_key_exists('logs_db', $settings) && $settings['logs_db']);
		self::$initialized = true;
	}

	/**
	 * Создаёт контекстный логгер для указанного модуля.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $module  Имя модуля или сервиса.
	 *
	 * @return ContextLogger Экземпляр логгера с привязанным контекстом.
	 *
	 * @example
	 *     $logger = LogGenerator::for('Admin');
	 *     $logger->log('Сообщение об ошибке', 'error');
	 */
	public static function for(string $module): ContextLogger {
		if($module === '') {
			throw new InvalidArgumentException('Модуль не может быть пустым');
		}

		return new ContextLogger($module);
	}

	/**
	 * Проверяет, включён ли режим отладочного логирования.
	 *
	 * @since 200.4.0
	 *
	 * @return bool true при активной сессии dc_debug или настройке debug.
	 *
	 * @example
	 *     $debug = LogGenerator::isDebugEnabled();
	 */
	public static function isDebugEnabled(): bool {
		if(isset($_SESSION['dc_debug']) && $_SESSION['dc_debug'] === true) {
			return true;
		}

		return (bool) (DevCraftConfig::raw()['debug'] ?? false);
	}

	/**
	 * Записывает отладочное сообщение в error_log и опционально в каналы логирования.
	 *
	 * @since 200.4.0
	 *
	 * @param   string                $channel  Имя канала или модуля.
	 * @param   mixed                 $message  Текст или структура сообщения.
	 * @param   array<string, mixed>  $context  Дополнительный контекст.
	 *
	 * @example
	 *     LogGenerator::debug('Router', 'Маршрут не найден', ['path' => '/admin']);
	 */
	public static function debug(string $channel, mixed $message, array $context = []): void {
		if(!self::isDebugEnabled()) {
			return;
		}

		$payload = [
			'message' => $message,
			'context' => $context,
		];

		error_log(sprintf('[DevCraft:%s] %s', $channel, self::encodeDebugPayload($message, $context)));
		self::persistDebugLog($channel, self::resolveCallerContext(), $payload);
	}

	/**
	 * Генерирует лог при ошибке или другой значимой ситуации.
	 *
	 * @since 170.2.10
	 *
	 * @param   string  $plugin        Название плагина или модуля.
	 * @param   string  $functionName  Имя функции или метода-источника.
	 * @param   mixed   $message       Сообщение о событии.
	 * @param   string  $type          Тип события (error, info, warn и т. д.).
	 *
	 * @example
	 *     LogGenerator::dispatchLog('Translation', 'loadDictionary', 'Файл не найден', 'warn');
	 */
	public static function dispatchLog(
		string $plugin,
		string $functionName,
		mixed  $message,
		string $type = 'error',
	): void {
		self::init();

		if(!self::isLoggingEnabled()) {
			return;
		}

		if($plugin === '' || $functionName === '') {
			return;
		}

		$dateTime      = self::eventTime()->format('Y-m-d H:i');
		$separatorPos  = strrpos($functionName, '::');
		$methodSegment = $separatorPos !== false? substr($functionName, $separatorPos + 2) : $functionName;
		$logDirectory  = Paths::logs() . "/{$plugin}/{$methodSegment}";

		if(!self::createLogDirectory($logDirectory)) {
			self::fileLog(
				Paths::logs() . '/LogGenerator/bootstrap/error.log',
				[
					'plugin'        => $plugin,
					'function_name' => $functionName,
					'datetime'      => $dateTime,
					'message'       => self::errorNotificationText($plugin, $functionName, $type, $dateTime, $message),
				],
				Analog::ERROR,
			);

			return;
		}

		$logFile    = "{$logDirectory}/{$type}.log";
		$logMessage = [
			'plugin'        => $plugin,
			'function_name' => $functionName,
			'datetime'      => $dateTime,
			'message'       => $message,
		];

		self::fileLog($logFile, $logMessage, self::logLevelForType($type));
		self::telegramLog($logMessage, $type);
		self::dbLog($logMessage, $type);
	}

	/**
	 * Определяет имя вызывающего метода по стеку вызовов.
	 *
	 * @since 200.4.0
	 *
	 * @param   list<string>  $skipClasses  Классы, которые следует пропустить.
	 *
	 * @return string Строка вида ClassName::method или file::closure.
	 *
	 * @example
	 *     $caller = LogGenerator::resolveCallerContext();
	 */
	public static function resolveCallerContext(array $skipClasses = []): string {
		$trace       = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$skipClasses = array_merge(
			[
				self::class,
				ContextLogger::class,
			],
			$skipClasses,
		);

		foreach($trace as $frame) {
			$class    = $frame['class'] ?? NULL;
			$function = $frame['function'] ?? 'unknown';

			if($class !== NULL && in_array($class, $skipClasses, true)) {
				continue;
			}

			if($class !== NULL) {
				$shortClass = basename(str_replace('\\', '/', $class));

				return "{$shortClass}::{$function}";
			}

			if(str_contains($function, '{closure}')) {
				$file = basename((string) ($frame['file'] ?? 'unknown'));

				return "{$file}::{closure}";
			}

			if($function !== '') {
				return basename(str_replace('\\', '/', $function));
			}

			return 'global::unknown';
		}

		return 'unknown::unknown';
	}

	/**
	 * Проверяет, включено ли логирование в файлы и каналы.
	 *
	 * @since 173.3.0
	 *
	 * @return bool true, если логирование активно.
	 *
	 * @example
	 *     $enabled = LogGenerator::isLoggingEnabled();
	 */
	public static function isLoggingEnabled(): bool {
		self::init();

		return self::$logs;
	}

	/**
	 * Устанавливает состояние логирования.
	 *
	 * @since 173.3.0
	 *
	 * @param   bool|int  $logs  Индикатор включения логирования.
	 *
	 * @example
	 *     LogGenerator::setLogs(true);
	 */
	public static function setLogs(bool|int $logs): void {
		self::$logs = (bool) $logs;
	}

	/**
	 * Устанавливает флаг отправки логов через Telegram.
	 *
	 * @since 173.3.0
	 *
	 * @param   bool  $telegram_send  true — включить отправку.
	 *
	 * @example
	 *     LogGenerator::setTelegramSend(true);
	 */
	public static function setTelegramSend(bool $telegram_send = false): void {
		self::$telegram_send = $telegram_send;
	}

	/**
	 * Проверяет, включена ли отправка логов через Telegram.
	 *
	 * @since 173.3.0
	 *
	 * @return bool true при активной отправке и включённом логировании.
	 *
	 * @example
	 *     $send = LogGenerator::isTelegramSend();
	 */
	public static function isTelegramSend(): bool {
		self::init();

		return self::$telegram_send && self::isLoggingEnabled();
	}

	/**
	 * Устанавливает идентификатор Telegram-канала.
	 *
	 * @since 173.3.0
	 *
	 * @param   int|string|null  $telegram_channel  ID, имя канала или null.
	 *
	 * @example
	 *     LogGenerator::setTelegramChannel('@devcraft_logs');
	 */
	public static function setTelegramChannel(int|string|null $telegram_channel): void {
		self::$telegram_channel = $telegram_channel;
	}

	/**
	 * Возвращает идентификатор Telegram-канала.
	 *
	 * @since 173.3.0
	 *
	 * @return int|string|null ID или имя канала.
	 *
	 * @example
	 *     $channel = LogGenerator::telegramChannel();
	 */
	public static function telegramChannel(): int|string|null {
		return self::$telegram_channel;
	}

	/**
	 * Устанавливает токен Telegram-бота.
	 *
	 * @since 173.3.0
	 *
	 * @param   string|null  $telegram_bot  API-токен или null.
	 *
	 * @example
	 *     LogGenerator::setTelegramBot('123456:ABC-DEF');
	 */
	public static function setTelegramBot(?string $telegram_bot): void {
		self::$telegram_bot = $telegram_bot;
	}

	/**
	 * Возвращает токен Telegram-бота.
	 *
	 * @since 173.3.0
	 *
	 * @return string Токен бота или пустая строка.
	 *
	 * @example
	 *     $token = LogGenerator::telegramBot();
	 */
	public static function telegramBot(): string {
		return (string) self::$telegram_bot;
	}

	/**
	 * Устанавливает фильтр типов логов для Telegram.
	 *
	 * @since 173.3.0
	 *
	 * @param   string|null  $telegram_type  Строка типов через пробел или null.
	 *
	 * @example
	 *     LogGenerator::setTelegramType('error critical');
	 */
	public static function setTelegramType(?string $telegram_type): void {
		self::$telegram_type = $telegram_type;
	}

	/**
	 * Возвращает фильтр типов логов для Telegram.
	 *
	 * @since 173.3.0
	 *
	 * @return string Строка типов; по умолчанию all.
	 *
	 * @example
	 *     $types = LogGenerator::telegramType();
	 */
	public static function telegramType(): string {
		return self::$telegram_type? : 'all';
	}

	/**
	 * Возвращает массив допустимых типов сообщений с локализованными описаниями.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, string> Ключ типа => описание.
	 *
	 * @example
	 *     $types = LogGenerator::allowedTypes();
	 */
	public static function allowedTypes(): array {
		$baseTypes = [
			'all'      => __('Все типы ошибок'),
			'error'    => __('Ошибка'),
			'info'     => __('Информация'),
			'notice'   => __('Уведомление / К справке'),
			'warning'  => __('Предупреждение'),
			'critical' => __('Критическая ошибка'),
			'debug'    => __('Отладка'),
			'urgent'   => __('Требует срочного решения'),
		];

		$baseTypes['warn']      = $baseTypes['warning'];
		$baseTypes['crit']      = $baseTypes['critical'];
		$baseTypes['emergency'] = $baseTypes['urgent'];

		return $baseTypes;
	}

	/**
	 * Возвращает локализованное описание типа лога.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $type  Ключ типа из allowedTypes().
	 *
	 * @return string Описание типа или исходный ключ.
	 *
	 * @example
	 *     $label = LogGenerator::allowedType('critical');
	 */
	public static function allowedType(string $type): string {
		return self::allowedTypes()[$type] ?? $type;
	}

	/**
	 * Устанавливает логирование операций в базу данных.
	 *
	 * @since 173.3.0
	 *
	 * @param   bool  $db_logs  true — сохранять логи в БД.
	 *
	 * @example
	 *     LogGenerator::setDbLogs(true);
	 */
	public static function setDbLogs(bool $db_logs): void {
		self::$db_logs = $db_logs;
	}

	/**
	 * Проверяет, включено ли логирование в базу данных.
	 *
	 * @since 173.3.0
	 *
	 * @return bool true, если логи пишутся в БД.
	 *
	 * @example
	 *     $db = LogGenerator::isDbLogs();
	 */
	public static function isDbLogs(): bool {
		self::init();

		return self::$db_logs;
	}

	/**
	 * Сопоставляет строковый тип лога с уровнем Analog.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $type  Тип события.
	 *
	 * @return int Константа уровня Analog.
	 */
	private static function logLevelForType(string $type): int {
		return match (strtolower($type)) {
			'info'                => Analog::INFO,
			'notice'              => Analog::NOTICE,
			'warn', 'warning'     => Analog::WARNING,
			'crit', 'critical'    => Analog::CRITICAL,
			'alert'               => Analog::ALERT,
			'urgent', 'emergency' => Analog::URGENT,
			'debug'               => Analog::DEBUG,
			default               => Analog::ERROR,
		};
	}

	/**
	 * Создаёт директорию для логов, если она ещё не существует.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $path  Абсолютный путь к каталогу.
	 *
	 * @return bool true при успешном создании или существующей директории.
	 */
	private static function createLogDirectory(string $path): bool {
		return is_dir($path) || mkdir($path, 0755, true);
	}

	/**
	 * Формирует текстовое уведомление об ошибке создания каталога логов.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $service       Название модуля.
	 * @param   string  $functionName  Имя функции.
	 * @param   string  $type          Тип события.
	 * @param   string  $dateTime      Дата и время.
	 * @param   mixed   $message       Сообщение об ошибке.
	 *
	 * @return string Многострочный текст уведомления.
	 */
	private static function errorNotificationText(
		string $service,
		string $functionName,
		string $type,
		string $dateTime,
		mixed  $message,
	): string {
		$fields = [
			__('Уведомление')  => $type,
			__('Модуль')       => $service,
			__('Функция')      => $functionName,
			__('Дата и время') => $dateTime,
			__('Ошибка')       => $message,
		];

		return implode(
			"\n",
			array_map(
				static fn(string $key, mixed $value): string => "{$key}: " . self::normalizeLogValue($value),
				array_keys($fields),
				array_values($fields),
			),
		);
	}

	/**
	 * Рекурсивно заменяет объекты CURLFile на массивы для сериализации.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed  $message  Исходное значение сообщения.
	 *
	 * @return mixed Значение без CURLFile-объектов.
	 */
	private static function preventCurlFile(mixed $message): mixed {
		if(is_array($message)) {
			foreach($message as $idx => $mess) {
				if($mess instanceof \CURLFile) {
					$message[$idx] = (array) $mess;
				}

				if(is_array($mess)) {
					$message[$idx] = self::preventCurlFile($mess);
				}
			}
		}

		if($message instanceof \CURLFile) {
			return (array) $message;
		}

		return $message;
	}

	/**
	 * Сохраняет отладочный лог в файлы, Telegram и БД при включённых каналах.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $plugin        Имя модуля.
	 * @param   string  $functionName  Контекст вызывающего метода.
	 * @param   mixed   $message       Полезная нагрузка отладки.
	 */
	private static function persistDebugLog(string $plugin, string $functionName, mixed $message): void {
		self::init();

		if($plugin === '' || $functionName === '') {
			return;
		}

		$settings     = DevCraftConfig::raw();
		$fileLogging  = (bool) ($settings['logs'] ?? false);
		$dbLogging    = (bool) ($settings['logs_db'] ?? false);
		$telegramSend = (bool) ($settings['logs_telegram'] ?? false);

		if(!$fileLogging && !$dbLogging && !$telegramSend) {
			return;
		}

		$dateTime      = self::eventTime()->format('Y-m-d H:i');
		$separatorPos  = strrpos($functionName, '::');
		$methodSegment = $separatorPos !== false? substr($functionName, $separatorPos + 2) : $functionName;
		$logDirectory  = Paths::logs() . "/{$plugin}/{$methodSegment}";
		$type          = 'debug';
		$logMessage    = [
			'plugin'        => $plugin,
			'function_name' => $functionName,
			'datetime'      => $dateTime,
			'message'       => $message,
		];

		if($fileLogging) {
			if(!self::createLogDirectory($logDirectory)) {
				return;
			}

			$logFile = "{$logDirectory}/{$type}.log";
			self::writeFileLog($logFile, $logMessage, self::logLevelForType($type));
		}

		if($telegramSend) {
			self::telegramLog($logMessage, $type);
		}

		if($dbLogging) {
			self::dbLog($logMessage, $type);
		}
	}

	/**
	 * Кодирует отладочную полезную нагрузку в JSON для error_log.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed                 $message  Текст сообщения.
	 * @param   array<string, mixed>  $context  Контекст отладки.
	 *
	 * @return string JSON-строка или строковое представление message.
	 */
	private static function encodeDebugPayload(mixed $message, array $context): string {
		$encoded = json_encode(
			[
				'message' => $message,
				'context' => $context,
			],
			JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR,
		);

		return is_string($encoded)? $encoded : (string) $message;
	}

	/**
	 * Записывает сериализованное сообщение в файл через Analog.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $file     Путь к лог-файлу.
	 * @param   mixed   $message  Данные сообщения.
	 * @param   int     $level    Уровень Analog.
	 */
	private static function writeFileLog(string $file, mixed $message, int $level): void {
		$message = self::preventCurlFile($message);

		try {
			$directory = dirname($file);

			if($directory !== '' && !self::createLogDirectory($directory)) {
				return;
			}

			Analog::handler(File::init($file));
			Analog::log(serialize($message), $level);
		} catch(Throwable) {
			// Каталог логов недоступен для записи — не прерываем bootstrap.
		}
	}

	/**
	 * Логирует сообщение в файл при включённом логировании.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $file     Путь к лог-файлу.
	 * @param   mixed   $message  Данные сообщения.
	 * @param   int     $level    Уровень Analog.
	 */
	private static function fileLog(string $file, mixed $message, int $level): void {
		self::init();

		if(!self::isLoggingEnabled()) {
			return;
		}

		$message = self::preventCurlFile($message);
		self::writeFileLog($file, $message, $level);
	}

	/**
	 * Отправляет лог-сообщение в Telegram при включённых настройках.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $message  Структура лога (plugin, function_name, datetime, message).
	 * @param   string                $type     Тип события.
	 */
	private static function telegramLog(array $message, string $type): void {
		self::init();

		if(!self::isTelegramSend()) {
			return;
		}

		$allowedTypes = explode(' ', self::telegramType());

		if(!in_array('all', $allowedTypes, true) && !in_array($type, $allowedTypes, true)) {
			return;
		}

		$typeDescription = self::allowedType($type);
		$tgMessage       = [
			'<b>' . __('Тип') . "</b>: {$typeDescription}",
			'<b>' . __('Время') . "</b>: {$message['datetime']}",
			'<b>' . __('Плагин') . "</b>: {$message['plugin']}",
			'<b>' . __('Функция') . "</b>: {$message['function_name']}",
			'<b>' . __('Описание') . '</b>: <code>' . self::normalizeLogValue($message['message'] ?? '') . '</code>',
		];

		$botToken         = self::telegramBot();
		$chatId           = str_replace('chat_id=%40', 'chat_id=@', (string) self::telegramChannel());
		$tgMessageEncoded = urlencode(implode('<br>', $tgMessage));

		$url = sprintf(
			'https://api.telegram.org/bot%s/sendMessage?parse_mode=HTML&chat_id=%s&text=%s',
			$botToken,
			$chatId,
			$tgMessageEncoded,
		);

		$response = @file_get_contents($url);

		if($response === false) {
			$originalTelegramSendFlag = self::$telegram_send;
			self::setTelegramSend(false);
			self::dispatchLog('LogGenerator', 'telegramLog', ['response' => $response, 'url' => $url]);
			self::setTelegramSend($originalTelegramSendFlag);
		}
	}

	/**
	 * Сохраняет лог в базу данных через LogRecord.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $message  Структура лога.
	 * @param   string                $type     Тип события.
	 */
	private static function dbLog(array $message, string $type): void {
		self::init();

		if(!self::isDbLogs()) {
			return;
		}

		$formattedMessage = self::formatMessage($message['message'] ?? NULL);
		$parsedTime       = DateTimeImmutable::createFromFormat('Y-m-d H:i', (string) ($message['datetime'] ?? ''));

		$log           = new LogRecord();
		$log->log_type = $type;
		$log->plugin   = (string) ($message['plugin'] ?? 'unknown');
		$log->fn_name  = (string) ($message['function_name'] ?? 'unknown');
		$log->time     = $parsedTime !== false? $parsedTime : self::eventTime();
		$log->message  = br2nl(htmlspecialchars((string) $formattedMessage, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'));

		try {
			Application::instance()->database()->create($log);
		} catch(Throwable $e) {
			self::fileLog(
				Paths::logs() . '/LogGenerator/dbLog/error.log',
				[
					'plugin'        => 'LogGenerator',
					'function_name' => 'dbLog',
					'datetime'      => self::eventTime()->format('Y-m-d H:i'),
					'message'       => $e->getMessage(),
				],
				Analog::ERROR,
			);
		}
	}

	/**
	 * Приводит значение лога к строке: объекты — в массив, массивы — в JSON.
	 *
	 * @since 200.4.0
	 *
	 * @param   mixed  $value  Исходное значение.
	 *
	 * @return string Строковое представление.
	 */
	private static function normalizeLogValue(mixed $value): string {
		if($value === NULL) {
			return 'null';
		}

		if(is_bool($value)) {
			return $value? 'true' : 'false';
		}

		if(is_int($value) || is_float($value)) {
			return (string) $value;
		}

		if(is_string($value)) {
			return $value;
		}

		if($value instanceof Throwable) {
			return $value->getMessage();
		}

		if($value instanceof DateTimeImmutable) {
			return $value->format('Y-m-d H:i:s');
		}

		if($value instanceof \DateTimeInterface) {
			return $value->format('Y-m-d H:i:s');
		}

		if($value instanceof \JsonSerializable) {
			$value = $value->jsonSerialize();
		} elseif(is_object($value)) {
			if(method_exists($value, '__toString')) {
				return (string) $value;
			}

			$objectClass = $value::class;
			$value       = get_object_vars($value);

			if($value === []) {
				$value = ['class' => $objectClass];
			}
		}

		if(is_array($value)) {
			$value   = self::preventCurlFile($value);
			$encoded = json_encode(
				$value,
				JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR,
			);

			return is_string($encoded)? $encoded : '[]';
		}

		return (string) $value;
	}

	/**
	 * Форматирует сообщение лога для отображения в HTML или БД.
	 *
	 * @since 173.3.0
	 *
	 * @param   mixed  $message  Строка или массив данных.
	 *
	 * @return string Отформатированное сообщение.
	 */
	private static function formatMessage(mixed $message): string {
		if(!is_array($message)) {
			return self::normalizeLogValue($message);
		}

		$message = self::preventCurlFile($message);

		return implode(
			'<br />',
			array_map(
				static fn(string|int $key, mixed $value): string => is_string($key) || is_int($key)
					? '<b>' . $key . '</b>: ' . self::normalizeLogValue($value)
					: self::normalizeLogValue($value),
				array_keys($message),
				$message,
			),
		);
	}

	/**
	 * Возвращает текущее время события логирования.
	 *
	 * @since 200.4.0
	 *
	 * @return DateTimeImmutable Момент записи лога.
	 */
	private static function eventTime(): DateTimeImmutable {
		return new DateTimeImmutable();
	}

}
