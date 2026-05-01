<?php

return [
	'ctrl' => [
		'title' => 'List Item inline for Card List Group',
		'label' => 'listitem',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'enablecolumns' => [],
		'hideTable' => true,
		'searchFields' => '',
		'iconfile' => 'EXT:t3sbootstrap/Resources/Public/Icons/tx_t3sbootstrap_domain_model_config.gif',
		'security' => [
			'ignorePageTypeRestriction' => true,
		],
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'languageField' => 'sys_language_uid',
		'translationSource' => 'l10n_source',
	],

	'columns' => [
		'hidden' => [
			'config' => [
				'type' => 'check',
				'items' => [
                    ['label' => 'Disable',],
				]
			],
		],
		'sys_language_uid' => [
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'language',
			],
		],
		'parentid' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		'parenttable' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		'listitem' => [
			'label' => 'List Item',
			'config' => [
				'type' => 'input',
			],
		],
	],

	'types' => [
		'0' => [
			'showitem' => 'listitem',
		],
	],

];