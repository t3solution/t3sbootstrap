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
	'types' => [
		'1' => ['showitem' => ''],
	],
	'columns' => [

		'updated' => [
			'exclude' => false,
			'label' => 'updated',
			'config' => [
				'type' => 'input'
			]
		],

		'gridupdated' => [
			'exclude' => false,
			'label' => 'gridupdated',
			'config' => [
				'type' => 'input'
			]
		],

		'homepage_uid' => [
			'exclude' => false,
			'label' => 'Homepage_uid',
			'config' => [
				'type' => 'input'
			]
		],


		'general_rootline' => [
			'exclude' => false,
			'label' => '"T3SB Config" on subpages',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Use configuration from rootline (slide) if enabled or from rootpage if disabled.
						<br /> If your installation only needs one configuration, this should be disabled!'
					]
				]
			]
		],
		'content_only_on_rootpage' => [
			'exclude' => false,
			'label' => 'Content Only On Rootpage',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'disable navbar, jumbotron, breadcrumb and footer on rootpage if enabled'
					]
				]
			]
		],
		'jquery_header' => [
			'exclude' => false,
			'label' => 'jQuery in header',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'load jquery into the header if activated'
					]
				]
			]
		],
		'compress' => [
			'exclude' => false,
			'label' => 'Compress',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'compress and concatenate JS and CSS - did not work with CDN'
					]
				]
			]
		],
		'disable_prefix_comment' => [
			'exclude' => false,
			'label' => 'Disable Prefix Comment',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'if set, the stdWrap property prefixComment will be disabled'
					]
				]
			]
		],
		'container_error' => [
			'exclude' => false,
			'label' => 'Container Error',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'shows an error message if a container is expected but no container has been selected'
					]
				]
			]
		],
		'body_extra_class' => [
			'exclude' => false,
			'label' => 'Body',
			'accordion_id' => 1,
			'accordion_sub' => '1-1',
			'info' => 'Extra Class for the body (body-tag) - e.g. bg-warning or any other classes',
			'config' => [
				'type' => 'input'
			]
		],
		'page_content_extra_class' => [
			'exclude' => false,
			'label' => 'Page Content',
			'accordion_id' => 1,
			'accordion_sub' => '1-1',
			'info' => 'Extra Class for the entire content (id="page-content") - e.g. bg-danger or any other classes',
			'config' => [
				'type' => 'input',
			]
		],
		'main_extra_class' => [
			'exclude' => false,
			'label' => 'Main',
			'accordion_id' => 1,
			'accordion_sub' => '1-1',
			'info' => 'Extra Class for the main content only (main-tag) - e.g. bg-info or any other classes',
			'config' => [
				'type' => 'input'
			]
		],
		'global_padding_top' => [
			'exclude' => false,
			'label' => 'Global Top Padding',
			'accordion_id' => 1,
			'accordion_sub' => '1-2',
			'info' => 'Extra Padding for colPos=0,1 & 2 (main- and aside-tag)',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				['Default', 'pt-5'],
				'items' => [
					['none', ''],
					['pt-1', 'pt-1'],
					['pt-2', 'pt-2'],
					['pt-3', 'pt-3'],
					['pt-4', 'pt-4'],
					['pt-5', 'pt-5'],
				],
			]
		],
		'content_margin_top' => [
			'exclude' => false,
			'label' => 'Content Element',
			'accordion_id' => 1,
			'accordion_sub' => '1-2',
			'info' => 'here you can set the default space (margin-top) for each content-element (colPos=0)',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['mt-1', 'mt-1'],
					['mt-2', 'mt-2'],
					['mt-3', 'mt-3'],
					['mt-4', 'mt-4'],
					['mt-5', 'mt-5'],
				],
			]
		],
		'loading_spinner' => [
			'exclude' => false,
			'label' => 'make your selection',
			'accordion_id' => 1,
			'accordion_sub' => '1-3',
			'info' => 'Bootstrap “spinners” can be used to show the loading state in your project',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Border spinner [border]', 'border'],
					['Growing spinner [grow]', 'grow'],
				],
			]
		],
		'loading_spinner_color' => [
			'exclude' => false,
			'label' => 'Loading Spinner Color',
			'accordion_id' => 1,
			'accordion_sub' => '1-3',
			'info' => 'By default the spinner is built with "currentColor", so you can easily change its appearance with text color utilities',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['current color', ''],
					['primary', 'primary'],
					['secondary', 'secondary'],
					['success', 'success'],
					['danger', 'danger'],
					['warning', 'warning'],
					['info', 'info'],
					['light', 'light'],
					['dark', 'dark'],
				],
			]
		],
		'lightbox_selection' => [
			'exclude' => false,
			'label' => 'make your selection',
			'accordion_id' => 1,
			'accordion_sub' => '1-4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', 0],
					['Baguettbox [1]', 1],
					['Ekkolightbox [2]', 2],
					['Lightcase [3]', 3],
				],
			]
		],
		'magnifying' => [
			'exclude' => false,
			'label' => 'Magnifying glass icon',
			'accordion_id' => 1,
			'accordion_sub' => '1-4',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'in the center of an image on hover'
					]
				]
			]
		],
		'sectionmenu_anchor_offset' => [
			'exclude' => false,
			'label' => 'Anchor extra offset (int)',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'info' => 'for Section-Menu-Items: in px - (default 29)',
			'config' => [
				'type' => 'input'
			]
		],
		'sectionmenu_scrollspy_offset' => [
			'exclude' => false,
			'label' => 'Scrollspy offset (int)',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'info' => 'in px - to activate the menu item - (default 130)',
			'config' => [
				'type' => 'input'
			]
		],
		'sectionmenu_sticky_top' => [
			'exclude' => false,
			'label' => 'Sticky Top',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'for #sectionmenu, .submenu or .make-me-sticky'
					]
				]
			]
		],
		'sidebar_section_mobile' => [
			'exclude' => false,
			'label' => 'Section mobile',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Shows the section menu also in the mobile if enabled'
					]
				]
			]
		],
		'background_image_enable' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 1,
			'accordion_sub' => '1-6',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'first image from pages media'
					]
				]
			]
		],
		'background_image_slide' => [
			'exclude' => false,
			'label' => 'Slide',
			'accordion_id' => 1,
			'accordion_sub' => '1-6',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'rootline sliding for the background image'
					]
				]
			]
		],
		'subheader_color' => [
			'exclude' => false,
			'label' => 'Subheader Color',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'info' => 'Bootstrap contextual text classes',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['current color', ''],
					['primary', 'primary'],
					['secondary', 'secondary'],
					['success', 'success'],
					['danger', 'danger'],
					['warning', 'warning'],
					['info', 'info'],
					['light', 'light'],
					['dark', 'dark'],
				],
			]
		],
		'date_format' => [
			'exclude' => false,
			'label' => 'Date Format',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'info' => 'the date format to use in ext:t3sbootstrap - default: d.m.Y',
			'config' => [
				'type' => 'input'
			]
		],
		'favicon' => [
			'exclude' => false,
			'label' => 'Favicon',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'info' => 'path to your favicon e.g.: EXT:t3sbootstrap/Resources/Public/Icons/favicon.ico',
			'config' => [
				'type' => 'input'
			]
		],
		'fa_link_icons' => [
			'exclude' => false,
			'label' => 'FA icons for RTE-Links',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'loads the required CSS-style for links set in the RTE or used global if activated'
					]
				]
			]
		],
		'card_flipper_on_Click' => [
			'exclude' => false,
			'label' => 'Card Flipper',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'rotate the cards on click (not on hover) if activated'
					]
				]
			]
		],
		'last_modified_content_element' => [
			'exclude' => false,
			'label' => 'Last Modified',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'display the date of the last modified content on current page in the footer'
					]
				]
			]
		],
		'recently_updated_content_elements' => [
			'exclude' => false,
			'label' => 'Updated Content Elements',
			'accordion_id' => 1,
			'accordion_sub' => '1-7',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'better solution in the Template MenuRecentlyUpdated.html if enabled'
					]
				]
			]
		],
		'meta_enable' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 2,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Left align [start]', 'start'],
					['Right align [end]', 'end'],
					['Nav-scroller (only left align) [scroller]', 'scroller'],
				]
			]
		],
		'meta_value' => [
			'exclude' => false,
			'label' => 'Value',
			'accordion_id' => 2,
			'info' => 'Comma-separated list of page ids.',
			'config' => [
				'type' => 'input'
			]
		],
		'meta_container' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 2,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'meta_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 2,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'meta_text' => [
			'exclude' => false,
			'label' => 'Text only',
			'accordion_id' => 2,
			'info' => 'e.g. e-mail address and phone number',
				'config' => [
				'type' => 'input'
			]
		],
		'navbar_enable' => [
			'exclude' => false,
			'label' => 'NavBar',
			'accordion_id' => 3,
			'info' => 'Choose from navbar-light for use with light background colors, or navbar-dark for dark background colors',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['navbar-dark [dark]', 'dark'],
					['navbar-light [light]', 'light'],
					['navbar-slide (no dropdown) [slide]', 'slide'],
				]
			]
		],
		'navbar_entrylevel' => [
			'exclude' => false,
			'label' => 'Entry Level (int)',
			'accordion_id' => 3,
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_levels' => [
			'exclude' => false,
			'label' => 'Levels (int)',
			'accordion_id' => 3,
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_excludeuiduist' => [
			'exclude' => false,
			'label' => 'Exclude',
			'accordion_id' => 3,
			'info' => 'Comma-separated list of page ids.',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_right_menu_uid_list' => [
			'exclude' => false,
			'label' => 'Right Menu',
			'accordion_id' => 3,
			'info' => 'Comma-separated list of uid`s (pages) for a right menu in the navbar.',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_sectionmenu' => [
			'exclude' => false,
			'label' => 'Sectionmenu',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Enable for "One Page Layout"'
					]
				]
			]
		],
		'navbar_megamenu' => [
			'exclude' => false,
			'label' => 'Megamenu',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Info: https://www.t3sbootstrap.de/demo/mega-menu/'
					]
				]
			]
		],
		'navbar_includespacer' => [
			'exclude' => false,
			'label' => 'Include Spacer',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Enable spacer in dropdown'
					]
				]
			]
		],
		'navbar_justify' => [
			'exclude' => false,
			'label' => 'Fill and justify',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Force your .nav’s contents to extend the full available width'
					]
				]
			]
		],
		'navbar_hover' => [
			'exclude' => false,
			'label' => 'Hover',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Open dropdown on hover'
					]
				]
			]
		],
		'navbar_clickableparent' => [
			'exclude' => false,
			'label' => 'Clickable parent',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Clickable parent if dropdown menu is open'
					]
				]
			]
		],
		'navbar_dropdown_animate' => [
			'exclude' => false,
			'label' => 'Dropdown animation',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none [0]', 0],
					['Slide In [1]', 1],
					['Slide Down (JS) [2]', 2],
					['Slide In [3]', 3],
					['Fade [4]', 4],
				]
			]
		],
		'navbar_extra_row' => [
			'exclude' => false,
			'label' => 'Extra Row',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Enable extra row(s) in the navbar - (fileadmin)/Resources/Private/Partials/Page/NavbarExtraRow.html'
					]
				]
			]
		],
		'navbar_brand' => [
			'exclude' => false,
			'label' => 'Options',
			'accordion_id' => 3,
			'accordion_sub' => '3-2',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['As a link [link]', 'link'],
					['As a heading [heading]', 'heading'],
					['Just an image [image]', 'image'],
					['Image and text [imgText]', 'imgText'],
				]
			]
		],
		'company' => [
			'exclude' => false,
			'label' => 'Text',
			'accordion_id' => 3,
			'accordion_sub' => '3-2',
			'info' => 'e.g. Company name (Multilingual Support with pipe "|")',
			'config' => [
				'type' => 'input'
			],
		],
		'navbar_image' => [
			'exclude' => false,
			'label' => 'Image',
			'accordion_id' => 3,
			'accordion_sub' => '3-2',
			'info' => 'Path to your image - Only if "Brand Options" is "Just an image" or "Image and text"',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_color' => [
			'exclude' => false,
			'label' => 'Color scheme',
			'accordion_id' => 3,
			'accordion_sub' => '3-3',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['bg-light [light]', 'light'],
					['bg-dark [dark]', 'dark'],
					['bg-primary [primary]', 'primary'],
					['bg-secondary [secondary]', 'secondary'],
					['bg-success [success]', 'success'],
					['bg-danger [danger]', 'danger'],
					['bg-warning [warning]', 'warning'],
					['bg-info [info]', 'info'],
					['bg-white [white]', 'white'],
					['bg-color [color]', 'color'],
				]
			]
		],
		'navbar_background' => [
			'exclude' => false,
			'label' => 'Background Color',
			'accordion_id' => 3,
			'accordion_sub' => '3-3',
			'info' => 'HTML-color - Color schemes "bg-color" must be activated',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_container' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', 'none'],
					['inside', 'inside'],
					['outside', 'outside'],
					['fluid', 'fluid'],
				]
			]
		],
		'navbar_placement' => [
			'exclude' => false,
			'label' => 'Placement',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['default', ''],
					['fixed-top', 'fixed-top'],
					['fixed-bottom', 'fixed-bottom'],
					['sticky-top (JS solution)', 'sticky-top'],
				]
			]
		],
		'navbar_alignment' => [
			'exclude' => false,
			'label' => 'Alignment',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['left', 'left'],
					['right', 'right'],
				]
			]
		],
		'navbar_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'info' => 'e.g. "mb-5" for margin-bottom: 3rem',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_height' => [
			'exclude' => false,
			'label' => 'NavBar Height (int)',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'info' => 'Is used as padding-top in the body tag - use only if NavBar is fixed-top (int+ px / default: "56")',
			'config' => [
				'type' => 'input'
			]
		],
		'navbar_searchbox' => [
			'exclude' => false,
			'label' => 'Searchbox',
			'accordion_id' => 3,
			'accordion_sub' => '3-4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['Form only [form]', 'form'],
					['Form & Button [button]', 'button'],
				]
			]
		],
		'navbar_shrinkcolor' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 3,
			'accordion_sub' => '3-5',
			'info' => 'Choose from navbar-light for use with light background colors, or navbar-dark for dark background colors',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['navbar-dark [dark]', 'dark'],
					['navbar-light [light]', 'light'],
				]
			]
		],
		'navbar_shrinkcolorschemes' => [
			'exclude' => false,
			'label' => 'Color schemes',
			'accordion_id' => 3,
			'accordion_sub' => '3-5',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['bg-light [light]', 'light'],
					['bg-dark [dark]', 'dark'],
					['bg-primary [primary]', 'primary'],
					['bg-secondary [secondary]', 'secondary'],
					['bg-success [success]', 'success'],
					['bg-danger [danger]', 'danger'],
					['bg-warning [warning]', 'warning'],
					['bg-info [info]', 'info'],
					['bg-white [white]', 'white'],
				]
			]
		],
		'shrinking_nav_padding' => [
			'exclude' => false,
			'label' => 'Padding (top & bottom)',
			'accordion_id' => 3,
			'accordion_sub' => '3-5',
			'info' => 'py-x can be set by your stylesheet',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['py-1', '1'],
					['py-2', '2'],
					['py-3', '3'],
					['py-4', '4'],
					['py-5', '5'],
					['py-x', 'x'],
				],
			]
		],
		'navbar_toggler' => [
			'exclude' => false,
			'label' => 'Toggler',
			'accordion_id' => 3,
			'accordion_sub' => '3-6',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['left', 'left'],
					['right', 'right'],
				]
			]
		],
		'navbar_breakpoint' => [
			'exclude' => false,
			'label' => 'Breakpoint',
			'accordion_id' => 3,
			'accordion_sub' => '3-6',
			'info' => 'Grouping and hiding navbar contents by a parent breakpoint',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Small (<= 576px) [sm]', 'sm'],
					['Medium (<= 768px [md])', 'md'],
					['Large (<= 992px) [lg]', 'lg'],
					['Extra large (<= 1200px) [xl]', 'xl'],
					['Never expand [no]', 'no'],
				]
			]
		],
		'navbar_offcanvas' => [
			'exclude' => false,
			'accordion_id' => 3,
			'accordion_sub' => '3-6',
			'label' => 'Offcanvas',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Change navbar collapse to offcanvas on mobile screen'
					]
				]
			]
		],
		'navbar_langmenu' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 3,
			'accordion_sub' => '3-7',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Setting is taken from the site configuration'
					]
				]
			]
		],
		'lang_menu_with_fa_icon' => [
			'exclude' => false,
			'label' => 'Style',
			'accordion_id' => 3,
			'accordion_sub' => '3-7',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Fontawesome icon (globe) or current language with flag if enabled'
					]
				]
			]
		],

		'navbar_lang_flags' => [
			'exclude' => false,
			'label' => 'Flags',
			'accordion_id' => 3,
			'accordion_sub' => '3-7',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Show flags in the language menu if enabled'
					]
				]
			]
		],



		'jumbotron_enable' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 4,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'jumbotron_bgimage' => [
			'exclude' => false,
			'label' => 'Background image',
			'accordion_id' => 4,
			'info' => 'Enable background image from pages media OR slider if more than 1 images',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['only on this page [page]', 'page'],
					['on this and all child pages (slide) [root]', 'root'],
				]
			]
		],
		'jumbotron_fluid' => [
			'exclude' => false,
			'label' => 'Fluid',
			'accordion_id' => 4,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'To make the jumbotron full width, and without rounded corners'
					]
				]
			]
		],
		'jumbotron_slide' => [
			'exclude' => false,
			'label' => 'Slide',
			'accordion_id' => 4,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Content of Jumbotron "slide" through the rootline'
					]
				]
			]
		],
		'jumbotron_position' => [
			'exclude' => false,
			'label' => 'Position',
			'accordion_id' => 4,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Above the NavBar [above]', 'above'],
					['Below the NavBar [below]', 'below'],
				]
			]
		],
		'jumbotron_container' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 4,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'jumbotron_containerposition' => [
			'exclude' => false,
			'label' => 'Container position',
			'accordion_id' => 4,
			'info' => 'If "Container" is not "none"',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside [Inside]', 'Inside'],
					['outside [Outside]', 'Outside'],
				]
			]
		],
		'jumbotron_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 4,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'jumbotron_carousel_interval' => [
			'exclude' => false,
			'label' => 'Interval',
			'accordion_id' => 4,
			'accordion_sub' => '4-1',
			'config' => [
				'type' => 'input'
			]
		],
		'jumbotron_carousel_pause' => [
			'exclude' => false,
			'label' => 'Pause',
			'accordion_id' => 4,
			'accordion_sub' => '4-1',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'page_title' => [
			'exclude' => false,
			'label' => 'Page title (h1)',
			'accordion_id' => 5,
			'info' => 'Image replacement: Replace h1 with the brand image (enable in Navbar). INFO: http://getbootstrap.com/docs/4.0/utilities/image-replacement/',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none (bad solution)', ''],
					['in the Jumbotron [jumbotron]', 'jumbotron'],
					['in the Main Content [content]', 'content'],
					['above the Breadcrumb [breadcrumb]', 'breadcrumb'],
					['Image replacement [replace]', 'replace'],
					['in the Expanded top content (if enabled) [expanded]', 'expanded'],
				]
			]
		],
		'page_titlealign' => [
			'exclude' => false,
			'label' => 'Alignment',
			'accordion_id' => 5,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['default', ''],
					['center', 'center'],
					['right', 'right'],
					['left', 'left'],
				]
			]
		],
		'page_titlecontainer' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 5,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'page_titleclass' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 5,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],


		'breadcrumb_enable' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 6,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'indicate the current page’s location within a navigational hierarchy'
					]
				]
			]
		],
		'breadcrumb_notonrootpage' => [
			'exclude' => false,
			'label' => 'Not on rootpage',
			'accordion_id' => 6,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Not on rootpage if enabled'
					]
				]
			]
		],
		'breadcrumb_faicon' => [
			'exclude' => false,
			'label' => 'Fontawesome icon',
			'accordion_id' => 6,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'FA icon instead of text for level=0 only if enabled'
					]
				]
			]
		],
		'breadcrumb_corner' => [
			'exclude' => false,
			'label' => 'No rounded corner',
			'accordion_id' => 6,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'To make the breadcrumb without rounded corners'
					]
				]
			]
		],
		'breadcrumb_bottom' => [
			'exclude' => false,
			'label' => 'Below the content',
			'accordion_id' => 6,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Show the breadcrumb menu below the content (only or also)'
					]
				]
			]
		],
		'breadcrumb_position' => [
			'exclude' => false,
			'label' => 'Breadcrumb position',
			'accordion_id' => 6,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['Above the NavBar [aboveNav]', 'aboveNav'],
					['Below the NavBar [belowNav]', 'belowNav'],
					['Above the Jumbotron [aboveJum]', 'aboveJum'],
					['Below the Jumbotron [belowJum]', 'belowJum'],
				]
			]
		],
		'breadcrumb_container' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 6,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'breadcrumb_containerposition' => [
				'exclude' => false,
				'label' => 'Container position',
				'accordion_id' => 6,
				'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside', 'inside'],
					['outside', 'outside'],
				]
			]
		],
		'breadcrumb_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 6,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'sidebar_enable' => [
			'exclude' => false,
			'label' => 'Enable in left Sidebar',
			'accordion_id' => 7,
			'info' => 'if sidebar is available',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['submenu [Sub]', 'Sub'],
					['sectionmenu [Section]', 'Section']
				]
			]
		],
		'sidebar_rightenable' => [
			'exclude' => false,
			'label' => 'Enable in right Sidebar',
			'accordion_id' => 7,
			'info' => 'if sidebar is available',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['submenu [Sub]', 'Sub'],
					['sectionmenu [Section]', 'Section']
				]
			]
		],
		'sidebar_entrylevel' => [
			'exclude' => false,
			'label' => 'Entry Level (int)',
			'accordion_id' => 7,
			'info' => '99 = Submenu of current page',
			'config' => [
				'type' => 'input'
			]
		],
		'sidebar_levels' => [
			'exclude' => false,
			'label' => 'Levels (int)',
			'accordion_id' => 7,
			'config' => [
				'type' => 'input'
			]
		],
		'sidebar_excludeuiduist' => [
			'exclude' => false,
			'label' => 'Exclude',
			'accordion_id' => 7,
			'info' => 'Comma-separated list of page ids.',
			'config' => [
				'type' => 'input'
			]
		],
		'sidebar_includespacer' => [
			'exclude' => false,
			'label' => 'Spacer',
			'accordion_id' => 7,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Enable spacer'
					]
				]
			]
		],
		'slide_left_aside' => [
			'exclude' => false,
			'label' => 'Slide left Sidebar',
			'accordion_id' => 7,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'content slide for colPos=1 if enabled'
					]
				]
			]
		],
		'slide_right_aside' => [
			'exclude' => false,
			'label' => 'Slide right Sidebar',
			'accordion_id' => 7,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'content slide for colPos=2 if enabled'
					]
				]
			]
		],
		'aside_extra_class' => [
			'exclude' => false,
			'label' => 'Extra Class',
			'accordion_id' => 7,
			'info' => 'e.g. bg-warning or any other classes',
			'config' => [
				'type' => 'input'
			]
		],
		'sidebar_menu_position' => [
			'exclude' => false,
			'label' => 'Menu Position',
			'accordion_id' => 7,
			'info' => 'above or below the content',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['above', 'above'],
					['below', 'below']
				],
			]
		],

		'expandedcontent_enabletop' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 8,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'expandedcontent_slidetop' => [
			'exclude' => false,
			'label' => 'Content slide',
			'accordion_id' => 8,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'expandedcontent_containertop' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 8,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'expandedcontent_containerpositiontop' => [
			'exclude' => false,
			'label' => 'Container position',
			'accordion_id' => 8,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside [Inside]', 'Inside'],
					['outside [Outside]', 'Outside'],
				]
			]
		],
		'expandedcontent_classtop' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 8,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'expandedcontent_enablebottom' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 9,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'expandedcontent_slidebottom' => [
			'exclude' => false,
			'label' => 'Content slide',
			'accordion_id' => 9,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Content of Expanded Content Bottom "slide" through the rootline'
					]
				]
			]
		],
		'expandedcontent_containerbottom' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 9,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['container', 'container'],
					['container-fluid', 'container-fluid'],
					['container-sm','container-sm'],
					['container-md','container-md'],
					['container-lg','container-lg'],
					['container-xl','container-xl'],
				]
			]
		],
		'expandedcontent_containerpositionbottom' => [
			'exclude' => false,
			'label' => 'Container position',
			'accordion_id' => 9,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside [Inside]', 'Inside'],
					['outside [Outside]', 'Outside'],
				]
			]
		],
		'expandedcontent_classbottom' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 9,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'footer_enable' => [
			'exclude' => false,
			'label' => 'Enable',
			'accordion_id' => 10,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'footer_sticky' => [
			'exclude' => false,
			'label' => 'Sticky Footer',
			'accordion_id' => 10,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'footer_fluid' => [
			'exclude' => false,
			'label' => 'Fluid',
			'accordion_id' => 10,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'To make the footer full width, and without rounded corners'
					]
				]
			]
		],
		'footer_slide' => [
			'exclude' => false,
			'label' => 'Slide',
			'accordion_id' => 10,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Content of Footer "slide" through the rootline'
					]
				]
			]
		],
		'footer_container' => [
			'exclude' => false,
			'label' => 'Container',
			'accordion_id' => 10,
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['container', 'container'],
						['container-fluid', 'container-fluid'],
						['container-sm','container-sm'],
						['container-md','container-md'],
						['container-lg','container-lg'],
						['container-xl','container-xl'],
				]
			]
		],
		'footer_containerposition' => [
			'exclude' => false,
			'label' => 'Container position',
			'accordion_id' => 10,
			'info' => 'If "Container" is not "none"',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['inside [Inside]', 'Inside'],
					['outside [Outside]', 'Outside'],
				]
			]
		],
		'footer_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 10,
			'info' => 'e.g. "mb-0" for margin-bottom: 0',
			'config' => [
				'type' => 'input'
			]
		],
		'footer_pid' => [
			'exclude' => false,
			'accordion_id' => 10,
			'info' => 'Page uid (int) for the footer content (colPos= 0)',
			'label' => 'Content (int)',
			'config' => [
				'type' => 'input'
			]
		],
		'sticky_footer_extra_padding' => [
			'exclude' => false,
			'label' => 'Extra padding',
			'accordion_id' => 10,
			'info' => 'if "footer-sticky" is activated, the padding-bottom for the body is given by JS.
			 If you like an extra space between the footer and the content, you can do it here (in px)',
			'config' => [
				'type' => 'input'
			]
		],
	],
];
