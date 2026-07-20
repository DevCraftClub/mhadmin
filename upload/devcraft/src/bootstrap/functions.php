<?php
//===============================================================
// Файл: functions.php                                          =
// Путь: devcraft/src/bootstrap/functions.php                   =
// Последнее изменение: 2026-06-13 19:29:35                     =
// ==============================================================
// Автор: Maxim Harder <dev@devcraft.club> © 2024 - 2026        =
// Сайт: https://devcraft.club                                  =
// Телеграм: http://t.me/MaHarder                               =
// ==============================================================
// Менять на свой страх и риск!                                 =
// Код распространяется по лицензии MIT                         =
//===============================================================

/**
 * Глобальные вспомогательные функции DevCraft: перевод, обход директорий, замена br.
 *
 * @package    DevCraft
 * @since      200.4.0
 * @subpackage Bootstrap
 */

declare(strict_types=1);

use DevCraft\Core\I18n\Translation;
use DevCraft\Core\Support\DataManager;
use DevCraft\Core\Logging\LogGenerator;
use DevCraft\Core\Config\DevCraftConfig;

if(!function_exists('translate')) {
	/**
	 * Переводит заданную фразу с использованием модуля перевода.
	 *
	 * Если язык и путь для локалей не установлены в конфигурации, возвращает исходную фразу.
	 * В зависимости от переданных параметров поддерживает простой перевод, подстановку
	 * параметров и формы множественного числа. При ошибке логирует сообщение и возвращает
	 * исходную фразу.
	 *
	 * @since 173.3.0
	 *
	 * @see   DevCraftConfig::raw() Используется для получения конфигурации без загрузки схемы.
	 * @see   Translation::setTranslator() Устанавливает текущий модуль перевода.
	 * @see   Translation::getTranslation() Получает простой перевод фразы.
	 * @see   Translation::getTranslationWithParameters() Получает перевод с параметрами.
	 * @see   Translation::getTranslationPlural() Получает множественную форму перевода.
	 * @see   Translation::getTranslationPluralWithParameters() Получает множественный перевод с параметрами.
	 * @see   LogGenerator::for() Логирует ошибки при работе функции.
	 *
	 * @param   string                $phrase  Фраза для перевода.
	 * @param   array<string, mixed>  $params  Параметры для подстановки в строку перевода.
	 * @param   int                   $count   Количество для выбора формы множественного числа.
	 *
	 * @return string Переведённая строка.
	 *
	 * @example
	 *     echo translate('Сохранено');
	 *     echo translate('Найдено %count% записей', ['%count%' => 5]);
	 *     echo translate('Яблоко', [], 3);
	 */
	function translate(string $phrase, array $params = [], int $count = 0): string {
		if(DevCraftConfig::isSchemaLoading()) {
			return $phrase;
		}

		$config = DevCraftConfig::raw();

		if(!isset($config['language']) && !isset($config['locales_path'])) {
			return $phrase;
		}

		try {
			Translation::setTranslator();

			if($count > 0) {
				if($params === []) {
					return Translation::getTranslationPlural($phrase, $count);
				}

				return Translation::getTranslationPluralWithParameters($phrase, $count, $params);
			}

			if($params === []) {
				return Translation::getTranslation($phrase);
			}

			return Translation::getTranslationWithParameters($phrase, $params);
		} catch(Throwable $e) {
			LogGenerator::for('functions')->log($e->getMessage());

			return $phrase;
		}
	}
}

if(!function_exists('__')) {
	/**
	 * Синоним функции перевода translate для упрощённого использования.
	 *
	 * Служит для вызова перевода текстовых строк с возможностью передачи параметров
	 * и обработки множественного числа.
	 *
	 * @since 173.3.0
	 *
	 * @see   translate()
	 *
	 * @param   array<string, mixed>  $params  Ассоциативный массив параметров для подстановки в строку.
	 * @param   int                   $count   Количество для обработки множественного числа.
	 *
	 * @param   string                $phrase  Переводимая строка.
	 *
	 * @return string Переведённая строка.
	 *
	 * @example
	 *     echo __('Сохранено');
	 *     $title = __('Ошибка');
	 */
	function __(string $phrase, array $params = [], int $count = 0): string {
		return translate($phrase, $params, $count);
	}
}

if(!function_exists('dirToArray')) {
	/**
	 * Преобразует заданный путь к директории в массив дерева папок и файлов.
	 *
	 * Делегирует обход файловой системы классу DataManager. Папки представлены ключами,
	 * файлы — элементами массива. Поддерживает исключение указанных расширений и имён.
	 *
	 * @since 200.4.0
	 *
	 * @see   DataManager::dirToArray()
	 *
	 * @param   array<mixed>  $ignoredExtensions  Список файлов или расширений для исключения (например: ['.log', '.tmp']).
	 *
	 * @param   string        $dir                Абсолютный путь к директории.
	 *
	 * @return array<mixed> Дерево файловой структуры; каждая директория содержит вложенные файлы и папки.
	 *
	 * @example
	 *     $tree = dirToArray('/path/to/dir', ['.log']);
	 */
	function dirToArray(string $dir, array $ignoredExtensions = []): array {
		return DataManager::dirToArray($dir, $ignoredExtensions);
	}
}

if(!function_exists('br2nl')) {
	/**
	 * Преобразует теги `<br>` в заданный разделитель строк.
	 *
	 * Заменяет все варианты `<br>`, `<br/>` и `<br />` на указанный разделитель.
	 * Если переданный разделитель не входит в список допустимых значений, используется `PHP_EOL`.
	 *
	 * @since 200.4.0
	 *
	 * @param   string  $string     Входная строка с тегами `<br>`.
	 * @param   string  $separator  Разделитель строк (по умолчанию: PHP_EOL).
	 *                              Допустимые значения: "\n", "\r", "\r\n", "\n\r", chr(30), chr(155), PHP_EOL.
	 *
	 * @return string Строка, в которой все `<br>` заменены на указанный разделитель.
	 *
	 * @example
	 *     $plain = br2nl('<p>Строка 1<br/>Строка 2</p>');
	 */
	function br2nl(string $string, string $separator = PHP_EOL): string {
		static $validSeparators = ["\n", "\r", "\r\n", "\n\r", chr(30), chr(155), PHP_EOL];
		$separator = in_array($separator, $validSeparators, true)? $separator : PHP_EOL;

		return preg_replace('#<br\s*/?>#i', $separator, $string) ?? $string;
	}
}
