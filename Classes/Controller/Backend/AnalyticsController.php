<?php
namespace Aashro\AtAnalyticsLite\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Aashro\AtAnalyticsLite\Repository\VisitRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

class AnalyticsController extends ActionController
{
    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected VisitRepository $visitRepository
    ) {}

    public function dashboardAction(): ResponseInterface
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addJsFile('https://cdn.datatables.net/v/dt/dt-3.0.0/datatables.min.js', 'text/javascript');
        $pageRenderer->addJsFile('EXT:at_analytics_lite/Resources/Public/Javascript/DataTable.js', 'text/javascript');
            
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $last7DaysTraffic = $this->visitRepository->findLastDaysTraffic(7);
        $maxTraffic = max(1, ...array_map(static fn(array $row): int => (int)$row['total'], $last7DaysTraffic));
        $trafficChart = array_map(
            static fn(array $row): array => $row + ['percent' => ((int)$row['total'] / $maxTraffic) * 100],
            $last7DaysTraffic
        );
        $deviceBreakdown = $this->visitRepository->getDeviceBreakdown();

        $moduleTemplate->assignMultiple([
            'totalVisits' => $this->visitRepository->countAll(),
            'visitsToday' => $this->visitRepository->countToday(),
            'liveVisitors' => $this->visitRepository->countLastMinutes(5),
            'deviceDesktop' => $deviceBreakdown['desktop'],
            'deviceMobile' => $deviceBreakdown['mobile'],
            'deviceTablet' => $deviceBreakdown['tablet'],
            'trafficChart' => $trafficChart,
            'topPages' => $this->visitRepository->findTopPages()
        ]);

        return $moduleTemplate->renderResponse('Backend/Dashboard');
    }
}