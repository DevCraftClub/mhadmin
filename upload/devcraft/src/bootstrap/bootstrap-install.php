<?php

declare(strict_types=1);

/**
 * Фоновый запуск composer install для bootstrap без vendor/autoload.
 */

if(!defined('DATALIFEENGINE')) {
	die('Hacking attempt!');
}

/**
 * @return array{dir:string,log:string,pid:string,exit:string}
 */
function dc_bootstrap_state_paths(): array {
	$dir = ROOT_DIR . '/devcraft/cache/bootstrap';

	return [
		'dir'  => $dir,
		'log'  => $dir . '/install.log',
		'pid'  => $dir . '/install.pid',
		'exit' => $dir . '/install.exit',
	];
}

function dc_bootstrap_ensure_state_dir(): void {
	$paths = dc_bootstrap_state_paths();

	if(!is_dir($paths['dir']) && !mkdir($paths['dir'], 0755, true) && !is_dir($paths['dir'])) {
		throw new RuntimeException('Не удалось создать каталог bootstrap: ' . $paths['dir']);
	}
}

function dc_bootstrap_vendor_ready(): bool {
	return is_file(ROOT_DIR . '/devcraft/vendor/autoload.php');
}

function dc_bootstrap_apply_composer_env(): void {
	$home         = ROOT_DIR . '/devcraft';
	$composerHome = $home . '/.composer';

	if(!is_dir($composerHome) && !mkdir($composerHome, 0755, true) && !is_dir($composerHome)) {
		throw new RuntimeException('Не удалось создать каталог COMPOSER_HOME: ' . $composerHome);
	}

	putenv('HOME=' . $home);
	putenv('COMPOSER_HOME=' . $composerHome);
}

function dc_bootstrap_ensure_composer_phar(): bool {
	$composerPhar = ROOT_DIR . '/devcraft/composer.phar';

	if(is_file($composerPhar)) {
		return true;
	}

	$download = @file_get_contents('https://getcomposer.org/composer-stable.phar');
	if($download === false) {
		return false;
	}

	file_put_contents($composerPhar, $download);

	return is_file($composerPhar);
}

function dc_bootstrap_read_log_excerpt(int $maxBytes = 65536): string {
	$paths = dc_bootstrap_state_paths();

	if(!is_file($paths['log'])) {
		return '';
	}

	$size = (int) filesize($paths['log']);
	if($size <= $maxBytes) {
		$content = file_get_contents($paths['log']);

		return $content === false? '' : $content;
	}

	$handle = fopen($paths['log'], 'rb');
	if($handle === false) {
		return '';
	}

	fseek($handle, -$maxBytes, SEEK_END);
	$chunk = (string) stream_get_contents($handle);
	fclose($handle);

	return $chunk;
}

function dc_bootstrap_read_pid(): int {
	$paths = dc_bootstrap_state_paths();

	if(!is_file($paths['pid'])) {
		return 0;
	}

	return (int) trim((string) file_get_contents($paths['pid']));
}

function dc_bootstrap_is_pid_running(int $pid): bool {
	if($pid <= 0) {
		return false;
	}

	if(function_exists('posix_kill')) {
		return @posix_kill($pid, 0);
	}

	return is_file('/proc/' . $pid);
}

function dc_bootstrap_read_exit_code(): ?int {
	$paths = dc_bootstrap_state_paths();

	if(!is_file($paths['exit'])) {
		return NULL;
	}

	return (int) trim((string) file_get_contents($paths['exit']));
}

function dc_bootstrap_reset_install_state(): void {
	$paths = dc_bootstrap_state_paths();

	foreach([$paths['log'], $paths['pid'], $paths['exit']] as $file) {
		if(is_file($file)) {
			unlink($file);
		}
	}
}

function dc_bootstrap_start_install(): void {
	dc_bootstrap_ensure_state_dir();
	dc_bootstrap_reset_install_state();
	dc_bootstrap_apply_composer_env();

	$paths        = dc_bootstrap_state_paths();
	$composerPhar = ROOT_DIR . '/devcraft/composer.phar';
	$workingDir   = ROOT_DIR . '/devcraft';
	$home         = ROOT_DIR . '/devcraft';
	$composerHome = $home . '/.composer';

	file_put_contents($paths['log'], "Запуск composer install...\n");

	$installShell = sprintf(
		'export HOME=%s COMPOSER_HOME=%s; php %s install --working-dir=%s --no-interaction --no-ansi >> %s 2>&1; echo $? > %s',
		escapeshellarg($home),
		escapeshellarg($composerHome),
		escapeshellarg($composerPhar),
		escapeshellarg($workingDir),
		escapeshellarg($paths['log']),
		escapeshellarg($paths['exit']),
	);

	$launcher = sprintf(
		'nohup bash -c %s </dev/null >/dev/null 2>&1 & echo $!',
		escapeshellarg($installShell),
	);

	$output = [];
	exec($launcher, $output);

	$pid = isset($output[0])? (int) trim((string) $output[0]) : 0;
	if($pid <= 0) {
		throw new RuntimeException('Не удалось запустить фоновый composer install');
	}

	file_put_contents($paths['pid'], (string) $pid);
}

/**
 * @return array{currentStep:string,status:string,message:string,logExcerpt:string}
 */
function dc_bootstrap_build_response(string $operation): array {
	if(!dc_bootstrap_vendor_ready()) {
		if(!dc_bootstrap_ensure_composer_phar()) {
			return [
				'currentStep' => 'download_composer',
				'status'      => 'failed',
				'message'     => 'Не удалось скачать composer.phar',
				'logExcerpt'  => '',
			];
		}

		$pid         = dc_bootstrap_read_pid();
		$isRunning   = dc_bootstrap_is_pid_running($pid);
		$shouldStart = in_array($operation, ['start', 'retry'], true);

		if(!$isRunning && $shouldStart) {
			dc_bootstrap_start_install();
			$isRunning = true;
		}

		if($isRunning) {
			return [
				'currentStep' => 'install_defaults',
				'status'      => 'in_progress',
				'message'     => 'Установка пакетов Composer…',
				'logExcerpt'  => dc_bootstrap_read_log_excerpt(),
			];
		}

		$exitCode = dc_bootstrap_read_exit_code();

		if($exitCode === NULL && !dc_bootstrap_vendor_ready()) {
			return [
				'currentStep' => 'install_defaults',
				'status'      => 'in_progress',
				'message'     => 'Завершение установки пакетов…',
				'logExcerpt'  => dc_bootstrap_read_log_excerpt(),
			];
		}

		if($exitCode === 0 && dc_bootstrap_vendor_ready()) {
			return [
				'currentStep' => 'finalize',
				'status'      => 'completed',
				'message'     => 'Инициализация завершена',
				'logExcerpt'  => dc_bootstrap_read_log_excerpt(),
			];
		}

		return [
			'currentStep' => 'install_defaults',
			'status'      => 'failed',
			'message'     => 'Не удалось установить default-пакеты',
			'logExcerpt'  => dc_bootstrap_read_log_excerpt(),
		];
	}

	return [
		'currentStep' => 'finalize',
		'status'      => 'completed',
		'message'     => 'Инициализация завершена',
		'logExcerpt'  => '',
	];
}

function dc_bootstrap_send_json(array $payload): void {
	echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}
