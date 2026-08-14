<?php
return [
    'frontend' => [
        'at-analytics-lite-tracker' => [
            'target' => \Aashro\AtAnalyticsLite\Middleware\AnalyticsTrackingMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site'
            ]
        ]
    ]
];