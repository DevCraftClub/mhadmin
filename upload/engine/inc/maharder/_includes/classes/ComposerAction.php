<?php

class ComposerAction {
	private static ?string $composerPath = null;
	private static ?string $projectPath  = null;

	/**
	 * Инициализация класса
	 *
	 * @param string|null $projectPath  Путь к проекту (где находится composer.json)
	 * @param string|null $composerPath Путь к временному Composer (опционально)
	 */
	public static function init(?string $projectPath = null, ?string $composerPath = null): void {
		self::$projectPath = $projectPath ? realpath($projectPath) : MH_ADMIN;
		if (!is_dir(self::$projectPath)) {
			throw new \InvalidArgumentException("Директория проекта не существует: $projectPath");
		}

		self::$composerPath = $composerPath ?? COMPOSER_DIR . '/composer.phar';
	}

	/**
	 * Проверяет наличие установленного Composer
	 *
	 * @return bool
	 */
	public static function isComposerInstalled(): bool {
		try {
			// Пытаемся проверить наличие composer.phar или встроенной команды composer
			$composerPath = self::$composerPath ?? 'composer';

			// Проверяем существование файла composer.phar
			if ($composerPath !== 'composer' && file_exists($composerPath)) {
				return true;
			}

			if (PHP_OS_FAMILY === 'Windows') return false;

			// Используем `which` или альтернативу для проверки выполнения команды через PATH
			$exitCode = null;
			@exec('which composer', $output, $exitCode);

			if (isset($output[0])) {
				$newPath = realpath($output[0]);
				if ($newPath !== false) {
					self::$composerPath = $newPath;
				}
			}

			return $exitCode === 0;
		} catch (Exception $e) {
			throw new \http\Exception\RuntimeException($e->getMessage());
		}
	}

	/**
	 * Устанавливает временный Composer
	 *
	 * @throws \RuntimeException При ошибках загрузки или сохранения
	 */
	public static function installTemporaryComposer(): void {
		$composerDir = dirname(self::$composerPath);
		if (!is_dir($composerDir) && !mkdir($composerDir, 0755, true)) {
			throw new \RuntimeException("Не удалось создать директорию: $composerDir");
		}

		$context = stream_context_create(['http' => ['timeout' => 30]]);

		$pharContent =
			@file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar', false, $context);
		if ($pharContent === false) {
			throw new \RuntimeException('Ошибка загрузки Composer');
		}

		$expectedHash = @file_get_contents(
			'https://getcomposer.org/download/latest-stable/composer.phar.sha256',
			false,
			$context
		);
		if ($expectedHash === false) {
			throw new \RuntimeException('Ошибка загрузки хэша');
		}

		if (hash('sha256', $pharContent) !== trim($expectedHash)) {
			throw new \RuntimeException(
				sprintf(
					'Неверная контрольная сумма Composer! Ожидалось %s; получено %s!<br>1. Загрузите <a href="%s" target="_blank">composer.phar</a> вручную и закиньте загруженный файл по пути %s.<br>2. Обновите страницу!',
					$expectedHash,
					hash('sha256', $pharContent),
					'https://getcomposer.org/download/latest-stable/composer.phar',
					dir(self::$composerPath)
				)
			);
		}

		if (!@file_put_contents(self::$composerPath, $pharContent)) {
			throw new \RuntimeException("Ошибка сохранения в " . self::$composerPath);
		}

		chmod(self::$composerPath, 0755);
	}

	/**
	 * Устанавливает зависимости
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function installDependencies(): void {
		self::runCommand('install');
	}

	/**
	 * Обновляет зависимости
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function updateDependencies(): void {
		self::runCommand('update');
	}

	/**
	 * Удаляет пакет
	 *
	 * @param string $package Имя пакета
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function removePackage(string $package): void {
		self::runCommand("remove $package");
	}

	/**
	 * Устанавливает новый пакет
	 *
	 * @param string|array $package Имя пакета
	 * @param string|null  $version Версия пакета (опционально)
	 * @param bool         $isDev
	 * @param string|null  $lockerFile
	 *
	 * @throws JsonException
	 * @throws Throwable
	 */
	public static function requirePackage(string|array $package, ?string $version = null, bool $isDev = false, ?string $lockerFile = null): void {

		$runRequire = $lockerFile === null;
		if (!$runRequire) $runRequire = !file_exists($lockerFile);


		if ($runRequire) {
			if (is_array($package)) {
				foreach ($package as $p => $v) {
					self::requirePackage($p, $v, $isDev, $lockerFile);
				}

				return;
			}

			$dev     = $isDev ? '-dev' : '';
			$command = "require{$dev} $package";
			if ($version !== null) {
				$command .= ":$version";
			}
			self::runCommand($command);
		}

		if ($lockerFile) {
			DataManager::createLockFile($lockerFile);
		}
	}

	/**
	 * Выполняет команду Composer
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	private static function runCommand(string $command): string {
		if (self::$projectPath === null) {
			throw new \RuntimeException("Класс не инициализирован. Вызовите метод init()");
		}

		$fullCommand = sprintf(
			'%s %s --working-dir=%s',
			self::getComposerCommand(),
			$command,
			escapeshellarg(self::$projectPath)
		);

		$output = self::executeCommand($fullCommand);
		return $output;
	}

	/**
	 * Возвращает команду для вызова Composer
	 */
	private static function getComposerCommand(): string {
		$prefix = '';
		if(file_exists(self::$composerPath)) {
			$pathInfo = pathinfo(self::$composerPath);
			if (in_array($pathInfo['extension'], ['phar', 'phar.exe', 'bat', 'cmd'])) {
				$prefix = 'php ';
			}
		}
		return file_exists(self::$composerPath) ? $prefix . escapeshellarg(self::$composerPath) : self::$composerPath;
	}

	/**
	 * Выполняет команду в оболочке
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	private static function executeCommand(string $command): string {
		$descriptors = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w']
		];

		$process = proc_open(
			command        : $command,
			descriptor_spec: $descriptors,
			pipes          : $pipes,
			cwd            : self::$projectPath ?? null
		);

		if (!is_resource($process)) {
			throw new \RuntimeException("Ошибка запуска процесса");
		}

		fclose($pipes[0]);

		try {
			$stdout = self::readStream($pipes[1]);
			$stderr = self::readStream($pipes[2]);
		} finally {
			foreach ($pipes as $pipe) {
				if (is_resource($pipe)) {
					fclose($pipe);
				}
			}
		}

		$exitCode = proc_close($process);

		if ($exitCode !== 0) {
			$output = trim("$stdout\n$stderr");
			throw new \RuntimeException("Ошибка ({$exitCode}): $output");
		}

		return trim($stdout);

	}

	private static function readStream($stream): string {
		if (!is_resource($stream)) {
			throw new \InvalidArgumentException("Переданный аргумент не является потоком");
		}

		$buffOpen = true;
		$buffOut = '';

		while ($buffOpen) {
			$read[] = $stream;

			$write  = null;
			$except = null;
			$ready  = stream_select($read, $write, $except, 30); // Таймаут 30 секунд

			if ($ready === false) {
				throw new \RuntimeException("Ошибка при ожидании данных из процесса");
			}

			// Чтение stdout
			if (in_array($stream, $read)) {
				$data = fread($stream, 8192);
				if ($data === false || strlen($data) === 0) {
					fclose($stream);
					$buffOpen = false;
				} else {
					$buffOut .= $data;
				}
			}

		}

		return $buffOut;
	}

}
