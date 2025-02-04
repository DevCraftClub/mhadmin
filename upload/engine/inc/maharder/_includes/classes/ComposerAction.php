<?php

class ComposerAction {
	private static ?string $composerPath = null;
	private static ?string $projectPath  = null;

	/**
	 * Инициализация класса
	 *
	 * @param string|null $projectPath  Путь к проекту (где находится composer.json)
	 * @param string|null $composerPath Путь к временному Composer (опционально)
	 *
	 */
	public static function init(?string $projectPath = null, ?string $composerPath = null): void {
		ComposerAction::$projectPath = $projectPath ? realpath($projectPath) : MH_ADMIN;
		if (!is_dir(ComposerAction::$projectPath)) {
			throw new \InvalidArgumentException("Директория проекта не существует: $projectPath");
		}

		ComposerAction::$composerPath = $composerPath ?? COMPOSER_DIR . '/composer.phar';
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

			// Используем `which` или альтернативу для проверки выполнения команды через PATH
			$command  = PHP_OS_FAMILY === 'Windows' ? 'where composer' : 'which composer';
			$exitCode = null;
			@exec($command, $output, $exitCode);

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
		$composerDir = dirname(ComposerAction::$composerPath);
		if (!is_dir($composerDir) && !mkdir($composerDir, 0755, true)) {
			throw new \RuntimeException("Не удалось создать директорию: $composerDir");
		}

		$context = stream_context_create(['http' => ['timeout' => 30]]);

		$pharContent = @file_get_contents('https://getcomposer.org/composer.phar', false, $context);
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
			throw new \RuntimeException('Неверная контрольная сумма Composer');
		}

		if (!@file_put_contents(ComposerAction::$composerPath, $pharContent)) {
			throw new \RuntimeException("Ошибка сохранения в " . ComposerAction::$composerPath);
		}

		chmod(ComposerAction::$composerPath, 0755);
	}

	/**
	 * Устанавливает зависимости
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function installDependencies(): void {
		ComposerAction::runCommand('install');
	}

	/**
	 * Обновляет зависимости
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function updateDependencies(): void {
		ComposerAction::runCommand('update');
	}

	/**
	 * Удаляет пакет
	 *
	 * @param string $package Имя пакета
	 *
	 * @throws \RuntimeException При ошибке выполнения
	 */
	public static function removePackage(string $package): void {
		ComposerAction::runCommand("remove $package");
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
					ComposerAction::requirePackage($p, $v, $isDev, $lockerFile);
				}

				return;
			}

			$dev     = $isDev ? '-dev' : '';
			$command = "require{$dev} $package";
			if ($version !== null) {
				$command .= ":$version";
			}
			ComposerAction::runCommand($command);
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
		if (ComposerAction::$projectPath === null) {
			throw new \RuntimeException("Класс не инициализирован. Вызовите метод init()");
		}

		$fullCommand = sprintf(
			'%s %s --working-dir=%s',
			ComposerAction::getComposerCommand(),
			$command,
			escapeshellarg(ComposerAction::$projectPath)
		);

		$output = ComposerAction::executeCommand($fullCommand);
		return $output;
	}

	/**
	 * Возвращает команду для вызова Composer
	 */
	private static function getComposerCommand(): string {
		return file_exists(ComposerAction::$composerPath) ? escapeshellarg(ComposerAction::$composerPath) : 'composer';
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

//		echo "<strong>Обработка команды</strong>: <code>{$command}</code><br>";

		$process = proc_open($command, $descriptors, $pipes, ComposerAction::$projectPath);
		if (!is_resource($process)) {
			throw new \RuntimeException("Ошибка запуска процесса");
		}

		// Используем try-finally для гарантированного освобождения ресурсов

		fclose($pipes[0]); // Закрываем STDIN, так как оно не используется

		$stdout     = '';
		$stderr     = '';
		$stdoutOpen = true;
		$stderrOpen = true;

		while ($stdoutOpen || $stderrOpen) {
			$read = [];
			if ($stdoutOpen) $read[] = $pipes[1];
			if ($stderrOpen) $read[] = $pipes[2];

			$write  = null;
			$except = null;
			$ready  = stream_select($read, $write, $except, 30); // Таймаут 30 секунд

			if ($ready === false) {
				throw new \RuntimeException("Ошибка при ожидании данных из процесса");
			}

			// Чтение stdout
			if (in_array($pipes[1], $read)) {
				$data = fread($pipes[1], 8192);
				if ($data === false || strlen($data) === 0) {
					fclose($pipes[1]);
					$stdoutOpen = false;
				} else {
					$stdout .= $data;
				}
			}

			// Чтение stderr
			if (in_array($pipes[2], $read)) {
				$data = fread($pipes[2], 8192);
				if ($data === false || strlen($data) === 0) {
					fclose($pipes[2]);
					$stderrOpen = false;
				} else {
					$stderr .= $data;
				}
			}

//			echo "Идёт обработка! Пожалуйста, подождите...<br>";
		}

		$exitCode = proc_close($process);
		$output   = trim($stdout . "\n" . $stderr);

		if ($exitCode !== 0) {
			throw new \RuntimeException("Ошибка ($exitCode): $output");
		}

		return $output;
	}
}
