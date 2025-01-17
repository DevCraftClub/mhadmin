<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DateTimeFormatter extends AbstractExtension {

	/**
	 * Преобразует объект DateTimeImmutable или строку в строку формата даты и времени.
	 *
	 * @param DateTimeImmutable|string|null $dateString Исходное значение даты и времени,
	 *                                                  которое может быть объектом DateTimeImmutable,
	 *                                                  строкой или null.
	 * @param string $format Формат, в который нужно преобразовать дату (по умолчанию 'Y-m-d H:i:s').
	 * @param string $sourceFormat Формат исходной строки даты, если $dateString является строкой
	 *                              (по умолчанию 'Y-m-d H:i:s').
	 * @return string Отформатированное значение даты и времени или пустая строка, если $dateString равно null.
	 *
	 * @throws ValueError Выбрасывается, если невозможно создать объект DateTimeImmutable из строки
	 *                    с помощью указанного формата.
	 *
	 * @see DateTimeImmutable::createFromFormat()
	 * @see DateTimeImmutable::format()
	 * @global string $sourceFormat
	 * @global string $format
	 */
	public function dateTimeImmutable(DateTimeImmutable|string|null $dateString, string $format = 'Y-m-d H:i:s', string $sourceFormat = 'Y-m-d H:i:s'): string {
		if (!$dateString) return '';

		if (is_string($dateString)) {
			$dateString = DateTimeImmutable::createFromFormat($sourceFormat, $dateString);
		}
		return $dateString->format($format);
	}

	public function getFunctions(): array {
		return [
			new TwigFunction('from_dti', [$this, 'dateTimeImmutable'])

		];
	}

	public function getFilters(): array {
		return [
			new TwigFilter('from_dti', [$this, 'dateTimeImmutable'])
		];
	}
}