<?php
defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['at_analytics_lite']['trackIntervalMinutes'] ??= 15;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Aashro\AtAnalyticsLite\Task\CleanupOldVisitsTask::class] = [
    'extension' => 'at_analytics_lite',
    'title' => 'LLL:EXT:at_analytics_lite/Resources/Private/Language/locallang_tasks.xlf:task.cleanup.title',
    'description' => 'LLL:EXT:at_analytics_lite/Resources/Private/Language/locallang_tasks.xlf:task.cleanup.description',
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Aashro\AtAnalyticsLite\Task\AggregateStatsTask::class] = [
    'extension' => 'at_analytics_lite',
    'title' => 'LLL:EXT:at_analytics_lite/Resources/Private/Language/locallang_tasks.xlf:task.aggregate.title',
    'description' => 'LLL:EXT:at_analytics_lite/Resources/Private/Language/locallang_tasks.xlf:task.aggregate.description',
];