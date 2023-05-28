<?php
defined('TYPO3') || die();

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

/***************
 * Add new EXT:container CTypes
 */

# GRID COLUMNS
if (array_key_exists('preview', $extconf) && $extconf['preview'] === '1') {

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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
				'three_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.title',
				'description',
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# ROW CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# CAROUSEL CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
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
		->setBackendTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridTemplate('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Templates/Grid.html')
		->setGridPartialPaths(['EXT:backend/Resources/Private/Partials/', 'EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/'])
		->addGridPartialPath('EXT:t3sbootstrap/Resources/Private/Backend/Preview/Partials/')
	);
	# SWIPE CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
				'three_columns',
				'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_be.xlf:tx_container.threeColumns.title',
				'description',
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
	# ROW CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
	# CAROUSEL CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
	# SWIPE CONTAINER
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class)->configureContainer(
		(
			new \B13\Container\Tca\ContainerConfiguration(
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
