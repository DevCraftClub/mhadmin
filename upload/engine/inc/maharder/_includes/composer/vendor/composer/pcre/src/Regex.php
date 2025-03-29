<?php










namespace Composer\Pcre;

class Regex
{



public static function isMatch(string $pattern, string $subject, int $offset = 0): bool
{
return (bool) Preg::match($pattern, $subject, $matches, 0, $offset);
}





public static function match(string $pattern, string $subject, int $flags = 0, int $offset = 0): MatchResult
{
if (($flags & PREG_OFFSET_CAPTURE) !== 0) {
throw new \InvalidArgumentException('PREG_OFFSET_CAPTURE is not supported as it changes the return type, use matchWithOffsets() instead');
}

$count = Preg::match($pattern, $subject, $matches, $flags, $offset);

return new MatchResult($count, $matches);
}







public static function matchWithOffsets(string $pattern, string $subject, int $flags = 0, int $offset = 0): MatchWithOffsetsResult
{
$count = Preg::matchWithOffsets($pattern, $subject, $matches, $flags, $offset);

return new MatchWithOffsetsResult($count, $matches);
}





public static function matchAll(string $pattern, string $subject, int $flags = 0, int $offset = 0): MatchAllResult
{
if (($flags & PREG_OFFSET_CAPTURE) !== 0) {
throw new \InvalidArgumentException('PREG_OFFSET_CAPTURE is not supported as it changes the return type, use matchAllWithOffsets() instead');
}

if (($flags & PREG_SET_ORDER) !== 0) {
throw new \InvalidArgumentException('PREG_SET_ORDER is not supported as it changes the return type');
}

$count = Preg::matchAll($pattern, $subject, $matches, $flags, $offset);

return new MatchAllResult($count, $matches);
}







public static function matchAllWithOffsets(string $pattern, string $subject, int $flags = 0, int $offset = 0): MatchAllWithOffsetsResult
{
$count = Preg::matchAllWithOffsets($pattern, $subject, $matches, $flags, $offset);

return new MatchAllWithOffsetsResult($count, $matches);
}





public static function replace($pattern, $replacement, $subject, int $limit = -1): ReplaceResult
{
$result = Preg::replace($pattern, $replacement, $subject, $limit, $count);

return new ReplaceResult($count, $result);
}






public static function replaceCallback($pattern, callable $replacement, $subject, int $limit = -1, int $flags = 0): ReplaceResult
{
$result = Preg::replaceCallback($pattern, $replacement, $subject, $limit, $count, $flags);

return new ReplaceResult($count, $result);
}






public static function replaceCallbackArray(array $pattern, $subject, int $limit = -1, int $flags = 0): ReplaceResult
{
$result = Preg::replaceCallbackArray($pattern, $subject, $limit, $count, $flags);

return new ReplaceResult($count, $result);
}
}
