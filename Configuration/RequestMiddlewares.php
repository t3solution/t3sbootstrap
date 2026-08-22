<?php

return [
    'frontend' => [
        // must run before TypoScript is resolved, see the class doc block
        'typo3/t3sbootstrap/ensure-generated-files' => [
            'target' => \T3SBS\T3sbootstrap\Middleware\EnsureGeneratedFiles::class,
            'after' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
