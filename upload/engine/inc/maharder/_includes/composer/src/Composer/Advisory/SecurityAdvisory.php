<?php declare(strict_types=1);











namespace Composer\Advisory;

use Composer\Semver\Constraint\ConstraintInterface;
use DateTimeImmutable;

class SecurityAdvisory extends PartialSecurityAdvisory
{




public $title;





public $cve;





public $link;





public $reportedAt;





public $sources;





public function __construct(string $packageName, string $advisoryId, ConstraintInterface $affectedVersions, string $title, array $sources, DateTimeImmutable $reportedAt, ?string $cve = null, ?string $link = null)
{
parent::__construct($packageName, $advisoryId, $affectedVersions);

$this->title = $title;
$this->sources = $sources;
$this->reportedAt = $reportedAt;
$this->cve = $cve;
$this->link = $link;
}
}
