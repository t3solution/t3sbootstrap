<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_db.xlf:tx_t3sbootstrap_domain_model_config',
		'label' => 'homepage_uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'enablecolumns' => [],
		'hideTable' => 1,
		'searchFields' => '',
		'iconfile' => 'EXT:t3sbootstrap/Resources/Public/Icons/tx_t3sbootstrap_domain_model_config.gif'
	],
	'interface' => [
		'showRecordFieldList' => '',
	],
	'types' => [
		'1' => ['showitem' => ''],
	],
	'columns' => [

		'company' => [
			'exclude' => false,
			'label' => 'Company',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'homepage_uid' => [
			'exclude' => false,
			'label' => 'Homepage_uid',
			'config' => [
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			]
		],
		'page_title' => [
			'exclude' => false,
			'label' => 'Page title (h1)',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none (bad solution)', ''],
					['in the Jumbotron', 'jumbotron'],
					['in the Main Content', 'content'],
					['above the Breadcrumb', 'breadcrumb'],
					['Image replacement', 'replace'],
					['in the Expanded top content (if enabled)', 'expanded'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'page_titlealign' => [
			'exclude' => false,
			'label' => 'page_titlealign',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Default', ''],
					['Center', 'center'],
					['Right', 'right'],
					['Left', 'left'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'page_titlecontainer' => [
			'exclude' => false,
			'label' => 'Page Title Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'page_titleclass' => [
			'exclude' => false,
				'label' => 'Extra class',
				'config' => [
				'type' => 'input',
				'size' => 20
				]
		],
		'meta_enable' => [
			'exclude' => false,
			'label' => 'Enable Meta-Navigation',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Left align', 'start'],
					['Right align', 'end'],
					['Nav-scroller (only left align)', 'scroller'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'meta_value' => [
			'exclude' => false,
			'label' => 'Navigation Value',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			]
		],
		'meta_container' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'meta_class' => [
			'exclude' => false,
				'label' => 'Extra class',
				'config' => [
				'type' => 'input',
				'size' => 20
				]
		],
		'meta_text' => [
			'exclude' => false,
				'label' => 'Text only',
				'config' => [
				'type' => 'input',
				'size' => 20
				]
		],
		'navbar_enable' => [
			'exclude' => false,
			'label' => 'navbar_enable',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['navbar-dark', 'dark'],
					['navbar-light', 'light'],
					['navbar-slide (no dropdown)', 'slide'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_entrylevel' => [
			'exclude' => false,
			'label' => 'NavBar navbar_entryLevel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],
		'navbar_levels' => [
			'exclude' => false,
			'label' => 'NavBar Levels',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],
		'navbar_excludeuiduist' => [
			'exclude' => false,
			'label' => 'navbar excludeUidList',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],
		'navbar_includespacer' => [
			'exclude' => false,
			'label' => 'navbar includeSpacer',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_sectionmenu' => [
			'exclude' => false,
			'label' => 'Section Menu',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_justify' => [
			'exclude' => false,
			'label' => 'Fill and justify',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_megamenu' => [
			'exclude' => false,
			'label' => 'Mega Menu',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_hover' => [
			'exclude' => false,
			'label' => 'navbar_hover',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_clickableparent' => [
			'exclude' => false,
			'label' => 'navbar clickable parent',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_brand' => [
			'exclude' => false,
			'label' => 'Brand',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['As a link', 'link'],
					['As a heading', 'heading'],
					['Just an image', 'image'],
					['Image and text', 'imgText'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_image' => [
			'exclude' => false,
			'label' => 'Image',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			]
		],
		'navbar_color' => [
			'exclude' => false,
			'label' => 'Color schemes',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['bg-light', 'light'],
					['bg-dark', 'dark'],
					['bg-primary', 'primary'],
					['bg-secondary', 'secondary'],
					['bg-success', 'success'],
					['bg-danger', 'danger'],
					['bg-warning', 'warning'],
					['bg-info', 'info'],
					['bg-white', 'white'],
					['bg-color', 'color'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_background' => [
			'exclude' => false,
			'label' => 'Background color',
			'config' => [
				'type' => 'input',
				'renderType' => 'colorpicker',
				'size' => 10
			]
		],


		'navbar_shrinkcolor' => [
			'exclude' => false,
			'label' => 'Enable',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['navbar-dark', 'dark'],
					['navbar-light', 'light'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_shrinkcolorschemes' => [
			'exclude' => false,
			'label' => 'Shrink Color schemes',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['bg-light', 'light'],
					['bg-dark', 'dark'],
					['bg-primary', 'primary'],
					['bg-secondary', 'secondary'],
					['bg-success', 'success'],
					['bg-danger', 'danger'],
					['bg-warning', 'warning'],
					['bg-info', 'info'],
					['bg-white', 'white'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],


		'navbar_container' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', 'none'],
					['inside', 'inside'],
					['outside', 'outside'],
					['fluid', 'fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_placement' => [
			'exclude' => false,
			'label' => 'Placement',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['default', ''],
					['fixed-top', 'fixed-top'],
					['fixed-bottom', 'fixed-bottom'],
					['sticky-top (JS solution)', 'sticky-top'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_alignment' => [
			'exclude' => false,
			'label' => 'Alignment',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['left', 'left'],
					['right', 'right'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_toggler' => [
			'exclude' => false,
			'label' => 'Toggler',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['left', 'left'],
					['right', 'right'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],
		'navbar_breakpoint' => [
			'exclude' => false,
			'label' => 'Breakpoint',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Small (<= 576px)', 'sm'],
					['Medium (<= 768px)', 'md'],
					['Large (<= 992px)', 'lg'],
					['Extra large (<= 1200px)', 'xl'],
					['Never expand', 'no'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_offcanvas' => [
			'exclude' => false,
			'label' => 'Offcanvas',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'navbar_height' => [
			'exclude' => false,
			'label' => 'NavBar Height',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],
		'navbar_searchbox' => [
			'exclude' => false,
			'label' => 'Searchbox',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Form only', 'form'],
					['Form & Button', 'button'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'navbar_langmenu' => [
			'exclude' => false,
			'label' => 'Navbar Langmenu',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'jumbotron_enable' => [
			'exclude' => false,
			'label' => 'Jumbotron_enable',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'jumbotron_bgimage' => [
			'exclude' => false,
			'label' => 'Background image',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['only on this page', 'page'],
					['on this and all child pages (slide)', 'root'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'jumbotron_fluid' => [
			'exclude' => false,
			'label' => 'Jumbotron Fluid',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'enable'
					]
				]
			]
		],
		'jumbotron_slide' => [
			'exclude' => false,
			'label' => 'Jumbotron slide content',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'jumbotron_position' => [
			'exclude' => false,
			'label' => 'Jumbotron position',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Above the NavBar', 'above'],
					['Below the NavBar', 'below'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'jumbotron_container' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'jumbotron_containerposition' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'Inside'],
					['outside', 'Outside'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'jumbotron_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],

		'breadcrumb_enable' => [
			'exclude' => false,
			'label' => 'Breadcrumb_enable',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'breadcrumb_notonrootpage' => [
			'exclude' => false,
			'label' => 'breadcrumb_notOnRootpage',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'breadcrumb_faicon' => [
			'exclude' => false,
			'label' => 'fa_icon',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'breadcrumb_corner' => [
				'exclude' => false,
				'label' => 'Breadcrumb Corner',
				'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'enable'
					]
				]
			]
		],
		'breadcrumb_bottom' => [
				'exclude' => false,
				'label' => 'Breadcrumb bottom',
				'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'enable'
					]
				]
			]
		],
		'breadcrumb_position' => [
			'exclude' => false,
			'label' => 'Breadcrumb position',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Above the NavBar', 'aboveNav'],
					['Below the NavBar', 'belowNav'],
					['Above the Jumbotron', 'aboveJum'],
					['Below the Jumbotron', 'belowJum'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'breadcrumb_container' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'breadcrumb_containerposition' => [
				'exclude' => false,
				'label' => 'Container position',
				'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'inside'],
					['outside', 'outside'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'breadcrumb_class' => [
			'exclude' => false,
				'label' => 'Extra class',
				'config' => [
				'type' => 'input',
				'size' => 20
				]
		],
		'sidebar_enable' => [
			'exclude' => false,
			'label' => 'enable sidebar',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Submenu', 'Sub'],
					['Sectionmenu', 'Section']
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'sidebar_rightenable' => [
			'exclude' => false,
			'label' => 'enable sidebar right',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Submenu', 'Sub'],
					['Sectionmenu', 'Section']
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'sidebar_entrylevel' => [
			'exclude' => false,
			'label' => 'sidebar_entrylevel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],
		'sidebar_levels' => [
			'exclude' => false,
			'label' => 'sidebar Levels',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],
		'sidebar_excludeuiduist' => [
			'exclude' => false,
				'label' => 'sidebar excludeUidList',
				'config' => [
				'type' => 'input',
				'size' => 20
				]
		],
		'sidebar_includespacer' => [
			'exclude' => false,
			'label' => 'sidebar includeSpacer',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'footer_enable' => [
			'exclude' => false,
			'label' => 'Footer_enable',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'footer_fluid' => [
			'exclude' => false,
			'label' => 'Footer Fluid',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'enable'
					]
				]
			]
		],
		'footer_slide' => [
			'exclude' => false,
			'label' => 'Footer slide content',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'footer_sticky' => [
			'exclude' => false,
			'label' => 'Footer Fluid',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'enable'
					]
				]
			]
		],
		'footer_container' => [
			'exclude' => false,
			'label' => 'Container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['container', 'container'],
						['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'footer_containerposition' => [
			'exclude' => false,
			'label' => 'Container position',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'Inside'],
					['outside', 'Outside'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'footer_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],
		'footer_pid' => [
			'exclude' => false,
			'label' => 'footer_pid',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int,trim'
			]
		],

		'expandedcontent_enabletop' => [
			'exclude' => false,
			'label' => 'Enable top expanded content',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'expandedcontent_slidetop' => [
			'exclude' => false,
			'label' => 'Content slide top',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'expandedcontent_containerpositiontop' => [
			'exclude' => false,
			'label' => 'Top container position',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'Inside'],
					['outside', 'Outside'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'expandedcontent_containertop' => [
			'exclude' => false,
			'label' => 'Top container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'expandedcontent_classtop' => [
			'exclude' => false,
			'label' => 'Top extra class',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],
		'expandedcontent_enablebottom' => [
			'exclude' => false,
			'label' => 'Disable bottom expanded content',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'expandedcontent_slidebottom' => [
			'exclude' => false,
			'label' => 'Content slide bottom',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
					]
				]
			]
		],
		'expandedcontent_containerpositionbottom' => [
			'exclude' => false,
			'label' => 'Bottom container position',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'Inside'],
					['outside', 'Outside'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'expandedcontent_containerbottom' => [
			'exclude' => false,
			'label' => 'Bottom container',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
				],
				'size' => 1,
				'maxitems' => 1
			]
		],
		'expandedcontent_classbottom' => [
			'exclude' => false,
			'label' => 'Bottom extra class',
			'config' => [
				'type' => 'input',
				'size' => 20
			]
		],

	],
];
