<?php

declare(strict_types=1);

namespace DevCraft\Modules\Admin\Services;

/**
 * Формирует HTML- и текстовое представление сообщения журнала для readonly-страницы.
 */
final class LogMessagePresenter {

	/**
	 * @return array{display_html: string, copy_text: string, is_structured: bool}
	 */
	public function present(string $rawMessage): array {
		$displayHtml  = $rawMessage;
		$copyText     = html_entity_decode(strip_tags($rawMessage), ENT_QUOTES|ENT_HTML5, 'UTF-8');
		$isStructured = false;

		$trimmed = trim($copyText);
		if($trimmed !== '' && ($trimmed[0] === '{' || $trimmed[0] === '[')) {
			$decoded = json_decode($trimmed, true);
			if(json_last_error() === JSON_ERROR_NONE) {
				$encoded = json_encode($decoded, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
				if(is_string($encoded)) {
					$copyText     = $encoded;
					$isStructured = true;
				}
			}
		}

		return [
			'display_html'  => $displayHtml,
			'copy_text'     => $copyText,
			'is_structured' => $isStructured,
		];
	}

}
