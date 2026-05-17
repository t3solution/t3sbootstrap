<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

# Extension configuration
$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

$dbModel = 't3sbootstrap.db:tx_t3sbootstrap_domain_model_config';

/**
 * Add extra field tx_t3sbootstrap_extra_class etc. to sys_file_reference record
 */
$tempSysFileReferenceColumns = [
    'tx_t3sbootstrap_extra_class' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileExtraclass',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    [ 'label' => 'center (mx-auto)', 'value' => 'mx-auto', ],
                    [ 'label' => 'right (float-end)', 'value' => 'float-end', ],
                    [ 'label' => 'm-3 (margin)', 'value' => 'm-3', ],
                    [ 'label' => 'mt-3 (margin-top)', 'value' => 'mt-3', ],
                    [ 'label' => 'mb-3 (margin-bottom)', 'value' => 'mb-3', ],
                    [ 'label' => 'ms-3 (margin-left)', 'value' => 'ms-3', ],
                    [ 'label' =>  'me-3 (margin-right)', 'value' => 'me-3', ],
                    [ 'label' => 'mx-3 (margin-left and -right)', 'value' => 'mx-3', ],
                    [ 'label' => 'my-3 (margin-top and -bottom)', 'value' => 'my-3', ],
                    [ 'label' => 'Hover zoom (basic)', 'value' => 'img-hover-zoom', ],
                    [ 'label' => 'Hover zoom (rotate)', 'value' => 'img-hover-zoom--zoom-n-rotate', ],
                    [ 'label' => 'Hover zoom (slowmo)', 'value' => 'img-hover-zoom--slowmo', ],
                    [ 'label' => 'Hover zoom (brightness)', 'value' => 'img-hover-zoom--brightness', ],
                    [ 'label' => 'Hover zoom (blurzoom)', 'value' => 'img-hover-zoom--blur', ],
                    [ 'label' => 'Hover zoom (colorize)', 'value' => 'img-hover-zoom--colorize', ]
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_extra_imgclass' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileExtraclassImg',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    [ 'label' => 'img-transform scale', 'value' => 'img-transform', ],
                    [ 'label' => 'rounded', 'value' => 'rounded', ],
                    [ 'label' => 'rounded-circle', 'value' => 'rounded-circle', ],
                    [ 'label' => 'img-thumbnail', 'value' => 'img-thumbnail', ],
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_hover_effect' => [
        'label' => $dbModel.'.t3sbootstrapSysfileHovereffect',
        'exclude' => 1,
        'displayCond' => [
             'AND' => [
                'FIELD:tablenames:=:tt_content',
                'FIELD:fieldname:=:assets',
             ]
        ],
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => 'none',
                    'value' => '',
                ],
                [
                    'label' => 'Effect 1',
                    'value' => 'snip1273',
                ],
                [
                    'label' => 'Effect 2',
                    'value' => 'snip1321',
                ],
                [
                    'label' => 'Effect 3',
                    'value' => 'snip1577',
                ],
                [
                    'label' => 'Effect 4',
                    'value' => 'snip0015',
                ],
                [
                    'label' => 'Effect 5 (title only)',
                    'value' => 'snip1573',
                ],
                [
                    'label' => 'Effect 6',
                    'value' => 'snip1477',
                ],
                [
                    'label' => 'Effect 7',
                    'value' => 'snip1361',
                ],
                [
                    'label' => 'Effect 8',
                    'value' => 'snip1206',
                ],
                [
                    'label' => 'Effect 9',
                    'value' => 'snip1190',
                ],
                [
                    'label' => 'Effect 10',
                    'value' => 'snip0016',
                ],
            ],
            'default' => ''
        ]
    ],
    'tx_t3sbootstrap_lazy_load' => [
        'label' => $dbModel.'.t3sbootstrapSysfileLazyload',
        'exclude' => 1,
        'displayCond' => [
            'OR' => [
                'FIELD:tablenames:=:tt_content',
                'FIELD:tablenames:=:tx_news_domain_model_news',
                'FIELD:fieldname:=:assets',
                'FIELD:fieldname:=:image',
            ],
        ],
        'config' => [
            'type' => 'check'
        ]
    ],
    'tx_t3sbootstrap_description_align' => [
        'label' => $dbModel.'.t3sbootstrapSysfileDescriptionalign',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => $dbModel.'.t3sbootstrapSysfileDescriptionalign.item1',
                    'value' => 'start',
                ],
                [
                    'label' => $dbModel.'.t3sbootstrapSysfileDescriptionalign.item2',
                    'value' => 'center',
                ],
                [
                    'label' => $dbModel.'.t3sbootstrapSysfileDescriptionalign.item3',
                    'value' => 'end',
                ],
            ],
            'default' => 'start'
        ]
    ],
    'tx_t3sbootstrap_copyright' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileCopyright',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
        ],
    ],
    'tx_t3sbootstrap_copyright_color' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileCopyrightColor',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => 'light',
                    'value' => 'text-light',
                ],
                [
                    'label' => 'dark',
                    'value' => 'text-dark',
                ],
                [
                    'label' => 'primary',
                    'value' => 'text-primary',
                ],
                [
                    'label' => 'secondary',
                    'value' => 'text-secondary',
                ],
                [
                    'label' => 'success',
                    'value' => 'text-success',
                ],
                [
                    'label' => 'danger',
                    'value' => 'text-danger',
                ],
                [
                    'label' => 'warning',
                    'value' => 'text-warning',
                ],
                [
                    'label' => 'info',
                    'value' => 'text-info',
                ],
                [
                    'label' => 'white',
                    'value' => 'text-white',
                ],
            ],
            'default' => 'text-dark',
            'size' => 1,
            'maxitems' => 1
        ]
    ],
    'tx_t3sbootstrap_copyright_source' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileCopyrightSource',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
        ],
    ],
    'tx_t3sbootstrap_imgtag' => [
        'label' => $dbModel.'.t3sbootstrapSysfileImgtag',
        'description' => $dbModel.'.t3sbootstrapSysfileImgtag.description',
        'exclude' => 1,
        'displayCond' => [
             'AND' => [
                'FIELD:tablenames:=:tt_content',
                'FIELD:fieldname:=:assets',
             ]
        ],
        'config' => [
            'type' => 'check'
        ]
    ],
    'tx_t3sbootstrap_shift_vertical' => [
        'label' => $dbModel.'.t3sbootstrapSysfileShiftVertical',
        'description' => $dbModel.'.t3sbootstrapSysfileShiftVertical.description',
        'displayCond' => [
             'AND' => [
                'FIELD:tablenames:=:tt_content',
                'FIELD:fieldname:=:assets',
             ]
        ],
        'config' => [
            'type' => 'number',
            'size' => 5,
            'eval' => 'trim',
            'range' => [
                'lower' => 0,
                'upper' => 50
            ],
            'default' => 0,
            'slider' => [
                'step' => 1,
                'width' => 200,
            ]
        ],
    ],
    'tx_t3sbootstrap_shift_horizontal' => [
        'label' => $dbModel.'.t3sbootstrapSysfileShiftHorizontal',
        'description' => $dbModel.'.t3sbootstrapSysfileShiftHorizontal.description',
        'displayCond' => [
             'AND' => [
                'FIELD:tablenames:=:tt_content',
                'FIELD:fieldname:=:assets',
             ]
        ],
        'config' => [
            'type' => 'number',
            'size' => 5,
            'eval' => 'trim',
            'range' => [
                'lower' => 0,
                'upper' => 50
            ],
            'default' => 0,
            'slider' => [
                'step' => 1,
                'width' => 200,
            ]
        ],
    ],
    'tx_t3sbootstrap_video_ratio' => [
        'exclude' => 1,
        'label' => $dbModel.'.t3sbootstrapSysfileShiftVideoRatio',
        'description' => $dbModel.'.t3sbootstrapSysfileShiftVideoRatio.description',
        'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->textmedia',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    [
                        'label' => '16:9 (widescreen)', 
                        'value' => '16:9'
                    ],
                    [
                        'label' => '9:16 (vertical)', 
                        'value' => '9:16'
                    ],
                    [
                        'label' => '1:1 (square)', 
                        'value' => '1:1'
                    ],
                    [
                        'label' => '4:3 (fullscreen)', 
                        'value' => '4:3'
                    ],
                    [
                        'label' => '21:9 (cinematic widescreen)', 
                        'value' => '21:9'
                    ]
                ],
            ],
            'default' => '16:9'
        ],
    ],

];

ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $tempSysFileReferenceColumns);
unset($tempSysFileReferenceColumns);

ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_description_align', 'after:description');

ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_extra_class', 'after:tx_t3sbootstrap_description_align');

ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_extra_imgclass', 'after:tx_t3sbootstrap_extra_class');

if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright']) {
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_copyright', 'after:tx_t3sbootstrap_extra_imgclass');

    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_copyright_color', 'after:tx_t3sbootstrap_copyright');

    if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '2') {
        ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_copyright_source', 'after:tx_t3sbootstrap_copyright_color');
    }
}

if (array_key_exists('linkHoverEffect', $extconf) && $extconf['linkHoverEffect'] === '1') {
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_hover_effect', 'after:tx_t3sbootstrap_extra_imgclass');
}

if (array_key_exists('lazyLoad', $extconf) && $extconf['lazyLoad'] === '2') {
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_lazy_load', 'after:tx_t3sbootstrap_extra_imgclass');
}

if (array_key_exists('imgtag', $extconf) && $extconf['imgtag']) {
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_imgtag', 'after:tx_t3sbootstrap_description_align');
}

if (array_key_exists('ratio', $extconf) && $extconf['ratio']) {
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'videoOverlayPalette', '--linebreak--,tx_t3sbootstrap_video_ratio', 'after:autoplay');
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_shift_vertical', 'after:tx_t3sbootstrap_description_align');
    ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_shift_horizontal', 'after:tx_t3sbootstrap_shift_vertical');
}
