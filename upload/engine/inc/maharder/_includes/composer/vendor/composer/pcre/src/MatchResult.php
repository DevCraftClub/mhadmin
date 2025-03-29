<?php










namespace Composer\Pcre;

final class MatchResult
{






public $matches;





public $matched;





public function __construct($count, array $matches)
{
$this->matches = $matches;
$this->matched = (bool) $count;
}
}
