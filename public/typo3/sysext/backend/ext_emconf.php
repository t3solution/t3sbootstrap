<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Backend',
    'description' => 'Classes for the TYPO3 backend.',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'TYPO3 Core Team',
    'author_email' => 'typo3cms@typo3.org',
    'author_company' => '',
    'version' => '11.5.16',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.16',
            'recordlist' => '11.5.16',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
