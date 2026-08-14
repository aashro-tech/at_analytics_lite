<?php
$EM_CONF[$_EXTKEY] = [
    'title' => '[AASHRO] Analytics Lite',
    'description' => 'Privacy friendly analytics for TYPO3',
    'category' => 'plugin',
    'author' => 'Team AASHRO',
    'author_email' => 'info@aashro.com',
    'author_company' => 'AASHRO Tech',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.9.99'
        ],
        'suggests' => [
            'mobiledetect/mobiledetectlib' => '*'
        ]
    ]
];