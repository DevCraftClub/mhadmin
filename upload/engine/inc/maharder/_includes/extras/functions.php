<?php

	if (!function_exists('translate')) {
		/**
		 * @version 2.0.9
		 * @since   2.0.9
		 *
		 * @param    string    $module
		 * @param    string    $phrase
		 * @param    array     $params
		 * @param    int       $count
		 *
		 * @return string
		 */
		function translate(string $module, string $phrase, array $params = [], int $count = 0): string {
			$mh = DataLoader::getConfig('maharder');
			MhTranslation::setTranslator($module);
			if (!isset($mh['language']) && !isset($mh['locales_path'])) {
				return $phrase;
			}
			try {
				if ($count > 0) {
					if (count($params) == 0) {
						return MhTranslation::getTranslationPlural($phrase, $count);
					} else {
						return MhTranslation::getTranslationPluralWithParameters(
							$phrase, $count, $params
						);
					}
				} else {
					if (count($params) == 0) {
						return MhTranslation::getTranslation($phrase);
					} else {
						return MhTranslation::getTranslationWithParameters(
							$phrase, $params
						);
					}
				}
			} catch (Exception $e) {
				LogGenerator::generate_log('functions', 'translate', $e->getMessage());

				return $phrase;
			}
		}
	}

	if (!function_exists('__')) {
		/**
		 * Synonym to translate fn above
		 * Для упрощённого использования
		 *
		 * @version 2.0.9
		 * @since   2.0.9
		 *
		 * @param    string    $module
		 * @param    string    $phrase
		 * @param    array     $params
		 * @param    int       $count
		 *
		 * @return string
		 */
		function __(string $module, string $phrase, array $params = [], int $count = 0): string {
			return translate($module, $phrase, $params, $count);
		}
	}

	if (!function_exists('dirToArray')) {
		/**
		 * Преобразует путь в массив с папками и файлами
		 *
		 * @param    string    $dir
		 * @param    array     $_ext
		 *
		 * @return array
		 */
		function dirToArray($dir, array $_ext = []): array {
			$ext = [
				'.',
				'..',
				'.htaccess',
			];
			foreach ($_ext as $e) {
				if (!in_array($e, $ext)) {
					$ext[] = $e;
				}
			}

			$result = [];

			$dir = str_replace(ENGINE_DIR, ROOT.DIRECTORY_SEPARATOR.'engine', $dir);

			if (is_dir($dir)) {
				foreach (scandir($dir, SCANDIR_SORT_NONE) as $key => $value) {
					if (!in_array($value, $ext, true)) {
						if (is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
							$result[$value] = dirToArray($dir.DIRECTORY_SEPARATOR.$value);
						} else {
							$result[] = $value;
						}
					}
				}
			}

			return $result;
		}
	}

	if (!function_exists('br2nl')) {
		/**
		 * Convert BR tags to newlines and carriage returns.
		 *
		 * @link https://www.php.net/manual/ru/function.nl2br.php#115182
		 *
		 * @param    string    $string       The string to convert
		 * @param    string    $separator    The string to use as line separator
		 *
		 * @return string The converted string
		 */
		function br2nl($string, $separator = PHP_EOL) {
			$separator = in_array($separator, ["\n", "\r", "\r\n", "\n\r", chr(30), chr(155), PHP_EOL]
			) ? $separator : PHP_EOL;  // Checks if provided $separator is valid.

			return preg_replace('/\<br(\s*)?\/?\>/i', $separator, $string);
		}
	}