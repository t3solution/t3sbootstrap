<?php

return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:t3sbootstrap/Resources/Private/Language/locallang_db.xlf:tx_t3sbootstrap_domain_model_config',
        'label' => '',
        'label_alt' => 'company,homepage_uid',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'enablecolumns' => [],
        'type' => 'record_type',
        'hideTable' => true,
        'typeicon_classes' => [
            'default' => 'bootstraplogo',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        'general' => [
            'title' =>  'T3S Bootstrap Configuration', 
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;generalSettings,
            --div--;Various Extra Classes,
                --palette--;;variousExtraClasses,
            --div--;Various Margin & Padding,
                --palette--;;variousMarginPadding,
            --div--;Loading Spinner,
                --palette--;;loadingSpinner,
            --div--;Lightbox Settings,
                --palette--;;lightboxSettings,
            --div--;Section Menu,
                --palette--;;sectionMenuSettings,
            --div--;Background Image,
                --palette--;;backgroundImage,
            --div--;Other Settings,
                --palette--;;otherSettings,
        '],
        'meta' => [
            'title' =>  'Meta Navigation',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;metaNavigation',
        ],
        'navbar' => [
            'title' =>  'Navbar',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;navbar,
            --div--;Various Settings,
                --palette--;;variousSettings,
            --div--;Brand,
                --palette--;;brand,
            --div--; Background Color,
                --palette--;;backgroundColor,
            --div--;Layout/Placement,
                --palette--;;layoutPlacement,
            --div--;Shrinking Navbar,
                --palette--;;shrinkingNavbar,
            --div--;Responsive behaviors,
                --palette--;;responsiveBehaviors,
            --div--; Language Menu,
                --palette--;;languageMenu,',
        ],
        'jumbo' => [
            'title' =>  'Jumbotron',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;jumbotron,
            --div--; Background,
                --palette--;;background,
                --palette--;;backgroundcarousel,',
        ],
        'ptitle' => [
            'title' =>  'Page Title',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;pageTitle,',
        ],
        'breadcrumb' => [
            'title' =>  'Breadcrumb',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;breadcrumb,',
        ],
        'sidebar' => [
            'title' =>  'Sidebar',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;sidebar,',
        ],
        'extra' => [
            'title' =>  'Expanded Content',
            'showitem' => '
                record_type,--linebreak--,
                expandedcontent_enabletop,--linebreak--,
                --palette--;;expandedContentTop,
                --palette--;;expandedContentBottom,',
        ],
        'footer' => [
            'title' =>  'Footer',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;footer,',
        ],
        'scss' => [
            'title' =>  'Custom SCSS',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;customScss,',
        ],
        // FALLBACK: Essential to prevent "No fields to edit" if DB value is empty
        '0' => [
            'title' =>  'T3S Bootstrap Configuration', 
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;generalSettings,
            --div--;Various Extra Classes,
                --palette--;;variousExtraClasses,
            --div--;Various Margin & Padding,
                --palette--;;variousMarginPadding,
            --div--;Loading Spinner,
                --palette--;;loadingSpinner,
            --div--;Lightbox Settings,
                --palette--;;lightboxSettings,
            --div--;Section Menu,
                --palette--;;sectionMenuSettings,
            --div--;Background Image,
                --palette--;;backgroundImage,
            --div--;Other Settings,
                --palette--;;otherSettings,
        '],
    ],

    'palettes' => [
        'generalSettings' => [
            'label' => 'General Settings',
            'showitem' => '
                content_only_on_rootpage,--linebreak--,
                disable_prefix_comment
            ',
        ],
        'variousExtraClasses' => [
            'label' => 'Various Extra Classes',
            'description' => 'Here you can apply any CSS "Extra Class" you like - e.g. bg-warning mt-4 or any other classes',
            'showitem' => '
                body_extra_class,--linebreak--,
                page_content_extra_class,--linebreak--,
                main_extra_class,--linebreak--,
                page_wrapper_extra_class,
            ',
        ],
        'variousMarginPadding' => [
            'label' => 'Various Margin and Padding',
            'showitem' => '
                global_padding_top,--linebreak--,
                content_margin_top,
            ',
        ],
        'loadingSpinner' => [
            'label' => 'Loading Spinner',
            'description' => 'Bootstrap “spinners” can be used to show the loading state in your project.
            By default the spinner is built with "currentColor",so you can easily change its appearance with text color utilities',
            'showitem' => '
                loading_spinner,loading_spinner_color,
            ',
        ],
        'lightboxSettings' => [
            'label' => 'Lightbox Settings',
            'showitem' => '
                lightbox_selection,--linebreak--,
                magnifying,
            ',
        ],
        'sectionMenuSettings' => [
            'label' => 'Section Menu Settings',
            'description' => 'INFO:
                https://developer.mozilla.org/en-US/docs/Web/API/IntersectionObserver/IntersectionObserver#threshold
                https://developer.mozilla.org/en-US/docs/Web/API/IntersectionObserver/rootMargin',
            'showitem' => '
                sectionmenu_anchor_offset,sectionmenu_scrollspy_threshold,sectionmenu_scrollspy_root_margin,--linebreak--,
                sectionmenu_scrollspy,sectionmenu_icons,--linebreak--,
                sectionmenu_sticky_top,sidebar_section_mobile,
            ',
        ],
        'backgroundImage' => [
            'label' => 'Background Image',
            'showitem' => '
                background_image_enable,background_image_slide,
            ',
        ],
        'otherSettings' => [
            'label' => 'Other Settings',
            'showitem' => '
                subheader_color,date_format,favicon,--linebreak--,
                card_flipper_on_Click,--linebreak--,
                last_modified_content_element,--linebreak--,
                recently_updated_content_elements,
            ',
        ],
        'metaNavigation' => [
            'label' => 'Meta Navigation',
            'showitem' => '
                meta_enable,meta_container,--linebreak--,
                meta_value,meta_class,--linebreak--,
                meta_text,
            ',
        ],
        'navbar' => [
            'label' => 'Navbar',
            'showitem' => '
                navbar_enable,--linebreak--,
                navbar_entrylevel,navbar_levels,--linebreak--,
                navbar_excludeuiduist,--linebreak--,
                navbar_right_menu_uid_list,--linebreak--,
                navbar_dark_mode,
            ',
        ],
        'variousSettings' => [
            'label' => 'Various Settings',
            'showitem' => '
                navbar_sectionmenu,navbar_megamenu,--linebreak--,
                navbar_includespacer,navbar_hover,--linebreak--,
                navbar_extra_row,navbar_clickableparent,--linebreak--,
                navbar_plusicon,navbar_dropdown_animate,
            ',
        ],
        'brand' => [
            'label' => 'Brand',
            'description' => 'INFO: https://getbootstrap.com/docs/5.3/components/navbar/#brand',
            'showitem' => '
                navbar_brand,--linebreak--,
                navbarbrand_alignment,--linebreak--,
                company,--linebreak--,
                navbar_image,
            ',
        ],
        'backgroundColor' => [
            'label' => 'Background Color',
            'description' => 'INFO: https://getbootstrap.com/docs/5.3/components/navbar/#color-schemes',
            'showitem' => '
                navbar_color,--linebreak--,
                navbar_background,--linebreak--,
                navbar_transparent,
            ',
        ],
        'layoutPlacement' => [
            'label' => 'Layout/Placement',
            'description' => 'INFO: https://getbootstrap.com/docs/5.3/components/navbar/#placement',
            'showitem' => '
                navbar_container,navbar_innercontainer,--linebreak--,
                navbar_placement,navbar_alignment,--linebreak--,
                navbar_class,--linebreak--,
                navbar_height,--linebreak--,
                navbar_searchbox,
            ',
        ],
        'shrinkingNavbar' => [
            'label' => 'Shrinking Navbar',
            'description' => 'Shrinking Navbar on scrolling: Set "Placement" to "fixed-top".
            Set "Navbar height" must be adapted for body-padding.
            Does not work with "Background color" only with "Color schemes".
            Transparency: "Color schemes" = "bg-color" and "Background color" & "Navbar height" without entry.',
            'showitem' => '
                navbar_shrinkcolor,--linebreak--,
                shrinking_nav_padding,--linebreak--,
                navbar_shrinkcolorschemes,
            ',
        ],
        'responsiveBehaviors' => [
            'label' => 'Responsive behaviors',
            'description' => 'INFO: https://getbootstrap.com/docs/5.3/components/navbar/#responsive-behaviors',
            'showitem' => '
                navbar_toggler,--linebreak--,
                navbar_breakpoint,--linebreak--,
                navbar_animatedtoggler,navbar_offcanvas,
            ',
        ],
        'languageMenu' => [
            'label' => 'Language Menu',
            'showitem' => '
                navbar_langmenu,--linebreak--,
                lang_menu_with_fa_icon,--linebreak--,
                navbar_lang_flags,
            ',
        ],
        'jumbotron' => [
            'label' => 'Jumbotron',
            'showitem' => '
                jumbotron_enable,jumbotron_slide,--linebreak--,
                jumbotron_position,--linebreak--,
                jumbotron_container,jumbotron_containerposition,--linebreak--,
                jumbotron_class,jumbotron_alignitem
            ',
        ],
        'background' => [
            'label' => 'Background Image',
            'showitem' => '
                jumbotron_bgimage,jumbotron_bgimageratio,
            ',
        ],
        'backgroundcarousel' => [
            'label' => 'Background Carousel',
            'description' => 'If pages media contains more than 1 image, a carousel slider is displayed.',
            'showitem' => '
                jumbotron_carousel_interval,jumbotron_carousel_pause,
            ',
        ],
        'pageTitle' => [
            'label' => 'Page Title',
            'showitem' => '
                page_title,--linebreak--,
                page_titlealign,--linebreak--,
                page_titlecontainer,--linebreak--,
                page_titleclass
            ',
        ],
        'breadcrumb' => [
            'label' => 'Breadcrumb',
            'description' => 'indicate the current page’s location within a navigational hierarchy.
            INFO: https://getbootstrap.com/docs/5.3/components/breadcrumb/',
            'showitem' => '
                breadcrumb_enable,breadcrumb_notonrootpage,--linebreak--,
                breadcrumb_container,breadcrumb_containerposition,--linebreak--,
                breadcrumb_position,breadcrumb_class,--linebreak--,
                breadcrumb_bottom,breadcrumb_faicon,breadcrumb_corner,
            ',
        ],
        'sidebar' => [
            'label' => 'Sidebar',
            'showitem' => '
                sidebar_enable,sidebar_rightenable,--linebreak--,
                sidebar_entrylevel,sidebar_levels,--linebreak--,
                sidebar_excludeuiduist,--linebreak--,
                sidebar_includespacer,slide_left_aside,--linebreak--,
                slide_right_aside,submenu_sticky,--linebreak--,
                aside_extra_class,sidebar_menu_position,
            ',
        ],

        'expandedContentTop' => [
            'label' => 'Expanded Content Top',
            'showitem' => '
                expandedcontent_slidetop,--linebreak--,
                expandedcontent_containertop,expandedcontent_containerpositiontop,--linebreak--,
                expandedcontent_classtop,
            ',
        ],

        'expandedContentBottom' => [
            'label' => 'Expanded Content Bottom',
            'showitem' => '
                expandedcontent_slidebottom,--linebreak--,
                expandedcontent_containerbottom,expandedcontent_containerpositionbottom,--linebreak--,
                expandedcontent_classbottom,
            ',
        ],

        'footer' => [
            'label' => 'Footer',
            'showitem' => '
                footer_enable,footer_sticky,footer_slide,--linebreak--,
                footer_container,footer_containerposition,--linebreak--,
                footer_class,footer_pid,--linebreak--,
                sticky_footer_extra_padding,
            ',
        ],
        'customScss' => [
            'label' => 'Custom Scss',
            'showitem' => '
                custom_variables_scss,--linebreak--,
                custom_scss,
            ',
        ],
    
    ],

    'columns' => [

        'record_type' => [
            'exclude' => false,
            'label' => 'Configuration Type',
            'description' => 'A Bootstrap component can be selected here.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'General Settings', 'value' => 'general'],
                    ['label' => 'Meta Navigation','value' => 'meta'],
                    ['label' => 'Navbar','value' => 'navbar'],
                    ['label' => 'Jumbotron','value' => 'jumbo'],
                    ['label' => 'Page Title','value' => 'ptitle'],
                    ['label' => 'Breadcrumb','value' => 'breadcrumb'],
                    ['label' => 'Sidebar','value' => 'sidebar'],
                    ['label' => 'Expanded Content','value' => 'extra'],
                    ['label' => 'Footer','value' => 'footer'],
                    ['label' => 'Custom SCSS','value' => 'scss'],
                ],
            ],
        ],
        'homepage_uid' => [
            'exclude' => false,
            'label' => 'Homepage Uid',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'content_only_on_rootpage' => [
            'exclude' => false,
            'label' => 'Content Only On Rootpage',
            'description' => 'disable navbar, jumbotron, breadcrumb and footer on rootpage if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ]
            ]
        ],
        'disable_prefix_comment' => [
            'exclude' => false,
            'label' => 'Disable Prefix Comment',
            'description' => 'if set, the stdWrap property prefixComment will be disabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'body_extra_class' => [
            'exclude' => false,
            'label' => 'Body',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'page_content_extra_class' => [
            'exclude' => false,
            'label' => 'Page Content',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'main_extra_class' => [
            'exclude' => false,
            'label' => 'Main',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'page_wrapper_extra_class' => [
            'exclude' => false,
            'label' => 'Page Wrapper',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
       'global_padding_top' => [
            'exclude' => false,
            'label' => 'Global Top Padding',
            'description' => 'Extra Padding for colPos=0,1 & 2 (main- and aside-tag)',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'pt-1', 'value' => 'pt-1'],
                    ['label' => 'pt-2', 'value' => 'pt-2'],
                    ['label' => 'pt-3', 'value' => 'pt-3'],
                    ['label' => 'pt-4', 'value' => 'pt-4'],
                    ['label' => 'pt-5', 'value' => 'pt-5'],
                ],
                'default' => '',
            ]
        ],
        'content_margin_top' => [
            'exclude' => false,
            'label' => 'Content Element',
            'description' => 'here you can set the default space (margin-top) for each content-element (colPos=0).
            You can overwrite this value in the “Extra Class” field in each content-element.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'mt-1', 'value' => 'mt-1'],
                    ['label' => 'mt-2', 'value' => 'mt-2'],
                    ['label' => 'mt-3', 'value' => 'mt-3'],
                    ['label' => 'mt-4', 'value' => 'mt-4'],
                    ['label' => 'mt-5', 'value' => 'mt-5'],
                ],
                'default' => '',
            ]
        ],
        'loading_spinner' => [
            'exclude' => false,
            'label' => 'make your selection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'Border spinner [border]', 'value' => 'border'],
                    ['label' => 'Growing spinner [grow]', 'value' => 'grow'],
                ],
                'default' => '',
            ]
        ],
        'loading_spinner_color' => [
            'exclude' => false,
            'label' => 'Loading Spinner Color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'current color', 'value' => ''],
                    ['label' => 'primary', 'value' => 'primary'],
                    ['label' => 'secondary', 'value' => 'secondary'],
                    ['label' => 'success', 'value' => 'success'],
                    ['label' => 'danger', 'value' => 'danger'],
                    ['label' => 'warning', 'value' => 'warning'],
                    ['label' => 'description', 'value' => 'description'],
                    ['label' => 'light', 'value' => 'light'],
                    ['label' => 'dark', 'value' => 'dark'],
                ],
                'default' => '',
            ]
        ],
        'lightbox_selection' => [
            'exclude' => false,
            'label' => 'make your selection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
#                    ['label' => 'none', 'value' => ''],
                    ['label' => 'none', 'value' => 0],
                    ['label' => 'Baguettbox', 'value' => 1],
                    ['label' => 'Halkabox', 'value' => 2],
                    ['label' => 'GLightbox', 'value' => 3],
                ],
                'default' => 0,
            ]
        ],
        'magnifying' => [
            'exclude' => false,
            'label' => 'Magnifying glass icon',
            'description' => 'in the center of an image on hover',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_anchor_offset' => [
            'exclude' => false,
            'label' => 'Anchor extra offset (int)',
            'description' => 'for Section-Menu-Items and "OnePageLayout": in px - (default 29)',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy_threshold' => [
            'exclude' => false,
            'label' => 'Scrollspy threshold (string)',
            'description' => 'default: 0.1, 0.5, 1',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy_root_margin' => [
            'exclude' => false,
            'label' => 'Scrollspy rootMargin (string)',
            'description' => 'default: 0px 0px -25%',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy' => [
            'exclude' => false,
            'label' => 'Scrollspy',
            'description' => 'activate/deaktivate scrollspy',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_sticky_top' => [
            'exclude' => false,
            'label' => 'Sticky Top',
            'description' => 'for #sectionmenu, .submenu or .make-me-sticky',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_icons' => [
            'exclude' => false,
            'label' => 'Icons',
            'description' => 'Shows Icons in section menu',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'sidebar_section_mobile' => [
            'exclude' => false,
            'label' => 'Section mobile',
            'description' => 'Shows the section menu also in the mobile if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'background_image_enable' => [
            'exclude' => false,
            'label' => 'Enable',
            'description' => 'first image from pages media',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'background_image_slide' => [
            'exclude' => false,
            'label' => 'Slide',
            'description' => 'rootline sliding for the background image',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'subheader_color' => [
            'exclude' => false,
            'label' => 'Subheader Color',
            'description' => 'Bootstrap contextual text classes',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'current color', 'value' => ''],
                    ['label' => 'primary', 'value' => 'primary'],
                    ['label' => 'secondary', 'value' => 'secondary'],
                    ['label' => 'success', 'value' => 'success'],
                    ['label' => 'danger', 'value' => 'danger'],
                    ['label' => 'warning', 'value' => 'warning'],
                    ['label' => 'description', 'value' => 'description'],
                    ['label' => 'light', 'value' => 'light'],
                    ['label' => 'dark', 'value' => 'dark'],
                ],
                'default' => '',
            ]
        ],
        'date_format' => [
            'exclude' => false,
            'label' => 'Date Format',
            'description' => 'the date format to use in ext:t3sbootstrap - default: d.m.Y',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'favicon' => [
            'exclude' => false,
            'label' => 'Favicon',
            'description' => 'path to your favicon e.g.: EXT:t3sbootstrap/Resources/Public/Icons/favicon.ico',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'card_flipper_on_Click' => [
            'exclude' => false,
            'label' => 'Card Flipper',
            'description' => 'rotate the cards on click (not on hover) if activated',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'last_modified_content_element' => [
            'exclude' => false,
            'label' => 'Last Modified',
            'description' => 'display the date of the last modified content on current page in the footer',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'recently_updated_content_elements' => [
            'exclude' => false,
            'label' => 'Updated Content Elements',
            'description' => 'another solution in the Template MenuRecentlyUpdated.fluid.html if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'meta_enable' => [
            'exclude' => false,
            'label' => 'Enable',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'Left align [start]', 'value' => 'start'],
                    ['label' => 'Right align [end]', 'value' => 'end'],
                    ['label' => 'Nav-scroller (only left align) [scroller]', 'value' => 'scroller'],
                ],
                'default' => '',
            ]
        ],
        'meta_value' => [
            'exclude' => false,
            'label' => 'Value',
            'description' => 'Comma-separated list of page ids.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ],
            'size' => 50,
        ],
        'meta_container' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'meta_class' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. text-white text-shadow bg-primary',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'meta_text' => [
            'exclude' => false,
            'label' => 'Text only',
            'description' => 'e.g. e-mail address and phone number',
                'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'navbar_enable' => [
            'exclude' => false,
            'label' => 'NavBar',
            'description' => 'Choose from navbar-light for use with light background colors, or navbar-dark for dark background colors',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'navbar-dark [dark]', 'value' => 'dark'],
                    ['label' => 'navbar-light [light]', 'value' => 'light'],
                ],
                'default' => '',
            ]
        ],
        'navbar_entrylevel' => [
            'exclude' => false,
            'label' => 'Entry Level (int)',
            'description' => 'Defines at which level in the rootLine the menu should start.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_levels' => [
            'exclude' => false,
            'label' => 'Levels (int)',
            'description' => 'The entry 1 for the first level always must exist.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_excludeuiduist' => [
            'exclude' => false,
            'label' => 'Exclude',
            'description' => 'Comma-separated list of page ids.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_right_menu_uid_list' => [
            'exclude' => false,
            'label' => 'Right Menu',
            'description' => 'Comma-separated list of uid`s (pages) for a right menu in the navbar.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_dark_mode' => [
            'exclude' => false,
            'label' => 'Color mode toggler',
            'description' => 'Enable as right menu dropdown - To allow visitors or users to toggle color modes.',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_sectionmenu' => [
            'exclude' => false,
            'label' => 'Sectionmenu',
            'description' => 'Enable for "One Page Layout"',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_megamenu' => [
            'exclude' => false,
            'label' => 'Megamenu',
            'description' => 'description: https://www.t3sbootstrap.de/demo/mega-menu/',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_includespacer' => [
            'exclude' => false,
            'label' => 'Include Spacer',
            'description' => 'Enable spacer in dropdown',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_hover' => [
            'exclude' => false,
            'label' => 'Hover',
            'description' => 'Open dropdown on hover',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_clickableparent' => [
            'exclude' => false,
            'label' => 'Clickable parent',
            'description' => 'Clickable parent if dropdown menu is open',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_plusicon' => [
            'exclude' => false,
            'label' => 'Plus icon for dropdown',
            'description' => 'Extra plus icon to open dropdown (Hover is disabled by default if activated!)',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_dropdown_animate' => [
            'exclude' => false,
            'label' => 'Dropdown animation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 0],
                    ['label' => 'Slide In [1]', 'value' => 1],
                    ['label' => 'Fade [2]', 'value' => 2],
                ],
                'default' => 0,
            ]
        ],
        'navbar_extra_row' => [
            'exclude' => false,
            'label' => 'Extra Row',
            'description' => 'Enable extra row(s) in the navbar - .../Resources/Private/Partials/Page/Navbar/NavbarExtraRow.fluid.html',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_brand' => [
            'exclude' => false,
            'label' => 'Options',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'As a link [link]', 'value' => 'link'],
                    ['label' => 'As a heading [heading]', 'value' => 'heading'],
                    ['label' => 'Just an image [image]', 'value' => 'image'],
                    ['label' => 'Image and text [imgText]', 'value' => 'imgText'],
                ],
                'default' => '',
            ]
        ],
        'navbarbrand_alignment' => [
            'exclude' => false,
            'label' => 'Alignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'left', 'value' => 'left'],
                    ['label' => 'right', 'value' => 'right'],
                ]
            ]
        ],
        'company' => [
            'exclude' => false,
            'label' => 'Text',
            'description' => 'e.g. Company name (Multilingual Support with pipe "|")',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ],
        ],
        'navbar_image' => [
            'exclude' => false,
            'label' => 'Image',
            'description' => 'Path to your image - Only if "Brand Options" is "Just an image" or "Image and text"',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_color' => [
            'exclude' => false,
            'label' => 'Color scheme',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'bg-light', 'value' => 'light'],
                    ['label' => 'bg-light bg-gradient', 'value' => 'light bg-gradient'],
                    ['label' => 'bg-dark', 'value' => 'dark'],
                    ['label' => 'bg-dark bg-gradient', 'value' => 'dark bg-gradient'],
                    ['label' => 'bg-primary', 'value' => 'primary'],
                    ['label' => 'bg-primary bg-gradient', 'value' => 'primary bg-gradient'],
                    ['label' => 'bg-secondary', 'value' => 'secondary'],
                    ['label' => 'bg-secondary bg-gradient', 'value' => 'bg-secondary bg-gradient'],
                    ['label' => 'bg-success ', 'value' => 'success'],
                    ['label' => 'bg-success bg-gradient', 'value' => 'success bg-gradient'],
                    ['label' => 'bg-danger ', 'value' => 'danger'],
                    ['label' => 'bg-danger bg-gradient', 'value' => 'danger bg-gradient'],
                    ['label' => 'bg-warning ', 'value' => 'warning'],
                    ['label' => 'bg-warning bg-gradient', 'value' => 'warning bg-gradient'],
                    ['label' => 'bg-description ', 'value' => 'description'],
                    ['label' => 'bg-description bg-gradient', 'value' => 'description bg-gradient'],
                    ['label' => 'bg-white', 'value' => 'white'],
                    ['label' => 'bg-body', 'value' => 'body'],
                    ['label' => 'bg-transparent', 'value' => 'transparent'],
                    ['label' => 'bg-color', 'value' => 'color'],
                ]
            ]
        ],
        'navbar_background' => [
            'exclude' => false,
            'label' => 'Background Color',
            'description' => 'HTML-color - Color schemes "bg-color" must be activated',
            'config' => [
                'type' => 'color',
                'opacity' => true,
                'searchable' => false
            ]
        ],
        
        'navbar_transparent' => [
            'exclude' => false,
            'label' => 'Transparent Navbar',
            'description' => 'create a transparent navbar which changes its style on scroll',
            'description' => 'Placement must be "fixed-top"',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_container' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none','value' => ''],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ],
                'default' => '',
            ]
        ],
        'navbar_innercontainer' => [
            'exclude' => false,
            'label' => 'Inner-Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ],
            ]
        ],
        'navbar_placement' => [
            'exclude' => false,
            'label' => 'Placement',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'default','value' => ''],
                    ['label' => 'fixed-top', 'value' => 'fixed-top'],
                    ['label' => 'fixed-bottom', 'value' => 'fixed-bottom'],
                    ['label' => 'sticky-top', 'value' => 'sticky-top'],
                ],
                'default' => '',
            ]
        ],
        'navbar_alignment' => [
            'exclude' => false,
            'label' => 'Alignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'left','value' => 'left'],
                    ['label' => 'right','value' => 'right'],
                    ['label' => 'center','value' => 'center'],
                    ['label' => 'fill (every nav item will be the same width)','value' => 'fill'],
                    ['label' => 'justified (all horizontal space will be occupied by nav links)','value' => 'justified'],
                ]
            ]
        ],
        'navbar_class' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-5" for margin-bottom: 3rem',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_height' => [
            'exclude' => false,
            'label' => 'NavBar Height (int)',
            'description' => 'Is used as padding-top in the body tag - use only if NavBar is fixed-top (int+ px / default: "56")',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_searchbox' => [
            'exclude' => false,
            'label' => 'Searchbox',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none','value' => ''],
                    ['label' => 'Form only [form]','value' => 'form'],
                    ['label' => 'Form & Button [button]','value' => 'button'],
                ],
                'default' => '',
            ]
        ],
        'navbar_shrinkcolor' => [
            'exclude' => false,
            'label' => 'Enable',
            'description' => 'Choose from navbar-light for use with light background colors, or navbar-dark for dark background colors',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none','value' => ''],
                    ['label' => 'navbar-dark [dark]','value' => 'dark'],
                    ['label' => 'navbar-light [light]','value' => 'light'],
                ],
                'default' => '',
            ]
        ],
        'navbar_shrinkcolorschemes' => [
            'exclude' => false,
            'label' => 'Color schemes',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'bg-light', 'value' => 'light'],
                    ['label' => 'bg-light bg-gradient', 'value' => 'light bg-gradient'],
                    ['label' => 'bg-dark', 'value' => 'dark'],
                    ['label' => 'bg-dark bg-gradient', 'value' => 'dark bg-gradient'],
                    ['label' => 'bg-primary', 'value' => 'primary'],
                    ['label' => 'bg-primary bg-gradient', 'value' => 'primary bg-gradient'],
                    ['label' => 'bg-secondary', 'value' => 'secondary'],
                    ['label' => 'bg-secondary bg-gradient', 'value' => 'bg-secondary bg-gradient'],
                    ['label' => 'bg-success ', 'value' => 'success'],
                    ['label' => 'bg-success bg-gradient', 'value' => 'success bg-gradient'],
                    ['label' => 'bg-danger ', 'value' => 'danger'],
                    ['label' => 'bg-danger bg-gradient', 'value' => 'danger bg-gradient'],
                    ['label' => 'bg-warning ', 'value' => 'warning'],
                    ['label' => 'bg-warning bg-gradient', 'value' => 'warning bg-gradient'],
                    ['label' => 'bg-description ', 'value' => 'description'],
                    ['label' => 'bg-description bg-gradient', 'value' => 'description bg-gradient'],
                    ['label' => 'bg-white', 'value' => 'white'],
                    ['label' => 'bg-body', 'value' => 'body'],
                    ['label' => 'bg-transparent', 'value' => 'transparent'],
                ]
            ]
        ],
        'shrinking_nav_padding' => [
            'exclude' => false,
            'label' => 'Padding (top & bottom)',
            'description' => 'py-x can be set by your stylesheet',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'py-1', 'value' => '1'],
                    ['label' => 'py-2', 'value' => '2'],
                    ['label' => 'py-3', 'value' => '3'],
                    ['label' => 'py-4', 'value' => '4'],
                    ['label' => 'py-5', 'value' => '5'],
                    ['label' => 'py-x', 'value' => 'x'],
                ],
            ]
        ],
        'navbar_toggler' => [
            'exclude' => false,
            'label' => 'Toggler',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'left', 'value' => 'left'],
                    ['label' => 'right', 'value' => 'right'],

                ]
            ]
        ],
        'navbar_animatedtoggler' => [
            'exclude' => false,
            'description' => 'Doing it with plain HTML and pure CSS - does not work with "Offcanvas"',
            'label' => 'Animated Toggler',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_breakpoint' => [
            'exclude' => false,
            'label' => 'Breakpoint',
            'description' => 'Grouping and hiding navbar contents by a parent breakpoint',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Small (≥576px) [sm]', 'value' => 'sm'],
                    ['label' => 'Medium (≥768px [md]', 'value' => 'md'],
                    ['label' => 'Large (≥992px) [lg]', 'value' => 'lg'],
                    ['label' => 'Extra large (≥1200px) [xl]', 'value' => 'xl'],
                    ['label' => 'Extra extra large (≥1400px) [xxl]', 'value' => 'xxl'],
                    ['label' => 'Never expand [no]', 'value' => 'no'],
                ]
            ]
        ],
        'navbar_offcanvas' => [
            'exclude' => false,
            'description' => 'Change navbar collapse to offcanvas on mobile screen',
            'label' => 'Offcanvas',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'navbar_langmenu' => [
            'exclude' => false,
            'label' => 'Enable',
            'description' => 'Setting is taken from the site configuration',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'lang_menu_with_fa_icon' => [
            'exclude' => false,
            'label' => 'Style',
            'description' => 'Fontawesome icon (globe) or current language with flag if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],

        'navbar_lang_flags' => [
            'exclude' => false,
            'label' => 'Flags',
            'description' => 'Show flags in the language menu if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_enable' => [
            'exclude' => false,
            'label' => 'Enable',
            'description' => 'Enable Jumbotron in backend layout',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_bgimage' => [
            'exclude' => false,
            'label' => 'Background image',
            'description' => 'Enable background image from pages media OR slider if more than 1 image.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'only on this page [page]', 'value' => 'page'],
                    ['label' => 'on this and all child pages (slide) [root]', 'value' => 'root'],
                ],
                'default' => '',
            ]
        ],
        'jumbotron_bgimageratio' => [
            'exclude' => false,
            'label' => 'Background image ratio',
            'description' => 'Only to be used with a background image - not with videos and/or "Full height section".',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'jumbotron_alignitem' => [
            'exclude' => false,
            'label' => 'Align content items',
            'description' => 'Vertical align for the content (An inside container must be selected)',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'start', 'value' => 'start'],
                    ['label' => 'end', 'value' => 'end'],
                    ['label' => 'center', 'value' => 'center'],
                    ['label' => 'baseline', 'value' => 'baseline'],
                    ['label' => 'stretch', 'value' => 'stretch'],
                ],
                'default' => '',
            ]
        ],
        'jumbotron_slide' => [
            'exclude' => false,
            'label' => 'Slide',
            'description' => 'Content of Jumbotron "slide" through the rootline',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_position' => [
            'exclude' => false,
            'label' => 'Position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Above the NavBar [above]', 'value' => 'above'],
                    ['label' => 'Below the NavBar [below]', 'value' => 'below'],
                ]
            ]
        ],
        'jumbotron_container' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'jumbotron_containerposition' => [
            'exclude' => false,
            'label' => 'Container position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'inside [Inside]', 'value' => 'Inside'],
                    ['label' => 'outside [Outside]','value' => 'Outside'],
                ]
            ]
        ],
        'jumbotron_class' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-0" for margin-bottom: 0',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'jumbotron_carousel_interval' => [
            'exclude' => false,
            'label' => 'Interval',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'jumbotron_carousel_pause' => [
            'exclude' => false,
            'label' => 'Pause on hover',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'page_title' => [
            'exclude' => false,
            'label' => 'Page title (h1)',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none (bad solution - you have to set the <H1> tag by yourself.)','value' => ''],
                    ['label' => 'in the Jumbotron [jumbotron]','value' => 'jumbotron'],
                    ['label' => 'in the Main Content [content]','value' => 'content'],
                    ['label' => 'above the Breadcrumb [breadcrumb]','value' => 'breadcrumb'],
                    ['label' => 'in the Expanded top content (if enabled) [expanded]','value' => 'expanded'],
                ],
                'default' => '',
            ]
        ],
        'page_titlealign' => [
            'exclude' => false,
            'label' => 'Alignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'default','value' => ''],
                    ['label' => 'center','value' => 'center'],
                    ['label' => 'right','value' => 'right'],
                    ['label' => 'left','value' => 'left'],
                ],
                'default' => '',
            ]
        ],
        'page_titlecontainer' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ],
                'default' => '',
            ]
        ],
        'page_titleclass' => [
            'exclude' => false,
            'label' => 'Extra class',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],


        'breadcrumb_enable' => [
            'exclude' => false,
            'label' => 'Enable',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_notonrootpage' => [
            'exclude' => false,
            'label' => 'Not on rootpage',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_faicon' => [
            'exclude' => false,
            'label' => 'Fontawesome icon',
            'description' => 'FA icon instead of text for level=0 only if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_corner' => [
            'exclude' => false,
            'label' => 'No rounded corner',
            'description' => 'To make the breadcrumb without rounded corners',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_bottom' => [
            'exclude' => false,
            'label' => 'Below the content',
            'description' => 'Show the breadcrumb menu below the content (only or also)',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_position' => [
            'exclude' => false,
            'label' => 'Breadcrumb position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Above the NavBar [aboveNav]', 'value' => 'aboveNav'],
                    ['label' => 'Below the NavBar [belowNav]','value' => 'belowNav'],
                    ['label' => 'Above the Jumbotron [aboveJum]', 'value' => 'aboveJum'],
                    ['label' => 'Below the Jumbotron [belowJum]', 'value' => 'belowJum'],
                ]
            ]
        ],
        'breadcrumb_container' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'breadcrumb_containerposition' => [
                'exclude' => false,
                'label' => 'Container position',
                'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'inside', 'value' => 'inside'],
                    ['label' => 'outside', 'value' => 'outside'],
                ]
            ]
        ],
        'breadcrumb_class' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-0" for margin-bottom: 0',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_enable' => [
            'exclude' => false,
            'label' => 'Enable in left Sidebar',
            'description' => 'if sidebar is available',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'submenu [Sub]','value' => 'Sub'],
                    ['label' => 'sectionmenu [Section]', 'value' => 'Section'],
                ],
                'default' => '',
            ]
        ],
        'sidebar_rightenable' => [
            'exclude' => false,
            'label' => 'Enable in right Sidebar',
            'description' => 'if sidebar is available',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => 'submenu [Sub]','value' => 'Sub'],
                    ['label' => 'sectionmenu [Section]', 'value' => 'Section'],
                ],
                'default' => '',
            ]
        ],
        'sidebar_entrylevel' => [
            'exclude' => false,
            'label' => 'Entry Level (int)',
            'description' => '99 = Submenu of current page',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_levels' => [
            'exclude' => false,
            'label' => 'Levels (int)',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_excludeuiduist' => [
            'exclude' => false,
            'label' => 'Exclude',
            'description' => 'Comma-separated list of page ids.',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_includespacer' => [
            'exclude' => false,
            'label' => 'Spacer',
            'description' => 'Enable spacer',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'slide_left_aside' => [
            'exclude' => false,
            'label' => 'Slide left Sidebar',
            'description' => 'content slide for colPos=1 if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'slide_right_aside' => [
            'exclude' => false,
            'label' => 'Slide right Sidebar',
            'description' => 'content slide for colPos=2 if enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'aside_extra_class' => [
            'exclude' => false,
            'label' => 'Extra Class',
            'description' => 'e.g. bg-warning or any other classes',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_menu_position' => [
            'exclude' => false,
            'label' => 'Menu Position',
            'description' => 'above or below the content',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'above', 'value' => 'above'],
                    ['label' => 'below','value' => 'below'],
                ],
            ]
        ],
        'submenu_sticky' => [
            'exclude' => false,
            'label' => 'Sticky top',
            'description' => 'If using sectionmenu check settings under "General Settings - position of the submenu at the top of the viewport"',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_enabletop' => [
            'exclude' => false,
            'label' => 'Enable to show Expanded Content Top & Bottom as Backend Layout',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_slidetop' => [
            'exclude' => false,
            'label' => 'Content slide',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_containertop' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'expandedcontent_containerpositiontop' => [
            'exclude' => false,
            'label' => 'Container position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'inside [Inside]', 'value' => 'Inside'],
                    ['label' => 'outside [Outside]','value' => 'Outside'],
                ]
            ]
        ],
        'expandedcontent_classtop' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-0" for margin-bottom: 0',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'expandedcontent_slidebottom' => [
            'exclude' => false,
            'label' => 'Content slide',
            'description' => 'Content of Expanded Content Bottom "slide" through the rootline',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_containerbottom' => [
            'exclude' => false,
            'label' => 'Container',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'expandedcontent_containerpositionbottom' => [
            'exclude' => false,
            'label' => 'Container position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'inside [Inside]', 'value' => 'Inside'],
                    ['label' => 'outside [Outside]', 'value' => 'Outside'],
                ]
            ]
        ],
        'expandedcontent_classbottom' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-0" for margin-bottom: 0',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'footer_enable' => [
            'exclude' => false,
            'label' => 'Enable',
            'description' => 'Enable Footer in backend layout',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'footer_sticky' => [
            'exclude' => false,
            'label' => 'Sticky Footer',
            'description' => 'Pin a footer to the bottom of the viewport',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'footer_slide' => [
            'exclude' => false,
            'label' => 'Slide',
            'description' => 'Content of Footer "slide" through the rootline',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                     ]
                ],
            ]
        ],
        'footer_container' => [
            'exclude' => false,
            'label' => 'Container',
            'description' => 'Container for the Footer',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => 'none'],
                    ['label' => 'container','value' => 'container'],
                    ['label' => 'container-sm (< 576px)', 'value' => 'container-sm'],
                    ['label' => 'container-md (≥ 576px)', 'value' => 'container-md'],
                    ['label' => 'container-lg (≥ 768px)', 'value' => 'container-lg'],
                    ['label' => 'container-xl (≥ 992px)', 'value' => 'container-xl'],
                    ['label' => 'container-xxl (≥ 1200px)', 'value' => 'container-xxl'],
                    ['label' => 'container-fluid (≥ 1400px)', 'value' => 'container-fluid'],
                ]
            ]
        ],
        'footer_containerposition' => [
            'exclude' => false,
            'label' => 'Container position',
            'description' => 'If "Container" is not "none"',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'inside [Inside]', 'value' => 'Inside'],
                    ['label' => 'outside [Outside]', 'value' => 'Outside'],
                ]
            ]
        ],
        'footer_class' => [
            'exclude' => false,
            'label' => 'Extra class',
            'description' => 'e.g. "mb-0" for margin-bottom: 0',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'footer_pid' => [
            'exclude' => false,
            'description' => 'Page uid (int) for the footer content (colPos= 0)',
            'label' => 'Content (int)',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'sticky_footer_extra_padding' => [
            'exclude' => false,
            'label' => 'Extra padding',
            'description' => 'if "footer-sticky" is activated, the padding-bottom for the body is given by JS.,
             If you like an extra space between the footer and the content, you can do it here (in px)',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'custom_scss' => [
            'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->checkScssVisibility',
            'exclude' => false,
            'label' => 'Custom Scss',
            'description' => 'You can set your own SCSS here:',
            'config' => [
                'type' => 'text',
                'renderType' => 'codeEditor',
                'format' => 'css',
                'rows' => 7,
                'searchable' => false,
            ]
        ],
        'custom_variables_scss' => [
            'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->checkScssVisibility',
            'exclude' => false,
            'label' => 'Custom Variables Scss',
            'description' => 'You can override default Bootstrap variables here:',
            'config' => [
                'type' => 'text',
                'renderType' => 'codeEditor',
                'format' => 'css',
                'rows' => 7,
                'searchable' => false,
            ]
        ],

    ],
];
