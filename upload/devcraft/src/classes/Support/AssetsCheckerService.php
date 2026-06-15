<?php
//===============================================================
// Файл: AssetsCheckerService.php                               =
// Путь: devcraft/src/classes/Support/AssetsCheckerService.php  =
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

namespace DevCraft\Core\Support;

use Throwable;
use JsonException;
use DevCraft\Core\Config\Paths;
use DevCraft\Core\Logging\LogGenerator;

/**
 * Сканирует публичные ассеты, сравнивает с удалённым манифестом и загружает недостающие файлы.
 *
 * Порт логики трейта AssetsChecker.
 *
 * @package    DevCraft
 * @since      173.3.0
 * @subpackage Core.Support
 */
final class AssetsCheckerService {

	/**
	 * URL удалённого манифеста assets.devcraft.club.
	 *
	 * @since 173.3.0
	 * @var string
	 */
	private const REMOTE_URL = 'https://assets.devcraft.club/assets.json';

	/**
	 * Путь к локальному JSON-манифесту.
	 *
	 * @since 173.3.0
	 * @var string
	 */
	private readonly string $manifestPath;

	/**
	 * @since 173.3.0
	 *
	 * @param   string|null  $manifestPath  Путь к assets.json или null для Paths::config().
	 */
	public function __construct(?string $manifestPath = NULL) {
		$this->manifestPath = $manifestPath ?? Paths::config() . '/assets.json';
	}

	/**
	 * Сканирует локальные файлы по ключам удалённого манифеста и записывает локальный MD5-манифест.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, array<string, mixed>> Манифест file => metadata.
	 *
	 * @example
	 *     $manifest = $checker->scan();
	 */
	public function scan(): array {
		$manifest    = [];
		$remoteFiles = $this->normalizeManifestFiles($this->fetchRemoteManifest(self::REMOTE_URL) ?? []);
		$keys        = $remoteFiles !== []? array_keys($remoteFiles) : array_keys($this->normalizeManifestFiles($this->readManifest()));

		foreach($keys as $manifestKey) {
			if(!is_string($manifestKey)) {
				continue;
			}

			$sourceMeta   = is_array($remoteFiles[$manifestKey] ?? NULL)? $remoteFiles[$manifestKey] : ['file' => $manifestKey];
			$absolutePath = $this->deployAbsolutePath($sourceMeta);

			if($absolutePath === NULL) {
				continue;
			}

			$metadata = $this->fileMetadata($absolutePath, $sourceMeta);

			if($metadata !== NULL) {
				$fileKey                = (string) ($metadata['file'] ?? $manifestKey);
				$manifest[$fileKey] = $metadata;
			}
		}

		$this->writeManifest($manifest);

		return $manifest;
	}

	/**
	 * Сравнивает локальный манифест с удалённым по hash.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $remoteUrl  URL удалённого assets.json.
	 *
	 * @return array{missing: string[], outdated: string[], ok: string[]} Списки путей.
	 *
	 * @example
	 *     $diff = $checker->compare();
	 */
	public function compare(string $remoteUrl = self::REMOTE_URL): array {
		$this->scan();
		$localFiles  = $this->normalizeManifestFiles($this->readManifest());
		$remoteFiles = $this->normalizeManifestFiles($this->fetchRemoteManifest($remoteUrl) ?? []);

		if($remoteFiles === []) {
			return [
				'missing'  => [],
				'outdated' => [],
				'ok'       => [],
			];
		}

		$missing  = [];
		$outdated = [];
		$ok       = [];

		foreach($remoteFiles as $path => $remoteMeta) {
			if(!isset($localFiles[$path])) {
				$missing[] = $path;
				continue;
			}

			$localHash  = (string) ($localFiles[$path]['hash'] ?? '');
			$remoteHash = (string) (is_array($remoteMeta)? ($remoteMeta['hash'] ?? '') : '');

			if($localHash !== '' && $remoteHash !== '' && $localHash !== $remoteHash) {
				$outdated[] = $path;
				continue;
			}

			$ok[] = $path;
		}

		return [
			'missing'  => $missing,
			'outdated' => $outdated,
			'ok'       => $ok,
		];
	}

	/**
	 * Формирует расширенный отчёт сравнения с метаданными файлов.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $remoteUrl  URL удалённого assets.json.
	 *
	 * @return array{
	 *     remote_count: int,
	 *     local_count: int,
	 *     missing: string[],
	 *     outdated: string[],
	 *     ok: string[],
	 *     missing_files: array<string, array<string, mixed>>,
	 *     outdated_files: array<string, array<string, mixed>>,
	 *     diff_count: int,
	 *     has_diff: bool
	 * }
	 *
	 * @example
	 *     $report = $checker->compareReport();
	 */
	public function compareReport(string $remoteUrl = self::REMOTE_URL): array {
		$remoteFiles = $this->normalizeManifestFiles($this->fetchRemoteManifest($remoteUrl) ?? []);
		$compare     = $this->compare($remoteUrl);
		$localFiles  = $this->normalizeManifestFiles($this->readManifest());

		$missingFiles  = [];
		$outdatedFiles = [];

		foreach($compare['missing'] as $path) {
			if(isset($remoteFiles[$path]) && is_array($remoteFiles[$path])) {
				$missingFiles[$path] = $remoteFiles[$path];
			}
		}

		foreach($compare['outdated'] as $path) {
			if(isset($remoteFiles[$path]) && is_array($remoteFiles[$path])) {
				$outdatedFiles[$path] = $remoteFiles[$path];
			}
		}

		$diffCount = count($compare['missing']) + count($compare['outdated']);

		return [
			'remote_count'   => count($remoteFiles),
			'local_count'    => count($localFiles),
			'missing'        => $compare['missing'],
			'outdated'       => $compare['outdated'],
			'ok'             => $compare['ok'],
			'missing_files'  => $missingFiles,
			'outdated_files' => $outdatedFiles,
			'diff_count'     => $diffCount,
			'has_diff'       => $diffCount > 0,
		];
	}

	/**
	 * Загружает все файлы из удалённого манифеста.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $remoteUrl  URL удалённого assets.json.
	 *
	 * @return int Число успешно загруженных файлов.
	 *
	 * @example
	 *     $count = $checker->downloadAll();
	 */
	public function downloadAll(string $remoteUrl = self::REMOTE_URL): int {
		$remoteFiles = $this->normalizeManifestFiles($this->fetchRemoteManifest($remoteUrl) ?? []);

		if($remoteFiles === []) {
			return 0;
		}

		$downloaded = 0;

		foreach(array_keys($remoteFiles) as $manifestKey) {
			$meta = $remoteFiles[$manifestKey] ?? NULL;

			if(!is_array($meta)) {
				continue;
			}

			if($this->downloadAssetFile((string) $manifestKey, $meta)) {
				$downloaded++;
			}
		}

		if($downloaded > 0) {
			$this->scan();
		}

		return $downloaded;
	}

	/**
	 * Загружает только missing и outdated файлы из diff.
	 *
	 * @since 173.3.0
	 *
	 * @param   array{missing?: string[], outdated?: string[]}  $diff  Результат compare().
	 *
	 * @return int Число успешно загруженных файлов.
	 *
	 * @example
	 *     $count = $checker->downloadMissing($compare);
	 */
	public function downloadMissing(array $diff): int {
		$remoteFiles = $this->normalizeManifestFiles($this->fetchRemoteManifest(self::REMOTE_URL) ?? []);

		if($remoteFiles === []) {
			return 0;
		}

		$targets = array_values(array_unique(array_merge(
			$diff['missing'] ?? [],
			$diff['outdated'] ?? [],
		)));

		$downloaded = 0;

		foreach($targets as $manifestKey) {
			$meta = $remoteFiles[$manifestKey] ?? NULL;

			if(!is_array($meta)) {
				continue;
			}

			if($this->downloadAssetFile($manifestKey, $meta)) {
				$downloaded++;
			}
		}

		if($downloaded > 0) {
			$this->scan();
		}

		return $downloaded;
	}

	/**
	 * Выполняет полный цикл: scan → compare → downloadMissing.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, mixed> Ключи scan, compare, downloaded.
	 *
	 * @example
	 *     $result = Application::instance()->assetsChecker()->run();
	 */
	public function run(): array {
		$scan    = $this->scan();
		$compare = $this->compare();
		$count   = $this->downloadMissing($compare);

		return [
			'scan'       => $scan,
			'compare'    => $compare,
			'downloaded' => $count,
		];
	}

	/**
	 * Формирует метаданные одного файла для манифеста.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                $absolutePath  Абсолютный путь к файлу на диске.
	 * @param   array<string, mixed>  $sourceMeta    Метаданные из удалённого манифеста.
	 *
	 * @return array{path: string, file: string, link: string, hash: string, alt: string, alt_name: string, required: bool}|null Метаданные или null.
	 */
	public function fileMetadata(string $absolutePath, array $sourceMeta): ?array {
		if(!is_file($absolutePath)) {
			return NULL;
		}

		$hash = hash_file('md5', $absolutePath);

		if($hash === false) {
			return NULL;
		}

		$deployFile = (string) ($sourceMeta['file'] ?? '');
		$pathinfo   = pathinfo($deployFile);

		return [
			'alt'      => (string) ($sourceMeta['alt'] ?? ''),
			'alt_name' => (string) ($sourceMeta['alt_name'] ?? ($pathinfo['basename'] ?? '')),
			'path'     => (string) ($sourceMeta['path'] ?? ($pathinfo['dirname'] ?? '')),
			'file'     => $deployFile,
			'link'     => (string) ($sourceMeta['link'] ?? ''),
			'required' => (bool) ($sourceMeta['required'] ?? false),
			'hash'     => $hash,
		];
	}

	/**
	 * Возвращает абсолютный путь развёртывания из поля file манифеста (от ROOT_DIR).
	 *
	 * @since 200.4.0
	 *
	 * @param   array<string, mixed>  $meta  Метаданные записи манифеста.
	 */
	private function deployAbsolutePath(array $meta): ?string {
		$deployFile = (string) ($meta['file'] ?? '');

		if($deployFile === '' || !str_starts_with($deployFile, '/devcraft/')) {
			return NULL;
		}

		if(!defined('ROOT_DIR')) {
			Paths::register();
		}

		return DataManager::normalizePath(ROOT_DIR . '/' . ltrim($deployFile, '/'));
	}

	/**
	 * Извлекает секцию files из манифеста или фильтрует плоский массив.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, mixed>  $manifest  Сырой декодированный JSON.
	 *
	 * @return array<string, array<string, mixed>> Карта path => metadata.
	 */
	private function normalizeManifestFiles(array $manifest): array {
		if(isset($manifest['files']) && is_array($manifest['files'])) {
			/** @var array<string, array<string, mixed>> $files */
			$files = $manifest['files'];

			return $files;
		}

		$files = [];

		foreach($manifest as $key => $value) {
			if(!is_array($value)) {
				continue;
			}

			$file = (string) ($value['file'] ?? (is_string($key)? $key : ''));

			if($file === '' || !str_starts_with($file, '/devcraft/')) {
				continue;
			}

			$files[$file] = $value;
		}

		return $files;
	}

	/**
	 * Записывает локальный JSON-манифест на диск.
	 *
	 * @since 173.3.0
	 *
	 * @param   array<string, array<string, mixed>>  $manifest  Данные манифеста.
	 */
	private function writeManifest(array $manifest): void {
		$directory = dirname($this->manifestPath);

		if(!DataManager::createDir($directory)) {
			LogGenerator::for('AssetsCheckerService')->log(__("Не удалось создать каталог манифеста: {directory}", ['{directory}' => $directory]));

			return;
		}

		try {
			file_put_contents(
				$this->manifestPath,
				json_encode($manifest, JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT),
				LOCK_EX,
			);
		} catch(JsonException|Throwable $exception) {
			LogGenerator::for('AssetsCheckerService')->log($exception->getMessage());
		}
	}

	/**
	 * Читает локальный JSON-манифест assets.json.
	 *
	 * @since 173.3.0
	 *
	 * @return array<string, mixed> Декодированный манифест или пустой массив.
	 */
	private function readManifest(): array {
		if(!is_readable($this->manifestPath)) {
			return [];
		}

		$content = file_get_contents($this->manifestPath);

		if($content === false || $content === '') {
			return [];
		}

		try {
			/** @var array<string, mixed> $decoded */
			$decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

			return $decoded;
		} catch(JsonException $exception) {
			LogGenerator::for('AssetsCheckerService')->log($exception->getMessage());

			return [];
		}
	}

	/**
	 * Загружает и декодирует удалённый JSON-манифест.
	 *
	 * @since 173.3.0
	 *
	 * @param   string  $remoteUrl  URL assets.json.
	 *
	 * @return array<string, mixed>|null Манифест или null при ошибке.
	 */
	private function fetchRemoteManifest(string $remoteUrl): ?array {
		$content = @file_get_contents($remoteUrl);

		if($content === false || $content === '') {
			LogGenerator::for('AssetsCheckerService')->log(__("Удалённый манифест недоступен: {remoteUrl}", ['{remoteUrl}' => $remoteUrl]));

			return NULL;
		}

		try {
			/** @var array<string, mixed> $decoded */
			$decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

			return $decoded;
		} catch(JsonException $exception) {
			LogGenerator::for('AssetsCheckerService')->log($exception->getMessage());

			return NULL;
		}
	}

	/**
	 * Скачивает один файл ассета по link/alt из метаданных.
	 *
	 * @since 173.3.0
	 *
	 * @param   string                $manifestKey  Ключ /assets/... .
	 * @param   array<string, mixed>  $meta         Метаданные из манифеста.
	 *
	 * @return bool true при успешной записи файла.
	 */
	private function downloadAssetFile(string $manifestKey, array $meta): bool {
		$url = (string) ($meta['link'] ?? '');

		$content = $url !== ''? @file_get_contents($url) : false;

		if($content === false || $content === '') {
			$alt = (string) ($meta['alt'] ?? '');

			if($alt !== '') {
				$content = @file_get_contents($alt);
			}
		}

		if($content === false || $content === '') {
			LogGenerator::for('AssetsCheckerService')->log(__("Не удалось загрузить файл: {manifestKey}", ['{manifestKey}' => $manifestKey]));

			return false;
		}

		$targetPath = $this->deployAbsolutePath($meta);

		if($targetPath === NULL || $targetPath === '') {
			LogGenerator::for('AssetsCheckerService')->log(__("Неизвестный путь развёртывания: {manifestKey}", ['{manifestKey}' => $manifestKey]));

			return false;
		}

		$directory = dirname($targetPath);

		if(!DataManager::createDir($directory)) {
			LogGenerator::for('AssetsCheckerService')->log(__("Не удалось создать каталог: {directory}", ['{directory}' => $directory]));

			return false;
		}

		if(file_put_contents($targetPath, $content, LOCK_EX) === false) {
			LogGenerator::for('AssetsCheckerService')->log(__("Не удалось записать файл: {targetPath}", ['{targetPath}' => $targetPath]));

			return false;
		}

		LogGenerator::for('AssetsCheckerService')->log(__("Ресурс загружен: {manifestKey}", ['{manifestKey}' => $manifestKey]));

		return true;
	}

}
