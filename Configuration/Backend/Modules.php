<?php
declare(strict_types=1);

use T3SBS\T3sbootstrap\Controller\ConfigController;

/**
 * Definitions for modules provided by EXT:t3sbootstrap
 */
return [

    'web_t3sbootstrap' => [
        'parent' => 'content',
        'position' => ['after' => 'web_list'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/t3sbootstrap',
        'labels' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_m1.xlf',
        'extensionName' => 'T3sbootstrap',
        'iconIdentifier' => 'bootstraplogo',
        'controllerActions' => [
            // Since TYPO3 v12 a backend module only dispatches the actions
            // listed here - anything missing silently falls back to the first
            // entry. 'list' must stay first, it is the default action.
            ConfigController::class => [
                'list',
                'export',
                'import',
            ],
        ],
    ],

];

