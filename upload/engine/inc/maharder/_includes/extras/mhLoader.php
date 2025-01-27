<?php

@ini_set('pcre.recursion_limit', 10000000);
@ini_set('pcre.backtrack_limit', 10000000);
@ini_set('pcre.jit', false);

/**
 * Автозагрузчик для классов, который ищет файлы классов в указанных путях, записанных в глобальной переменной
 * $mh_loader_paths, проверяет их на корректность с помощью метода `DLEPlugins::Check` и подключает файл, если он
 * существует. Ожидается, что глобальная переменная $mh_loader_paths — это массив путей, в которых может располагаться
 * файл класса.
 *
 * @param string $class_name Имя класса, для которого необходимо найти и подключить файл.
 *
 * @return void
 * @throws \Exception Если возникает ошибка при обращении к методу `DLEPlugins::Check` или подключении файла.
 * @throws \RuntimeException Если глобальная переменная $mh_loader_paths не определена или не является массивом.
 */
spl_autoload_register(
	function ($class_name) {
		global $mh_loader_paths;

		foreach ($mh_loader_paths as $path) {
			$filePath    = "{$path}/{$class_name}.php"; // Строим путь к файлу сразу
			$checkedPath = DLEPlugins::Check($filePath);

			if (file_exists($checkedPath)) { // Проверяем существование файла
				include_once $checkedPath;
				return; // Завершаем выполнение, если файл найден
			}
		}

	}
);
