<?php
defined('TYPO3') || die();

# Extension configuration
$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

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
					[ 'm-3 (margin)', 'm-3', ],
					[ 'mt-3 (margin-top)', 'mt-3', ],
					[ 'mb-3 (margin-bottom)', 'mb-3', ],
					[ 'ml-3 (margin-left)', 'ml-3', ],
					[ 'mr-3 (margin-right)', 'mr-3', ],
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
					[ 'float-left', 'float-left', ],
					[ 'float-right', 'float-right', ],
					[ 'mx-auto d-block', 'mx-auto d-block', ],
				],
			],
		],
	],
	'tx_t3sbootstrap_hover_effect' => [
		'label' => 'Link Hover Effect (title and/or description)',
		'exclude' => 1,
/*
		'displayCond' => [
			 'AND' => [
				'FIELD:tablenames:=:tt_content',
				'OR' => [
						'FIELD:CType:=:textmedia',
						'FIELD:CType:=:textpic',
						'FIELD:CType:=:image',
				]
			 ]
		],
*/
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['none',''],
				['Effect 1','snip1273'],
				['Effect 2','snip1321'],
				['Effect 3','snip1577'],
				['Effect 4','snip0015'],
				['Effect 5 (title only)','snip1573'],
				['Effect 6','snip1477'],
				['Effect 7','snip1361'],
				['Effect 8','snip1206'],
				['Effect 9','snip1190'],
				['Effect 10','snip0016']
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
				'FIELD:fieldname:=:assets',
				'FIELD:fieldname:=:image',
			],
		],
		'config' => [
			'type' => 'check',
				'items' => [
				'1' => [
					'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
				]
			]
		]
	],
	'tx_t3sbootstrap_description_align' => [
		'label' => 'Description align',
		'exclude' => 1,
		'config' => [
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['left','left'],
				['center','center'],
				['right','right']
			],
			'default' => 'left'
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
				['light', 'text-light'],
				['dark', 'text-dark'],
				['primary', 'text-primary'],
				['secondary', 'text-secondary'],
				['success', 'text-success'],
				['danger', 'text-danger'],
				['warning', 'text-warning'],
				['info', 'text-info'],
				['white', 'text-white'],
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
		'exclude' => 1,
/*
		'displayCond' => [
			 'AND' => [
				'FIELD:tablenames:=:tt_content',
				'OR' => [
						'FIELD:CType:=:textmedia',
						'FIELD:CType:=:textpic',
						'FIELD:CType:=:image',
				]
			 ]
		],
*/

		'config' => [
			'type' => 'check',
				'items' => [
				'1' => [
					'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
				]
			]
		]
	],

];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_reference',$tempSysFileReferenceColumns);
unset($tempSysFileReferenceColumns);


TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_description_align','after:title');

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_extra_class','after:tx_t3sbootstrap_description_align');

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_extra_imgclass','after:tx_t3sbootstrap_extra_class');

if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright']) {
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_copyright','after:tx_t3sbootstrap_extra_imgclass');

	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_copyright_color','after:tx_t3sbootstrap_copyright');

	if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '2') {
		TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_copyright_source','after:tx_t3sbootstrap_copyright_color');

	}
}

if (array_key_exists('linkHoverEffect', $extconf) && $extconf['linkHoverEffect'] === '1') {
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_hover_effect','after:tx_t3sbootstrap_extra_imgclass');
}

if (array_key_exists('lazyLoad', $extconf) && $extconf['lazyLoad'] === '2') {
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_lazy_load','after:tx_t3sbootstrap_extra_imgclass');
}

if (array_key_exists('imgtag', $extconf) && $extconf['imgtag']) {
	TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette','--linebreak--,tx_t3sbootstrap_imgtag','after:tx_t3sbootstrap_description_align');
}

