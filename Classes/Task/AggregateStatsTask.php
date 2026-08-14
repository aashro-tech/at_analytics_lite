<?php
namespace Aashro\AtAnalyticsLite\Task;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class AggregateStatsTask extends AbstractTask
{
    public function execute(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_atanalyticslite_daily_stat');

        $now = time();

        // Rebuild compact daily stats from raw visits.
        $connection->executeStatement('TRUNCATE TABLE tx_atanalyticslite_daily_stat');
        $connection->executeStatement(
            'INSERT INTO tx_atanalyticslite_daily_stat (stat_date, page_uid, language_uid, visits, tstamp, crdate)
             SELECT visit_date, page_uid, language_uid, COUNT(uid) AS visits, ?, ?
             FROM tx_atanalyticslite_visit
             GROUP BY visit_date, page_uid, language_uid',
            [$now, $now]
        );

        return true;
    }
}