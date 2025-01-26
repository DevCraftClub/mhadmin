<?php

//===============================================================
// Файл: functions.php                                          =
// Путь: /engine/inc/maharder/_includes/extras/functions.php    =
// Дата создания: 2025-01-02 13:08:21                           =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2025               =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
// Соглашение о распространении и модификации:                  =
// https://devcraft.club/pages/licence-agreement/               =
//===============================================================

if (!function_exists('translate')) {
	/**
	 * Переводит заданную фразу с использованием модуля перевода.
	 *
	 * Если язык и путь для локалей не установлены в конфигурации, возвращает исходную фразу.
	 * В зависимости от переданных параметров, поддерживает как обрабатываемый,
	 * так и базовый перевод с использованием модулей `MhTranslation`.
	 * При возникновении ошибки логирует её и возвращает исходную фразу.
	 *
	 * @since   173.3.0
	 *
	 * @param string $phrase Фраза для перевода.
	 * @param array  $params Параметры для подстановки в строку перевода (опционально).
	 * @param int    $count  Количество для выбора формы множественного числа (опционально).
	 *
	 * @return string Переведённая строка.
	 *
	 * @throws Exception|Throwable Если возникает ошибка при работе с переводом.
	 *
	 * @version 173.3.0
	 *
	 * @see DataManager::getConfig() Используется для получения конфигурации.
	 * @see MhTranslation::setTranslator() Устанавливает текущий модуль для перевода.
	 * @see MhTranslation::getTranslation() Получает простой перевод фразы.
	 * @see MhTranslation::getTranslationWithParameters() Получает перевод с параметрами.
	 * @see MhTranslation::getTranslationPlural() Получает множественную форму перевода.
	 * @see MhTranslation::getTranslationPluralWithParameters() Получает множественный перевод с параметрами.
	 * @see LogGenerator::generateLog() Логирует ошибки при работе функции.
	 */
	function translate(string $phrase, array $params = [], int $count = 0): string {
		$mh = DataManager::getConfig('maharder');
		MhTranslation::setTranslator();
		if (!isset($mh['language']) && !isset($mh['locales_path'])) {
			return $phrase;
		}
		try {
			if ($count > 0) {
				if (count($params) == 0) {
					return MhTranslation::getTranslationPlural($phrase, $count);
				} else {
					return MhTranslation::getTranslationPluralWithParameters(
						$phrase,
						$count,
						$params
					);
				}
			} else {
				if (count($params) == 0) {
					return MhTranslation::getTranslation($phrase);
				} else {
					return MhTranslation::getTranslationWithParameters(
						$phrase,
						$params
					);
				}
			}
		} catch (Exception $e) {
			LogGenerator::generateLog('functions', 'translate', $e->getMessage());

			return $phrase;
		}
	}
}

if (!function_exists('__')) {
	/**
	 * Синоним функции перевода translate для упрощённого использования.
	 *
	 * Служит для вызова функции перевода текстовых строк с возможностью передачи параметров и обработки множественного числа.
	 *
	 * @version 173.3.0
	 * @param string $phrase Переводимая строка.
	 * @param array  $params Ассоциативный массив параметров для подстановки в строку.
	 * @param int    $count  Количество для обработки множественного числа (опционально).
	 *
	 * @return string Переведённая строка.
	 *
	 * @throws Throwable
	 * @since   173.3.0
	 * @see     translate()
	 * @see     DataManager::getConfig()
	 */
	function __(string $phrase, array $params = [], int $count = 0): string {
		return translate($phrase, $params, $count);
	}
}

if (!function_exists('dirToArray')) {
	/**
	 * Преобразует заданный путь к директории в массив, содержащий дерево папок и файлов.
	 *
	 * Эта функция позволяет получить структуру файловой системы в виде ассоциативного массива.
	 * Папки представлены в виде ключей, а файлы - в виде элементов массива.
	 * Также поддерживает возможность исключения определенных файлов и расширений.
	 *
	 * @param string $dir               Абсолютный путь к директории, структуру которой нужно обработать.
	 * @param array  $ignoredExtensions Список дополнительных файлов/расширений для исключения из результата (например: ['.log', '.tmp']).
	 *
	 * @return array Массив, представляющий собой дерево файловой структуры. Каждая директория содержит вложенные файлы/папки.
	 *
	 * @throws RuntimeException Если `scandir` не удается получить содержимое директории.
	 */
	function dirToArray(string $dir, array $ignoredExtensions = []): array {
		// Общий список игнорируемых файлов
		$defaultIgnored = ['.', '..', '.htaccess'];
		$ignoredItems = array_merge($defaultIgnored, $ignoredExtensions);

		// Приведение путей к стандартному формату
		$resolvedDir = str_replace(ENGINE_DIR, ROOT . DIRECTORY_SEPARATOR . 'engine', $dir);
		if (!is_dir($resolvedDir)) {
			return []; // Если директория не существует, возвращаем пустой массив
		}

		// Читаем содержимое директории
		$filesAndDirs = scandir($resolvedDir, SCANDIR_SORT_NONE);
		if ($filesAndDirs === false) {
			return []; // Ошибка чтения директории
		}

		// Переменная результата
		$result = [];

		foreach ($filesAndDirs as $item) {
			if (in_array($item, $ignoredItems, true)) {
				// Пропускаем элементы, указанные в игнорируемом списке
				continue;
			}

			$itemPath = $resolvedDir . DIRECTORY_SEPARATOR . $item;

			if (is_dir($itemPath)) {
				// Рекурсивно вызываем функцию для вложенных папок
				$result[$item] = dirToArray($itemPath, $ignoredExtensions);
			} else {
				// Добавляем только имя файла в результирующий массив
				$result[] = $item;
			}
		}

		return $result;
	}
}

if (!function_exists('br2nl')) {
	/**
	 * Преобразует теги `<br>` в заданный разделитель строк.
	 *
	 * Данная функция заменяет все теги `<br>` (включая различные его варианты, такие как `<br>`, `<br/>` и `<br />`)
	 * на указанный разделитель строк. Если переданный разделитель не входит в список допустимых
	 * значений, будет использован стандартный разделитель `PHP_EOL`.
	 *
	 * @param string $string    Входная строка, содержащая теги `<br>`, которые нужно заменить.
	 * @param string $separator Разделитель строк, который будет использоваться для замены `<br>` (по умолчанию: PHP_EOL).
	 *                          Допустимые значения: "\n", "\r", "\r\n", "\n\r", символы chr(30), chr(155), и PHP_EOL.
	 *                          Если переданный разделитель не соответствует этим значениям, будет выбран `PHP_EOL`.
	 *
	 * @return string Строка, в которой все `<br>` заменены на указанный разделитель.
	 *
	 * @throws \InvalidArgumentException Исключение выбрасывается, если входные данные недействительны или пустые.
	 */
	function br2nl(string $string, string $separator = PHP_EOL): string {
		// Validate and normalize the separator
		static $validSeparators = ["\n", "\r", "\r\n", "\n\r", chr(30), chr(155), PHP_EOL];
		$separator = in_array($separator, $validSeparators, true) ? $separator : PHP_EOL;

		// Replace `<br>` tags with the provided line separator
		return preg_replace('#<br\s*/?>#i', $separator, $string);
	}
}