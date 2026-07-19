<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

use DevCraft\Core\Composer\ComposerRuntimeAdapter;

final class BootstrapSetupService {

	public function runStep(string $operation): array {
		if($operation === 'start' || $operation === 'next' || $operation === 'retry') {
			$composerPhar = ROOT_DIR . '/devcraft/composer.phar';
			if(!is_file($composerPhar)) {
				$download = @file_get_contents('https://getcomposer.org/composer-stable.phar');
				if($download === false) {
					return $this->failed('download_composer', 'Не удалось скачать composer.phar');
				}
				file_put_contents($composerPhar, $download);
			}

			$result = (new ComposerRuntimeAdapter())->runInstallDefaults();
			if($result['status'] !== 'ok') {
				return $this->failed('install_defaults', 'Не удалось установить default-пакеты', $result['details']['output'] ?? '');
			}

			return [
				'currentStep' => 'finalize',
				'status'      => 'completed',
				'message'     => 'Инициализация завершена',
				'logExcerpt'  => (string) ($result['details']['output'] ?? ''),
			];
		}

		return $this->failed('check_vendor', 'Неизвестная операция bootstrap');
	}

	private function failed(string $step, string $message, string $log = ''): array {
		return [
			'currentStep' => $step,
			'status'      => 'failed',
			'message'     => $message,
			'logExcerpt'  => $log,
		];
	}

}
