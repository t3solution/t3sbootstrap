<?php
defined('TYPO3') || die();

 # if typoscript_rendering is loaded
if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('typoscript_rendering') ) {

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'T3sbootstrap',
		'Pi1',
		'Content Consent'
	);

	$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase('t3sbootstrap');
	$pluginSignature = strtolower($extensionName) . '_pi1';
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Consent.xml');

}

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

/***************
 * Add new EXT:container CTypes
 */

# GRID COLUMNS
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'three_columns', // CType
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
);
$GLOBALS['TCA']['tt_content']['types']['three_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['four_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['six_columns']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];


# CARD WRAPPER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'card_wrapper',
			'Card Wrapper',
			'In addition to styling the content within cards, Bootstrap includes a few options for laying out series of cards.',
			[
				[
					['name' => 'Card Wrapper', 'colPos' => 270, 'allowed' => ['CType' => 't3sbs_card']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['card_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];


# BUTTON GROUP
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'button_group',
			'Button Group',
			'Group a series of buttons together on a single line with the button group.',
			[
				[
					['name' => 'Button Group', 'colPos' => 271, 'allowed' => ['CType' => 't3sbs_button']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/bars.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['button_group']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# AUTO LAYOUT
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'autoLayout_row',
			'Auto-layout',
			'Options: "Equal-width", "Setting one column width" or "Variable width content".',
			[
				[
					['name' => 'Auto-layout', 'colPos' => 272]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['autoLayout_row']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# BACKGROUND WRAPPER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'background_wrapper',
			'Background Wrapper',
			'Options: "Full width container with background color -image or -Youtube vido.',
			[
				[
					['name' => 'Background Wrapper', 'colPos' => 273]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-background_wrapper.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
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

$GLOBALS['TCA']['tt_content']['types']['background_wrapper']['columnsOverrides'] = [
	'assets' => [
		'config' => [
			'maxitems' => 1
		],
	]
];

# PARALLAX WRAPPER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'parallax_wrapper',
			'Parallax Wrapper',
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
);
$GLOBALS['TCA']['tt_content']['types']['parallax_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['background_wrapper']['showitem'];

# CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# CAROUSEL CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'carousel_container',
			'Carousel Container',
			'A container for several Carousel slides (CE:t3sb_carousel)',
			[
				[
					['name' => 'Carousel Container', 'colPos' => 276, 'allowed' => ['CType' => 't3sbs_carousel']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['carousel_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# COLLAPSIBLE CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['collapsible_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# COLLAPSIBLE ELEMENT
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['collapsible_accordion']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['background_wrapper']['showitem'];

# MODAL CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['modal']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# TAB CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['tabs_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# TAB
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
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
);
$GLOBALS['TCA']['tt_content']['types']['tabs_tab']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# LIST GROUP WRAPPER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'listGroup_wrapper',
			'List Group Wrapper',
			'Shows other CEs in a bootstrap list group. Nice with "Link the entire Content Element"',
			[
				[
					['name' => 'List Group Wrapper', 'colPos' => 282]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-accordion-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['listGroup_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# MASONRY
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'masonry_wrapper',
			'Masonry Wrapper',
			'Masonry with the Bootstrap grid system',
			[
				[
					['name' => 'Masonry', 'colPos' => 283]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-card-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['masonry_wrapper']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];

# SWIPE CONTAINER
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
	(
		new \B13\Container\Tca\ContainerConfiguration(
			'swiper_container',
			'Swiper Container',
			'A container for several Swipe slides (CE:t3sb_carousel)',
			[
				[
					['name' => 'Swipe Container', 'colPos' => 300, 'allowed' => ['CType' => 't3sbs_carousel']]
				]
			]
		)
	)
	->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
	->setSaveAndCloseInNewContentElementWizard(false)
);
$GLOBALS['TCA']['tt_content']['types']['swiper_container']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['two_columns']['showitem'];


/***************
 * Add new CTypes
 */
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
				['none',''],
				['display-1','display-1'],
				['display-2','display-2'],
				['display-3','display-3'],
				['display-4','display-4'],
		['display-5','display-5'],
		['display-6','display-6']
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
					['typo3', 'fab fa-typo3'],
					['envelope', 'far fa-envelope'],
					['info-circle', 'fas fa-info-circle'],
					['exclamation-circle', 'fas fa-exclamation-circle'],
					['question-circle', 'fas fa-question-circle'],
					['check-circle', 'fas fa-check-circle'],
					['chevron-circle-left', 'fas fa-chevron-circle-left'],
					['chevron-circle-right', 'fas fa-chevron-circle-right'],
					['youtube', 'fab fa-youtube'],
					['vimeo', 'fab fa-vimeo-square'],
				],
			],
		],
	],
	'tx_t3sbootstrap_header_celink' => [
		'exclude' => 1,
		'label' => 'Link the entire Content Element',
		'config' => [
			'type' => 'check',
				'items' => [
				'1' => [
					'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
				]
			]
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
				['Above the image (default)','above'],
				['Beside or under the image','beside']
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
				['no padding',''],
				['padding on all 4 sides','blank'],
				['padding-top','t'],
				['padding-bottom','b'],
				['padding-left','s'],
				['padding-right','e'],
				['padding-left and -right','x'],
				['padding-top and -bottom','y']
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
				['0','0'],
				['1 (.25 rem)','1'],
				['2 (.5 rem)','2'],
				['3 (1 rem)','3'],
				['4 (1.5 rem)','4'],
				['5 (3 rem)','5']
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
				['no margin',''],
				['margin on all 4 sides','blank'],
				['margin-top','t'],
				['margin-bottom','b'],
				['margin-left','s'],
				['margin-right','e'],
				['margin-left and -right','x'],
				['margin-top and -bottom','y']
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
				['0','0'],
				['1 (.25 rem)','1'],
				['2 (.5 rem)','2'],
				['3 (1 rem)','3'],
				['4 (1.5 rem)','4'],
				['5 (3 rem)','5']
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
				['no container','0'],
				['container','container'],
				['container-fluid','container-fluid'],
				['container-fluid px-0','container-fluid px-0'],
				['container-sm','container-sm'],
				['container-md','container-md'],
				['container-lg','container-lg'],
				['container-xl','container-xl'],
				['container-xxl','container-xxl']
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
				'two_columns' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/TwoColumns.xml',
				'three_columns' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/ThreeColumns.xml',
				'four_columns' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/FourColumns.xml',
				'six_columns' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Container/SixColumns.xml',
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
			]
		]
	],
	'tx_t3sbootstrap_extra_class' => [
		'label'	 => 'Extra Class',
		'exclude' => 1,
		'config' => [
			'type' => 'input',
			'size' => 35
		]
	],
	'tx_t3sbootstrap_extra_style' => [
		'label'	 => 'Extra Style',
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
			'type' => 'input',
			'renderType' => 'colorpicker',
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
				[1,1],
				[2,2],
				[3,3],
				[4,4],
				[5,5],
				[6,6],
				[7,7],
				[8,8],
				[9,9],
				[10,10],
				[11,11],
				[12,12]
			],
			'default' => 4
		]
	],
	'tx_t3sbootstrap_galleryGutters' => [
		'label' => 'Gallery gutters',
		'exclude' => 1,
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				[1,1],
				[2,2],
				[3,3],
				[4,4],
				[5,5]
			],
			'default' => 4
		]
	],
	'tx_t3sbootstrap_bgopacity' => [
		 'label' => 'Opacity for Background color only',
		'exclude' => 1,
		 	'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->color_'.$extconf['color'],
		 'config' => [
			 'type' => 'input',
			 'size' => 10,
			 'eval' => 'trim,int',
			 'range' => [
				 'lower' => 0,
				 'upper' => 100,
			 ],
			 'default' => 100,
			 'slider' => [
				 'step' => 1,
				 'width' => 200,
			 ],
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
				['none',''],
				['primary','primary'],
				['secondary', 'secondary'],
				['success','success'],
				['info','info'],
				['warning','warning'],
				['danger','danger'],
				['light','light'],
				['dark','dark'],
				['body','body'],
				['transparent','transparent'],
				['custom 1','customOne'],
				['custom 2','customTwo']
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
				['default',''],
				['white','white'],
				['muted','muted'],
				['secondary', 'secondary'],
				['primary','primary'],
				['success','success'],
				['info','info'],
				['warning','warning'],
				['danger','danger'],
				['light','light'],
				['dark','dark'],
				['body','body'],
				['custom 1','customOne'],
				['custom 2','customTwo']
			],
			'default' => ''
		]
	],
	'tx_t3sbootstrap_inTextImgRowWidth' => [
		'label' => 'Gallery row width in %',
		'exclude' => 1,
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['auto','auto'],
				[25,'w-25'],
				[33,'w-33'],
				[50,'w-50'],
				[66,'w-66'],
				[75,'w-75'],
				[100,'w-100'],
				['none','none']
			],
			'default' => 'auto'
		]
	],
	'tx_t3sbootstrap_bordercolor' => [
		'label' => 'Border color',
		'exclude' => 1,
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['default',''],
				['white','white'],
				['muted','muted'],
				['secondary', 'secondary'],
				['primary','primary'],
				['success','success'],
				['info','info'],
				['warning','warning'],
				['danger','danger'],
				['light','light'],
				['dark','dark']
			],
			'default' => ''
		]
	],
	'tx_t3sbootstrap_image_ratio' => [
		'label' => 'Image Ratio',
		'exclude' => 1,
		'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->ratio_'.$extconf['ratio'],
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['none',''],
				['1:1','1:1'],
				['2:1','2:1'],
				['4:3','4:3'],
				['3:2','3:2'],
				['16:9','16:9'],
				['21:9','21:9']
			],
			'default' => ''
		]
	],
	'tx_t3sbootstrap_image_orig' => [
		'exclude' => 1,
		'label' => 'Use Original Image',
		'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->ratio_'.$extconf['origimage'],
		'config' => [
			'type' => 'check',
				'items' => [
				'1' => [
					'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
				]
			]
		]
	],
	'tx_t3sbootstrap_animateCss' => [
		'exclude' => 1,
		'l10n_display' => 'hideDiff',
		'label' => 'Animate.css',
		'config' => [
			'type' => 'select',
			'items' => [
				['None', '0'],
				['bounce', 'bounce'],
				['flash', 'flash'],
				['pulse', 'pulse'],
				['rubberBand', 'rubberBand'],
				['shake', 'shake'],
				['headShake', 'headShake'],
				['swing', 'swing'],
				['tada', 'tada'],
				['wobble', 'wobble'],
				['jello', 'jello'],
				['bounceIn', 'bounceIn'],
				['bounceInDown', 'bounceInDown'],
				['bounceInLeft', 'bounceInLeft'],
				['bounceInRight', 'bounceInRight'],
				['bounceInUp', 'bounceInUp'],
				['bounceOut', 'bounceOut'],
				['bounceOutDown', 'bounceOutDown'],
				['bounceOutLeft', 'bounceOutLeft'],
				['bounceOutRight', 'bounceOutRight'],
				['bounceOutUp', 'bounceOutUp'],
				['fadeIn', 'fadeIn'],
				['fadeInDown', 'fadeInDown'],
				['fadeInDownBig', 'fadeInDownBig'],
				['fadeInLeft', 'fadeInLeft'],
				['fadeInLeftBig', 'fadeInLeftBig'],
				['fadeInRight', 'fadeInRight'],
				['fadeInRightBig', 'fadeInRightBig'],
				['fadeInUp', 'fadeInUp'],
				['fadeInUpBig', 'fadeInUpBig'],
				['fadeOut', 'fadeOut'],
				['fadeOutDown', 'fadeOutDown'],
				['fadeOutDownBig', 'fadeOutDownBig'],
				['fadeOutLeft', 'fadeOutLeft'],
				['fadeOutLeftBig', 'fadeOutLeftBig'],
				['fadeOutRight', 'fadeOutRight'],
				['fadeOutRightBig', 'fadeOutRightBig'],
				['fadeOutUp', 'fadeOutUp'],
				['fadeOutUpBig', 'fadeOutUpBig'],
				['flipInX', 'flipInX'],
				['flipInY', 'flipInY'],
				['flipOutX', 'flipOutX'],
				['flipOutY', 'flipOutY'],
				['lightSpeedIn', 'lightSpeedIn'],
				['lightSpeedOut', 'lightSpeedOut'],
				['rotateIn', 'rotateIn'],
				['rotateInDownLeft', 'rotateInDownLeft'],
				['rotateInDownRight', 'rotateInDownRight'],
				['rotateInUpLeft', 'rotateInUpLeft'],
				['rotateInUpRight', 'rotateInUpRight'],
				['rotateOut', 'rotateOut'],
				['rotateOutDownLeft', 'rotateOutDownLeft'],
				['rotateOutDownRight', 'rotateOutDownRight'],
				['rotateOutUpLeft', 'rotateOutUpLeft'],
				['rotateOutUpRight', 'rotateOutUpRight'],
				['hinge', 'hinge'],
				['rollIn', 'rollIn'],
				['rollOut', 'rollOut'],
				['zoomIn', 'zoomIn'],
				['zoomInDown', 'zoomInDown'],
				['zoomInLeft', 'zoomInLeft'],
				['zoomInRight', 'zoomInRight'],
				['zoomInUp', 'zoomInUp'],
				['zoomOut', 'zoomOut'],
				['zoomOutDown', 'zoomOutDown'],
				['zoomOutLeft', 'zoomOutLeft'],
				['zoomOutRight', 'zoomOutRight'],
				['zoomOutUp', 'zoomOutUp'],
				['slideInDown', 'slideInDown'],
				['slideInLeft', 'slideInLeft'],
				['slideInRight', 'slideInRight'],
				['slideInUp', 'slideInUp'],
				['slideOutDown', 'slideOutDown'],
				['slideOutLeft', 'slideOutLeft'],
				['slideOutRight', 'slideOutRight'],
				['slideOutUp', 'slideOutUp'],
			],
			'renderType' => 'selectSingle'
		]
	],
	'tx_t3sbootstrap_animateCssRepeat' => [
		'exclude' => 1,
		'label' => 'Repeat (jQuery-viewport-checker option)',
		'config' => [
			'type' => 'check'
		]
	],
	'tx_t3sbootstrap_animateCssDuration' => [
		'label' => 'Duration in seconds',
		'exclude' => 1,
		'config' => [
			'type' => 'input',
			'eval' => 'int',
			'size' => 3
		]
	],
	'tx_t3sbootstrap_animateCssDelay' => [
		'label' => 'Delay in seconds',
		'exclude' => 1,
		'config' => [
			'type' => 'input',
			'eval' => 'int',
			'size' => 3
		]
	],
	'tx_t3sbootstrap_sectionOrder' => [
		'label' => 'Custom order in section Menu',
		'exclude' => 1,
		'config' => [
			'type' => 'input',
			'eval' => 'int',
			'size' => 3
		]
	],

];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content',$tempContentColumns);
unset($tempContentColumns);


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
			image,
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
 		'image' => [
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
		'assets' => [
			'config' => [
				'maxitems' => 2
			]
		]
	]
];
// Add flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/CardContent.xml', 't3sbs_card');


/***************
 * Toasts - t3sbs_toast
 */
$GLOBALS['TCA']['tt_content']['types']['t3sbs_toast'] = $GLOBALS['TCA']['tt_content']['types']['t3sbs_mediaobject'];


/***************
 * Bullets
 */
// add extra column
$GLOBALS['TCA']['tt_content']['columns']['bullets_type']['config']['items'][2] = ['BS Inline list', 2];
$GLOBALS['TCA']['tt_content']['columns']['bullets_type']['config']['items'][3] = ['BS Unstyled list',3];
$GLOBALS['TCA']['tt_content']['columns']['bullets_type']['config']['items'][4] = ['BS Listengruppen',4];
$GLOBALS['TCA']['tt_content']['columns']['bullets_type']['config']['items'][5] = ['BS Definition list (use pipe "|")',5];


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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tt_content',
	'mediaAdjustments',
	'tx_t3sbootstrap_bordercolor',
	'after:imageborder'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tt_content',
	'mediaAdjustments',
	'tx_t3sbootstrap_image_ratio',
	'after:tx_t3sbootstrap_bordercolor'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tt_content',
	'mediaAdjustments',
	'tx_t3sbootstrap_image_orig',
	'before:tx_t3sbootstrap_image_ratio'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tt_content',
	'mediaAdjustments',
	'tx_t3sbootstrap_inTextImgRowWidth',
	'after:tx_t3sbootstrap_bordercolor'
);


# add palette bootstrap etc
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
# add palette animate if EXT:content_animations is not loaded
if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('content_animations') ) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'tt_content',
		'--palette--;Animation;animate',
		'',
		'after:layout'
	);
}

$GLOBALS['TCA']['tt_content']['palettes']['bsRowWidth'] = [
  'showitem' => 'tx_t3sbootstrap_image_ratio, tx_t3sbootstrap_inTextImgColumns, tx_t3sbootstrap_galleryGutters'
];

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

if ( $extconf['preview'] ) {
	/***************
	 * Show preview of tt_content elements in page module
	 */
	$GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer'] = T3SBS\T3sbootstrap\Backend\Preview\DefaultPreviewRenderer::class;
	$containers = ['two_columns', 'three_columns', 'four_columns', 'six_columns', 'card_wrapper', 'button_group', 'autoLayout_row',
	 'background_wrapper','parallax_wrapper',' container', 'carousel_container', 'collapsible_container', 'collapsible_accordion',
	 'modal', 'tabs_container', 'tabs_tab', 'listGroup_wrapper', 'masonry_wrapper', 'swiper_container'];
	foreach ($containers as $container) {
		$GLOBALS['TCA']['tt_content']['types'][trim($container)]['previewRenderer'] = T3SBS\T3sbootstrap\Backend\Preview\T3sbPreviewRenderer::class;
	}
}


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['t3sbootstrap_pi1'] = 'recursive,select_key,pages';

