<?php

declare(strict_types=1);

namespace Aashro\AtAnalyticsLite\Provider;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use Aashro\AtAnalyticsLite\Repository\VisitRepository;

final class PageModuleAnalyticsProvider
{
    public function __construct(
        private readonly VisitRepository $visitRepository
    ) {}

    #[AsEventListener('at-analytics-lite-page-module')]
    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $request = $event->getRequest();
        $pageUid = (int)($request->getQueryParams()['id'] ?? 0);
        if ($pageUid <= 0) {
            return;
        }

        $visitsThisMonth = $this->visitRepository->countPageVisitsCurrentMonth($pageUid);
        $event->addHeaderContent($this->renderBox($visitsThisMonth));
    }

    private function renderBox(int $visitsThisMonth): string
    {
        return '<div class="panel panel-default" style="margin-bottom: 16px;">'
            . '<div class="panel-heading"><strong>Per-Page Analytics</strong></div>'
            . '<div class="panel-body">'
            . '<p style="margin:0;font-size:14px;">This page has <strong>' . number_format($visitsThisMonth) . '</strong> visits this month.</p>'
            . '</div>'
            . '</div>';
    }
}