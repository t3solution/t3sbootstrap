<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

# Extension configuration
$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

/**
 * Add extra field tx_t3sbootstrap_extra_class etc. to sys_file_reference record
 */
$tempSysFileReferenceColumns = [
    'tx_t3sbootstrap_extra_class' => [
        'exclude' => 1,
        'label' => 'Extra Class - figure-tag',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    [ 'center', 'mx-auto', ],
                    [ 'right', 'float-end', ],
                    [ 'm-3 (margin)', 'm-3', ],
                    [ 'mt-3 (margin-top)', 'mt-3', ],
                    [ 'mb-3 (margin-bottom)', 'mb-3', ],
                    [ 'ms-3 (margin-left)', 'ms-3', ],
                    [ 'me-3 (margin-right)', 'me-3', ],
                    [ 'mx-3 (margin-left and -right)', 'mx-3', ],
                    [ 'my-3 (margin-top and -bottom)', 'my-3', ],
                    [ 'Hover zoom (basic)', 'img-hover-zoom', ],
                    [ 'Hover zoom (rotate)', 'img-hover-zoom--zoom-n-rotate', ],
                    [ 'Hover zoom (slowmo)', 'img-hover-zoom--slowmo', ],
                    [ 'Hover zoom (brightness)', 'img-hover-zoom--brightness', ],
                    [ 'Hover zoom (blurzoom)', 'img-hover-zoom--blur', ],
                    [ 'Hover zoom (colorize)', 'img-hover-zoom--colorize', ]
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_extra_imgclass' => [
        'exclude' => 1,
        'label' => 'Extra Class - img-tag',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    [ 'img-transform scale', 'img-transform', ],
                    [ 'rounded', 'rounded', ],
                    [ 'rounded-circle', 'rounded-circle', ],
                    [ 'img-thumbnail', 'img-thumbnail', ],
                ],
            ],
        ],
    ],
    'tx_t3sbootstrap_hover_effect' => [
        'label' => 'Link Hover Effect (title and/or description)',
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
        'label' => 'Lazy loading',
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
        'label' => 'Description align',
        'exclude' => 1,
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => 'left',
                    'value' => 'start',
                ],
                [
                    'label' => 'center',
                    'value' => 'center',
                ],
                [
                    'label' => 'right',
                    'value' => 'end',
                ],
            ],
            'default' => 'start'
        ]
    ],
    'tx_t3sbootstrap_copyright' => [
        'exclude' => 1,
        'label' => 'Copyright note',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
        ],
    ],
    'tx_t3sbootstrap_copyright_color' => [
        'exclude' => 1,
        'label' => 'Copyright color',
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
        'label' => 'Copyright source',
        'config' => [
            'type' => 'input',
            'size' => 50,
            'eval' => 'trim',
        ],
    ],
    'tx_t3sbootstrap_imgtag' => [
        'label' => 'Output image in <img> - instead in <picture> tag',
        'description' => 'Did not work with any CType!',
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
        'label' => 'Vertical shift - if the original image is higher than wide',
        'description' => 'only useful if aspect ratio (tx_t3sbootstrap_image_ratio) is used - otherwise the input is rejected',
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
        'label' => 'Horizontal shift - if the original is wider than high',
        'description' => 'only useful if aspect ratio (tx_t3sbootstrap_image_ratio) is used - otherwise the input is rejected',
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
        'label' => 'Custom aspect ratio (default: 16:9)',
        'description' => 'you can use any aspect ratio - e.g.: 4:3 (textmedia only)',
        'displayCond' => 'FIELD:tablenames:=:tt_content',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'trim',
            'valuePicker' => [
                'items' => [
                    ['16:9 (widescreen)', '16:9'],
                    ['9:16 (vertical)', '9:16'],
                    ['1:1 (square)', '1:1'],
                    ['4:3 (fullscreen)', '4:3'],
                    ['21:9 (cinematic widescreen)', '21:9']
                ],
            ],
            'default' => '16:9'
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $tempSysFileReferenceColumns);
unset($tempSysFileReferenceColumns);

ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,tx_t3sbootstrap_description_align', 'after:title');

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
