<?php

defined('TYPO3') || die();

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

$tempPagesColumns = [
	'tx_t3sbootstrap_smallColumns' => [
		'label' => 'Aside columns width',
		'exclude' => 1,
		'description' => 'makes no sense for Backend Layout "1 Column"',
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['1',1],
				['2',2],
				['3',3],
				['4',4],
				['6',6]
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
				['no container','0'],
				['container','container'],
				['container-sm (< 576px)','container-sm'],
				['container-md (≥ 576px)','container-md'],
				['container-lg (≥ 768px)','container-lg'],
				['container-xl (≥ 992px)','container-xl'],
				['container-xxl (≥ 1200px)','container-xxl'],
				['container-fluid (≥ 1400px)','container-fluid']
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
				['Default','default'],
				['Top (both)','top'],
				['Bottom (both)','bottom'],
				['Left Aside Top','leftTop'],
				['Left Aside Bottom','leftBottom'],
				['Right Aside Top','rightTop'],
				['Right Aside Bottom','rightBottom']
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
				['Default','md'],
				['sm','sm'],
				['md','md'],
				['lg','lg'],
				['xl','xl'],
				['xxl','xxl']
			],
			'default' => 'md'
		]
	],
	'tx_t3sbootstrap_fontawesome_icon' => [
		'exclude' => 1,
		'label'	=> 'e.g.: fab fa-typo3 fa-lg',
		'config' => [
			'type' => 'input',
			'size' => 20,
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
	'tx_t3sbootstrap_cmi_generatexml' => [
		'label'	 => 'Generate cmi5.xml',
		'config' => [
			'type' => 'check',
			'items' => [
				 ['Check to generate cmi5.xml', '0'],
			],
	 ],
	]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempPagesColumns);
unset($tempPagesColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'pages',
	'--div--;cmi5,
		tx_t3sbootstrap_cmi_generatexml;',
	'',
	''
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'title','--linebreak--,tx_t3sbootstrap_titlecolor','after:title');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'title','--linebreak--,tx_t3sbootstrap_subtitlecolor','after:subtitle');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_smallColumns','after:backend_layout_next_level');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_mobileOrder','after:tx_t3sbootstrap_smallColumns');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_breakpoint','after:tx_t3sbootstrap_mobileOrder');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_dropdownRight','after:tx_t3sbootstrap_breakpoint');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_container','after:tx_t3sbootstrap_dropdownRight');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_linkToTop','after:tx_t3sbootstrap_container');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_megamenu','after:tx_t3sbootstrap_linkToTop');

if (array_key_exists('navigationColor', $extconf) && $extconf['navigationColor'] === '1') {
	# add palette Navigation Colors
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'pages',
		'--palette--; Navigation Colors for dropdown items;navColors',
		'',
		'after:title'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
		'pages', 'navColors','--linebreak--,tx_t3sbootstrap_navigationcolor','after:tx_t3sbootstrap_subtitlecolor'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
		'pages', 'navColors','--linebreak--,tx_t3sbootstrap_navigationactivecolor','after:tx_t3sbootstrap_navigationcolor'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
		'pages', 'navColors','--linebreak--,tx_t3sbootstrap_navigationhover','after:tx_t3sbootstrap_navigationactivecolor'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
		'pages', 'navColors','--linebreak--,tx_t3sbootstrap_navigationbgcolor','after:tx_t3sbootstrap_navigationhover'
	);
}

if (array_key_exists('fontawesome', $extconf) && $extconf['fontawesome'] === '1') {
	$GLOBALS['TCA']['pages']['palettes']['fontawesome'] = [
		 'showitem' => 'tx_t3sbootstrap_fontawesome_icon,
				tx_t3sbootstrap_icon_only',
			 'canNotCollapse' => 1
	];

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
		'pages',
		'--palette--;Fontawesome Icon;fontawesome',
		'',
		'after:title'
	);
}


$menuheader = 198;

// Add new page type:
$GLOBALS['PAGES_TYPES'][$menuheader] = [
	'allowedTables' => '*',
];

// Add new page type as possible select item:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
	'pages',
	'doktype',
	[
		'Dropdownmenu header',
		$menuheader,
		'content-header'
	],
	'2',
	'after'
);

// Add icon for new page type:
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
	$GLOBALS['TCA']['pages'],
	[
		'ctrl' => [
			'typeicon_classes' => [
				$menuheader => 'content-header',
			],
		],
	]
);
			

/***************
 * Register PageTSConfig Files
*/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
	't3sbootstrap',
	'Configuration/TSConfig/Registered/Textpic.tsconfig',
	'Remove CType textpic'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
	't3sbootstrap',
	'Configuration/TSConfig/Registered/Text.tsconfig',
	'Remove CType text'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
	't3sbootstrap',
	'Configuration/TSConfig/Registered/Image.tsconfig',
	'Remove CType image'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
	't3sbootstrap',
	'Configuration/TSConfig/Registered/Header.tsconfig',
	'Remove CType header'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
	't3sbootstrap',
	'Configuration/TSConfig/Registered/Callouts.tsconfig',
	'Add BS-Callouts options in Layout field'
);
