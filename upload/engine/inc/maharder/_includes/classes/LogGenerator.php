<?php
//===============================================================
// Файл: LogGenerator.php                                       =
// Путь: engine/inc/maharder/_includes/classes/LogGenerator.php =
// Дата создания: 2024-03-19 13:53:11                           =
// Последнее изменение: 2024-03-19 13:53:10                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

use JetBrains\PhpStorm\ExpectedValues;

if (!class_exists('MhLog') || !class_exists('DataLoader')) {
	require_once (DLEPlugins::Check(MH_INCLUDES . '/extras/paths.php'));
}

/**
 * Абстрактный класс для управления процессом логирования. Класс включает в себя базовый функционал для записи логов
 * в файлы, базу данных и отправки уведомлений в Telegram.
 *
 * @since 170.2.10
 */
abstract class LogGenerator {

	/**
	 * Проверяет, сформирован класс или нет
	 *
	 * @since 173.3.0
	 * @var bool
	 */
	protected static bool $initialized = false;
	/**
	 * Регулятор логирования системы
	 *
	 * @var bool
	 */
	protected static bool $logs = false;

	/**
	 * Регулятор отправки логов в телеграм канал
	 * По умолчанию - выключен
	 *
	 * @var bool
	 */
	protected static bool $telegram_send = false;
	/**
	 * ID канала, куда будут отправляться логи
	 *
	 * @var int|string|null
	 */
	protected static string|int|null $telegram_channel = null;
	/**
	 * API телеграм бота, который будет отправлять логи
	 *
	 * @var string|null
	 */
	protected static ?string $telegram_bot = null;
	/**
	 * Тип логов, которые будут отправлены в телеграм
	 *
	 * @var string|null
	 */
	protected static ?string $telegram_type = null;
	/**
	 * Сохранять логи в базе данных
	 *
	 * @var bool
	 */
	protected static bool $db_logs = false;

	/**
	 * Инициализирует настройки для логирования. Функция настраивает параметры телеграм-логирования, базы данных для
	 * логов и другие параметры, используя данные конфигурации. Флаг `initialized` предотвращает повторную
	 * инициализацию.
	 *
	 * @since   173.3.0
	 * @return void
	 * @throws JsonException Исключение выбрасывается, если манипуляции с JSON в процессе получения данных конфигурации
	 * или установки параметров окажутся некорректными.
	 *
	 * @version 170.2.10
	 *
	 */
	public static function init(): void {
		if (!self::$initialized) {
			$mh_settings = DataManager::getConfig('maharder');
			self::setLogs(isset($mh_settings['logs']));
			self::setTelegramType($mh_settings["logs_telegram_type"]);
			self::setTelegramBot($mh_settings["logs_telegram_api"]);
			self::setTelegramChannel($mh_settings["logs_telegram_channel"]);
			self::setTelegramSend(isset($mh_settings["logs_telegram"]));
			self::setDbLogs(isset($mh_settings["logs_db"]));
			self::$initialized = true;
		}
	}

	/**
	 * Генерация лог-файлов при возникновении ошибки или другой значимой ситуации в процессе работы системы.
	 *
	 * Данная функция создаёт лог-файлы в зависимости от указанного типа ошибки, хранит подробную информацию о сервисе,
	 * функции, дате/времени и сообщении. Помимо записи в файл, опционально отправляются уведомления в Telegram и
	 * сохраняются логи в базу данных.
	 *
	 * @since 170.2.10
	 *
	 * @param string $functionName Название функции, вызвавшей логирование.
	 * @param mixed  $message      Сообщение, описывающее ошибку или событие.
	 * @param string $type         Тип события (например: 'error', 'info', 'debug' и т. д.). По умолчанию — 'error'.
	 *
	 * @param string $service      Название сервиса, к которому относится лог.
	 *
	 * @return void
	 *
	 * @throws JsonException|Throwable        Исключение, связанное с ошибками в JSON-конверсии (может быть выброшено
	 *                                        при выполнении Telegram-лога).
	 *
	 */
	public static function generateLog(
		string $service,
		string $functionName,
		mixed $message,
		#[ExpectedValues(values: ['info', 'notice', 'warn', 'warning', 'crit', 'critical', 'alert', 'urgent', 'emergency', 'debug' ])]
		string $type = 'error'): void {
		self::init();

		if (!self::getLogs()) {
			return; // Если логирование отключено, нет смысла продолжать
		}

		$rootDir      = dirname(__DIR__, 2);
		$dateTime     = (new DateTimeImmutable())->format('Y-m-d H:i');
		$logDirectory = "{$rootDir}/_logs/{$service}/{$functionName}";

		// Создание директории и проверка на её успешность
		if (!self::createLogDirectory($service, $functionName, $logDirectory)) {
			echo self::getErrorNotification($service, $functionName, $type, $dateTime, $message);
			return;
		}

		$logFile    = "{$logDirectory}/{$type}.log";
		$logMessage = [
			'plugin'        => $service,
			'function_name' => $functionName,
			'datetime'      => $dateTime,
			'message'       => $message,
		];

		// Определение уровня логирования
		$logLevel = match (strtolower($type)) {
			'info'                => Analog::INFO,
			'notice'              => Analog::NOTICE,
			'warn', 'warning'     => Analog::WARNING,
			'crit', 'critical'    => Analog::CRITICAL,
			'alert'               => Analog::ALERT,
			'urgent', 'emergency' => Analog::URGENT,
			'debug'               => Analog::DEBUG,
			default               => Analog::ERROR,
		};

		// Логирование в разные каналы
		self::fileLog($logFile, $logMessage, $logLevel);
		self::telegramLog($logMessage, $type);
		self::dbLog($logMessage, $type);
	}

	/**
	 * Создает директорию для логов с учетом указанного сервиса, модуля и пути.
	 *
	 * Функция вызывает метод {@see DataManager::createDir()} для создания директории,
	 * а также дополнительно проверяет существование директории через {@see is_dir()}.
	 *
	 * @param string $service Название сервиса, для которого создается директория.
	 * @param string $module  Название модуля.
	 * @param string $path    Абсолютный путь к создаваемой директории.
	 *
	 * @return bool Возвращает true, если директория была успешно создана или уже существует;
	 *              false, если создание директории завершилось ошибкой.
	 *
	 * @throws Throwable В случае ошибки при вызове {@see DataManager::createDir()}
	 *                   могут быть выброшены исключения, которые логируются в системе.
	 */
	private static function createLogDirectory(string $service, string $module, string $path): bool {
		return DataManager::createDir(service: $service, module: $module, _path: $path) || is_dir($path);
	}

	/**
	 * Генерирует HTML-уведомление об ошибке для указанного модуля и функции.
	 *
	 * Формирует содержимое уведомления с информацией о модуле, функции, типе ошибки, дате и времени, а также
	 * сообщением об ошибке.
	 *
	 * @param string $service      Название модуля, в котором произошла ошибка.
	 * @param string $functionName Название функции, вызвавшей ошибку.
	 * @param string $type         Тип ошибки (например, критическая, предупреждение и т.д.).
	 * @param string $dateTime     Дата и время возникновения ошибки в формате строки.
	 * @param mixed  $message      Сообщение об ошибке, переданное в функцию. Может быть строкой, массивом, объектом и
	 *                             т.д.
	 *
	 * @return string Возвращает сгенерированное HTML-уведомление с детализированной информацией об ошибке.
	 */
	private static function getErrorNotification(string $service, string $functionName, string $type, string $dateTime, mixed $message): string {
		$fields = [
			__('Уведомление') => $type,
			__('Модуль') => $service,
			__('Функция') => $functionName,
			__('Дата и время') => $dateTime,
			__('Ошибка') => $message
		];

		return implode('<br>', array_map(
			fn($key, $value) => "<b>{$key}</b>: {$value}",
			array_keys($fields),
			$fields
		));
	}

	/**
	 * Логирование сообщений в файл.
	 *
	 * Метод записывает переданное сообщение в указанный лог-файл, если включено логирование.
	 * Сообщение предварительно сериализуется перед записью.
	 *
	 * @param string $file    Путь к файлу, в который будет записано сообщение лога.
	 * @param mixed  $message Сообщение, которое необходимо записать в лог. Может быть любым типом данных.
	 * @param int    $level   Уровень логирования, соответствующий уровням Analog (например, INFO, ERROR и т.д.).
	 *
	 * @return void Функция ничего не возвращает.
	 *
	 * @throws JsonException Генерируется, если в процессе инициализации (инициализация через `self::init()`)
	 *                       возникает ошибка, связанная с обработкой JSON (например, при работе с конфигурацией
	 *                       DataManager::getConfig()).
	 */
	private static function fileLog(string $file, mixed $message, int $level): void {
		self::init();
		if (self::getLogs()) {
			Analog::handler(Analog\Handler\File::init($file));
			Analog::log(serialize($message), $level);
		}
	}

	/**
	 * Отправляет лог-сообщение в Telegram.
	 *
	 * Функция формирует и отправляет сообщение с логом в указанный Telegram-чат,
	 * проверяя настройки и типы логов, допустимые для отправки.
	 * Если отправка не удалась, сообщение об ошибке записывается в лог приложения.
	 *
	 * @param array  $message Ассоциативный массив с данными сообщения. Должны быть указаны следующие ключи:
	 *                        - 'datetime' (string): Дата и время события.
	 *                        - 'plugin' (string): Название плагина, вызвавшего функцию.
	 *                        - 'function_name' (string): Имя вызывающей функции.
	 *                        - 'message' (string): Текст описания события.
	 * @param string $type    Тип лога, например "error", "info". Используется для фильтрации типов сообщений.
	 *
	 * @return void Функция ничего не возвращает.
	 *
	 * @throws \RuntimeException В случае ошибки HTTP-запроса или сбоя при работе с Telegram API.
	 * @throws JsonException|Throwable При возникновении ошибок в процессе работы с JSON или других исключений.
	 */
	private static function telegramLog(array $message, string $type): void {
		self::init();

		// Проверяем отправку логов в Telegram
		if (!self::isTelegramSend()) {
			return;
		}

		// Получаем допустимые типы логов
		$allowedTypes = explode(' ', self::getTelegramType());
		if (!in_array('all', $allowedTypes, true) && !in_array($type, $allowedTypes, true)) {
			return;
		}

		// Формируем сообщение
		$typeDescription = self::getAllowedType($type);
		$tgMessage       = [
			"<b>" . __('Тип') . "</b>: $typeDescription",
			"<b>" . __('Время') . "</b>: {$message['datetime']}",
			"<b>" . __('Плагин') . "</b>: {$message['plugin']}",
			"<b>" . __('Функция') . "</b>: {$message['function_name']}",
			"<b>" . __('Описание') . "</b>: <code>{$message['message']}</code>",
		];

		// Собираем текст и кодируем
		$botToken         = self::getTelegramBot();
		$chatId           = str_replace('chat_id=%40', 'chat_id=@', self::getTelegramChannel());
		$tgMessageEncoded = urlencode(implode('<br>', $tgMessage));

		$url = sprintf(
			"https://api.telegram.org/bot%s/sendMessage?parse_mode=HTML&chat_id=%s&text=%s",
			$botToken,
			$chatId,
			$tgMessageEncoded
		);

		// Отправляем запрос
		$response = file_get_contents($url);
		if ($response === false) {
			// Логируем при ошибке
			$originalTelegramSendFlag = self::isTelegramSend();
			self::setTelegramSend(false); // Отключаем отправку
			self::generateLog('LogGenerator', 'telegramLog', ['response' => $response, 'url' => $url]);
			self::setTelegramSend($originalTelegramSendFlag); // Восстанавливаем состояние
		}
	}

	/**
	 * Логирует сообщение в базу данных, если включены настройки логирования в БД.
	 *
	 * Функция инициализирует настройки логирования, проверяет, включено ли логирование в базу данных,
	 * создает объект лога, форматирует сообщение и сохраняет его в базу данных.
	 *
	 * @param array  $message Ассоциативный массив с информацией о логе. Поддерживает следующие ключи:
	 *                        - 'message' (string): Основное сообщение для лога.
	 *                        - 'plugin' (string): Имя плагина, откуда отправлен лог. По умолчанию - 'unknown'.
	 *                        - 'function_name' (string): Имя функции, где возник лог. По умолчанию - 'unknown'.
	 *                        - 'datetime' (string): Дата и время логирования. Если не задано, используется текущее
	 *                        время.
	 * @param string $type    Тип лога, определяющий категорию сообщения.
	 *
	 * @return void
	 *
	 * @throws RuntimeException Если не удается сохранить лог в базу данных.
	 * @throws JsonException|Throwable При возникновении ошибок в процессе работы с JSON или других исключений.
	 */
	private static function dbLog(array $message, string $type): void {
		global $MHDB;

		self::init();

		if (!self::isDbLogs()) {
			return;
		}

		// Создаем экземпляр MhLog
		$log = new MhLog();

		// Форматируем сообщение
		$formattedMessage = self::formatMessage($message['message'] ?? '');

		// Устанавливаем свойства лога
		$log->setLogType($type);
		$log->setPlugin($message['plugin'] ?? 'unknown');
		$log->setFnName($message['function_name'] ?? 'unknown');
		$log->setTime(DateTimeImmutable::createFromFormat('Y-m-d H:i', $message['datetime']) ?? date('Y-m-d H:i:s'));
		$log->setMessage(br2nl(htmlspecialchars($formattedMessage)));

		// Сохраняем в базу
		$MHDB->create($log);
	}

	/**
	 * Форматирует сообщение для отображения в виде строки.
	 *
	 * Если сообщение передано в виде массива, функция преобразует его в строку,
	 * где каждый элемент массива будет представлен как отдельная строка,
	 * разделенная тегами `<br />`. В случае, если ключ является строкой, он
	 * будет выделен тегом `<b>`. Если сообщение уже является строкой, возвращает
	 * его без изменений.
	 *
	 * @param string|array $message Сообщение для форматирования. Может быть строкой
	 *                              или массивом. В случае массива, ключи могут быть
	 *                              строками или числовыми индексами.
	 *
	 * @return string Возвращает отформатированное сообщение в виде строки. Если
	 *                входной параметр — строка, возвращается без изменений,
	 *                если массив — объединяется в строку с использованием
	 *                тега `<br />`.
	 *
	 * @throws InvalidArgumentException Если входной параметр не является строкой
	 *                                  или массивом (сценарий невозможен в данной
	 *                                  реализации, но документация указывает на
	 *                                  строгие ожидания типов).
	 */
	private static function formatMessage(string|array $message): string {
		// Если сообщение является массивом, форматируем
		if (is_array($message)) {
			return implode(
				"<br />",
				array_map(
					fn($key, $value) => is_string($key)
						? "<b>{$key}</b>: {$value}"
						: $value,
					array_keys($message),
					$message
				)
			);
		}

		// Если сообщение строка, просто возвращаем
		return $message;
	}

	/**
	 * Получает текущий статус логирования.
	 *
	 * Метод инициализирует необходимые настройки через `self::init()`, если они еще не были установлены,
	 * и устанавливает значение `self::$logs`, если оно не было задано. Значение берется из настроек,
	 * полученных через `DataManager::getConfig('maharder')`.
	 *
	 * @return bool Возвращает текущее состояние логирования.
	 *
	 * @throws \RuntimeException|JsonException Если `DataManager::getConfig()` выбрасывает исключение (например, при
	 *                                         ошибке чтения конфигурации).
	 * @throws Exception Если возникнут ошибки во время инициализации (в методе init()).
	 */
	public static function getLogs(): bool {
		self::init();
		if (self::$logs === null) {
			$config = DataManager::getConfig('maharder');
			self::setLogs($config['logs'] ?? false);
		}

		return self::$logs;
	}

	/**
	 * Устанавливает состояние логирования.
	 *
	 * Если передано значение `true`, включается логирование. Если передано значение `false`, логирование выключается.
	 * Также поддерживается передача целочисленного значения, которое будет приведено к булевому типу.
	 *
	 * @see self::init() Метод для инициализации статических параметров.
	 *
	 * @param bool|int $logs Индикатор логирования: булево значение или целое число, которое будет приведено к bool.
	 *
	 * @return void Функция не возвращает значения.
	 *
	 * @throws \Exception Если возникнут ошибки во время инициализации (в методе init()).
	 *
	 */
	public static function setLogs(bool|int $logs): void {
		self::$logs = (bool)$logs;
	}

	/**
	 * Устанавливает флаг для отправки сообщений через Telegram.
	 *
	 * Этот метод изменяет состояние статического свойства `$telegram_send`,
	 * которое указывает, должны ли сообщения отправляться через Telegram.
	 *
	 * @param bool $telegram_send  Флаг, указывающий, нужно ли отправлять сообщения через Telegram.
	 *                             Значение по умолчанию — `false`.
	 *
	 * @return void Метод не возвращает значения.
	 */
	public static function setTelegramSend(bool $telegram_send = false): void {
		self::$telegram_send = $telegram_send;
	}

	/**
	 * Проверяет, включена ли отправка логов через Telegram.
	 *
	 * Если флаг `$telegram_send` еще не установлен (`false`) и есть логи (определяется методом `getLogs()`),
	 * функция загружает конфигурацию из `DataManager` и устанавливает флаг на основе значения опции `logs_telegram`.
	 * В противном случае отправка через Telegram отключается.
	 *
	 * @return bool Возвращает `true`, если отправка логов через Telegram включена, или `false`, если отключена.
	 *
	 * @throws RuntimeException|JsonException Если возникает ошибка при загрузке или обработке конфигурации из
	 *                                        DataManager.
	 */
	public static function isTelegramSend(): bool {
		if (!self::$telegram_send && self::getLogs()) {
			$config = DataManager::getConfig('maharder');
			self::setTelegramSend($config['logs_telegram'] ?? false);
		} else {
			self::setTelegramSend(false);
		}
		return self::$telegram_send;
	}

	/**
	 * Устанавливает идентификатор или имя канала Telegram.
	 *
	 * Эта функция задаёт значение статического свойства `$telegram_channel`, позволяя указать идентификатор
	 * канала, имя или установить значение в `null`, чтобы сбросить текущее состояние.
	 *
	 * @param int|string|null $telegram_channel Идентификатор канала Telegram (целое число),
	 *                                          имя канала (строка) или `null` для сброса значения.
	 *
	 * @return void
	 */
	public static function setTelegramChannel(int|string|null $telegram_channel): void {
		self::$telegram_channel = $telegram_channel;
	}

	/**
	 * Возвращает идентификатор Telegram-канала.
	 *
	 * Метод возвращает идентификатор канала в различных форматах:
	 * - Если канал настроен, он может быть представлен числом (целочисленным идентификатором) или строкой (например, в
	 * виде alias).
	 * - Если канал отсутствует, метод вернет null.
	 *
	 * @return int|string|null Возвращает идентификатор Telegram-канала в виде числа, строки или null, если канал не
	 *                         настроен.
	 */
	public static function getTelegramChannel(): int|string|null {
		return self::$telegram_channel;
	}

	/**
	 * Устанавливает идентификатор или токен Telegram-бота.
	 *
	 * Метод позволяет задать идентификатор или токен используемого Telegram-бота,
	 * если он используется в приложении. Для удаления текущего значения передайте `null`.
	 *
	 * @param string|null $telegram_bot Идентификатор или токен Telegram-бота.
	 *                                  Значение null сбрасывает текущую настройку.
	 *
	 * @return void
	 */
	public static function setTelegramBot(?string $telegram_bot): void {
		self::$telegram_bot = $telegram_bot;
	}

	/**
	 * Возвращает идентификатор Telegram-бота.
	 *
	 * Метод возвращает текущее значение идентификатора Telegram-бота,
	 * хранящегося в статическом свойстве `$telegram_bot`.
	 *
	 * @return string Идентификатор Telegram-бота.
	 * @throws \RuntimeException Если идентификатор Telegram-бота не установлен (равен null).
	 */
	public static function getTelegramBot(): string {
		return self::$telegram_bot;
	}

	/**
	 * Устанавливает тип Telegram для текущего экземпляра.
	 *
	 * @param string|null $telegram_type Тип Telegram или null, если значение нужно сбросить.
	 *
	 * @return void
	 */
	public static function setTelegramType(?string $telegram_type): void {
		self::$telegram_type = $telegram_type;
	}

	/**
	 * Возвращает тип Telegram в зависимости от установленного значения.
	 *
	 * Если свойство `$telegram_type` не задано (null или пустое значение),
	 * возвращает значение по умолчанию `'all'`.
	 *
	 * @return string Тип Telegram. Может возвращать либо значение `$telegram_type`,
	 * либо строку `'all'` в качестве значения по умолчанию.
	 */
	public static function getTelegramType(): string {
		return self::$telegram_type ?: 'all';
	}

	/**
	 * Возвращает массив доступных типов сообщений с их локализованными описаниями.
	 *
	 * Типы сообщений включают различные уровни, такие как ошибка, информация, предупреждение, и другие.
	 * Также добавляются сокращенные обозначения для некоторых ключевых типов, таких как "warn", "crit" или
	 * "emergency".
	 * Локализация осуществляется с использованием функции `__`.
	 *
	 * @since   173.3.0
	 *
	 * @return array Ассоциативный массив, где ключи — идентификаторы типов сообщений, а значения — локализованные
	 *               описания.
	 */
	public static function getAllowedTypes(): array {

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
	 * Возвращает описание разрешенного типа ошибки по его ключу.
	 *
	 * Перечень всех доступных типов с их описаниями
	 * задается методом `getAllowedTypes()`. Если указанный ключ `$type`
	 * отсутствует в списке, будет сгенерирована ошибка типа PHP `undefined index`.
	 *
	 * @since 173.3.0
	 *
	 * @param string $type Ключ типа ошибки, значение должно быть одним из ключей возвращаемых `getAllowedTypes()`.
	 *
	 * @return string Описание типа ошибки, соответствующее переданному ключу.
	 *
	 *                         `getAllowedTypes()`.
	 */
	public static function getAllowedType($type): string {
		return self::getAllowedTypes()[$type];
	}

	/**
	 * Устанавливает логирование работы с базой данных.
	 *
	 * @param bool $db_logs Флаг для включения (true) или отключения (false) логирования операций с базой данных.
	 *
	 * @return void
	 */
	public static function setDbLogs(bool $db_logs): void {
		self::$db_logs = $db_logs;
	}

	/**
	 * Проверяет, включены ли логирования в базе данных.
	 *
	 * Если состояние логов в базе данных еще не было установлено, метод загружает
	 * конфигурацию из DataManager и устанавливает значение на основании
	 * параметра `logs_db` в конфигурации. Если параметр отсутствует,
	 * логирование в БД считается выключенным по умолчанию.
	 *
	 * @return bool Возвращает `true`, если логирование в базе данных включено, иначе `false`.
	 *
	 * @throws RuntimeException|JsonException Если конфигурация приложения не может быть получена
	 * (зависит от реализации DataManager).
	 */
	public static function isDbLogs(): bool {
		if (self::$db_logs === null) {
			$config = DataManager::getConfig('maharder');
			self::setDbLogs($config['logs_db'] ?? false);
		}

		return self::$db_logs;
	}

}
