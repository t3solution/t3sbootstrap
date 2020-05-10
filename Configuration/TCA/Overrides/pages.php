<?php
defined('TYPO3_MODE') or die();

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');


$tempPagesColumns = [
	'tx_t3sbootstrap_smallColumns' => [
		'label' => 'Aside columns width (makes no sense for Backend Layout "1 Column")',
		'exclude' => 1,
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
				['no container',0],
				['container','container'],
				['container-fluid','container-fluid']
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
				['xl','xl']
			],
			'default' => 'md'
		]
	],
	'tx_t3sbootstrap_fontawesome_icon' => [
		'exclude' => 1,
		'label'	=> 'e.g.: fab fa-typo3 fa-lg',
		'config' => [
			'type' => 'input',
			'size' => '20',
		]
	],
	'tx_t3sbootstrap_icon_only' => [
		'exclude' => 1,
		'label' => 'Icon only',
		'config' => [
				'type' => 'check',
		]
	]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempPagesColumns);
unset($tempPagesColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_smallColumns','after:backend_layout_next_level');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_mobileOrder','after:tx_t3sbootstrap_smallColumns');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_breakpoint','after:tx_t3sbootstrap_mobileOrder');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_dropdownRight','after:tx_t3sbootstrap_breakpoint');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_container','after:tx_t3sbootstrap_dropdownRight');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_linkToTop','after:tx_t3sbootstrap_container');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'layout','--linebreak--,tx_t3sbootstrap_megamenu','after:tx_t3sbootstrap_linkToTop');



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
		'after:layout'
	);
}
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
