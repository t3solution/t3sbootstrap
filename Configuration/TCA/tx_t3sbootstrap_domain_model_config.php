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
		'general_override' => [
			'exclude' => false,
			'label' => 'Override all settings',
			'accordion_id' => 1,
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'The configuration from rootline (slide) if enabled or from rootpage will be overwritten.
						<br /> This option is only needed in rare cases on subpages only!'
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
					['Halkabox [2]', 2],
					['GLightbox [3]', 3],
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
			'info' => 'for Section-Menu-Items (also for "OnePageLayout"): in px - (default 29)',
			'config' => [
				'type' => 'input'
			]
		],
		'sectionmenu_scrollspy_threshold' => [
			'exclude' => false,
			'label' => 'Scrollspy threshold (string)',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'info' => 'https://developer.mozilla.org/en-US/docs/Web/API/IntersectionObserver/IntersectionObserver#threshold - (default: 0.1, 0.5, 1)',
			'config' => [
				'type' => 'input'
			]
		],	
		'sectionmenu_scrollspy_root_margin' => [
			'exclude' => false,
			'label' => 'Scrollspy rootMargin (string)',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'info' => 'https://developer.mozilla.org/en-US/docs/Web/API/IntersectionObserver/rootMargin - (default: 0px 0px -25%)',
			'config' => [
				'type' => 'input'
			]
		],
		'sectionmenu_scrollspy' => [
			'exclude' => false,
			'label' => 'Scrollspy',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'activate/deaktivate scrollspy'
					]
				]
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
		'sectionmenu_icons' => [
			'exclude' => false,
			'label' => 'FA icons',
			'accordion_id' => 1,
			'accordion_sub' => '1-5',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'Shows FA icons in section menu'
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
				]
			]
		],
		'meta_class' => [
			'exclude' => false,
			'label' => 'Extra class',
			'accordion_id' => 2,
			'info' => 'e.g. text-white text-shadow bg-primary',
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
		'navbar_dark_mode' => [
			'exclude' => false,
			'label' => 'Color mode toggler',
			'info' => 'To allow visitors or users to toggle color modes.',
			'accordion_id' => 3,
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Enable as right menu dropdown'
					]
				]
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
		'navbar_plusicon' => [
			'exclude' => false,
			'label' => 'Plus icon for dropdown',
			'accordion_id' => 3,
			'accordion_sub' => '3-1',
			'info' => 'Hover is disabled by default if activated!',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Extra plus icon to open dropdown'
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
					['Fade [2]', 2],
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
		'navbarbrand_alignment' => [
			'exclude' => false,
			'label' => 'Alignment',
			'accordion_id' => 3,
			'accordion_sub' => '3-2',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['left', 'left'],
					['right', 'right'],
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
					['bg-light', 'light'],
					['bg-light bg-gradient', 'light bg-gradient'],
					['bg-dark', 'dark'],
					['bg-dark bg-gradient', 'dark bg-gradient'],
					['bg-primary', 'primary'],
					['bg-primary bg-gradient', 'primary bg-gradient'],
					['bg-secondary', 'secondary'],
					['bg-secondary bg-gradient', 'secondary bg-gradient'],
					['bg-success', 'success'],
					['bg-success bg-gradient', 'success bg-gradient'],
					['bg-danger', 'danger'],
					['bg-danger bg-gradient', 'danger bg-gradient'],
					['bg-warning', 'warning'],
					['bg-warning bg-gradient', 'warning bg-gradient'],
					['bg-info', 'info'],
					['bg-info bg-gradient', 'info'],
					['bg-white', 'white'],
					['bg-body', 'body'],
					['bg-transparent', 'transparent'],
					['bg-color', 'color'],
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
		'navbar_transparent' => [
			'exclude' => false,
			'label' => 'Transparent Navbar',
			'accordion_id' => 3,
			'accordion_sub' => '3-3',
			'info' => 'Placement must be "fixed-top"',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'create a transparent navbar which changes its style on scroll'
					]
				]
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
					['none', ''],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
				]
			]
		],
		'navbar_innercontainer' => [
			'exclude' => false,
			'label' => 'Inner-Container',

					'accordion_id' => 3,
					'accordion_sub' => '3-4',

			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
				],
				'size' => 1,
				'maxitems' => 1
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
					['sticky-top', 'sticky-top'],
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
					['center', 'center'],
					['fill (every nav item will be the same width)', 'fill'],
					['justified (all horizontal space will be occupied by nav links)', 'justified'],
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
					['bg-light', 'light'],
					['bg-light bg-gradient', 'light bg-gradient'],
					['bg-dark', 'dark'],
					['bg-dark bg-gradient', 'dark bg-gradient'],
					['bg-primary', 'primary'],
					['bg-primary bg-gradient', 'primary bg-gradient'],
					['bg-secondary', 'secondary'],
					['bg-secondary bg-gradient', 'secondary bg-gradient'],
					['bg-success', 'success'],
					['bg-success bg-gradient', 'success bg-gradient'],
					['bg-danger', 'danger'],
					['bg-danger bg-gradient', 'danger bg-gradient'],
					['bg-warning', 'warning'],
					['bg-warning bg-gradient', 'warning bg-gradient'],
					['bg-info', 'info'],
					['bg-info bg-gradient', 'info'],
					['bg-white', 'white'],
					['bg-body', 'body'],
					['bg-transparent', 'transparent'],
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
		'navbar_animatedtoggler' => [
			'exclude' => false,
			'accordion_id' => 3,
			'accordion_sub' => '3-6',
			'label' => 'Animated Toggler',
			'config' => [
				'type' => 'check',
					'items' => [
					'1' => [
						'0' => 'Doing it with plain HTML and pure CSS - does not work with "Offcanvas"'
					]
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
					['Extra extra large (<= 1400px) [xxl]', 'xxl'],
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
			'info' => 'Enable background image from pages media OR slider if more than 1 image.',
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
		'jumbotron_bgimageratio' => [
			'exclude' => false,
			'label' => 'Background image ratio',
			'accordion_id' => 4,
			'info' => 'Only to be used with a background image - not with videos.',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['67:9', '67x9'],
					['56:9', '56x9'],
					['46:9', '46x9'],
					['37:9', '37x9'],
					['29:9', '29x9'],
					['21:9', '21x9'],
				]
			]
		],
		'jumbotron_alignitem' => [
			'exclude' => false,
			'label' => 'Align content items',
			'accordion_id' => 4,
			'info' => 'Vertical align for the content',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', ''],
					['start', 'start'],
					['end', 'end'],
					['center', 'center'],
					['baseline', 'baseline'],
					['stretch', 'stretch'],
				]
			]
		],
		'jumbotron_rounded' => [
			'exclude' => false,
			'label' => 'Rounded corner',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['none', '0'],
					['rounded', 'rounded'],
					['rounded-top', 'rounded-top'],
					['rounded-bottom', 'rounded-bottom'],
					['rounded-pill', 'rounded-pill'],
				],
				'size' => 1,
				'maxitems' => 1
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']

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
		'submenu_sticky' => [
			'exclude' => false,
			'label' => 'Sticky top',
			'accordion_id' => 7,
			'info' => 'If using sectionmenu check settings under "General Settings"',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'position the submenu at the top of the viewport'
					]
				]
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
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
					['none', 'none'],
					['container', 'container'],
					['container-sm (< 576px)', 'container-sm'],
					['container-md (≥ 576px)', 'container-md'],
					['container-lg (≥ 768px)', 'container-lg'],
					['container-xl (≥ 992px)', 'container-xl'],
					['container-xxl (≥ 1200px)', 'container-xxl'],
					['container-fluid (≥ 1400px)', 'container-fluid']
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
		'cmi_enabled' => [
			'exclude' => false,
			'label' => 'Enable cmi5',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Check to enable cmi5',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => ''
					]
				]
			]
		],
		'cmi_objprop_lang' => [
			'exclude' => false,
			'label' => 'Language',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Select Language of AU',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['de-DE', 'de-DE'],
						['en-EN', 'en-EN'],
						['fr-FR', 'fr-FR'],
						['es-ES', 'es-Es']
				]
			]
		],
		'cmi_datamodel_au_id' => [
			'exclude' => false,
			'label' => 'ID',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit AU ID (IRI Format)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_acttype' => [
			'exclude' => false,
			'label' => 'Activity Type',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Select Activity Type of AU',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['http://adlnet.gov/expapi/activities/course', 'http://adlnet.gov/expapi/activities/course'],
						['http://adlnet.gov/expapi/activities/assessment', 'http://adlnet.gov/expapi/activities/assessment'],
						['http://adlnet.gov/expapi/activities/module', 'http://adlnet.gov/expapi/activities/module'],
						['http://activitystrea.ms/schema/1.0/task', 'http://activitystrea.ms/schema/1.0/task']
				]
			]
		],
		'cmi_datamodel_au_title' => [
			'exclude' => false,
			'label' => 'Title',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Title of AU',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_descr' => [
			'exclude' => false,
			'label' => 'Description',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Description of AU (optional)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_objectives' => [
			'exclude' => false,
			'label' => 'Objectives',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Objectives of AU (optional)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_launchmethod' => [
			'exclude' => false,
			'label' => 'Launch Method',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Select Launch Method of AU',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['Own Window', 'OwnWindow'],
						['Any Window', 'AnyWindow']
				]
			]
		],
		'cmi_datamodel_au_moveon' => [
			'exclude' => false,
			'label' => 'MoveOn Criteria',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Select moveOn Criteria of AU',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
						['none', ''],
						['Passed', 'Passed'],
						['Completed', 'Completed'],
						['Completed and Passed', 'CompletedAndPassed'],
						['Completed or Passed', 'CompletedOrPassed'],
						['Not applicable', 'NotApplicable']
				]
			]
		],
		'cmi_datamodel_au_masteryscore' => [
			'exclude' => false,
			'label' => 'Mastery Score',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Mastery Score, for example 0.75 (75%)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_url' => [
			'exclude' => false,
			'label' => 'Link (URL)',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Link to AU as URL',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_launchparms' => [
			'exclude' => false,
			'label' => 'Launch Parameters',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Launch Parameters of AU (optional)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_au_entitlementkey' => [
			'exclude' => false,
			'label' => 'Entitlement Key',
			'accordion_id' => 11,
			'accordion_sub' => '11-1',
			'info' => 'Edit Entitlement Key of AU (optional)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_course_id' => [
			'exclude' => false,
			'label' => 'Course ID',
			'accordion_id' => 11,
			'accordion_sub' => '11-2',
			'info' => 'Edit Course ID (IRI Format)',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_course_title' => [
			'exclude' => false,
			'label' => 'Course Title',
			'accordion_id' => 11,
			'accordion_sub' => '11-2',
			'info' => 'Edit Title of Course',
			'config' => [
				'type' => 'input'
			]
		],
		'cmi_datamodel_course_descr' => [
			'exclude' => false,
			'label' => 'Course Description',
			'accordion_id' => 11,
			'accordion_sub' => '11-2',
			'info' => 'Edit Description of Course (optional)',
			'config' => [
				'type' => 'input'
			]
		],
	],
];
