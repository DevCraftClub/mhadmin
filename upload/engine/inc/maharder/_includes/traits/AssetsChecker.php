<?php

/**
 * Трейт для проверки и управления ассетами (файлы скриптов и стилей).
 */
trait AssetsChecker {

	/**
	 * Массив со всеми данными для обновления стилей и скриптов
	 *
	 * @var array
	 */
	private array $assets_arr = [];

	/**
	 * Путь до всех вспомогательных файлов
	 *
	 * @var string
	 */
	private string $assets_dir = ENGINE_DIR . '/inc/maharder/admin/assets';

	/**
	 * Файл с информацией и хешами вспомогательных файлов
	 *
	 * @var string
	 */
	private string $asset_file = ENGINE_DIR . '/inc/maharder/_includes/assets.json';

	/**
	 * Выполняет парсинг вспомогательных файлов для управления ассетами.
	 * Если файл с хешами ассетов существует, то парсинг выполняется только при принудительном запуске.
	 * В случае отсутствия указанного файла производится полное сканирование ассетов.
	 *
	 * @param bool $parse Указывает на необходимость принудительного выполнения парсинга
	 *                    даже при наличии файла с хешами ассетов.
	 *
	 * @return void
	 * @throws JsonException|\Throwable В случае ошибок в процессах обработки JSON-файлов.
	 *
	 * @see $asset_file Глобальная переменная, путь к файлу с хешами ассетов.
	 * @see parse_assets() Внутренний метод, вызываемый для выполнения парсинга.
	 */
	public function parseAssets(bool $parse = false) : void {
		if (file_exists($this->asset_file)) {
			if ($parse) {
				$this->parse_assets();
			}
		} else {
			$this->parse_assets();
		}
	}

	/**
	 * Проверяет целостность файлов между локальным хранилищем и сервером разработчика.
	 * Если файлы отсутствуют или имеют различия в хеш-суммах, информация об этих расхождениях
	 * возвращается в виде массива. При необходимости может быть выполнена перезапись
	 * локального файла с данными о ресурсах.
	 *
	 * @global string $asset_file Путь к локальному JSON-файлу с данными о ресурсах.
	 *
	 * @param bool    $rewrite    Если true, выполняется перезапись локального файла списка ресурсов
	 *                            с актуальными данными из указанной директории.
	 *
	 * @return array Ассоциативный массив с информацией о проверке файлов:
	 *               - `on_server` (int): Количество файлов, находящихся на сервере разработчика.
	 *               - `local` (int): Количество локальных файлов.
	 *               - `missing_count` (int): Количество недостающих файлов.
	 *               - `update_count` (int): Количество файлов, требующих обновления.
	 *               - `missing` (array): Массив с данными о недостающих файлах.
	 *               - `update` (array): Массив с данными о файлах, требующих обновления.
	 * @throws \JsonException
	 * @see DataManager::dirToArray() Метод для получения структуры указанной директории.
	 * @see self::prepare_assets() Метод для подготовки массива данных о локальных файлах.
	 */
	public function checkAssets(bool $rewrite = false) : array {
		if (!is_file($this->asset_file) || $rewrite) {
			$this->prepare_assets(DataManager::dirToArray($this->assets_dir), $this->assets_dir);
			file_put_contents($this->asset_file, json_encode($this->assets_arr, JSON_UNESCAPED_UNICODE));
		}
		$assets =
			json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true, 512, JSON_THROW_ON_ERROR);
		$files  = json_decode(file_get_contents($this->asset_file), true, 512, JSON_THROW_ON_ERROR);

		$info = [
			'on_server'     => count($assets),
			'local'         => count($files),
			'missing_count' => 0,
			'update_count'  => 0,
			'missing'       => [],
			'update'        => []
		];

		foreach ($assets as $asset => $data) {
			if (isset($files[$asset])) {
				if ($files[$asset]['hash'] !== $data['hash']) {
					$info['update'][$asset] = $data;
					$info['update_count']++;
				}
			} else {
				$info['missing'][$asset] = $data;
				$info['missing_count']++;
			}
		}

		return $info;

	}

	/**
	 * Парсит данные о ресурсах (assets), проверяет наличие отсутствующих или обновленных,
	 * и обновляет информацию. Также сохраняет актуальные данные в локальный файл ресурсов.
	 *
	 * Процесс включает следующие этапы:
	 * 1. Получение списка локальных файлов и их данных через метод `prepare_assets`.
	 * 2. Получение данных об удаленных ресурсах через URL.
	 * 3. Сравнение данных о хэшах локальных и удаленных ресурсов.
	 * 4. Сохранение недостающих или обновленных ресурсов в локальное хранилище.
	 * 5. Запись актуализированных данных в локальный JSON-файл.
	 *
	 * @return void
	 * @throws JsonException|\Throwable Исключение выбрасывается в случае ошибки при работе с JSON.
	 *
	 * @see self::save_asset() Для сохранения конкретного ресурса.
	 * @see self::prepare_assets() Для создания массива данных локальных файлов.
	 * @see $this->assets_dir Директория, используемая для работы с локальными ресурсами.
	 * @see $this->assets_arr Массив данных подготовленных локальных ресурсов.
	 *
	 * @see DataManager::dirToArray() Для рекурсивного получения структуры файлов в указанной директории.
	 */
	private function parse_assets() : void {
		$this->prepare_assets(DataManager::dirToArray($this->assets_dir), $this->assets_dir);
		$files  = $this->assets_arr;
		$assets =
			json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true, 512, JSON_THROW_ON_ERROR);

		foreach ($assets as $asset => $data) {
			if (isset($files[$asset])) {
				if ($files[$asset]['hash'] !== $data['hash']) {
					$this->save_asset($data, $asset);
				}
			} else {
				$this->save_asset($data, $asset);
			}
		}

		file_put_contents($this->asset_file, json_encode($assets, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Сохраняет файл на сервер и возвращает информацию о нём или false в случае неуспеха.
	 *
	 * @param array  $data Ассоциативный массив с данными о файле. Ожидаются ключи:
	 *                     - 'link' (обязательный) — ссылка на файл.
	 *                     - 'alt' (необязательный) — альтернативная ссылка, если основная недоступна.
	 * @param string $file Путь для сохранения файла на сервере (относительно определённой директории).
	 *
	 * @return array|false Возвращает массив с информацией о сохранённом файле или false, если файл не удалось сохранить.
	 *     Массив содержит следующие ключи:
	 *     - 'realpath' (string): Полный реальный путь до файла.
	 *     - 'dirname' (string): Директория файла.
	 *     - 'basename' (string): Имя файла с расширением.
	 *     - 'filename' (string): Имя файла без расширения.
	 *     - 'extension' (string): Расширение файла.
	 *     - 'mime' (string): MIME-тип файла.
	 *     - 'encoding' (string): Тип кодирования файла.
	 *     - 'size' (int): Размер файла в байтах.
	 *     - 'size_string' (string): Размер файла в человекочитаемом формате (например, "1.5MB").
	 *     - 'atime' (int): Время последнего доступа к файлу (в метках времени Unix).
	 *     - 'mtime' (int): Время последней модификации файла (в метках времени Unix).
	 *     - 'permission' (string): Права доступа на файл (в формате UNIX-подобной строки, например, "0755").
	 *
	 * @throws JsonException|Throwable Генерирует исключение, если возникает ошибка обработки JSON.
	 *
	 * @see DataManager::createDir()
	 * @see LogGenerator::generateLog()
	 * @see AssetsChecker::fetchFileContent()
	 * @see AssetsChecker::saveFile()
	 * @see AssetsChecker::generateFileMetadata()
	 * @global string ENGINE_DIR Константа с базовым путём для сохранения файлов.
	 */
	public function save_asset(array $data, string $file) : bool|array {

		if (!function_exists('format_bytes')) {
			function format_bytes(int $size): string {
				$base = log($size, 1024);
				$suffixes = ['', 'KB', 'MB', 'GB', 'TB', 'PB'];
				return round(1024 ** ($base - floor($base)), 2) . ' ' . $suffixes[(int)floor($base)];
			}
		}

		$file_content = $this->fetchFileContent($data); // Новый метод для уменьшения вложенности

		if ($file_content) {
			$file_path = ENGINE_DIR . '/inc/maharder/admin' . $file;

			if (!DataManager::createDir(service: 'AssetsChecker', module: 'save_asset', _path: $file_path)) {
				return false; // Прерываем выполнение, если директория не создана
			}

			if (!$this->saveFile($file_path, $file_content, $file)) {
				return false; // Прерываем выполнение, если файл не сохранён
			}

			return $this->generateFileMetadata($file_path); // Новый метод для генерации метаданных
		}

		return false;
	}

	/**
	 * Получает содержимое файла по указанным ссылкам.
	 *
	 * Метод пытается загрузить содержимое файла с основной (`link`) и,
	 * при необходимости, с альтернативной (`alt`) ссылки, если содержимое
	 * по основной ссылке пустое.
	 *
	 * @param array $data Ассоциативный массив данных, содержащий ключи:
	 *                    - 'link' (string): основная ссылка на файл.
	 *                    - 'alt' (string): альтернативная ссылка на файл (опционально).
	 *
	 * @return string|null Возвращает содержимое файла, либо null, если
	 *                     не удалось загрузить файл.
	 *
	 * @see file_get_contents()
	 */
	private function fetchFileContent(array $data): ?string	{
		$file_content = file_get_contents($data['link'] ?? '');

		// Если содержимое пустое и есть альтернативная ссылка, пробуем получить её
		if (empty($file_content) && !empty($data['alt'])) {
			$file_content = file_get_contents($data['alt']);
		}

		return $file_content ?: null; // Возвращаем null, если файл не удалось загрузить
	}

	/**
	 * Сохраняет содержимое в файл и логирует сообщение при возникновении ошибки.
	 *
	 * Метод выполняет запись переданного содержимого в указанный путь файла,
	 * проверяет, доступен ли файл для записи, и логирует ошибку в случае неудачи.
	 *
	 * @param string $file_path Путь к файлу для сохранения содержимого.
	 * @param string $content   Содержимое, которое необходимо записать в файл.
	 * @param string $file      Имя файла для отображения в логе.
	 *
	 * @return bool Возвращает true, если файл успешно сохранён, или false — в случае ошибки.
	 *
	 * @throws Throwable
	 * @see LogGenerator::generateLog() Для логирования ошибок в случае невозможности сохранения файла.
	 */
	private function saveFile(string $file_path, string $content, string $file): bool {
		if (!file_put_contents($file_path, $content, FILE_USE_INCLUDE_PATH | LOCK_EX) || !is_writable($file_path)) {
			LogGenerator::generateLog(
				'maharder/admin',
				'save_asset',
				__("Файл '{file}' не был сохранён!", ['{file}' => $file])
			);
			return false;
		}
		return true;
	}

	/**
	 * Генерирует метаданные для указанного файла.
	 *
	 * @param string $file_path Абсолютный путь к файлу, для которого нужно сгенерировать метаданные.
	 *
	 * @return array Ассоциативный массив, содержащий следующую информацию о файле:
	 *               - 'realpath' (string|null): Абсолютный путь до файла.
	 *               - 'dirname' (string): Путь к директории, содержащей файл.
	 *               - 'basename' (string): Имя файла с расширением.
	 *               - 'filename' (string): Имя файла без расширения.
	 *               - 'extension' (string|null): Расширение файла.
	 *               - 'mime' (string): MIME-тип файла.
	 *               - 'encoding' (string): Кодировка файла.
	 *               - 'size' (int): Размер файла в байтах.
	 *               - 'size_string' (string): Читаемое представление размера файла (например, "10.5 KB").
	 *               - 'atime' (int): Временная метка последнего доступа к файлу.
	 *               - 'mtime' (int): Временная метка последней модификации файла.
	 *               - 'permission' (string): Права доступа к файлу (например, "0755").
	 *
	 * @throws RuntimeException Если файл не существует или недоступен.
	 *
	 * @see format_bytes() Преобразование размера файла в читаемый формат.
	 * @global \finfo Используется для получения MIME-типов и кодировки файла.
	 * @since 173.3.0
	 */
	private function generateFileMetadata(string $file_path): array	{
		$pathinfo = pathinfo($file_path);
		$stat = stat($file_path);

		$finfo = finfo_open();
		return [
			'realpath'    => realpath($file_path),
			'dirname'     => $pathinfo['dirname'],
			'basename'    => $pathinfo['basename'],
			'filename'    => $pathinfo['filename'],
			'extension'   => $pathinfo['extension'],
			'mime'        => finfo_file($finfo, $file_path, FILEINFO_MIME_TYPE),
			'encoding'    => finfo_file($finfo, $file_path, FILEINFO_MIME_ENCODING),
			'size'        => $stat['size'],
			'size_string' => format_bytes($stat['size']),
			'atime'       => $stat['atime'],
			'mtime'       => $stat['mtime'],
			'permission'  => substr(sprintf('%o', $stat['mode']), -4),
		];
	}


	/**
	 * Подготавливает данные о файлах, расположенных на локальном сервере, и сохраняет их в массив `assets_arr`.
	 *
	 * Метод рекурсивно обрабатывает массив с файлами и создает запись для каждого файла в конечном массиве `assets_arr`,
	 * содержащую путь, имя файла и его хэш.
	 *
	 * @param array  $arr  Массив с файлами, подлежащими обработке.
	 * @param string $dir  Исходная директория для поиска файлов. По умолчанию используется текущая директория.
	 *
	 * @return void
	 *
	 * @global array $assets_arr Массив, в который добавляются данные о выявленных файлах.
	 *
	 * @see hash_file()
	 * @see pathinfo()
	 */
	private function prepare_assets(array $arr, string $dir = __DIR__) : void {

		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$this->prepare_assets($value, $dir . '/' . $key);
			} else {
				$file      = $dir . '/' . $key;
				$file_info = pathinfo($key);
				if (empty($file_info['extension'])) $file = $dir . '/' . $value;
				$dir_arr  = str_replace(ENGINE_DIR . '/inc/maharder/admin', '', $file);
				$pathinfo = pathinfo($dir_arr);

				$this->assets_arr[$dir_arr] = [
					'path' => $pathinfo['dirname'],
					'file' => $dir_arr,
					'hash' => hash_file('md5', $file)
				];
			}
		}

	}

}