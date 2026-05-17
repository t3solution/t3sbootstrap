<?php

$dbModel = 't3sbootstrap.db:tx_t3sbootstrap_domain_model_config';

return [
    'ctrl' => [
        'title'	=> '',
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
            --div--;'.$dbModel.'.variousextraclasses,
                --palette--;;variousExtraClasses,
            --div--;'.$dbModel.'.variousmarginandpadding,
                --palette--;;variousMarginPadding,
            --div--;'.$dbModel.'.loadingspinner,
                --palette--;;loadingSpinner,
            --div--;'.$dbModel.'.lightbox,
                --palette--;;lightboxSettings,
            --div--;'.$dbModel.'.sectionmenu,
                --palette--;;sectionMenuSettings,
            --div--;'.$dbModel.'.backgroundimage,
                --palette--;;backgroundImage,
            --div--;'.$dbModel.'.othersettings,
                --palette--;;otherSettings,
        '],
        'meta' => [
            'title' => $dbModel.'.meta',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;metaNavigation',
        ],
        'navbar' => [
            'title' =>  $dbModel.'.navbar',
            'showitem' => '
                record_type,--linebreak--,
                --palette--;;navbar,
            --div--;'.$dbModel.'.varioussettings,
                --palette--;;variousSettings,
            --div--;'.$dbModel.'.brand,
                --palette--;;brand,
            --div--;'.$dbModel.'.backgroundcolor,
                --palette--;;backgroundColor,
            --div--;'.$dbModel.'.layoutplacement,
                --palette--;;layoutPlacement,
            --div--;Shrinking Navbar,
                --palette--;;shrinkingNavbar,
            --div--;'.$dbModel.'.responsiveBehaviors,
                --palette--;;responsiveBehaviors,
            --div--;'.$dbModel.'.languagemenu,
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
            'title' => $dbModel.'.ptitle',
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
            'label' => $dbModel.'.generalsettings',
            'showitem' => '
                content_only_on_rootpage,--linebreak--,
                disable_prefix_comment
            ',
        ],
        'variousExtraClasses' => [
            'label' =>  $dbModel.'.variousextraclasses',
            'description' => $dbModel.'.variousextraclasses.description',
            'showitem' => '
                body_extra_class,--linebreak--,
                page_wrapper_extra_class,--linebreak--,
                page_content_extra_class,--linebreak--,
                main_extra_class,
            ',
        ],
        'variousMarginPadding' => [
            'label' => $dbModel.'.variousmarginandpadding',
            'showitem' => '
                global_padding_top,--linebreak--,
                content_margin_top,
            ',
        ],
        'loadingSpinner' => [
            'label' => $dbModel.'.loadingspinner',
            'description' => $dbModel.'.loadingspinner.description',
            'showitem' => '
                loading_spinner,loading_spinner_color,
            ',
        ],
        'lightboxSettings' => [
            'label' => $dbModel.'.lightboxsettings',
            'showitem' => '
                lightbox_selection,--linebreak--,
                magnifying,
            ',
        ],
        'sectionMenuSettings' => [
            'label' => $dbModel.'.sectionmenusettings',
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
            'label' => $dbModel.'.backgroundimage',
            'showitem' => '
                background_image_enable,background_image_slide,
            ',
        ],
        'otherSettings' => [
            'label' => $dbModel.'.othersettings',
            'showitem' => '
                subheader_color,date_format,favicon,--linebreak--,
                card_flipper_on_Click,--linebreak--,
                last_modified_content_element,--linebreak--,
                recently_updated_content_elements,
            ',
        ],
        'metaNavigation' => [
            'label' => $dbModel.'.metanavigation',
            'showitem' => '
                meta_enable,meta_container,--linebreak--,
                meta_value,meta_class,--linebreak--,
                meta_text,
            ',
        ],
        'navbar' => [
            'label' => $dbModel.'.navbar',
            'showitem' => '
                navbar_enable,--linebreak--,
                navbar_entrylevel,navbar_levels,--linebreak--,
                navbar_excludeuiduist,--linebreak--,
                navbar_right_menu_uid_list,--linebreak--,
                navbar_dark_mode,
            ',
        ],
        'variousSettings' => [
            'label' => $dbModel.'.varioussettings',
            'showitem' => '
                navbar_sectionmenu,navbar_megamenu,--linebreak--,
                navbar_includespacer,navbar_hover,--linebreak--,
                navbar_extra_row,navbar_clickableparent,--linebreak--,
                navbar_plusicon,navbar_dropdown_animate,
            ',
        ],
        'brand' => [
            'label' => $dbModel.'.brand',
            'description' => $dbModel.'.brand.description',
            'showitem' => '
                navbar_brand,--linebreak--,
                navbarbrand_alignment,--linebreak--,
                company,--linebreak--,
                navbar_image,
            ',
        ],
        'backgroundColor' => [
            'label' => $dbModel.'.backgroundcolor',
            'description' => 'INFO: https://getbootstrap.com/docs/5.3/components/navbar/#color-schemes',
            'showitem' => '
                navbar_color,--linebreak--,
                navbar_background,--linebreak--,
                navbar_transparent,
            ',
        ],
        'layoutPlacement' => [
            'label' => $dbModel.'.layoutplacement',
            'description' => $dbModel.'.layoutplacement.description',
            'showitem' => '
                navbar_container,navbar_innercontainer,--linebreak--,
                navbar_placement,navbar_alignment,--linebreak--,
                navbar_class,--linebreak--,
                navbar_height,--linebreak--,
                navbar_searchbox,
            ',
        ],
        'shrinkingNavbar' => [
            'label' => $dbModel.'.shrinkingnavbar',
            'description' => $dbModel.'.shrinkingnavbar.description',
            'showitem' => '
                navbar_shrinkcolor,--linebreak--,
                shrinking_nav_padding,--linebreak--,
                navbar_shrinkcolorschemes,
            ',
        ],
        'responsiveBehaviors' => [
            'label' => $dbModel.'.responsiveBehaviors',
            'description' => $dbModel.'.responsiveBehaviors.description',
            'showitem' => '
                navbar_toggler,--linebreak--,
                navbar_breakpoint,--linebreak--,
                navbar_animatedtoggler,navbar_offcanvas,
            ',
        ],
        'languageMenu' => [
            'label' => $dbModel.'.languagemenu',
            'showitem' => '
                navbar_langmenu,--linebreak--,
                lang_menu_with_fa_icon,--linebreak--,
                navbar_lang_flags,
            ',
        ],
        'jumbotron' => [
            'label' => $dbModel.'.jumbotron',
            'showitem' => '
                jumbotron_enable,jumbotron_slide,--linebreak--,
                jumbotron_position,--linebreak--,
                jumbotron_container,jumbotron_containerposition,--linebreak--,
                jumbotron_class,jumbotron_alignitem
            ',
        ],
        'background' => [
            'label' => $dbModel.'.backgroundimage',
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
            'label' => $dbModel.'.pagetitle',
            'showitem' => '
                page_title,--linebreak--,
                page_titlealign,--linebreak--,
                page_titlecontainer,--linebreak--,
                page_titleclass
            ',
        ],
        'breadcrumb' => [
            'label' => $dbModel.'.breadcrumb',
            'description' => $dbModel.'.breadcrumb.description',
            'showitem' => '
                breadcrumb_enable,breadcrumb_notonrootpage,--linebreak--,
                breadcrumb_container,breadcrumb_containerposition,--linebreak--,
                breadcrumb_position,breadcrumb_class,--linebreak--,
                breadcrumb_bottom,breadcrumb_faicon,breadcrumb_corner,
            ',
        ],
        'sidebar' => [
            'label' => $dbModel.'.sidebar',
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
            'label' => $dbModel.'.expandedcontenttop',
            'showitem' => '
                expandedcontent_slidetop,--linebreak--,
                expandedcontent_containertop,expandedcontent_containerpositiontop,--linebreak--,
                expandedcontent_classtop,
            ',
        ],

        'expandedContentBottom' => [
            'label' => $dbModel.'.expandedcontentbottom',
            'showitem' => '
                expandedcontent_slidebottom,--linebreak--,
                expandedcontent_containerbottom,expandedcontent_containerpositionbottom,--linebreak--,
                expandedcontent_classbottom,
            ',
        ],

        'footer' => [
            'label' => $dbModel.'.footer',
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
            'label' => $dbModel.'.recordtype',
            'description' => $dbModel.'.recordtype.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.generalsettings', 'value' => 'general'],
                    ['label' => $dbModel.'.metanavigation','value' => 'meta'],
                    ['label' => $dbModel.'.navbar','value' => 'navbar'],
                    ['label' => $dbModel.'.jumbotron','value' => 'jumbo'],
                    ['label' => $dbModel.'.pagetitle','value' => 'ptitle'],
                    ['label' => $dbModel.'.breadcrumb','value' => 'breadcrumb'],
                    ['label' => $dbModel.'.sidebar','value' => 'sidebar'],
                    ['label' => $dbModel.'.expandedcontent','value' => 'extra'],
                    ['label' => $dbModel.'.footer','value' => 'footer'],
                    ['label' => $dbModel.'.customscss','value' => 'scss'],
                ],
            ],
        ],
        'homepage_uid' => [
            'exclude' => false,
            'label' => $dbModel.'.homepageuid',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'content_only_on_rootpage' => [
            'exclude' => false,
            'label' => $dbModel.'.contentonlyonrootpage',
            'description' => $dbModel.'.contentonlyonrootpage.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ]
            ]
        ],
        'disable_prefix_comment' => [
            'exclude' => false,
            'label' => $dbModel.'.disableprefixcomment',
            'description' => $dbModel.'.disableprefixcomment.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'body_extra_class' => [
            'exclude' => false,
            'label' => $dbModel.'.bodyclass',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'page_content_extra_class' => [
            'exclude' => false,
            'label' => $dbModel.'.pagecontent',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'main_extra_class' => [
            'exclude' => false,
            'label' => $dbModel.'.main',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
        'page_wrapper_extra_class' => [
            'exclude' => false,
            'label' => $dbModel.'.pagewrapper',
            'config' => [
                'type' => 'input',
                'searchable' => false,
            ]
        ],
       'global_padding_top' => [
            'exclude' => false,
            'label' => $dbModel.'.globaltoppadding',
            'description' => $dbModel.'.globaltoppadding.description',
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
            'label' => $dbModel.'.contentmargintop',
            'description' => $dbModel.'.contentmargintop.description',
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
            'label' => $dbModel.'.makeyourselection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.spinner.item1', 'value' => 'border'],
                    ['label' => $dbModel.'.spinner.item2', 'value' => 'grow'],
                ],
                'default' => '',
            ]
        ],
        'loading_spinner_color' => [
            'exclude' => false,
            'label' => $dbModel.'.loadingspinnercolor',
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
                    ['label' => 'light', 'value' => 'light'],
                    ['label' => 'dark', 'value' => 'dark'],
                ],
                'default' => '',
            ]
        ],
        'lightbox_selection' => [
            'exclude' => false,
            'label' => $dbModel.'.makeyourselection',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
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
            'label' => $dbModel.'.magnifyingglassicon',
            'description' => $dbModel.'.magnifyingglassicon.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_anchor_offset' => [
            'exclude' => false,
            'label' => $dbModel.'.anchorextraoffset',
            'description' => $dbModel.'.anchorextraoffset.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy_threshold' => [
            'exclude' => false,
            'label' => $dbModel.'.sectionmenuscrollspythreshold',
            'description' => $dbModel.'.sectionmenuscrollspythreshold.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy_root_margin' => [
            'exclude' => false,
            'label' => $dbModel.'.sectionmenuscrollspyrootmargin',
            'description' => $dbModel.'.sectionmenuscrollspyrootmargin.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sectionmenu_scrollspy' => [
            'exclude' => false,
            'label' => $dbModel.'.sectionmenuscrollspy',
            'description' => $dbModel.'.sectionmenuscrollspy.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_sticky_top' => [
            'exclude' => false,
            'label' => $dbModel.'.sectionmenustickytop',
            'description' => $dbModel.'.sectionmenustickytop.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'sectionmenu_icons' => [
            'exclude' => false,
            'label' => $dbModel.'.icons',
            'description' => $dbModel.'.icons.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'sidebar_section_mobile' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarsectionmobile',
            'description' => $dbModel.'.sidebarsectionmobile.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'background_image_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.backgroundimageenable',
            'description' => $dbModel.'.backgroundimageenable.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'background_image_slide' => [
            'exclude' => false,
            'label' => $dbModel.'.backgroundimageslide',
            'description' => $dbModel.'.backgroundimageslide.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'subheader_color' => [
            'exclude' => false,
            'label' => $dbModel.'.subheadercolor',
            'description' => $dbModel.'.subheadercolor.description',
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
                    ['label' => 'light', 'value' => 'light'],
                    ['label' => 'dark', 'value' => 'dark'],
                ],
                'default' => '',
            ]
        ],
        'date_format' => [
            'exclude' => false,
            'label' => $dbModel.'.dateformat',
            'description' => $dbModel.'.dateformat.description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'favicon' => [
            'exclude' => false,
            'label' => $dbModel.'.favicon',
            'description' => $dbModel.'.favicon.description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'card_flipper_on_Click' => [
            'exclude' => false,
            'label' => $dbModel.'.cardflipperonclick',
            'description' => $dbModel.'.cardflipperonclick.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'last_modified_content_element' => [
            'exclude' => false,
            'label' => $dbModel.'.lastmodifiedcontentelement',
            'description' => $dbModel.'.lastmodifiedcontentelement.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'recently_updated_content_elements' => [
            'exclude' => false,
            'label' => $dbModel.'.recentlyupdatedcontentelements',
            'description' => $dbModel.'.recentlyupdatedcontentelements.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'meta_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.metaenable',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.metaenable.item1', 'value' => 'start'],
                    ['label' => $dbModel.'.metaenable.item2', 'value' => 'end'],
                    ['label' => $dbModel.'.metaenable.item3', 'value' => 'scroller'],
                ],
                'default' => '',
            ]
        ],
        'meta_value' => [
            'exclude' => false,
            'label' => $dbModel.'.metavalue',
            'description' => $dbModel.'.metavalue.description',
            'config' => [
                'type' => 'input',
                'searchable' => false,
                'size' => 50
            ],
        ],
        'meta_container' => [
            'exclude' => false,
            'label' => $dbModel.'.container',
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
            'label' => $dbModel.'.metaclass',
            'description' => $dbModel.'.metaclass.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'meta_text' => [
            'exclude' => false,
            'label' => $dbModel.'.metatext',
            'description' => $dbModel.'.metatext.description',
                'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 1,
                'searchable' => false
            ]
        ],
        'navbar_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarenable',
            'description' => $dbModel.'.navbarenable.description',
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
            'label' => $dbModel.'.navbarentrylevel',
            'description' => $dbModel.'.navbarentrylevel.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_levels' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarlevels',
            'description' => $dbModel.'.navbarlevels.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_excludeuiduist' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarexcludeuiduist',
            'description' => $dbModel.'.navbarexcludeuiduist.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_right_menu_uid_list' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarrightmenuuidlist',
            'description' => $dbModel.'.navbarrightmenuuidlist.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_dark_mode' => [
            'exclude' => false,
            'label' => $dbModel.'.navbardarkmode',
            'description' => $dbModel.'.navbardarkmode.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_sectionmenu' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarsectionmenu',
            'description' => $dbModel.'.navbarsectionmenu.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_megamenu' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarmegamenu',
            'description' => $dbModel.'.navbarmegamenu.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_includespacer' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarincludespacer',
            'description' => $dbModel.'.navbarincludespacer.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_hover' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarhover',
            'description' => $dbModel.'.navbarhover.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_clickableparent' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarclickableparent',
            'description' => $dbModel.'.navbarclickableparent.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_plusicon' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarplusicon',
            'description' => $dbModel.'.navbarplusicon.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_dropdown_animate' => [
            'exclude' => false,
            'label' => $dbModel.'.navbardropdownanimate',
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
            'label' => $dbModel.'.navbarextrarow',
            'description' => $dbModel.'.navbarextrarow.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_brand' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarbrand',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.navbarbrand.item1', 'value' => 'link'],
                    ['label' => $dbModel.'.navbarbrand.item2', 'value' => 'heading'],
                    ['label' => $dbModel.'.navbarbrand.item3', 'value' => 'image'],
                    ['label' => $dbModel.'.navbarbrand.item4', 'value' => 'imgText'],
                ],
                'default' => '',
            ]
        ],
        'navbarbrand_alignment' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarbrandalignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.navbarbrandalignment.item1', 'value' => 'left'],
                    ['label' => $dbModel.'.navbarbrandalignment.item2', 'value' => 'right'],
                ]
            ]
        ],
        'company' => [
            'exclude' => false,
            'label' => $dbModel.'.company',
            'description' => $dbModel.'.company.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ],
        ],
        'navbar_image' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarimage',
            'description' => $dbModel.'.navbarimage.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_color' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarcolor',
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
            'label' => $dbModel.'.navbarbackground',
            'description' => $dbModel.'.navbarbackground.description',
            'config' => [
                'type' => 'color',
                'opacity' => true,
                'searchable' => false
            ]
        ],
        
        'navbar_transparent' => [
            'exclude' => false,
            'label' => $dbModel.'.navbartransparent',
            'description' => $dbModel.'.navbartransparent.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_container' => [
            'exclude' => false,
            'label' => $dbModel.'.container',
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
            'label' => $dbModel.'.navbarinnercontainer',
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
            'label' => $dbModel.'.navbarplacement',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.navbarplacement.item1', 'value' => ''],
                    ['label' => $dbModel.'.navbarplacement.item2', 'value' => 'fixed-top'],
                    ['label' => $dbModel.'.navbarplacement.item3', 'value' => 'fixed-bottom'],
                    ['label' => $dbModel.'.navbarplacement.item4', 'value' => 'sticky-top'],
                ],
                'default' => '',
            ]
        ],
        'navbar_alignment' => [
            'exclude' => false,
            'label' => $dbModel.'.navbaralignment',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.navbaralignment.item1','value' => 'left'],
                    ['label' => $dbModel.'.navbaralignment.item2','value' => 'right'],
                    ['label' => $dbModel.'.navbaralignment.item3','value' => 'center'],
                    ['label' => $dbModel.'.navbaralignment.item4','value' => 'fill'],
                    ['label' => $dbModel.'.navbaralignment.item5','value' => 'justified'],
                ]
            ]
        ],
        'navbar_class' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarclass',
            'description' => $dbModel.'.navbarclass.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_height' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarheight',
            'description' => $dbModel.'.navbarheight.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'navbar_searchbox' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarsearchbox',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none','value' => ''],
                    ['label' => $dbModel.'.navbarsearchbox.item1','value' => 'form'],
                    ['label' => $dbModel.'.navbarsearchbox.item2','value' => 'button'],
                ],
                'default' => '',
            ]
        ],
        'navbar_shrinkcolor' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarshrinkcolor',
            'description' => $dbModel.'.navbarshrinkcolor.description',
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
            'label' => $dbModel.'.navbarshrinkcolorschemes',
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
            'label' => $dbModel.'.shrinkingnavpadding',
            'description' => $dbModel.'.shrinkingnavpadding.description',
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
            'label' => $dbModel.'.navbartoggler',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.navbartoggler.item1', 'value' => 'left'],
                    ['label' => $dbModel.'.navbartoggler.item2', 'value' => 'right'],
                ]
            ]
        ],
        'navbar_animatedtoggler' => [
            'exclude' => false,
            'label' => $dbModel.'.navbaranimatedtoggler',
            'description' => $dbModel.'.navbaranimatedtoggler.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_breakpoint' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarbreakpoint',
            'description' => $dbModel.'.navbarbreakpoint.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.navbarbreakpoint.item1', 'value' => 'sm'],
                    ['label' => $dbModel.'.navbarbreakpoint.item2', 'value' => 'md'],
                    ['label' => $dbModel.'.navbarbreakpoint.item3', 'value' => 'lg'],
                    ['label' => $dbModel.'.navbarbreakpoint.item4', 'value' => 'xl'],
                    ['label' => $dbModel.'.navbarbreakpoint.item5', 'value' => 'xxl'],
                    ['label' => $dbModel.'.navbarbreakpoint.item6', 'value' => 'no'],
                ]
            ]
        ],
        'navbar_offcanvas' => [
            'exclude' => false,
            'label' => $dbModel.'.navbaroffcanvas',
            'description' => 'Change navbar collapse to offcanvas on mobile screen',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'navbar_langmenu' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarlangmenu',
            'description' => $dbModel.'.navbarlangmenu.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'lang_menu_with_fa_icon' => [
            'exclude' => false,
            'label' => $dbModel.'.langmenuwithfaicon',
            'description' => $dbModel.'.langmenuwithfaicon.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],

        'navbar_lang_flags' => [
            'exclude' => false,
            'label' => $dbModel.'.navbarlangflags',
            'description' => $dbModel.'.navbarlangflags.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.jumbotronenable',
            'description' => $dbModel.'.jumbotronenable.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_bgimage' => [
            'exclude' => false,
            'label' => $dbModel.'.backgroundimage',
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
            'label' => $dbModel.'.jumbotronalignitem',
            'description' => $dbModel.'.jumbotronalignitem.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.jumbotronalignitem.item1', 'value' => 'start'],
                    ['label' => $dbModel.'.jumbotronalignitem.item2', 'value' => 'end'],
                    ['label' => $dbModel.'.jumbotronalignitem.item3', 'value' => 'center'],
                    ['label' => $dbModel.'.jumbotronalignitem.item4', 'value' => 'baseline'],
                    ['label' => $dbModel.'.jumbotronalignitem.item5', 'value' => 'stretch'],
                ],
                'default' => '',
            ]
        ],
        'jumbotron_slide' => [
            'exclude' => false,
            'label' => $dbModel.'.jumbotronslide',
            'description' => $dbModel.'.jumbotronslide.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'jumbotron_position' => [
            'exclude' => false,
            'label' => $dbModel.'.jumbotronposition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.jumbotronposition.item1', 'value' => 'above'],
                    ['label' => $dbModel.'.jumbotronposition.item2', 'value' => 'below'],
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
            'label' => $dbModel.'.jumbotroncontainerposition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.jumbotroncontainerposition.item1', 'value' => 'Inside'],
                    ['label' => $dbModel.'.jumbotroncontainerposition.item2','value' => 'Outside'],
                ]
            ]
        ],
        'jumbotron_class' => [
            'exclude' => false,
            'label' => $dbModel.'.jumbotronclass',
            'description' => $dbModel.'.jumbotronclass.description',
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
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'page_title' => [
            'exclude' => false,
            'label' => $dbModel.'.pagetitle',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.pagetitle.item1','value' => ''],
                    ['label' => $dbModel.'.pagetitle.item2','value' => 'jumbotron'],
                    ['label' => $dbModel.'.pagetitle.item3','value' => 'content'],
                    ['label' => $dbModel.'.pagetitle.item4','value' => 'breadcrumb'],
                    ['label' => $dbModel.'.pagetitle.item5','value' => 'expanded'],
                ],
                'default' => '',
            ]
        ],
        'page_titlealign' => [
            'exclude' => false,
            'label' => $dbModel.'.pagetitlealign',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.pagetitlealign.item1','value' => ''],
                    ['label' => $dbModel.'.pagetitlealign.item2','value' => 'center'],
                    ['label' => $dbModel.'.pagetitlealign.item3','value' => 'right'],
                    ['label' => $dbModel.'.pagetitlealign.item4','value' => 'left'],
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
            'label' => $dbModel.'.pagetitleclass',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],


        'breadcrumb_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbenable',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_notonrootpage' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbnotonrootpage',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_faicon' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbfaicon',
            'description' => $dbModel.'.breadcrumbfaicon.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_corner' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbcorner',
            'description' => $dbModel.'.breadcrumbcorner.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_bottom' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbbottom',
            'description' => $dbModel.'.breadcrumbbottom.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'breadcrumb_position' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbposition',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.breadcrumbposition.item1', 'value' => 'aboveNav'],
                    ['label' => $dbModel.'.breadcrumbposition.item2','value' => 'belowNav'],
                    ['label' => $dbModel.'.breadcrumbposition.item3', 'value' => 'aboveJum'],
                    ['label' => $dbModel.'.breadcrumbposition.item4', 'value' => 'belowJum'],
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
                'label' => $dbModel.'.breadcrumbcontainerposition',
                'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.breadcrumbcontainerposition.item1', 'value' => 'inside'],
                    ['label' => $dbModel.'.breadcrumbcontainerposition.item2', 'value' => 'outside'],
                ]
            ]
        ],
        'breadcrumb_class' => [
            'exclude' => false,
            'label' => $dbModel.'.breadcrumbclass',
            'description' => $dbModel.'.breadcrumbclass.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarenable',
            'description' => $dbModel.'.sidebarenable.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.sidebar.item1','value' => 'Sub'],
                    ['label' => $dbModel.'.sidebar.item2', 'value' => 'Section'],
                ],
                'default' => '',
            ]
        ],
        'sidebar_rightenable' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarrightenable',
            'description' => $dbModel.'.sidebarrightenable.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'none', 'value' => ''],
                    ['label' => $dbModel.'.sidebare.item1','value' => 'Sub'],
                    ['label' => $dbModel.'.sidebare.item2', 'value' => 'Section'],
                ],
                'default' => '',
            ]
        ],
        'sidebar_entrylevel' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarentrylevel',
            'description' => $dbModel.'.sidebarentrylevel.description',
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
            'label' => $dbModel.'.sidebarexcludeuiduist',
            'description' => $dbModel.'.sidebarexcludeuiduist.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_includespacer' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarincludespacer',
            'description' => $dbModel.'.sidebarincludespacer.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'slide_left_aside' => [
            'exclude' => false,
            'label' => $dbModel.'.slideleftaside',
            'description' => $dbModel.'.slideleftaside.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'slide_right_aside' => [
            'exclude' => false,
            'label' => $dbModel.'.sliderightaside',
            'description' => $dbModel.'.sliderightaside.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'aside_extra_class' => [
            'exclude' => false,
            'label' => $dbModel.'.asideextraclass',
            'description' => $dbModel.'.asideextraclass.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'sidebar_menu_position' => [
            'exclude' => false,
            'label' => $dbModel.'.sidebarmenuposition',
            'description' => $dbModel.'.sidebarmenuposition.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.sidebarmenuposition.item1', 'value' => 'above'],
                    ['label' => $dbModel.'.sidebarmenuposition.item2','value' => 'below'],
                ],
            ]
        ],
        'submenu_sticky' => [
            'exclude' => false,
            'label' => $dbModel.'.submenusticky',
            'description' => $dbModel.'.submenusticky.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_enabletop' => [
            'exclude' => false,
            'label' => $dbModel.'.expandedcontentenabletop',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'expandedcontent_slidetop' => [
            'exclude' => false,
            'label' => $dbModel.'.expandedcontentslidetop',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
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
            'label' => $dbModel.'.expandedcontentcontainerpositiontop',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.expandedcontentcontainerpositiontop.item1', 'value' => 'Inside'],
                    ['label' => $dbModel.'.expandedcontentcontainerpositiontop.item2','value' => 'Outside'],
                ]
            ]
        ],
        'expandedcontent_classtop' => [
            'exclude' => false,
            'label' => $dbModel.'.expandedcontentclasstop',
            'description' => $dbModel.'.expandedcontentclasstop.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'expandedcontent_slidebottom' => [
            'exclude' => false,
            'label' => $dbModel.'.expandedcontentslidebottom',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
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
            'label' => $dbModel.'.expandedcontentcontainerpositionbottom',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $dbModel.'.expandedcontentcontainerpositionbottom.item1', 'value' => 'Inside'],
                    ['label' => $dbModel.'.expandedcontentcontainerpositionbottom.item2', 'value' => 'Outside'],
                ]
            ]
        ],
        'expandedcontent_classbottom' => [
            'exclude' => false,
            'label' => $dbModel.'.expandedcontentclassbottom',
            'description' => $dbModel.'.expandedcontentclassbottom.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'footer_enable' => [
            'exclude' => false,
            'label' => $dbModel.'.footerenable',
            'description' => $dbModel.'.footerenable.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'footer_sticky' => [
            'exclude' => false,
            'label' => $dbModel.'.footersticky',
            'description' => $dbModel.'.footersticky.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
                     ]
                ],
            ]
        ],
        'footer_slide' => [
            'exclude' => false,
            'label' => $dbModel.'.footerslide',
            'description' => $dbModel.'.footerslide.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                     [
                        'label' => '',
                        'labelChecked' => $dbModel.'.enabled',
                        'labelUnchecked' => $dbModel.'.disabled',
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
            'label' => $dbModel.'.footercontainerposition',
            'description' => $dbModel.'.footercontainerposition.description',
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
            'label' => $dbModel.'.footerclass',
            'description' => $dbModel.'.footerclass.description',
            'config' => [
                'type' => 'input',
                'searchable' => false
            ]
        ],
        'footer_pid' => [
            'exclude' => false,
            'label' => $dbModel.'.footerpid',
            'description' => $dbModel.'.footerpid.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'sticky_footer_extra_padding' => [
            'exclude' => false,
            'label' => $dbModel.'.stickyfooterextrapadding',
            'description' => $dbModel.'.stickyfooterextrapadding.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'searchable' => false
            ]
        ],
        'custom_scss' => [
            'displayCond' => 'USER:T3SBS\T3sbootstrap\UserFunction\TcaMatcher->checkScssVisibility',
            'exclude' => false,
            'label' => $dbModel.'.customscss',
            'description' => $dbModel.'.customscss.description',
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
            'label' => $dbModel.'.customvariablesscss',
            'description' => $dbModel.'.customvariablesscss.description',
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
