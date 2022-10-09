<?php

	require_once DLEPlugins::Check(ENGINE_DIR.'/inc/maharder/_includes/extras/paths.php');

	abstract class CacheControl {

		/**
		 * Путь до кеша
		 *
		 * @var string
		 */
		private static $path = null;

		/**
		 * @param    string|null    $path
		 *
		 */
		public static function init(string $path = null): void {
			$mh_config = DataLoader::getConfig('maharder');
			$path = !is_null($path) ? $path : $mh_config['cache_path'] ?? ENGINE_DIR.'/inc/maharder/_cache';
			self::setPath($path);
		}

		/**
		 * @param    string    $path
		 */
		public static function setPath(string $path): void {
			self::$path = $path;
		}

		/**
		 * Сохраняем в кеш
		 *
		 * @param $type
		 * @param $name
		 * @param $data
		 */
		public static function set_cache($type, $name, $data) {
			if (is_null(self::$path)) self::init();

			$file                = totranslit($name, true, false);
			$type                = totranslit($type, true, false);
			$concurrentDirectory = self::$path.'/'.$type;
			DataLoader::createDir($concurrentDirectory);
			if (!is_dir($concurrentDirectory)) {
				LogGenerator::generate_log('CacheControl', 'set_cache', sprintf('Directory "%s" was not created', $concurrentDirectory));
			}

			if (is_array($data) or is_int($data)) {
				file_put_contents(
					$concurrentDirectory.DIRECTORY_SEPARATOR.$file.'.php',
					json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX
				);
				@chmod($concurrentDirectory.DIRECTORY_SEPARATOR.$file.'.php', 0666);
			}
		}

		/**
		 * Получаем кеш
		 *
		 * @param $type
		 * @param $name
		 *
		 * @return array|false
		 */
		public static function get_cache($type, $name) {
			if (is_null(self::$path)) self::init();

			$file = totranslit($name, true, false);
			$type = totranslit($type, true, false);

			$data = @file_get_contents(self::$path.'/'.$type.DIRECTORY_SEPARATOR.$file.'.php');

			if ($data !== false) {
				$data = json_decode($data, true);
				if (is_array($data) || is_int($data)) {
					return $data;
				}
			}

			return false;
		}

		/**
		 * Очищаем кеш
		 *
		 * @version 2.0.9
		 *
		 * @param    string    $type
		 */
		public static function clear_cache(string $type = 'all') {
			if (is_null(self::$path)) self::init();

			$dirname = self::$path;
			if ($type !== 'all') {
				if (is_array($type)) {
					foreach ($type as $key) {
						self::clear_cache($key);
					}
				} else {
					$type    = totranslit($type, true, false);
					$dirname .= '/'.$type;
					foreach (dirToArray($dirname) as $i => $name) {
						try {
							if (is_array($name)) {
								@rmdir($dirname.DIRECTORY_SEPARATOR.$i);
							} else {
								@unlink($dirname.DIRECTORY_SEPARATOR.$name);
							}
						} catch (Exception $e) {
							LogGenerator::generate_log('CacheControl', 'clear_cache', $e->getMessage());
						}
					}
				}
			}
		}

	}