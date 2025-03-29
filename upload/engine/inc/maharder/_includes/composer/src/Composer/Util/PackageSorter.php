<?php declare(strict_types=1);











namespace Composer\Util;

use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;

class PackageSorter
{









public static function getMostCurrentVersion(array $packages): ?PackageInterface
{
return array_reduce($packages, static function ($carry, $pkg) {
if ($carry === null) {
return $pkg;
}

if ($pkg->isDefaultBranch()) {
return $pkg;
}

if (!$carry->isDefaultBranch() && version_compare($carry->getVersion(), $pkg->getVersion(), '<')) {
return $pkg;
}

return $carry;
});
}








public static function sortPackagesAlphabetically(array $packages): array
{
usort($packages, static function (PackageInterface $a, PackageInterface $b) {
return $a->getName() <=> $b->getName();
});

return $packages;
}










public static function sortPackages(array $packages, array $weights = []): array
{
$usageList = [];

foreach ($packages as $package) {
$links = $package->getRequires();
if ($package instanceof RootPackageInterface) {
$links = array_merge($links, $package->getDevRequires());
}
foreach ($links as $link) {
$target = $link->getTarget();
$usageList[$target][] = $package->getName();
}
}
$computing = [];
$computed = [];
$computeImportance = static function ($name) use (&$computeImportance, &$computing, &$computed, $usageList, $weights) {

if (isset($computed[$name])) {
return $computed[$name];
}


if (isset($computing[$name])) {
return 0;
}

$computing[$name] = true;
$weight = $weights[$name] ?? 0;

if (isset($usageList[$name])) {
foreach ($usageList[$name] as $user) {
$weight -= 1 - $computeImportance($user);
}
}

unset($computing[$name]);
$computed[$name] = $weight;

return $weight;
};

$weightedPackages = [];

foreach ($packages as $index => $package) {
$name = $package->getName();
$weight = $computeImportance($name);
$weightedPackages[] = ['name' => $name, 'weight' => $weight, 'index' => $index];
}

usort($weightedPackages, static function (array $a, array $b): int {
if ($a['weight'] !== $b['weight']) {
return $a['weight'] - $b['weight'];
}

return strnatcasecmp($a['name'], $b['name']);
});

$sortedPackages = [];

foreach ($weightedPackages as $pkg) {
$sortedPackages[] = $packages[$pkg['index']];
}

return $sortedPackages;
}
}
