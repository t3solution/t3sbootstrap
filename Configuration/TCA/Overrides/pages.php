<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

# Extension configuration
$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

$tempPagesColumns = [
    'tx_t3sbootstrap_smallColumns' => [
        'label' => 'Aside columns width',
        'exclude' => 1,
        'description' => 'makes no sense for Backend Layout "1 Column"',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => '1', 'value' => 1,],
                ['label' => '2', 'value' => 2,],
                ['label' => '3', 'value' => 3,],
                ['label' => '4', 'value' => 4,],
                ['label' => '5', 'value' => 5,],
                ['label' => '6', 'value' => 6,],
            ],
            'default' => 3
        ]
    ],
    'tx_t3sbootstrap_container' => [
        'label' => 'Container (for the whole page)',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => 'no container', 'value' => '0',],
                ['label' => 'container','value' => 'container',],
                ['label' => 'container-sm (< 576px)', 'value' => 'container-sm',],
                ['label' => 'container-md (≥ 576px)', 'value' => 'container-md',],
                ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg',],
                ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl',],
                ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl',],
                ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid',],
            ],
            'default' => 'container'
        ]
    ],
    'tx_t3sbootstrap_linkToTop' => [
        'exclude' => 1,
        'label' => 'Link to top',
        'config' => [
            'type' => 'check',
            'default' => 1
        ]
    ],
    'tx_t3sbootstrap_dropdownRight' => [
        'exclude' => 1,
        'label' => 'Dropdown menu right',
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_t3sbootstrap_megamenu' => [
        'exclude' => 1,
        'label' => 'Mega menu',
        'displayCond' => 'FIELD:doktype:=:4',
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_t3sbootstrap_mobileOrder' => [
        'label' => 'Aside order on mobile',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => 'Default', 'value' => 'default',],
                ['label' => 'Top (both)', 'value' => 'top',],
                ['label' => 'Bottom (both)', 'value' => 'bottom',],
                ['label' => 'Left Aside Top', 'value' => 'leftTop',],
                ['label' => 'Left Aside Bottom', 'value' => 'leftBottom',],
                ['label' => 'Right Aside Top', 'value' => 'rightTop',],
                ['label' => 'Right Aside Bottom', 'value' => 'rightBottom',],
            ],
            'default' => 'default'
        ]
    ],
    'tx_t3sbootstrap_breakpoint' => [
        'label' => 'Breakpoint',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => 'Default', 'value' => 'md',],
                ['label' => 'sm', 'value' => 'sm',],
                ['label' => 'md', 'value' => 'md',],
                ['label' => 'lg', 'value' => 'lg',],
                ['label' => 'xl', 'value' => 'xl',],
                ['label' => 'xxl', 'value' => 'xxl',],
            ],
            'default' => 'md'
        ]
    ],
    'tx_t3sbootstrap_fontawesome_icon' => [
        'exclude' => 1,
        'label'	=> 'e.g.: fa-brands fa-typo3 fa-lg',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['typo3', 'fa-brands fa-typo3'],
                    ['envelope', 'fa-solid fa-envelope'],
                    ['info-circle', 'fa-solid fa-circle-info'],
                    ['exclamation-circle', 'fa-solid fa-circle-exclamation'],
                    ['question-circle', 'fa-solid fa-circle-question'],
                    ['check-circle', 'fa-solid fa-circle-check'],
                    ['chevron-circle-left', 'fa-solid fa-circle-chevron-left'],
                    ['chevron-circle-right', 'fa-solid fa-circle-chevron-right'],
                    ['youtube', 'fa-brands fa-youtube'],
                    ['vimeo', 'fa-brands fa-square-vimeo'],
                ],
            ],
        ]
    ],
    'tx_t3sbootstrap_icon_only' => [
        'exclude' => 1,
        'label' => 'Icon only',
        'description' => 'for nav-item not for page title',
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_t3sbootstrap_titlecolor' => [
        'label' => 'Page Title Color',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_subtitlecolor' => [
        'label' => 'Subtitle Color',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_navigationcolor' => [
        'label' => 'Color',
        'displayCond' => 'USER:T3SBS\\T3sbootstrap\\UserFunction\\TcaMatcher->isDropdownMenu',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_navigationactivecolor' => [
        'label' => 'Active Color',
        'displayCond' => 'USER:T3SBS\\T3sbootstrap\\UserFunction\\TcaMatcher->isDropdownMenu',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_navigationhover' => [
        'label' => 'Hover Color',
        'displayCond' => 'USER:T3SBS\\T3sbootstrap\\UserFunction\\TcaMatcher->isDropdownMenu',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)'],
                    ['text-light', 'text-light'],
                    ['text-dark', 'text-dark'],
                    ['text-body', 'text-body'],
                    ['text-muted', 'text-muted'],
                    ['text-white', 'text-white'],
                    ['text-black-50', 'text-black-50'],
                    ['text-white-50', 'text-white-50'],
                    ['text-uppercase', 'text-uppercase'],
                    ['text-capitalize', 'text-capitalize'],
                    ['text-left', 'text-start'],
                    ['text-center', 'text-center'],
                    ['text-right', 'text-end'],
                    ['font-weight-bold', 'font-weight-bold'],
                    ['font-weight-bolder', 'font-weight-bolder'],
                    ['font-weight-normal', 'font-weight-normal'],
                    ['font-weight-light', 'font-weight-light'],
                    ['font-weight-lighter', 'font-weight-lighter'],
                    ['font-italic', 'font-italic'],
                    ['font-normal', 'font-normal'],
                    ['display-1','display-1'],
                    ['display-2','display-2'],
                    ['display-3','display-3'],
                    ['display-4','display-4'],
                    ['display-5','display-5'],
                    ['display-6','display-6']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_navigationbgcolor' => [
        'label' => 'Background Active & Hover Color',
        'displayCond' => 'USER:T3SBS\\T3sbootstrap\\UserFunction\\TcaMatcher->isDropdownMenu',
        'exclude' => 1,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['var(--bs-primary)', 'var(--bs-primary)'],
                    ['var(--bs-secondary)', 'var(--bs-secondary)'],
                    ['var(--bs-success)', 'var(--bs-success)'],
                    ['var(--bs-danger)', 'var(--bs-danger)'],
                    ['var(--bs-warning)', 'var(--bs-warning)'],
                    ['var(--bs-info)', 'var(--bs-info)'],
                    ['text-light', 'text-light'],
                    ['text-dark', 'text-dark'],
                    ['text-body', 'text-body'],
                    ['text-muted', 'text-muted'],
                    ['text-white', 'text-white'],
                    ['text-black-50', 'text-black-50'],
                    ['text-white-50', 'text-white-50'],
                    ['text-uppercase', 'text-uppercase'],
                    ['text-capitalize', 'text-capitalize'],
                    ['text-left', 'text-start'],
                    ['text-center', 'text-center'],
                    ['text-right', 'text-end'],
                    ['font-weight-bold', 'font-weight-bold'],
                    ['font-weight-bolder', 'font-weight-bolder'],
                    ['font-weight-normal', 'font-weight-normal'],
                    ['font-weight-light', 'font-weight-light'],
                    ['font-weight-lighter', 'font-weight-lighter'],
                    ['font-italic', 'font-italic'],
                    ['font-normal', 'font-normal'],
                    ['display-1','display-1'],
                    ['display-2','display-2'],
                    ['display-3','display-3'],
                    ['display-4','display-4'],
                    ['display-5','display-5'],
                    ['display-6','display-6']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_fullheightsection' => [
        'exclude' => 1,
        'label' => 'Full height section',
        'description' => 'Make a fullscreen section that`s full height of browser window',
        'config' => [
            'type' => 'check'
        ]
    ]
];

ExtensionManagementUtility::addTCAcolumns('pages', $tempPagesColumns);
unset($tempPagesColumns);


$GLOBALS['TCA']['pages']['palettes']['bootstrap'] = [
 'showitem' => 'tx_t3sbootstrap_smallColumns, tx_t3sbootstrap_mobileOrder, --linebreak--, tx_t3sbootstrap_container, tx_t3sbootstrap_breakpoint, --linebreak--, tx_t3sbootstrap_linkToTop, tx_t3sbootstrap_fullheightsection',
     'canNotCollapse' => 1
];

ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--palette--;T3S Bootstrap;bootstrap',
    '',
    'after:backend_layout'
);

if (array_key_exists('navigationColor', $extconf) && $extconf['navigationColor'] === '1') {
    # add palette Navigation Colors
    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--palette--; Navigation Colors for dropdown items;navColors',
        '',
        'after:title'
    );

    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'navColors',
        '--linebreak--,tx_t3sbootstrap_navigationcolor',
        'after:tx_t3sbootstrap_subtitlecolor'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'navColors',
        '--linebreak--,tx_t3sbootstrap_navigationactivecolor',
        'after:tx_t3sbootstrap_navigationcolor'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'navColors',
        '--linebreak--,tx_t3sbootstrap_navigationhover',
        'after:tx_t3sbootstrap_navigationactivecolor'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'navColors',
        '--linebreak--,tx_t3sbootstrap_navigationbgcolor',
        'after:tx_t3sbootstrap_navigationhover'
    );
}

if (array_key_exists('fontawesome', $extconf) && $extconf['fontawesome'] === '1') {
    $GLOBALS['TCA']['pages']['palettes']['fontawesome'] = [
         'showitem' => 'tx_t3sbootstrap_fontawesome_icon,
				tx_t3sbootstrap_icon_only',
             'canNotCollapse' => 1
    ];

    ExtensionManagementUtility::addToAllTCAtypes(
        'pages',
        '--palette--;Fontawesome Icon;fontawesome',
        '',
        'after:title'
    );
}


$menuheader = 198;
// Add the new doktype to the page type selector
ExtensionManagementUtility::addTcaSelectItem(
    'pages',
    'doktype',
    [
        'label' => 'Dropdownmenu header',
        'value' => $menuheader,
        'icon'  => 'content-header',
        'group' => 'special',
    ],
);
// Add the icon to the icon class configuration
$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$menuheader] = 'content-header';



/***************
 * Register PageTSConfig Files
*/
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Textpic.tsconfig',
    'Remove CType textpic'
);
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Text.tsconfig',
    'Remove CType text'
);
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Image.tsconfig',
    'Remove CType image'
);
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Header.tsconfig',
    'Remove CType header'
);
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Callouts.tsconfig',
    'Add BS-Callouts options in Layout field'
);
ExtensionManagementUtility::registerPageTSConfigFile(
    't3sbootstrap',
    'Configuration/TSConfig/Registered/Alerts.tsconfig',
    'Add Alerts options in Layout field'
);
