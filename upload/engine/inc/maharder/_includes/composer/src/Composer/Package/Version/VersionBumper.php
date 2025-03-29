<?php declare(strict_types=1);











namespace Composer\Package\Version;

use Composer\Package\PackageInterface;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Pcre\Preg;
use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Intervals;
use Composer\Util\Platform;





class VersionBumper
{













public function bumpRequirement(ConstraintInterface $constraint, PackageInterface $package): string
{
$parser = new VersionParser();
$prettyConstraint = $constraint->getPrettyString();
if (str_starts_with($constraint->getPrettyString(), 'dev-')) {
return $prettyConstraint;
}

$version = $package->getVersion();
if (str_starts_with($package->getVersion(), 'dev-')) {
$loader = new ArrayLoader($parser);
$dumper = new ArrayDumper();
$extra = $loader->getBranchAlias($dumper->dump($package));


if (null === $extra || $extra === VersionParser::DEFAULT_BRANCH_ALIAS) {
return $prettyConstraint;
}

$version = $extra;
}

$intervals = Intervals::get($constraint);


if (\count($intervals['branches']['names']) > 0) {
return $prettyConstraint;
}

$major = Preg::replace('{^(\d+).*}', '$1', $version);
$newPrettyConstraint = '^'.Preg::replace('{(?:\.(?:0|9999999))+(-dev)?$}', '', $version);


if (!Preg::isMatch('{^\^\d+(\.\d+)*$}', $newPrettyConstraint)) {
return $prettyConstraint;
}

$pattern = '{
            (?<=,|\ |\||^) # leading separator
            (?P<constraint>
                \^'.$major.'(?:\.\d+)* # e.g. ^2.anything
                | ~'.$major.'(?:\.\d+)? # e.g. ~2 or ~2.2 but no more
                | '.$major.'(?:\.[*x])+ # e.g. 2.* or 2.*.* or 2.x.x.x etc
            )
            (?=,|$|\ |\||@) # trailing separator
        }x';
if (Preg::isMatchAllWithOffsets($pattern, $prettyConstraint, $matches)) {
$modified = $prettyConstraint;
foreach (array_reverse($matches['constraint']) as $match) {
$modified = substr_replace($modified, $newPrettyConstraint, $match[1], Platform::strlen($match[0]));
}


$newConstraint = $parser->parseConstraints($modified);
if (Intervals::isSubsetOf($newConstraint, $constraint) && Intervals::isSubsetOf($constraint, $newConstraint)) {
return $prettyConstraint;
}

return $modified;
}

return $prettyConstraint;
}
}
