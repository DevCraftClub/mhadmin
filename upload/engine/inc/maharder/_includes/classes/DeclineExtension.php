<?php

namespace MaHarder\classes;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DeclineExtension extends AbstractExtension {


	public function getFunctions() {
		return [
			new TwigFunction('decline', [$this, 'decline'])
		];
	}

	/**
	 * @link https://gist.github.com/realmyst/1262561?permalink_comment_id=2032406#gistcomment-2032406
	 *
	 * @param $number
	 * @param $titles
	 *
	 * @return mixed
	 */
	public function decline($number, $titles) {
		return $titles[($number % 10 === 1 && $number % 100 !== 11)
			? 0
			: ($number % 10 >= 2 && $number % 10 <= 4
			   && ($number % 100 < 10
			       || $number % 100 >= 20) ? 1 : 2)];
	}
}