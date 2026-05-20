<?php
declare(strict_types=1);

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

# Extension configuration
$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

$dbModel = 't3sbootstrap.db:tx_t3sbootstrap_domain_model_config';

$tempPagesColumns = [
    'tx_t3sbootstrap_smallColumns' => [
        'label' => $dbModel.'.smallColumnsWidth',
        'exclude' => true,
        'description' => $dbModel.'.smallColumnsWidth.description',
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
        'label' => $dbModel.'.container',
        'exclude' => true,
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
                ['label' => 'no container - even if pages override', 'value' => 'none',],
            ],
            'default' => 'container'
        ]
    ],
    'tx_t3sbootstrap_linkToTop' => [
        'exclude' => true,
        'label' => $dbModel.'.linkToTop',
        'config' => [
            'type' => 'check',
            'default' => 1
        ]
    ],
    'tx_t3sbootstrap_dropdownRight' => [
        'exclude' => true,
        'label' => $dbModel.'.dropdownRight',
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_t3sbootstrap_megamenu' => [
        'exclude' => true,
        'label' => $dbModel.'.megamenu',
        'displayCond' => 'FIELD:doktype:=:4',
        'config' => [
            'type' => 'check',
        ]
    ],
    'tx_t3sbootstrap_mobileOrder' => [
        'label' => $dbModel.'.mobileOrder',
        'exclude' => true,
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
        'label' => $dbModel.'.navbarbreakpoint',
        'exclude' => true,
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
    'tx_t3sbootstrap_icon_only' => [
        'exclude' => true,
        'label' => $dbModel.'.iconOnly',
        'description' => $dbModel.'.iconOnly.description',
        'config' => [
            'type' => 'check',
        ]
    ],

    'tx_t3sbootstrap_titlecolor' => [
        'label' => $dbModel.'.titlecolor',
        'exclude' => true,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['label' => 'var(--bs-primary)', 'value' => 'var(--bs-primary)'],
                    ['label' => 'var(--bs-secondary)', 'value' => 'var(--bs-secondary)'],
                    ['label' => 'var(--bs-success)', 'value' => 'var(--bs-success)'],
                    ['label' => 'var(--bs-danger)', 'value' => 'var(--bs-danger)'],
                    ['label' => 'var(--bs-warning)', 'value' => 'var(--bs-warning)'],
                    ['label' => 'var(--bs-info)', 'value' => 'var(--bs-info)']
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_subtitlecolor' => [
        'label' => $dbModel.'.subtitlecolor',
        'exclude' => true,
        'description' => 'Hex color codes, RGB or CSS variables e.g. var(--bs-primary)',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['label' => 'var(--bs-primary)', 'value' => 'var(--bs-primary)'],
                    ['label' => 'var(--bs-secondary)', 'value' => 'var(--bs-secondary)'],
                    ['label' => 'var(--bs-success)', 'value' => 'var(--bs-success)'],
                    ['label' => 'var(--bs-danger)', 'value' => 'var(--bs-danger)'],
                    ['label' => 'var(--bs-warning)', 'value' => 'var(--bs-warning)'],
                    ['label' => 'var(--bs-info)', 'value' => 'var(--bs-info)']
                ],
            ],
        ],
    ],

    'tx_t3sbootstrap_fullheightsection' => [
        'exclude' => true,
        'label' => $dbModel.'.fullheightsection',
        'description' => $dbModel.'.fullheightsection.description',
        'config' => [
            'type' => 'check'
        ]
    ],
    
];

ExtensionManagementUtility::addTCAcolumns('pages', $tempPagesColumns);


$GLOBALS['TCA']['pages']['palettes']['bootstrap'] = [
	'showitem' => 'tx_t3sbootstrap_smallColumns, tx_t3sbootstrap_mobileOrder, 
	--linebreak--, tx_t3sbootstrap_container, tx_t3sbootstrap_breakpoint, 
	--linebreak--, tx_t3sbootstrap_linkToTop, tx_t3sbootstrap_fullheightsection, 
	--linebreak--, tx_t3sbootstrap_megamenu, tx_t3sbootstrap_dropdownRight',
	'canNotCollapse' => 1
];

ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--palette--;T3S Bootstrap;bootstrap',
    '',
    'after:backend_layout'
);


if (!empty($extconf['titlecolor'])) {
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'title',
        'tx_t3sbootstrap_titlecolor',
        'after:title'
    );
    ExtensionManagementUtility::addFieldsToPalette(
        'pages',
        'title',
        '--linebreak--, tx_t3sbootstrap_subtitlecolor',
        'after:subtitle'
    );
}

# if iconpack is loaded
if (ExtensionManagementUtility::isLoaded('iconpack')) {
	ExtensionManagementUtility::addFieldsToPalette(
	    'pages',
	    'title',
	    'tx_t3sbootstrap_icon_only, --linebreak--',
	    'after:title'
	);
}

$doktypeDropdownHeader = 198;
// Add the new doktype to the page type selector
ExtensionManagementUtility::addTcaSelectItem(
    'pages',
    'doktype',
    [
        'label' => $dbModel.'.dropdownmenuHeader',
        'value' => $doktypeDropdownHeader,
        'icon'  => 'content-header',
        'group' => 'special',
    ],
);
// Add the icon to the icon class configuration
$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$doktypeDropdownHeader] = 'content-header';

if (!empty($extconf['navbarmodal'])) {
    $doktypeModal = 197;
    // Add the new doktype to the page type selector
    ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'label' => $dbModel.'.navbarmodal',
            'value' => $doktypeModal,
            'icon'  => 'actions-duplicate',
            'group' => 'special',
        ],
    );
    // Add the icon to the icon class configuration
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][$doktypeModal] = 'actions-duplicate';
}

// Define a minimal fallback schema for doktype 197 & 198 to prevent the v14 crash
if (!isset($GLOBALS['TCA']['pages']['types']['197'])) {
    $GLOBALS['TCA']['pages']['types']['197'] = $GLOBALS['TCA']['pages']['types']['1'];
}
if (!isset($GLOBALS['TCA']['pages']['types']['198'])) {
    $GLOBALS['TCA']['pages']['types']['198'] = $GLOBALS['TCA']['pages']['types']['1'];
}


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
