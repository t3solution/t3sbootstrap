<?php
defined('TYPO3_MODE') or die();

 # if typoscript_rendering is loaded
if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('typoscript_rendering') ) {

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'T3SBS.T3sbootstrap',
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
 * Add new CTypes
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
	'tt_content',
	'CType',
	[
		'Bootstrap Media object',
		't3sbs_mediaobject',
		'content-textpic'
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
		'bs-card'
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
		'content-textpic'
	],
	't3sbs_card',
	'after'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
	'tt_content',
	'CType',
	[
		'Bootstrap Carousel Item (in carousel container)',
		't3sbs_carousel',
		'bs-carousel'
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
		'bs-button'
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
		'bs-fluidtemplate'
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
		'bs-gallery'
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
				['display-4','display-4']
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
					['ml-3 (margin-left)', 'ml-3'],
					['mr-3 (margin-right)', 'mr-3'],
					['mx-3 (margin-left and -right)', 'mx-3'],
					['my-3 (margin-top and -bottom)', 'my-3']
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
				['padding-left','l'],
				['padding-right','r'],
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
				['margin-left','l'],
				['margin-right','r'],
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
				['no container',''],
				['container','container'],
				['container-fluid','container-fluid'],
				['container-fluid px-0','container-fluid px-0']
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
			'ds_pointerField' => 'tx_gridelements_backend_layout,CType',
			'ds' => [
				'default' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Bootstrap.xml',
				'*,t3sbs_card' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/CardSetting.xml',
				'*,t3sbs_toast' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/ToastSetting.xml',
				'*,t3sbs_carousel' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Carousel.xml',
				'*,t3sbs_button' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Button.xml',
				'*,t3sbs_mediaobject' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Mediaobject.xml',
				'*,table' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Table.xml',
				'card_wrapper,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/CardWrapper.xml',
				'autoLayout_row,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/AutoLayoutRow.xml',
				'button_group,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/Buttongroup.xml',
				'container,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/Container.xml',
				'two_columns,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/TwoColumns.xml',
				'three_columns,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/ThreeColumns.xml',
				'four_columns,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/FourColumns.xml',
				'six_columns,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/SixColumns.xml',
				'background_wrapper,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/BackgroundWrapper.xml',
				'parallax_wrapper,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/ParallaxWrapper.xml',
				'carousel_container,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/CarouselContainer.xml',
				'collapsible_accordion,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/Collapse.xml',
				'collapsible_container,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/CollapseContainer.xml',
				'modal,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/Modal.xml',
				'tabs_container,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/Tabs.xml',
				'tabs_tab,gridelements_pi1' => 'FILE:EXT:t3sbootstrap/Configuration/FlexForms/Gridelements/TabsTab.xml',
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
				['danger','danger']
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
				['4:3','4:3'],
				['3:2','3:2'],
				['16:9','16:9'],
				['21:9','21:9']
			],
			'default' => ''
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
		--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
		--div--;LLL:EXT:gridelements/Resources/Private/Language/locallang_db.xlf:gridElements, tx_gridelements_container, tx_gridelements_columns
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
		--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
	--div--;LLL:EXT:gridelements/Resources/Private/Language/locallang_db.xlf:gridElements, tx_gridelements_container, tx_gridelements_columns
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
		--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
		--div--;LLL:EXT:gridelements/Resources/Private/Language/locallang_db.xlf:gridElements, tx_gridelements_container, tx_gridelements_columns
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
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
		--div--;LLL:EXT:gridelements/Resources/Private/Language/locallang_db.xlf:gridElements, tx_gridelements_container, tx_gridelements_columns
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


/***************
 * Gridelements
 */
$GLOBALS['TCA']['tt_content']['types']['gridelements_pi1']['showitem'] = str_replace('media,', '', $GLOBALS['TCA']['tt_content']['types']['gridelements_pi1']['showitem']);
$GLOBALS['TCA']['tt_content']['types']['gridelements_pi1']['showitem'] .= ',--div--;Media,assets';
$GLOBALS['TCA']['tt_content']['types']['gridelements_pi1']['columnsOverrides'] = [
	'assets' => [
		'config' => [
			'maxitems' => 1
		],
		'displayCond' => [
			'OR' => [
				'FIELD:tx_gridelements_backend_layout:=:background_wrapper',
				'FIELD:tx_gridelements_backend_layout:=:parallax_wrapper',
				'FIELD:tx_gridelements_backend_layout:=:collapsible_accordion'
			]
		]
	],
	'header_layout' => [
		'displayCond' => 'FIELD:tx_gridelements_backend_layout:!=:collapsible_accordion',
	],
	'tx_t3sbootstrap_header_display' => [
		'displayCond' => 'FIELD:tx_gridelements_backend_layout:!=:collapsible_accordion',
	],
	'date' => [
		'displayCond' => 'FIELD:tx_gridelements_backend_layout:!=:collapsible_accordion',
	],
	'header_link' => [
		'displayCond' => 'FIELD:tx_gridelements_backend_layout:!=:collapsible_accordion',
	]

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

/*
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tt_content',
	'mediaAdjustments',
	'tx_t3sbootstrap_image_ratio',
	'before:imageborder'
);
*/

# add palette bootstrap etc
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	'--palette--; ;bsRowWidth',
	'',
	'after:mediaAdjustments'
);

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
# add palette animate
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	'--palette--;Animation;animate',
	'',
	'after:layout'
);

$GLOBALS['TCA']['tt_content']['palettes']['bsRowWidth'] = [
  'showitem' => 'tx_t3sbootstrap_image_ratio, tx_t3sbootstrap_inTextImgRowWidth'
];

$GLOBALS['TCA']['tt_content']['palettes']['bsHeaderExtra'] = [
  'showitem' => 'tx_t3sbootstrap_header_display, tx_t3sbootstrap_header_position, --linebreak--,
  tx_t3sbootstrap_header_class, tx_t3sbootstrap_header_fontawesome'
];

$GLOBALS['TCA']['tt_content']['palettes']['bootstrapSpacing'] = [
  'showitem' => 'tx_t3sbootstrap_padding_sides, tx_t3sbootstrap_padding_size, --linebreak--,
  tx_t3sbootstrap_margin_sides, tx_t3sbootstrap_margin_size'
];

$GLOBALS['TCA']['tt_content']['palettes']['bootstrap'] = [
  'showitem' => 'tx_t3sbootstrap_extra_class,
  --linebreak--, tx_t3sbootstrap_container,
  --linebreak--, tx_t3sbootstrap_flexform'
];

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


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['t3sbootstrap_pi1'] = 'recursive,select_key,pages';
