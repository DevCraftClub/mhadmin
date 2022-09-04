<?php

require_once DLEPlugins::Check(ENGINE_DIR . '/inc/maharder/_includes/extras/paths.php');

trait AssetsChecker {

	/**
	 * Массив со всеми данными для обновления стилей и скриптов
	 *
	 * @var array
	 */
	private $assets_arr = [];

	/**
	 * Путь до всех вспомогательных файлов
	 *
	 * @var string
	 */
	private $assets_dir = ENGINE_DIR . '/inc/maharder/admin/assets';

	/**
	 * Файл с информацией и хешами вспомогательных файлов
	 *
	 * @var string
	 */
	private $asset_file = ENGINE_DIR . '/inc/maharder/_includes/assets.json';

	/**
	 * Инициализатор для парсинга вспомогательных файлов
	 * Если файла с хешами не существует, то начнёт проверять все файлы
	 * Если файл существует, то только при принудительной проверке будет перепроверять данные
	 *
	 * @param $parse // Принудительный парсинг при наличии файла с хешами
	 *
	 * @return void
	 */
	public function parseAssets($parse = false) {
		if (file_exists($this->asset_file)) {
			if ($parse) {
				$this->parse_assets();
			}
		} else {
			$this->parse_assets();
		}
	}

	/**
	 * Проверяет целостность файлов на сайте и на сервере разработчика
	 * Если существуют разницы в хеш суммах, о них сообщит в массиве информации
	 *
	 * @param $rewrite
	 *
	 * @return array
	 * @throws JsonException
	 */
	public function checkAssets($rewrite = false) {
		if (!is_file($this->asset_file) || $rewrite) {
			$this->prepare_assets($this->dirToArray($this->assets_dir), $this->assets_dir);
			file_put_contents($this->asset_file, json_encode($this->assets_arr, JSON_UNESCAPED_UNICODE));
		}
		$assets = json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true);
		$files = json_decode(file_get_contents($this->asset_file), true);

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
	 * Парсер нехватающих и обновляемых данных
	 *
	 * @return void
	 */
	private function parse_assets() {
		$this->prepare_assets($this->dirToArray($this->assets_dir), $this->assets_dir);
		$files = $this->assets_arr;
		$assets = json_decode(file_get_contents('https://assets.devcraft.club/assets.json'), true);

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
	 * Сохраняет полученнай файл на сервер сайта и возвращает данные о файле
	 *
	 * @param $data //  Массив данных о файле
	 * @param $file //  Путь файла
	 *
	 * @return array|false
	 * @throws JsonException
	 */
	public function save_asset(array $data, string $file) {

		if (!function_exists('format_bytes')) {
			function format_bytes(int $size) {
				$base = log($size, 1024);
				$suffixes = [
					'',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB'
				];
				return round(1024 ** ($base - floor($base)), 2) . '' . $suffixes[floor($base)];
			}
		}

		$asset_file = file_get_contents($data['link']);
		if (empty($asset_file) && !empty($data['alt'])) {
			$asset_file = file_get_contents($data['alt']);
		}
		if ($asset_file) {
			$file_path = ENGINE_DIR . '/inc/maharder/admin' . $file;
			if (!mkdir($concurrentDirectory = dirname($file_path), 0777, true) && !is_dir($concurrentDirectory)) {
				$this->generate_log('maharder/admin', 'save_asset', "Путь '{$concurrentDirectory}' не удалось создать");
			}
			if (!file_put_contents($file_path, $asset_file, FILE_USE_INCLUDE_PATH|LOCK_EX) || !is_writable($file_path)) {
				$this->generate_log('maharder/admin', 'save_asset', "Файл '{$file}' не был сохранён!");
			}
			$pathinfo = pathinfo($file_path);
			$stat = stat($file_path);
			$this->checkAssets(true);

			return [
				'realpath'    => realpath($file_path),
				'dirname'     => $pathinfo['dirname'],
				'basename'    => $pathinfo['basename'],
				'filename'    => $pathinfo['filename'],
				'extension'   => $pathinfo['extension'],
				'mime'        => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path),
				'encoding'    => finfo_file(finfo_open(FILEINFO_MIME_ENCODING), $file_path),
				'size'        => $stat[7],
				'size_string' => format_bytes($stat[7]),
				'atime'       => $stat[8],
				'mtime'       => $stat[9],
				'permission'  => substr(sprintf('%o', fileperms($file_path)), -4),
			];
		}
		return false;
	}

	/**
	 * Подгатавливает информацию о файле на локальном сервере и сохраняет в массиве с данными
	 *
	 * @param        $arr //  Массив с файлами
	 * @param string $dir //  Исходная папка для поиска
	 *
	 * @return void
	 */
	private function prepare_assets(array $arr, string $dir = __DIR__) {

		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				$this->prepare_assets($value, $dir . '/' . $key);
			} else {
				$file = $dir . '/' . $key;
				$file_info = pathinfo($key);
				if (empty($file_info['extension'])) $file = $dir . '/' . $value;
				$dir_arr = str_replace(ENGINE_DIR . '/inc/maharder/admin', '', $file);
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