<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use B13\Container\Tca\Registry;
use B13\Container\Tca\ContainerConfiguration;

# Extension configuration
$extconf = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

/***************
 * Add new EXT:container CTypes
 */

# GRID COLUMNS
if (array_key_exists('preview', $extconf) && $extconf['preview'] === '1') {

	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'two_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 221],
						['name' => 'Column 2', 'colPos' => 222]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-2_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'three_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.title',
				'description',
				[
					[
						['name' => 'Column 1', 'colPos' => 231],
						['name' => 'Column 2', 'colPos' => 232],
						['name' => 'Column 3', 'colPos' => 233]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-3_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'four_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 241],
						['name' => 'Column 2', 'colPos' => 242],
						['name' => 'Column 3', 'colPos' => 243],
						['name' => 'Column 4', 'colPos' => 244]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'six_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 261],
						['name' => 'Column 2', 'colPos' => 262],
						['name' => 'Column 3', 'colPos' => 263],
						['name' => 'Column 4', 'colPos' => 264],
						['name' => 'Column 5', 'colPos' => 265],
						['name' => 'Column 6', 'colPos' => 266]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# ROW CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'row_columns',
				'Row Columns',
				'Use these row columns classes to quickly create basic grid layouts.',
				[
					[
						['name' => 'Row Column', 'colPos' => 290]
					  ]
				]
			)
		)
		->setIcon('EXT:container/Resources/Public/Icons/container-4col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);

	# CARD WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# BUTTON GROUP
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# AUTO LAYOUT
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# BACKGROUND WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# PARALLAX WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# CAROUSEL CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'carousel_container',
				'Carousel Container',
				'A container for several Carousel slides (CE:t3sbs_carousel)',
				[
					[
						['name' => 'Carousel Container', 'colPos' => 276, 'allowed' => ['CType' => 't3sbs_carousel']]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# LIST GROUP WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# MASONRY
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# SWIPE CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'swiper_container',
				'Swiper Container',
				'A container for several Swipe slides (CE:t3sbs_carousel)',
				[
					[
						['name' => 'Swipe Container', 'colPos' => 300, 'allowed' => ['CType' => 't3sbs_carousel']]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-carousel-container.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# TOAST CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'toast_container',
				'Toast Container',
				'A container for several Toast content',
				[
					[
						['name' => 'Toast Container', 'colPos' => 310]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-modal.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);

} else {

	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'two_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.twoColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 221],
						['name' => 'Column 2', 'colPos' => 222]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-2_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'three_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.title',
				'description',
				[
					[
						['name' => 'Column 1', 'colPos' => 231],
						['name' => 'Column 2', 'colPos' => 232],
						['name' => 'Column 3', 'colPos' => 233]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-3_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'four_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.fourColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 241],
						['name' => 'Column 2', 'colPos' => 242],
						['name' => 'Column 3', 'colPos' => 243],
						['name' => 'Column 4', 'colPos' => 244]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'six_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.title',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.sixColumns.description',
				[
					[
						['name' => 'Column 1', 'colPos' => 261],
						['name' => 'Column 2', 'colPos' => 262],
						['name' => 'Column 3', 'colPos' => 263],
						['name' => 'Column 4', 'colPos' => 264],
						['name' => 'Column 5', 'colPos' => 265],
						['name' => 'Column 6', 'colPos' => 266]
					  ]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-4_col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);
	# ROW CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'row_columns',
				'Row Columns',
				'Use these row columns classes to quickly create basic grid layouts.',
				[
					[
						['name' => 'Row Column', 'colPos' => 290]
					  ]
				]
			)
		)
		->setIcon('EXT:container/Resources/Public/Icons/container-4col.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);
	# CARD WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# BUTTON GROUP
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# AUTO LAYOUT
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# BACKGROUND WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# PARALLAX WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	);
	# CAROUSEL CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'carousel_container',
				'Carousel Container',
				'A container for several Carousel slides (CE:t3sbs_carousel)',
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
	);
	# LIST GROUP WRAPPER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# MASONRY
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
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
	# SWIPE CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'swiper_container',
				'Swiper Container',
				'A container for several Swipe slides (CE:t3sbs_carousel)',
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
	# TOAST CONTAINER
	GeneralUtility::makeInstance(Registry::class)->configureContainer(
		(
			new ContainerConfiguration(
				'toast_container',
				'Toast Container',
				'A container for several Toast content',
				[
					[
						['name' => 'Toast Container', 'colPos' => 310]
					]
				]
			)
		)
		->setIcon('EXT:t3sbootstrap/Resources/Public/Icons/Register/ge-modal.svg')
		->setSaveAndCloseInNewContentElementWizard(false)
	);

}

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
