<?php

declare(strict_types=1);

namespace DevCraft\Core\Composer;

/**
 * Выполняет команды Composer CLI и возвращает нормализованный результат.
 *
 * @see PackagePolicyService Проверка политик перед вызовом.
 * @see ComposerDbSyncService Обновление БД после успешного действия.
 *
 * @example
 *     ComposerRuntimeAdapter::applyProcessEnvironment();
 *     $result = (new ComposerRuntimeAdapter())->install('vendor/package');
 */
final class ComposerRuntimeAdapter {

	/**
	 * Задаёт HOME и COMPOSER_HOME для запуска Composer из веб-контекста (php-fpm/apache).
	 */
	public static function applyProcessEnvironment(): void {
		$home         = ROOT_DIR . '/devcraft';
		$composerHome = $home . '/.composer';

		if(!is_dir($composerHome) && !mkdir($composerHome, 0755, true) && !is_dir($composerHome)) {
			throw new \RuntimeException('Не удалось создать каталог COMPOSER_HOME: ' . $composerHome);
		}

		putenv('HOME=' . $home);
		putenv('COMPOSER_HOME=' . $composerHome);
	}

	public function install(string $package, ?string $version = null): ComposerActionResult {
		$target = $version !== null && $version !== '' ? $package . ':' . $version : $package;

		return $this->run(['require', $target], 'Пакет успешно установлен');
	}

	public function update(string $package, ?string $version = null): ComposerActionResult {
		if($version !== null && $version !== '') {
			return $this->run(['require', $package . ':' . $version], 'Пакет успешно обновлён');
		}

		return $this->run(['update', $package], 'Пакет успешно обновлён');
	}

	public function remove(string $package): ComposerActionResult {
		return $this->run(['remove', $package], 'Пакет успешно удалён');
	}

	public function dumpAutoload(): ComposerActionResult {
		return $this->run(['dump-autoload'], 'Autoload успешно пересобран');
	}

	/**
	 * @return array{status:string,details:array<string,mixed>}
	 */
	public function runInstallDefaults(): array {
		$result = $this->run(['install'], 'Пакеты по умолчанию установлены');

		return [
			'status'  => $result->status,
			'details' => $result->details,
		];
	}

	/**
	 * @param list<string> $args
	 */
	private function run(array $args, string $successMessage): ComposerActionResult {
		$composerPhar = ROOT_DIR . '/devcraft/composer.phar';
		$composerJson = ROOT_DIR . '/devcraft/composer.json';

		if(!is_file($composerJson)) {
			return ComposerActionResult::error('Файл composer.json не найден');
		}

		$cmd = is_file($composerPhar)
			? 'php ' . escapeshellarg($composerPhar)
			: 'composer';

		$cmd .= ' ' . implode(' ', array_map(static fn(string $arg): string => escapeshellarg($arg), $args));
		$cmd .= ' --working-dir=' . escapeshellarg(ROOT_DIR . '/devcraft') . ' --no-interaction';

		self::applyProcessEnvironment();

		$output = [];
		$code   = 1;
		exec($cmd . ' 2>&1', $output, $code);

		if($code !== 0) {
			return ComposerActionResult::requiresDecision(
				'Команда Composer завершилась с ошибкой',
				[
					'command'   => $cmd,
					'exit_code' => $code,
					'output'    => implode(PHP_EOL, $output),
				],
			);
		}

		return ComposerActionResult::ok($successMessage, [
			'command' => $cmd,
			'output'  => implode(PHP_EOL, $output),
		]);
	}
}
