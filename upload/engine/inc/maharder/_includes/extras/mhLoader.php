<?php

@ini_set('pcre.recursion_limit', 10000000);
@ini_set('pcre.backtrack_limit', 10000000);
@ini_set('pcre.jit', false);

spl_autoload_register(function ($class_name) {
	global $mh_loader_paths;

	foreach ($mh_loader_paths as $path) {
		$filePath = "{$path}/{$class_name}.php"; // Строим путь к файлу сразу
		$checkedPath = DLEPlugins::Check($filePath);

		if (file_exists($checkedPath)) { // Проверяем существование файла
			include_once $checkedPath;
			return; // Завершаем выполнение, если файл найден
		}
	}

});
