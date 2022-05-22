<?php

namespace MaHarder\classes;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DeclineExtension extends AbstractExtension {


	public function getFunctions()
	{
		return array(
			new TwigFunction('decline', array($this, 'decline'))
		);
	}

	public function decline($number, $titles)

	{
		$cases = [2, 0, 1, 1, 1, 2];
		return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[($number%10<5)?$number%10:5] ];
	}
}