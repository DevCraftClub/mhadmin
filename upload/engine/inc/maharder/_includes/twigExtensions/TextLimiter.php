<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig-расширение, предоставляющее инструмент для ограничения длины текста.
 *
 * Класс расширяет AbstractExtension и включает фильтр для обрезки текста
 * до заданной длины. Полезно для шаблонов, где требуется ограничение
 * длины текста, например, для кратких описаний или превью.
 */
class TextLimiter extends AbstractExtension  {

	public function limit( ?string $text, int $limit = 100 ) : string {
		if (is_null($text)) return '';

		$formated_text = $text;
		if ( strlen( $formated_text ) > $limit ) {
			$formated_text = substr( $formated_text, 0, $limit ). '...';
		}

		return $formated_text;
	}

	public function getFunctions() : array {
		return [

		];
	}

	public function getFilters() : array {
		return [
			new TwigFilter('limit_text', [$this, 'limit'])
		];
	}
}