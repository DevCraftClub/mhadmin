<?php declare(strict_types=1);











namespace Composer\Package\Archiver;

use Composer\Downloader\DownloadManager;
use Composer\Package\RootPackageInterface;
use Composer\Pcre\Preg;
use Composer\Util\Filesystem;
use Composer\Util\Loop;
use Composer\Util\SyncHelper;
use Composer\Json\JsonFile;
use Composer\Package\CompletePackageInterface;





class ArchiveManager
{

protected $downloadManager;

protected $loop;




protected $archivers = [];




protected $overwriteFiles = true;




public function __construct(DownloadManager $downloadManager, Loop $loop)
{
$this->downloadManager = $downloadManager;
$this->loop = $loop;
}

public function addArchiver(ArchiverInterface $archiver): void
{
$this->archivers[] = $archiver;
}








public function setOverwriteFiles(bool $overwriteFiles): self
{
$this->overwriteFiles = $overwriteFiles;

return $this;
}








public function getPackageFilename(CompletePackageInterface $package): string
{
if ($package->getArchiveName()) {
$baseName = $package->getArchiveName();
} else {
$baseName = Preg::replace('#[^a-z0-9-_]#i', '-', $package->getName());
}
$nameParts = [$baseName];

if (null !== $package->getDistReference() && Preg::isMatch('{^[a-f0-9]{40}$}', $package->getDistReference())) {
array_push($nameParts, $package->getDistReference(), $package->getDistType());
} else {
array_push($nameParts, $package->getPrettyVersion(), $package->getDistReference());
}

if ($package->getSourceReference()) {
$nameParts[] = substr(sha1($package->getSourceReference()), 0, 6);
}

$name = implode('-', array_filter($nameParts, static function ($p): bool {
return !empty($p);
}));

return str_replace('/', '-', $name);
}














public function archive(CompletePackageInterface $package, string $format, string $targetDir, ?string $fileName = null, bool $ignoreFilters = false): string
{
if (empty($format)) {
throw new \InvalidArgumentException('Format must be specified');
}


$usableArchiver = null;
foreach ($this->archivers as $archiver) {
if ($archiver->supports($format, $package->getSourceType())) {
$usableArchiver = $archiver;
break;
}
}


if (null === $usableArchiver) {
throw new \RuntimeException(sprintf('No archiver found to support %s format', $format));
}

$filesystem = new Filesystem();

if ($package instanceof RootPackageInterface) {
$sourcePath = realpath('.');
} else {

$sourcePath = sys_get_temp_dir().'/composer_archive'.uniqid();
$filesystem->ensureDirectoryExists($sourcePath);

try {

$promise = $this->downloadManager->download($package, $sourcePath);
SyncHelper::await($this->loop, $promise);
$promise = $this->downloadManager->install($package, $sourcePath);
SyncHelper::await($this->loop, $promise);
} catch (\Exception $e) {
$filesystem->removeDirectory($sourcePath);
throw $e;
}


if (file_exists($composerJsonPath = $sourcePath.'/composer.json')) {
$jsonFile = new JsonFile($composerJsonPath);
$jsonData = $jsonFile->read();
if (!empty($jsonData['archive']['name'])) {
$package->setArchiveName($jsonData['archive']['name']);
}
if (!empty($jsonData['archive']['exclude'])) {
$package->setArchiveExcludes($jsonData['archive']['exclude']);
}
}
}

if (null === $fileName) {
$packageName = $this->getPackageFilename($package);
} else {
$packageName = $fileName;
}


$filesystem->ensureDirectoryExists($targetDir);
$target = realpath($targetDir).'/'.$packageName.'.'.$format;
$filesystem->ensureDirectoryExists(dirname($target));

if (!$this->overwriteFiles && file_exists($target)) {
return $target;
}


$tempTarget = sys_get_temp_dir().'/composer_archive'.uniqid().'.'.$format;
$filesystem->ensureDirectoryExists(dirname($tempTarget));

$archivePath = $usableArchiver->archive($sourcePath, $tempTarget, $format, $package->getArchiveExcludes(), $ignoreFilters);
$filesystem->rename($archivePath, $target);


if (!$package instanceof RootPackageInterface) {
$filesystem->removeDirectory($sourcePath);
}
$filesystem->remove($tempTarget);

return $target;
}
}
