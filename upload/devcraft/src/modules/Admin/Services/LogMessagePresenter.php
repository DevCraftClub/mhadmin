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
	/**
	 * Снимает экранирование записи из БД: сущности HTML, переводы строк, без тегов.
	 *
	 * @since 200.4.2
	 */
	public static function plainText(string $rawMessage): string {
		$text = html_entity_decode($rawMessage, ENT_QUOTES|ENT_HTML5, 'UTF-8');
		$text = function_exists('br2nl')? br2nl($text, "\n") : (preg_replace('#<br\s*/?>#i', "\n", $text) ?? $text);

		return trim(html_entity_decode(strip_tags($text), ENT_QUOTES|ENT_HTML5, 'UTF-8'));
	}

	public function present(string $rawMessage): array {
		$displayHtml  = $rawMessage;
		$copyText     = self::plainText($rawMessage);
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
