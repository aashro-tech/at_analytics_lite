<?php
use Aashro\AtAnalyticsLite\Controller\Backend\AnalyticsController;

return [
    'web_atanalyticslite' => [
        'parent' => 'web',
        'position' => ['after' => 'info'],
        'access' => 'user',
        'inheritNavigationComponentFromMainModule' => false,
        'navigationComponent' => '',
        'iconIdentifier' => 'module-atanalyticslite',
        'labels' => 'LLL:EXT:at_analytics_lite/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'AtAnalyticsLite',
        'controllerActions' => [
            AnalyticsController::class => ['dashboard']
        ]
    ]
];