<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DeclineExtension extends AbstractExtension {


	public function getFunctions() : array {
		return [
			new TwigFunction('decline', [$this, 'decline'])
		];
	}

	/**
	 * @link https://gist.github.com/realmyst/1262561?permalink_comment_id=2032406#gistcomment-2032406
	 *
	 * @param    int|float    $number
	 * @param    array        $titles
	 *
	 * @return mixed
	 */
	public function decline(int|float $number, array $titles) : mixed {
		$lastDigit = $number % 10; // Последняя цифра числа
		$lastTwoDigits = $number % 100; // Последние две цифры числа

		// Условие для склонения
		if ($lastDigit === 1 && $lastTwoDigits !== 11) {
			return $titles[0]; // Единичное число (например, "яблоко")
		}

		if ($lastDigit >= 2 && $lastDigit <= 4 && ($lastTwoDigits < 10 || $lastTwoDigits >= 20)) {
			return $titles[1]; // Склонения для 2, 3, 4 (например, "яблока")
		}

		return $titles[2]; // Всё остальное (например, "яблок")
	}
}