<?php
namespace Aashro\AtAnalyticsLite\Task;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class CleanupOldVisitsTask extends AbstractTask
{
    public function execute(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_atanalyticslite_visit');

        $cutoffDate = date('Y-m-d', strtotime('-180 days'));
        $connection->executeStatement(
            'DELETE FROM tx_atanalyticslite_visit WHERE visit_date < ?',
            [$cutoffDate]
        );

        return true;
    }
}