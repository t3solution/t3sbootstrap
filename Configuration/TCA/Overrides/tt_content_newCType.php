<?php

defined('TYPO3') || die();

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

if (!empty($extconf['flexformNoDefault'])) {
    $flexformTwoColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumnsNoDefaults.xml';
    $flexformThreeColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumnsNoDefaults.xml';
    $flexformFourColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumnsNoDefaults.xml';
    $flexformSixColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumnsNoDefaults.xml';
} else {
    $flexformTwoColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumns.xml';
    $flexformThreeColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumns.xml';
    $flexformFourColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumns.xml';
    $flexformSixColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumns.xml';
}

/***************
 * Add new CTypes
 */
 \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
     'tt_content',
     'CType',
     [
                'Plain CSS or JavaScript inline',
                't3sbs_assets',
                'cssJsIcon',
        ],
     'textmedia',
     'after'
 );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Media object',
                't3sbs_mediaobject',
                'content-beside-text-img-left'
        ],
    'textmedia',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Card',
                't3sbs_card',
                'content-card'
        ],
    't3sbs_mediaobject',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Toasts',
                't3sbs_toast',
                'content-widget-calltoaction'
        ],
    't3sbs_card',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Carousel Item (in carousel- or swiper-container)',
                't3sbs_carousel',
                'content-carousel-item-textandimage'
        ],
    't3sbs_toast',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Button',
                't3sbs_button',
                'form-radio-button'
        ],
    't3sbs_carousel',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Fluidtemplate',
                't3sbs_fluidtemplate',
                'actions-template-new'
        ],
    't3sbs_button',
    'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
                'Bootstrap Image Gallery',
                't3sbs_gallery',
                'apps-filetree-folder-media'
        ],
    't3sbs_fluidtemplate',
    'after'
);


/***************
 * New fields in table:tt_content
*/
$tempContentColumns = [
        'tx_t3sbootstrap_header_display' => [
                'label' => 'Display headings',
                'exclude' => 1,
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'none', 'value' => '',],
                                ['label' => 'display-1', 'value' => 'display-1',],
                                ['label' => 'display-2', 'value' => 'display-2',],
                                ['label' => 'display-3', 'value' => 'display-3',],
                                ['label' => 'display-4', 'value' => 'display-4',],
                                ['label' => 'display-5', 'value' => 'display-5',],
                                ['label' => 'display-6', 'value' => 'display-6',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_header_class' => [
                'label' => 'Header Extra Class',
                'exclude' => 1,
                'config' => [
                        'type' => 'input',
                        'size' => 40,
                        'eval' => 'trim',
                        'valuePicker' => [
                                'items' => [
                                        ['m-3 (margin)', 'm-3'],
                                        ['mt-3 (margin-top)', 'mt-3'],
                                        ['mb-3 (margin-bottom)', 'mb-3'],
                                        ['ms-3 (margin-left)', 'ms-3'],
                                        ['me-3 (margin-right)', 'me-3'],
                                        ['mx-3 (margin-left and -right)', 'mx-3'],
                                        ['my-3 (margin-top and -bottom)', 'my-3'],
                                        ['text-primary', 'text-primary'],
                                        ['text-secondary', 'text-secondary'],
                                        ['text-danger', 'text-danger'],
                                        ['text-success', 'text-success'],
                                        ['text-warning', 'text-warning'],
                                        ['text-info', 'text-info'],
                                        ['text-black', 'text-black'],
                                        ['text-white', 'text-white'],
                                        ['text-uppercase', 'text-uppercase'],
                                        ['One line left and right', 'h-line-1'],
                                        ['Two lines left and right', 'h-line-2']
                                ],
                        ],
                ],
        ],
        'tx_t3sbootstrap_header_fontawesome' => [
                'label' => 'Font Awesome Icon',
                'exclude' => 1,
                'config' => [
                        'type' => 'input',
                        'size' => 40,
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
                ],
        ],
        'tx_t3sbootstrap_header_celink' => [
                'exclude' => 1,
                'label' => 'Link the entire Content Element',
                'config' => [
                        'type' => 'check'
                ]
        ],
        'tx_t3sbootstrap_header_position' => [
                'label' => 'Header Position',
                'exclude' => 1,
                'displayCond' => [
                        'OR' => [
                                'FIELD:CType:=:textmedia',
                                'FIELD:CType:=:textpic',
                        ],
                ],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'Above the image (default)', 'value' => 'above',],
                                ['label' => 'Beside or under the image', 'value' => 'beside',],
                        ],
                        'default' => 'above'
                ]
        ],
        'tx_t3sbootstrap_header_sectionMenu' => [
                'label' => 'Section Menu Text',
                'exclude' => 1,
                'config' => [
                        'type' => 'input',
                        'size' => 40,
                        'eval' => 'trim',
                ],
        ],
        'tx_t3sbootstrap_padding_sides' => [
                'label' => 'Padding spacing side',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->spacing_'.$extconf['spacing'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'no padding', 'value' => '',],
                                ['label' => 'padding on all 4 sides', 'value' => 'blank',],
                                ['label' => 'padding-top', 'value' => 't',],
                                ['label' => 'padding-bottom', 'value' => 'b',],
                                ['label' => 'padding-left', 'value' => 's',],
                                ['label' => 'padding-right', 'value' => 'e',],
                                ['label' => 'padding-left and -right', 'value' => 'x',],
                                ['label' => 'padding-top and -bottom', 'value' => 'y',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_padding_size' => [
                'label' => 'Padding spacing size',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->spacing_'.$extconf['spacing'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => '0', 'value' => '0',],
                                ['label' => '1 (.25 rem)', 'value' => '1',],
                                ['label' => '2 (.5 rem)', 'value' => '2',],
                                ['label' => '3 (1 rem)', 'value' => '3',],
                                ['label' => '4 (1.5 rem)', 'value' => '4',],
                                ['label' => '5 (3 rem)', 'value' => '5',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_margin_sides' => [
                'label' => 'Margin spacing side',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->spacing_'.$extconf['spacing'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'no margin', 'value' => '',],
                                ['label' => 'margin on all 4 sides', 'value' => 'blank',],
                                ['label' => 'margin-top', 'value' => 't',],
                                ['label' => 'margin-bottom', 'value' => 'b',],
                                ['label' => 'margin-left', 'value' => 's',],
                                ['label' => 'margin-right', 'value' => 'e',],
                                ['label' => 'margin-left and -right', 'value' => 'x',],
                                ['label' => 'margin-top and -bottom', 'value' => 'y',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_margin_size' => [
                'label' => 'Margin spacing size',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->spacing_'.$extconf['spacing'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => '0','value' => '0',],
                                ['label' => '1 (.25 rem)', 'value' => '1',],
                                ['label' => '2 (.5 rem)', 'value' => '2',],
                                ['label' => '3 (1 rem)', 'value' => '3',],
                                ['label' => '4 (1.5 rem)', 'value' => '4',],
                                ['label' => '5 (3 rem)', 'value' => '5',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_container' => [
                'label' => 'Container',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->container_'.$extconf['container'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'no container', 'value' => '0',],
                                ['label' => 'container', 'value' => 'container',],
                                ['label' => 'container-fluid', 'value' => 'container-fluid',],
                                ['label' => 'container-fluid px-0', 'value' => 'container-fluid px-0',],
                                ['label' => 'container-sm (< 576px)', 'value' => 'container-sm',],
                                ['label' => 'container-md (≥ 576px)', 'value' => 'container-md',],
                                ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg',],
                                ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl',],
                                ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_flexform' => [
                'exclude' => 1,
                'l10n_display' => 'hideDiff',
                'label' => ' ',
                'config' => [
                        'type' => 'flex',
                        'ds_pointerField' => 'CType',    
                        'ds' => [
                                'default' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Bootstrap.xml',
                                't3sbs_card' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/CardSetting.xml',
                                't3sbs_toast' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/ToastSetting.xml',
                                't3sbs_carousel' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Carousel.xml',
                                't3sbs_button' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Button.xml',
                                't3sbs_mediaobject' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Mediaobject.xml',
                                'table' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Table.xml',
                                'two_columns' => $flexformTwoColumns,
                                'three_columns' => $flexformThreeColumns,
                                'four_columns' => $flexformFourColumns,
                                'six_columns' => $flexformSixColumns,
                                'card_wrapper' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CardWrapper.xml',
                                'button_group' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Buttongroup.xml',
                                'autoLayout_row' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/AutoLayoutRow.xml',
                                'background_wrapper' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/BackgroundWrapper.xml',
                                'parallax_wrapper' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ParallaxWrapper.xml',
                                'container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Container.xml',
                                'carousel_container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CarouselContainer.xml',
                                'collapsible_container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CollapseContainer.xml',
                                'collapsible_accordion' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Collapse.xml',
                                'modal' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Modal.xml',
                                'tabs_container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Tabs.xml',
                                'tabs_tab' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TabsTab.xml',
                                'masonry_wrapper' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/MasonryWrapper.xml',
                                'swiper_container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SwiperContainer.xml',
                                'toast_container' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ToastContainer.xml',
                                'row_columns' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/RowColumns.xml',
                        ]
                ]
        ],
        'tx_t3sbootstrap_extra_class' => [
                'label'  => 'Extra Class',
                'exclude' => 1,
                'config' => [
                        'type' => 'input',
                        'size' => 35
                ]
        ],
        'tx_t3sbootstrap_extra_style' => [
                'label'  => 'Extra Style',
                'exclude' => 1,
                'config' => [
                        'type' => 'input',
                        'size' => 35
                ]
        ],
        'tx_t3sbootstrap_bgcolor' => [
                'label' => 'Background color',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->color_'.$extconf['color'],
                'config' => [
                        'type' => 'color',
                        'size' => 20
                ],
        ],
        'tx_t3sbootstrap_inTextImgColumns' => [
                'label' => 'Gallery columns',
                'exclude' => 1,
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 1, 'value' => 1,],
                                ['label' => 2, 'value' => 2,],
                                ['label' => 3, 'value' => 3,],
                                ['label' => 4, 'value' => 4,],
                                ['label' => 5, 'value' => 5,],
                                ['label' => 6, 'value' => 6,],
                                ['label' => 7, 'value' => 7,],
                                ['label' => 8, 'value' => 8,],
                                ['label' => 9, 'value' => 9,],
                                ['label' => 10, 'value' => 10,],
                                ['label' => 11, 'value' => 11,],
                                ['label' => 12, 'value' => 12,],
                        ],
                        'default' => 4
                ]
        ],
        'tx_t3sbootstrap_bgopacity' => [
                'label' => 'Opacity (background)',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->color_'.$extconf['color'],
                'config' => [
                                'type' => 'number',
                                'format' => 'decimal',
                                'range' => [
                                        'lower' => 0,
                                        'upper' => 1
                                ],
                                'slider' => [
                                'step' => 0.1,
                                'width' => 200
                        ],
                        'default' => 1
                ],
        ],
        'tx_t3sbootstrap_contextcolor' => [
                'label' => 'Context color',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->color_'.$extconf['color'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'none', 'value' => '',],
                                ['label' => 'primary', 'value' => 'primary',],
                                ['label' => 'secondary', 'value' => 'secondary',],
                                ['label' => 'success', 'value' => 'success',],
                                ['label' => 'info', 'value' => 'info',],
                                ['label' => 'warning', 'value' => 'warning',],
                                ['label' => 'danger', 'value' => 'danger',],
                                ['label' => 'light', 'value' => 'light',],
                                ['label' => 'dark', 'value' => 'dark',],
                                ['label' => 'body', 'value' => 'body',],
                                ['label' => 'transparent', 'value' => 'transparent',],
                                ['label' => 'custom 1', 'value' => 'customOne',],
                                ['label' => 'custom 2', 'value' => 'customTwo',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_textcolor' => [
                'label' => 'Text color',
                'exclude' => 1,
                'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->color_'.$extconf['color'],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'default','value' => '',],
                                ['label' => 'white', 'value' => 'white',],
                                ['label' => 'muted', 'value' => 'muted',],
                                ['label' => 'secondary', 'value' => 'secondary',],
                                ['label' => 'primary', 'value' => 'primary',],
                                ['label' => 'success', 'value' => 'success',],
                                ['label' => 'info', 'value' => 'info',],
                                ['label' => 'warning', 'value' => 'warning',],
                                ['label' => 'danger', 'value' => 'danger',],
                                ['label' => 'light', 'value' => 'light',],
                                ['label' => 'dark', 'value' => 'dark',],
                                ['label' => 'body', 'value' => 'body',],
                                ['label' => 'transparent', 'value' => 'transparent',],
                                ['label' => 'custom 1', 'value' => 'customOne',],
                                ['label' => 'custom 2', 'value' => 'customTwo',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_inTextImgRowWidth' => [
                'label' => 'Gallery row width in %',
                'exclude' => 1,
                'displayCond' => [
                         'OR' => [
                                'FIELD:CType:=:textpic',
                                'FIELD:CType:=:textmedia',
                                'FIELD:CType:=:t3sbs_mediaobject',
                                'FIELD:CType:=:t3sbs_card',
                                'FIELD:CType:=:t3sbs_toast',
                                'FIELD:CType:=:t3sbs_gallery',
                         ],
                ],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'auto', 'value' => 'auto',],
                                ['label' => 25, 'value' => 'w-25',],
                                ['label' => 33, 'value' => 'w-33',],
                                ['label' => 50, 'value' => 'w-50',],
                                ['label' => 66, 'value' => 'w-66',],
                                ['label' => 75, 'value' => 'w-75',],
                                ['label' => 100, 'value' => 'w-100',],
                                ['label' => 'none', 'value' => 'none',],
                        ],
                        'default' => 'auto'
                ]
        ],
        'tx_t3sbootstrap_gutters' => [
                'label' => 'Horizontal gutters',
                'exclude' => 1,
                'description' => 'INFO: https://getbootstrap.com/docs/5.3/layout/gutters/#horizontal-gutters',
                'displayCond' => [
                         'OR' => [
                                'FIELD:CType:=:image',
                                'FIELD:CType:=:textpic',
                                'FIELD:CType:=:textmedia',
                                'FIELD:CType:=:t3sbs_gallery',
                         ],
                ],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'gx-0 (no gutters)', 'value' => 'gx-0',],
                                ['label' => 'gx-1', 'value' => 'gx-1',],
                                ['label' => 'gx-2', 'value' => 'gx-2',],
                                ['label' => 'gx-3', 'value' => 'gx-3',],
                                ['label' => 'gx-4 (default)', 'value' => 'gx-4',],
                                ['label' => 'gx-5', 'value' => 'gx-5',],
                        ],
                        'default' => 'gx-0'
                ]
        ],
        'tx_t3sbootstrap_verticalgutters' => [
                'label' => 'Vertical gutters',
                'exclude' => 1,
                'description' => 'INFO: https://getbootstrap.com/docs/5.3/layout/gutters/#vertical-gutters',
                'displayCond' => [
                         'OR' => [
                                'FIELD:CType:=:image',
                                'FIELD:CType:=:textpic',
                                'FIELD:CType:=:textmedia',
                                'FIELD:CType:=:t3sbs_gallery',
                         ],
                ],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'gy-0 (no gutters)', 'value' => 'mb-0',],
                                ['label' => 'gy-1', 'value' => 'mb-1',],
                                ['label' => 'gy-2', 'value' => 'mb-2',],
                                ['label' => 'gy-3', 'value' => 'mb-3',],
                                ['label' => 'gy-4 (default)', 'value' => 'mb-4',],
                                ['label' => 'gy-5', 'value' => 'mb-5',],
                        ],
                        'default' => 'mb-4'
                ]
        ],
        'tx_t3sbootstrap_bordercolor' => [
                'label' => 'Border color',
                'exclude' => 1,
                'onChange' => 'reload',
                'displayCond' => 'FIELD:imageborder:REQ:true',
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'default', 'value' => '',],
                                ['label' => 'white', 'value' => 'white',],
                                ['label' => 'muted', 'value' => 'muted',],
                                ['label' => 'secondary', 'value' => 'secondary',],
                                ['label' => 'primary', 'value' => 'primary',],
                                ['label' => 'success', 'value' => 'success',],
                                ['label' => 'info', 'value' => 'info',],
                                ['label' => 'warning', 'value' => 'warning',],
                                ['label' => 'danger', 'value' => 'danger',],
                                ['label' => 'light', 'value' => 'light',],
                                ['label' => 'dark', 'value' => 'dark',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_image_ratio' => [
                'label' => 'Image Ratio',
                'exclude' => 1,
                'displayCond' => [
                        'OR' => [
                                'FIELD:CType:=:textpic',
                                'FIELD:CType:=:textmedia',
                                'FIELD:CType:=:t3sbs_mediaobject',
                                'FIELD:CType:=:t3sbs_card',
                                'FIELD:CType:=:t3sbs_toast',
                                'FIELD:CType:=:t3sbs_gallery',
                        ]
                ],
                'config' => [
                        'type' => 'select',
                        'renderType' => 'selectSingle',
                        'items' => [
                                ['label' => 'none', 'value' => '',],
                                ['label' => '1:1', 'value' => '1:1',],
                                ['label' => '2:1', 'value' => '2:1',],
                                ['label' => '4:3', 'value' => '4:3',],
                                ['label' => '3:2', 'value' => '3:2',],
                                ['label' => '16:9', 'value' => '16:9',],
                                ['label' => '21:9', 'value' => '21:9',],
                        ],
                        'default' => ''
                ]
        ],
        'tx_t3sbootstrap_image_orig' => [
                'exclude' => 1,
                'label' => 'Use Original Image',
                'displayCond' => [
                        'OR' => [
                            'FIELD:CType:=:textpic',
                            'FIELD:CType:=:textmedia',
                            'FIELD:CType:=:t3sbs_mediaobject',
                            'FIELD:CType:=:t3sbs_card',
                            'FIELD:CType:=:t3sbs_toast',
                            'FIELD:CType:=:t3sbs_gallery',
                        ]
                ],
                'config' => [
                        'type' => 'check',
                        'renderType' => 'checkboxToggle'
                ]
        ],

        'tx_t3sbootstrap_zoom_orig' => [
                'exclude' => 1,
                'label' => 'Use Original Image for Lightbox',
                'description' => 'Only useful with image manipulation (cropping)',
                'displayCond' => [
                        'OR' => [
                            'FIELD:CType:=:textpic',
                            'FIELD:CType:=:textmedia',
                            'FIELD:CType:=:t3sbs_mediaobject',
                            'FIELD:CType:=:t3sbs_card',
                            'FIELD:CType:=:t3sbs_toast',
                            'FIELD:CType:=:t3sbs_gallery',
                        ]
                ],
                'config' => [
                        'type' => 'check',
                        'renderType' => 'checkboxToggle'
                ]
        ],

        'tx_t3sbootstrap_animateCss' => [
                'exclude' => 1,
                'l10n_display' => 'hideDiff',
                'label' => 'Animate.css',
                'config' => [
                        'type' => 'select',
                        'items' => [
                                ['label' => 'None', 'value' => '0',],
                                ['label' => 'bounce', 'value' => 'bounce',],
                                ['label' => 'flash', 'value' => 'flash',],
                                ['label' => 'pulse', 'value' => 'pulse',],
                                ['label' => 'rubberBand', 'value' => 'rubberBand',],
                                ['label' => 'shake', 'value' => 'shake',],
                                ['label' => 'headShake', 'value' => 'headShake',],
                                ['label' => 'swing', 'value' => 'swing',],
                                ['label' => 'tada', 'value' => 'tada',],
                                ['label' => 'jello', 'value' => 'jello',],
                                ['label' => 'bounceIn', 'value' => 'bounceIn',],
                                ['label' => 'bounceInDown', 'value' => 'bounceInDown',],
                                ['label' => 'bounceInLeft', 'value' => 'bounceInLeft',],
                                ['label' => 'bounceInRight', 'value' => 'bounceInRight',],
                                ['label' => 'bounceInUp', 'value' => 'bounceInUp',],
                                ['label' => 'bounceOut', 'value' => 'bounceOut',],
                                ['label' => 'bounceOutDown', 'value' => 'bounceOutDown',],
                                ['label' => 'bounceOutLeft', 'value' => 'bounceOutLeft',],
                                ['label' => 'bounceOutRight', 'value' => 'bounceOutRight',],
                                ['label' => 'bounceOutUp', 'value' => 'bounceOutUp',],
                                ['label' => 'fadeIn', 'value' => 'fadeIn',],
                                ['label' => 'fadeInDown', 'value' => 'fadeInDown',],
                                ['label' => 'fadeInDownBig', 'value' => 'fadeInDownBig',],
                                ['label' => 'fadeInLeft', 'value' => 'fadeInLeft',],
                                ['label' => 'fadeInLeftBig', 'value' => 'fadeInLeftBig',],
                                ['label' => 'fadeInRight', 'value' => 'fadeInRight',],
                                ['label' => 'fadeInRightBig', 'value' => 'fadeInRightBig',],
                                ['label' => 'fadeInUp', 'value' => 'fadeInUp',],
                                ['label' => 'fadeInUpBig', 'value' => 'fadeInUpBig',],
                                ['label' => 'fadeOut', 'value' => 'fadeOut',],
                                ['label' => 'fadeOutDown', 'value' => 'fadeOutDown',],
                                ['label' => 'fadeOutDownBig', 'value' => 'fadeOutDownBig',],
                                ['label' => 'fadeOutLeft', 'value' => 'fadeOutLeft',],
                                ['label' => 'fadeOutLeftBig', 'value' => 'fadeOutLeftBig',],
                                ['label' => 'fadeOutRight', 'value' => 'fadeOutRight',],
                                ['label' => 'fadeOutRightBig', 'value' => 'fadeOutRightBig',],
                                ['label' => 'fadeOutUp', 'value' => 'fadeOutUp',],
                                ['label' => 'fadeOutUpBig', 'value' => 'fadeOutUpBig',],
                                ['label' => 'flipInX', 'value' => 'flipInX',],
                                ['label' => 'flipInY', 'value' => 'flipInY',],
                                ['label' => 'flipOutX', 'value' => 'flipOutX',],
                                ['label' => 'flipOutY', 'value' => 'flipOutY',],
                                ['label' => 'lightSpeedIn', 'value' => 'lightSpeedIn',],
                                ['label' => 'lightSpeedOut', 'value' => 'lightSpeedOut',],
                                ['label' => 'rotateIn', 'value' => 'rotateIn',],
                                ['label' => 'rotateInDownLeft', 'value' => 'rotateInDownLeft',],
                                ['label' => 'rotateInDownRight', 'value' => 'rotateInDownRight',],
                                ['label' => 'rotateInUpLeft', 'value' => 'rotateInUpLeft',],
                                ['label' => 'rotateInUpRight', 'value' => 'rotateInUpRight',],
                                ['label' => 'rotateOut', 'value' => 'rotateOut',],
                                ['label' => 'rotateOutDownLeft', 'value' => 'rotateOutDownLeft',],
                                ['label' => 'rotateOutDownRight', 'value' => 'rotateOutDownRight',],
                                ['label' => 'rotateOutUpLeft', 'value' => 'rotateOutUpLeft',],
                                ['label' => 'rotateOutUpRight', 'value' => 'rotateOutUpRight',],
                                ['label' => 'hinge', 'value' => 'hinge',],
                                ['label' => 'rollIn', 'value' => 'rollIn',],
                                ['label' => 'rollOut', 'value' => 'rollOut',],
                                ['label' => 'zoomIn', 'value' => 'zoomIn',],
                                ['label' => 'zoomInDown', 'value' => 'zoomInDown',],
                                ['label' => 'zoomInLeft', 'value' => 'zoomInLeft',],
                                ['label' => 'zoomInRight', 'value' => 'zoomInRight',],
                                ['label' => 'zoomInUp', 'value' => 'zoomInUp',],
                                ['label' => 'zoomOut', 'value' => 'zoomOut',],
                                ['label' => 'zoomOutDown', 'value' => 'zoomOutDown',],
                                ['label' => 'zoomOutLeft', 'value' => 'zoomOutLeft',],
                                ['label' => 'zoomOutRight', 'value' => 'zoomOutRight',],
                                ['label' => 'zoomOutUp', 'value' => 'zoomOutUp',],
                                ['label' => 'slideInDown', 'value' => 'slideInDown',],
                                ['label' => 'slideInLeft', 'value' => 'slideInLeft',],
                                ['label' => 'slideInRight', 'value' => 'slideInRight',],
                                ['label' => 'slideInUp', 'value' => 'slideInUp',],
                                ['label' => 'slideOutDown', 'value' => 'slideOutDown',],
                                ['label' => 'slideOutLeft', 'value' => 'slideOutLeft',],
                                ['label' => 'slideOutRight', 'value' => 'slideOutRight',],
                                ['label' => 'slideOutUp', 'value' => 'slideOutUp',],
                        ],
                        'renderType' => 'selectSingle'
                ]
        ],
        'tx_t3sbootstrap_animateCssRepeat' => [
                'exclude' => 1,
                'label' => 'Repeat',
                'config' => [
                        'type' => 'check'
                ]
        ],
        'tx_t3sbootstrap_animateCssDuration' => [
                'label' => 'Duration in seconds',
                'exclude' => 1,
                'config' => [
                        'type' => 'number',
                        'size' => 3
                ]
        ],
        'tx_t3sbootstrap_animateCssDelay' => [
                'label' => 'Delay in seconds',
                'exclude' => 1,
                'config' => [
                        'type' => 'number',
                        'size' => 3
                ]
        ],
        'tx_t3sbootstrap_sectionOrder' => [
                'label' => 'Custom order in section Menu',
                'exclude' => 1,
                'config' => [
                        'type' => 'number',
                        'size' => 3
                ]
        ],
        'tx_t3sbootstrap_bodytext' => [
                'label' => 'Text bottom ',
                'config' => [
                        'type' => 'text',
                        'cols' => 80,
                        'rows' => 15,
                        'softref' => 'typolink_tag,email[subst],url',
                        'search' => [
                                'andWhere' => '{#CType}=\'t3sbs_card\'',
                        ],
                ],
        ],
        'tx_t3sbootstrap_cardheader' => [
                'label' => 'Card Header',
                'config' => [
                        'type' => 'input',
                        'size' => 50,
                        'max' => 255,
                ],
        ],
        'tx_t3sbootstrap_cardfooter' => [
                'label' => 'Card Header',
                'config' => [
                        'type' => 'input',
                        'size' => 50,
                        'max' => 255,
                ],
        ],
        'tx_t3sbootstrap_list_item' => [
                'label' => 'List Group',
                'config' => [
                        'type' => 'inline',
                        'foreign_table' => 'tx_t3sbootstrap_list_item_inline',
                        'foreign_field' => 'parentid',
                        'foreign_table_field' => 'parenttable',
                ],
        ],

];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempContentColumns);
unset($tempContentColumns);


/***************
 * Assets Inline
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_assets'] = [
        'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                        header; Internal title (not displayed),
        --div--;Java Script,
                bodytext;JavaScript,
                pi_flexform; Inline JavaScript Settings,
        --div--;CSS,
                tx_t3sbootstrap_bodytext;CSS,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden
        ',
        'columnsOverrides' => [
                'bodytext' => [
                        'config' => [
                                'type' => 'text',
                                'format' => 'javascript',
                                'renderType' => 't3editor',
                                'wrap' => 'virtual',
                                'rows' => 15,
                        ],
                ],
                'tx_t3sbootstrap_bodytext' => [
                        'config' => [
                                'type' => 'text',
                                'renderType' => 't3editor',
                                'format' => 'css',
                                'rows' => 15,
                                'wrap' => 'virtual',
                        ],
                ],
        ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:t3sbootstrap/Configuration/FlexForms/AssetInline.xml',
    't3sbs_assets'
);


/***************
 * Button - t3sbs_button
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_button'] = [
        'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                        --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                        --palette--;;hidden,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
        '
];
$GLOBALS['TCA']['tt_content']['types']['t3sbs_button']['columnsOverrides'] = [
    'header_link' => [
         'config' => [
                 'eval' => 'trim,required'
         ]
    ],
    'header' => [
         'config' => [
                 'eval' => 'trim,required'
         ]
    ],
];


/***************
 * Carousel item - t3sbs_carousel
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_carousel'] = [
        'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
                        bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.images,
                        assets,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,T3SFlex;tx_t3sbootstrap_flexform,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                        --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                        --palette--;;hidden,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                        categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                        rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
        ',
        'columnsOverrides' => [
                'bodytext' => [
                        'config' => [
                                'enableRichtext' => true
                        ]
                ],
                'assets' => [
                        'config' => [
                                'maxitems' => 1
                        ]
                ]
        ]
];

/***************
 * Media object - t3sbs_mediaobject
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_mediaobject'] = $GLOBALS['TCA']['tt_content']['types']['textmedia'];
$GLOBALS['TCA']['tt_content']['types']['t3sbs_mediaobject']['columnsOverrides'] = [
        'bodytext' => [
                'config' => [
                        'enableRichtext' => true
                ]
        ],
        'assets' => [
                'config' => [
                        'maxitems' => 1
                ]
        ]
];


/***************
 * Card - t3sbs_card
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_card'] = [
        'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
                --div--;Content,pi_flexform;Card Content,
                        tx_t3sbootstrap_cardheader;Card Header,
                        bodytext;Text top,
                        tx_t3sbootstrap_list_item;List Group,
                        tx_t3sbootstrap_bodytext;Text bottom,
                        tx_t3sbootstrap_cardfooter;Card Footer,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media,
                        assets,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.palette.mediaAdjustments;mediaAdjustments,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.palette.gallerySettings;gallerySettings,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.imagelinks;imagelinks,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                        --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                        --palette--;;hidden,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
        ',
        'columnsOverrides' => [
                'bodytext' => [
                        'config' => [
                                'enableRichtext' => true
                        ]
                ],
                'tx_t3sbootstrap_bodytext' => [
                        'config' => [
                                'enableRichtext' => true
                        ]
                ],
                'assets' => [
                        'config' => [
                                'maxitems' => 2
                        ]
                ]
        ]
];


/***************
 * Toasts - t3sbs_toast
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_toast'] = $GLOBALS['TCA']['tt_content']['types']['t3sbs_mediaobject'];


/***************
 * Bullets
 */
// add extra column
$GLOBALS['TCA']['tt_content']['columns']['bullets_type']['config']['items'] = [
        ['label' => 'BS Inline list', 'value' => 2,],
        ['label' => 'BS Unstyled list', 'value' => 3,],
        ['label' => 'BS Listengruppen', 'value' => 4,],
        ['label' => 'BS Definition list (use pipe "|")','value' => 5,],
];

/***************
 * FluidTemplate
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_fluidtemplate']['showitem'] = '
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
        header;Data Variable (optional),
        subheader;Path to your Fluid-Template,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access
';


/***************
 * BS Image Gallery
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_gallery'] = [
         'showitem' => '
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,rowDescription,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media,assets,
                        LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.ALT.uploads_formlabel,
                        --linebreak--, file_collections;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:file_collections.ALT.uploads_formlabel,
                        --linebreak--, filelink_sorting,
                        --palette--;;mediaAdjustments,imagecols,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                         --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                        hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.extended
        '
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'appearanceLinks',
    'tx_t3sbootstrap_header_sectionMenu',
    'after:sectionIndex'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'header',
    'tx_t3sbootstrap_header_celink',
    'after:header_link'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'headers',
    'tx_t3sbootstrap_header_celink',
    'after:header_link'
);

// Media Adjustments
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'mediaAdjustments',
    '--linebreak--, tx_t3sbootstrap_bordercolor',
    'after:imageborder'
);

// T3SB Image Gutters
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'imageGutters',
    'tx_t3sbootstrap_gutters',
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'imageGutters',
    'tx_t3sbootstrap_verticalgutters',
    'after:tx_t3sbootstrap_gutters'
);

// T3SB Image Settings
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'imageSettings',
    'tx_t3sbootstrap_inTextImgRowWidth'
);
if ($extconf['ratio']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'tt_content',
        'imageSettings',
        'tx_t3sbootstrap_image_ratio',
        'before:tx_t3sbootstrap_inTextImgRowWidth'
    );
}
if ($extconf['origimage']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'tt_content',
        'imageSettings',
        'tx_t3sbootstrap_image_orig',
        'after:tx_t3sbootstrap_inTextImgRowWidth'
    );
}

// Behavior
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'imagelinks',
    'tx_t3sbootstrap_zoom_orig',
    'after: image_zoom'
);


# add palettes
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--; ;bsHeaderExtra',
    '',
    'after:header'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;Bootstrap Color;bootstrapColor',
    '',
    'after:layout'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;Bootstrap Utilities;bootstrap',
    '',
    'after:layout'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;Bootstrap Spacing;bootstrapSpacing',
    '',
    'after:layout'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;T3SB Image Settings;imageSettings',
    '',
    'after:mediaAdjustments'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;T3SB Image Gutters;imageGutters',
    '',
    'after:mediaAdjustments'
);


# add palette animate if EXT:content_animations is not loaded
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('content_animations')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--palette--;Animation;animate',
        '',
        'after:layout'
    );
}

$GLOBALS['TCA']['tt_content']['palettes']['bsHeaderExtra'] = [
    'showitem' => 'tx_t3sbootstrap_header_display, tx_t3sbootstrap_header_position, --linebreak--,
    tx_t3sbootstrap_header_class, tx_t3sbootstrap_header_fontawesome'
];

$GLOBALS['TCA']['tt_content']['palettes']['bootstrapSpacing'] = [
    'showitem' => 'tx_t3sbootstrap_padding_sides, tx_t3sbootstrap_padding_size, --linebreak--,
    tx_t3sbootstrap_margin_sides, tx_t3sbootstrap_margin_size'
];

if ($extconf['extraStyle']) {
    $GLOBALS['TCA']['tt_content']['palettes']['bootstrap'] = [
    'showitem' => 'tx_t3sbootstrap_extra_class,
    --linebreak--, tx_t3sbootstrap_extra_style,
    --linebreak--, tx_t3sbootstrap_container,
    --linebreak--, tx_t3sbootstrap_flexform'
];
} else {
    $GLOBALS['TCA']['tt_content']['palettes']['bootstrap'] = [
    'showitem' => 'tx_t3sbootstrap_extra_class,
    --linebreak--, tx_t3sbootstrap_container,
    --linebreak--, tx_t3sbootstrap_flexform'
];
}

$GLOBALS['TCA']['tt_content']['palettes']['bootstrapColor'] = [
    'showitem' => 'tx_t3sbootstrap_contextcolor, tx_t3sbootstrap_bgcolor, --linebreak--, tx_t3sbootstrap_bgopacity, tx_t3sbootstrap_textcolor'
];

if ($extconf['animateCss']) {
    $GLOBALS['TCA']['tt_content']['palettes']['animate'] = [
            'showitem' => 'tx_t3sbootstrap_animateCss,
            tx_t3sbootstrap_animateCssDuration, --linebreak--,
            tx_t3sbootstrap_animateCssDelay,
            tx_t3sbootstrap_animateCssRepeat'
        ];
}

if ($extconf['sectionOrder']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'tt_content',
        'appearanceLinks',
        'tx_t3sbootstrap_sectionOrder',
        'after:sectionIndex'
    );
}

if ($extconf['preview']) {
    # Show preview of tt_content elements in page module
    $t3sbsContent = ['t3sbs_button', 't3sbs_card', 't3sbs_carousel', 't3sbs_fluidtemplate', 't3sbs_gallery', 't3sbs_mediaobject', 't3sbs_toast', 't3sbs_assets'];
    foreach ($t3sbsContent as $t3sb) {
        $GLOBALS['TCA']['tt_content']['types'][trim($t3sb)]['previewRenderer'] = \T3SBS\T3sbootstrap\Backend\Preview\DefaultPreviewRenderer::class;
    }
}
