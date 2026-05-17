<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use B13\Container\Tca\Registry;
use B13\Container\Tca\ContainerConfiguration;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

# Extension configuration
$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

/***************
 * Add new EXT:container CTypes
 */

# GRID COLUMNS
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'two_columns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.description',
			[
				[
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.0', 'colPos' => 221],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.1', 'colPos' => 222]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-2_col.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'three_columns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.description',
			[
				[
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.0', 'colPos' => 231],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.1', 'colPos' => 232],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.2', 'colPos' => 233]
				  ]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-3_col.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'four_columns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.description',
			[
				[
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.0', 'colPos' => 241],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.1', 'colPos' => 242],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.2', 'colPos' => 243],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.3', 'colPos' => 244]
				  ]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'six_columns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.description',
			[
				[
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.0', 'colPos' => 261],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.1', 'colPos' => 262],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.2', 'colPos' => 263],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.3', 'colPos' => 264],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.4', 'colPos' => 265],
					['name' => 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.anyColumns.colPos.5', 'colPos' => 266]
				  ]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
# ROW CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'row_columns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.rowColumns',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.rowColumns.description',
			[
				[
					['name' => 'Row Column', 'colPos' => 290]
				  ]
			]
		)
	)
	->setIcon('EXT:container/Resources/Public/Icons/container-4col.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
# CARD WRAPPER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'card_wrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.cardWrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.cardWrapper.description',
			[
				[
					['name' => 'Card Wrapper', 'colPos' => 270, 'allowed' => ['CType' => 't3sbs_card']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Wrapper')
);
# BUTTON GROUP
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'button_group',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.buttonGroup',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.buttonGroup.description',
			[
				[
					['name' => 'Button Group', 'colPos' => 271, 'allowed' => ['CType' => 't3sbs_button']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/bars.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# AUTO LAYOUT
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'autoLayout_row',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.autoLayoutRow',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.autoLayoutRow.description',
			[
				[
					['name' => 'Auto-layout', 'colPos' => 272]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Grid Layout')
);
# BACKGROUND WRAPPER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'background_wrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.backgroundWrapper.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.backgroundWrapper.description',
			[
				[
					['name' => 'Background Wrapper', 'colPos' => 273]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-background_wrapper.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Wrapper')
);
# PARALLAX WRAPPER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		
		
		
		
		new ContainerConfiguration(
			'parallax_wrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.parallaxWrapper.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.parallaxWrapper.description',
			[
				[
					['name' => 'Parallax Wrapper', 'colPos' => 274]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-parallax_wrapper.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Wrapper')
);
# CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'container',
			'Container',
			'Bootstrap .container',
			[
				[
					['name' => 'Container', 'colPos' => 275]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# CAROUSEL CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'carousel_container',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.carouselContainer.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.carouselContainer.description',
			[
				[
					['name' => 'Carousel Container', 'colPos' => 276, 'disallowed' => ['CType' => 'two_columns,three_columns,four_columns,six_columns,row_columns'], 'allowed' => ['CType' => 't3sbs_carousel']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Slider')
);
# COLLAPSIBLE CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'collapsible_container',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.collapsibleContainer.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.collapsibleContainer.description',
			[
				[
					['name' => 'Collapsible Container', 'colPos' => 277, 'allowed' => ['CType' => 'collapsible_accordion']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-accordion-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# COLLAPSIBLE ELEMENT
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'collapsible_accordion',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.collapsibleElement.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.collapsibleElement.description',
			[
				[
					['name' => 'Collapsible Element', 'colPos' => 278]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-accordion-element.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# MODAL CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'modal',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.modal.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.modal.description',
			[
				[
					['name' => 'Modal Container', 'colPos' => 279]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-modal.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# TAB CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'tabs_container',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.tabContainer.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.tabContainer.description',
			[
				[
					['name' => 'Tabs Container', 'colPos' => 280, 'allowed' => ['CType' => 'tabs_tab']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-tab-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# TAB
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'tabs_tab',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.tabElement.title',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.tabElement.description',
			[
				[
					['name' => 'Tab', 'colPos' => 281]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-tab-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);
# LIST GROUP WRAPPER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		
		
		new ContainerConfiguration(
			'listGroup_wrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.listGroupWrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.listGroupWrapper.description',
			[
				[
					['name' => 'List Group Wrapper', 'colPos' => 282]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-accordion-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Wrapper')
);
# MASONRY
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		new ContainerConfiguration(
			'masonry_wrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.masonryWrapper',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.masonryWrapper.description',
			[
				[
					['name' => 'Masonry', 'colPos' => 283]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Wrapper')
);
# SWIPE CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		
		
		
		new ContainerConfiguration(
			'swiper_container',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.swiperContainer',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.swiperContainer.description',
			[
				[
					['name' => 'Swipe Container', 'colPos' => 300, 'allowed' => ['CType' => 't3sbs_carousel']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Slider')
);
# TOAST CONTAINER
GeneralUtility::makeInstance(Registry::class)->configureContainer(
	(
		
		
		
		new ContainerConfiguration(
			'toast_container',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.toastContainer',
			'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:flexform.toastContainer.description',
			[
				[
					['name' => 'Toast Container', 'colPos' => 310]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-modal.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
	->setGroup('T3S Container')
);


$GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'] = '
		--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
			--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
			--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
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
';
/*
$GLOBALS['TCA']['tt_content']['types']['two_columns']['columnsOverrides']['bgimages']['config']['maxitems'] => 2;
$GLOBALS['TCA']['tt_content']['types']['two_columns']['columnsOverrides'] = [
	'bodytext' => [
		'config' => [
			'enableRichtext' => true
		]
	],
	'bgimages' => [
		'config' => [
			'maxitems' => 1
		]
	],
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Mediaobject.xml',
		],
	],
];
*/


$GLOBALS['TCA']['tt_content']['types']['background_wrapper']['showitem'] = '
		--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
			--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
			--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
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
';

 
$GLOBALS['TCA']['tt_content']['types']['three_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['four_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['six_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['row_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['card_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['button_group']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['autoLayout_row']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['parallax_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['background_wrapper']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['carousel_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['collapsible_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['collapsible_accordion']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['background_wrapper']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['modal']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['tabs_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['tabs_tab']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['listGroup_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['masonry_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['swiper_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];
$GLOBALS['TCA']['tt_content']['types']['toast_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

$GLOBALS['TCA']['tt_content']['types']['background_wrapper']['columnsOverrides'] = [
	'assets' => [
		'config' => [
			'maxitems' => 1
		],
	],
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/BackgroundWrapper.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['parallax_wrapper']['columnsOverrides'] = [
	'assets' => [
		'config' => [
			'maxitems' => 1
		],
	],
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ParallaxWrapper.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['card_wrapper']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CardWrapper.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['button_group']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ButtonGroup.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['carousel_container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CarouselContainer.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['autoLayout_row']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/AutolayoutRow.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Container.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['collapsible_container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CollapsibleContainer.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['collapsible_accordion']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/CollapsibleAccordion.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['modal']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/Modal.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['tabs_container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TabsContainer.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['tabs_tab']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TabsTab.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['masonry_wrapper']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/MasonryWrapper.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['swiper_container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SwiperContainer.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['toast_container']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ToastContainer.xml',
		],
	]
];
$GLOBALS['TCA']['tt_content']['types']['row_columns']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/RowColumns.xml',
		],
	]
];


/***************
 * Grid layout
 */
if (!empty($extconf['flexformNoDefault'])) {
	$flexformTwoColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumnsNoDefaults.xml';
	$flexformThreeColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumnsNoDefaults.xml';
	$flexformFourColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumnsNoDefaults.xml';
	$flexformSixColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumnsNoDefaults.xml';
} else {
	if (!empty($extconf['flexformMinCol'])) {
		$flexformTwoColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumnsMin.xml';
		$flexformThreeColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumnsMin.xml';
		$flexformFourColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumnsMin.xml';
		$flexformSixColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumnsMin.xml';
	} else {
		$flexformTwoColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumns.xml';
		$flexformThreeColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumns.xml';
		$flexformFourColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumns.xml';
		$flexformSixColumns = 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumns.xml';
	}
}

$GLOBALS['TCA']['tt_content']['types']['two_columns']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => $flexformTwoColumns,
		],
	],
];
$GLOBALS['TCA']['tt_content']['types']['three_columns']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => $flexformThreeColumns,
		],
	],
];
$GLOBALS['TCA']['tt_content']['types']['four_columns']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => $flexformFourColumns,
		],
	],
];
$GLOBALS['TCA']['tt_content']['types']['six_columns']['columnsOverrides'] = [
	'tx_t3sbootstrap_flexform' => [
		'config' => [
			'ds' => $flexformSixColumns,
		],
	],
];
