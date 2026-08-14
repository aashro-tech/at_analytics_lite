<?php
namespace Aashro\AtAnalyticsLite\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;

class VisitRepository
{
    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    public function countAll(): int
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');
        return (int)$qb->count('uid')
            ->from('tx_atanalyticslite_visit')
            ->executeQuery()
            ->fetchOne();
    }

    public function countToday(): int
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');
        return (int)$qb->count('uid')
            ->from('tx_atanalyticslite_visit')
            ->where(
                $qb->expr()->eq(
                    'visit_date',
                    $qb->createNamedParameter(date('Y-m-d'))
                )
            )
            ->executeQuery()
            ->fetchOne();
    }

    public function countLastMinutes(int $minutes): int
    {
        $minutes = max(1, $minutes);
        $sinceTimestamp = time() - ($minutes * 60);
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');

        return (int)$qb->count('uid')
            ->from('tx_atanalyticslite_visit')
            ->where(
                $qb->expr()->gte(
                    'tstamp',
                    $qb->createNamedParameter($sinceTimestamp, \Doctrine\DBAL\ParameterType::INTEGER)
                )
            )
            ->executeQuery()
            ->fetchOne();
    }

    public function countPageVisitsCurrentMonth(int $pageUid): int
    {
        if ($pageUid <= 0) {
            return 0;
        }

        $monthStart = date('Y-m-01');
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');

        return (int)$qb->count('uid')
            ->from('tx_atanalyticslite_visit')
            ->where(
                $qb->expr()->eq(
                    'page_uid',
                    $qb->createNamedParameter($pageUid, \Doctrine\DBAL\ParameterType::INTEGER)
                ),
                $qb->expr()->gte(
                    'visit_date',
                    $qb->createNamedParameter($monthStart)
                )
            )
            ->executeQuery()
            ->fetchOne();
    }

    public function findLastDaysTraffic(int $days = 7): array
    {
        $days = max(1, $days);
        $startDate = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');
        $rows = $qb->select('visit_date')
            ->addSelectLiteral('COUNT(uid) AS total')
            ->from('tx_atanalyticslite_visit')
            ->where(
                $qb->expr()->gte(
                    'visit_date',
                    $qb->createNamedParameter($startDate)
                )
            )
            ->groupBy('visit_date')
            ->orderBy('visit_date', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $totalsByDate = [];
        foreach ($rows as $row) {
            $totalsByDate[(string)$row['visit_date']] = (int)$row['total'];
        }

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $result[] = [
                'date' => $date,
                'label' => date('D', strtotime($date)),
                'total' => $totalsByDate[$date] ?? 0,
            ];
        }

        return $result;
    }

    public function getDeviceBreakdown(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');
        $rows = $qb->select('device_type')
            ->addSelectLiteral('COUNT(uid) AS total')
            ->from('tx_atanalyticslite_visit')
            ->groupBy('device_type')
            ->executeQuery()
            ->fetchAllAssociative();

        $breakdown = [
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
        ];

        foreach ($rows as $row) {
            $type = strtolower((string)($row['device_type'] ?? ''));
            if (isset($breakdown[$type])) {
                $breakdown[$type] = (int)$row['total'];
            }
        }

        return $breakdown;
    }

    public function findTopPages(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_atanalyticslite_visit');
        return $qb->select('v.page_uid')
            ->addSelect('p.title AS page_title')
            ->addSelectLiteral('COUNT(v.uid) as total')
            ->addSelect('v.visit_date AS visit_date')
            ->from('tx_atanalyticslite_visit', 'v')
            ->leftJoin('v', 'pages', 'p', 'p.uid = v.page_uid')
            ->groupBy('v.page_uid', 'p.title')
            ->orderBy('total', 'DESC')
            ->setMaxResults(1000)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}