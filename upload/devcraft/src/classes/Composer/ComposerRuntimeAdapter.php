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
	 * Каталоги HOME / COMPOSER_HOME под `devcraft/` (создаёт `.composer` при необходимости).
	 *
	 * @return array{home: string, composer_home: string}
	 */
	public static function composerEnvironmentPaths(): array {
		$home         = ROOT_DIR . '/devcraft';
		$composerHome = $home . '/.composer';

		if(!is_dir($composerHome) && !mkdir($composerHome, 0755, true) && !is_dir($composerHome)) {
			throw new \RuntimeException('Не удалось создать каталог COMPOSER_HOME: ' . $composerHome);
		}

		return [
			'home'          => $home,
			'composer_home' => $composerHome,
		];
	}

	/**
	 * Задаёт HOME и COMPOSER_HOME для запуска Composer из веб-контекста (php-fpm/apache).
	 *
	 * На хостингах `putenv` часто в `disable_functions`: тогда пишем только $_ENV / $_SERVER,
	 * а для дочернего процесса переменные передаём через префикс `env` в {@see run()}.
	 */
	public static function applyProcessEnvironment(): void {
		$paths = self::composerEnvironmentPaths();

		$_ENV['HOME']              = $paths['home'];
		$_SERVER['HOME']           = $paths['home'];
		$_ENV['COMPOSER_HOME']     = $paths['composer_home'];
		$_SERVER['COMPOSER_HOME']  = $paths['composer_home'];

		// putenv часто отключён на shared-хостинге — не падаем, а опираемся на env-префикс в run().
		if(function_exists('putenv')) {
			\putenv('HOME=' . $paths['home']);
			\putenv('COMPOSER_HOME=' . $paths['composer_home']);
		}
	}

	public function install(string $package, ?string $version = NULL): ComposerActionResult {
		$target = $version !== NULL && $version !== ''? $package . ':' . $version : $package;

		return $this->run(['require', $target], 'Пакет успешно установлен');
	}

	public function update(string $package, ?string $version = NULL): ComposerActionResult {
		if($version !== NULL && $version !== '') {
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
	 * Ставит все зависимости из `composer.json` / lock (`composer install`).
	 */
	public function installAllFromComposerJson(): ComposerActionResult {
		return $this->run(['install'], 'Все пакеты из composer.json установлены');
	}

	/**
	 * @return array{status:string,details:array<string,mixed>,message?:string}
	 */
	public function runInstallDefaults(): array {
		$result = $this->installAllFromComposerJson();
		$data   = $result->toArray();

		return [
			'status'  => $data['status'],
			'details' => $data['details'],
			'message' => $data['message'],
		];
	}

	/**
	 * @param   list<string>  $args
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
		$paths = self::composerEnvironmentPaths();

		if(!function_exists('exec')) {
			return ComposerActionResult::error(
				'На сервере отключена функция exec (disable_functions). Нужны exec и putenv.',
			);
		}

		// Явный env для дочернего процесса: putenv может быть недоступен (disable_functions).
		$cmd = sprintf(
			'env HOME=%s COMPOSER_HOME=%s %s',
			escapeshellarg($paths['home']),
			escapeshellarg($paths['composer_home']),
			$cmd,
		);

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

		if($code === 0) {
			$verb = (string) ($args[0] ?? '');

			if($verb === 'install' || $verb === 'update') {
				require_once ROOT_DIR . '/devcraft/src/bootstrap/composer_marker.php';
				dc_composer_mark_initialized(true);
			}
		}

		return ComposerActionResult::ok($successMessage, [
			'command' => $cmd,
			'output'  => implode(PHP_EOL, $output),
		]);
	}

}
