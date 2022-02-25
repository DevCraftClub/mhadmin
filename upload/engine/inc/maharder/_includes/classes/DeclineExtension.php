<?php

class DeclineExtension extends Twig_Extension {
	public function __construct()
	{

	}

	public function getFunctions()
	{
		$functions = array(
			new \Twig_SimpleFunction('decline', array($this, 'decline'))
		);

		return $functions;
	}

	public function decline($number, $titles)

	{
		$cases = [2, 0, 1, 1, 1, 2];
		return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[($number%10<5)?$number%10:5] ];
	}
}