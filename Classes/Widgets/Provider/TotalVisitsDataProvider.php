<?php
namespace Aashro\AtAnalyticsLite\Widgets\Provider;

use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;
use Aashro\AtAnalyticsLite\Repository\VisitRepository;

class TotalVisitsDataProvider implements NumberWithIconDataProviderInterface
{
    public function __construct(
        protected VisitRepository $visitRepository
    ) {}

    public function getNumber(): int
    {
        return $this->visitRepository->countAll();
    }
}