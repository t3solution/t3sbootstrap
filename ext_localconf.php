<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess;
use T3SBS\T3sbootstrap\Controller\ConsentController;
use T3SBS\T3sbootstrap\Backend\FormDataProvider\FlexFormManipulation;
use T3SBS\T3sbootstrap\Parser\ScssParser;
use T3SBS\T3sbootstrap\Hooks\PageRenderer\PreProcessHook;
use T3SBS\T3sbootstrap\ViewHelpers;

defined('TYPO3') || die();

(function () {

    /***************
     * Default Constants
     */
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.indexedsearch = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.news = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.kesearch = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.iconpack = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.linkHoverEffect = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.lazyLoad = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.animateCss = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.container = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.spacing = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.color = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cTypeClass = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customScss = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.editScss = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.keepVariables = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.bootswatch = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customSectionOrder = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbconcatenate = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbminify = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sitepackage = 0');
    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomepagetitle = 0');
	ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomeCss = 0');
	ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.chapter = 0');

	// Global namespace import
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['t3sb'] = [
		ViewHelpers::class,
	];

    /***************
     * Extension configuration
     */
    $extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

    /***************
     * Custom Extensions
     */
    // if ke_search is loaded
    if (ExtensionManagementUtility::isLoaded('ke_search')) {
        # Setup
        ExtensionManagementUtility::addTypoScript(
            't3sbootstrap',
            'setup',
            '@import "EXT:t3sbootstrap/Resources/Private/Extensions/ke_search/Configuration/TypoScript/setup.typoscript"'
        );
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.kesearch = 1');
    } else {
        # if indexed_search is loaded
        if (ExtensionManagementUtility::isLoaded('indexed_search')) {
            # Setup
            ExtensionManagementUtility::addTypoScript(
                't3sbootstrap',
                'setup',
                '@import "EXT:t3sbootstrap/Resources/Private/Extensions/indexed_search/Configuration/TypoScript/setup.typoscript"'
            );
            ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.indexedsearch = 1');
        }
    }
    # if iconpack is loaded
    if (ExtensionManagementUtility::isLoaded('iconpack')) {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.iconpack = 1');
	}
    # if news is loaded
    if (ExtensionManagementUtility::isLoaded('news') && array_key_exists('extNews', $extconf) && $extconf['extNews'] === '1') {
        ExtensionManagementUtility::addTypoScript(
            't3sbootstrap',
            'setup',
            '@import "EXT:t3sbootstrap/Resources/Private/Extensions/news/Configuration/TypoScript/setup.typoscript"'
        );
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.news = 1');
    }
    // Optional modify flexform fields
    if (array_key_exists('flexformModify', $extconf) && $extconf['flexformModify'] === '1') {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][FlexFormManipulation::class] = [
                 'depends' => [TcaFlexPrepare::class,],
                 'before' => [TcaFlexProcess::class,],
        ];
    }
    // CKEditor
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/Default.yaml';
    // Optional sitepackage
    if (ExtensionManagementUtility::isLoaded('t3sb_package') && array_key_exists('sitepackage', $extconf) && !empty($extconf['sitepackage'])) {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sitepackage = 1');
        ExtensionManagementUtility::addTypoScriptConstants('@import \'EXT:t3sb_package/Configuration/TypoScript/t3sbconstants.typoscript\'');
        ExtensionManagementUtility::addTypoScriptSetup('@import \'EXT:t3sb_package/Configuration/TypoScript/t3sbsetup.typoscript\'');
    } else {
        ExtensionManagementUtility::addTypoScriptConstants('@import \'fileadmin/T3SB/Configuration/TypoScript/t3sbconstants.typoscript\'');
        ExtensionManagementUtility::addTypoScriptSetup('@import \'fileadmin/T3SB/Configuration/TypoScript/t3sbsetup.typoscript\'');
    }
    // Optional Hover Link Effect (FAL)
    if (array_key_exists('linkHoverEffect', $extconf) && $extconf['linkHoverEffect'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.linkHoverEffect = 1');
    }
    // Optional Copyright notice (FAL)
    if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 1');
    } elseif (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '2') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 2');
    }
    // Optional concatenate CSS & JS files in asset collector
    if (array_key_exists('t3sbconcatenate', $extconf) && $extconf['t3sbconcatenate'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbconcatenate = 1');
    }
    // Optional minify CSS & JS with toptal(dot)com
    if (array_key_exists('t3sbminify', $extconf) && $extconf['t3sbminify'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbminify = 1');
    }

	// Optional icon in page title
	if (array_key_exists('fontawesomepagetitle', $extconf) && $extconf['fontawesomepagetitle'] === '1') {
	    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomepagetitle = 1');
	}
	// Optional fontawesome min
	if (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '1') {
	    ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomeCss = 1');
	}
    // Optional "chapter"
    if (array_key_exists('chapter', $extconf) && !empty($extconf['chapter'])) {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.chapter = 1');
    }

    // Optional lazyLoad
    if (array_key_exists('lazyLoad', $extconf)) {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.lazyLoad = '.$extconf['lazyLoad']);
    }
    // Optional animateCss
    if (array_key_exists('animateCss', $extconf) && $extconf['animateCss'] > '0' && !ExtensionManagementUtility::isLoaded('content_animations')) {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.animateCss = '.$extconf['animateCss']);
    }
    // Optional select-field for a .container or .container-fluid class in any content element
    if (array_key_exists('container', $extconf) && $extconf['container'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.container = 1');
    }
    // Optional select-fields for margin and padding in any content element
    if (array_key_exists('spacing', $extconf) && $extconf['spacing'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.spacing = 1');
    }
    // Optional "Bootstrap color palette"
    if (array_key_exists('color', $extconf) && $extconf['color'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.color = 1');
    }
    // Optional "cType in class"
    if (array_key_exists('cTypeClass', $extconf) && $extconf['cTypeClass'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cTypeClass = 1');
    }
    // Optional "custom scss"
    if (array_key_exists('customScss', $extconf) && $extconf['customScss'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customScss = 1');
        // Optional "bootswatch theme"
        if (array_key_exists('bootswatch', $extconf) && $extconf['bootswatch'] !== 'none') {
            ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.bootswatch = '.$extconf['bootswatch']);
        }
        // Edit in BE
        if (array_key_exists('editScss', $extconf) && $extconf['editScss'] === '1') {
            ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.editScss = 1');
        }
        if (array_key_exists('keepVariables', $extconf) && $extconf['keepVariables'] === '1') {
            ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.keepVariables = 1');
        }
    }

    // Optional "custom section menu order"
    if (array_key_exists('sectionOrder', $extconf) && $extconf['sectionOrder'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sectionOrder = tx_t3sbootstrap_sectionOrder');
    } else {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sectionOrder = sorting');
    }
    /***************
     * Override preview of tt_content elements in page module
     */
    if (array_key_exists('preview', $extconf) && $extconf['preview'] === '1') {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.preview = 1');
        $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Resources/Public/Backend/Style/bestyles.css';
    } else {
        ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.preview = 0');
    }


    # TYPO3 branch
    $branch = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch();

    if ( $branch === '12.4' ) {
    
        /***************
        * Add RootLine Fields: keywords & description
        */
        $rootlinefields = &$GLOBALS["TYPO3_CONF_VARS"]["FE"]["addRootLineFields"];
        if ($rootlinefields != '') {
            $rootlinefields .= ' , ';
        }
        $rootlinefields .= 'keywords,description';
    }

    /***************
     * Parser
     */
    // Register css processing parser
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'][ScssParser::class] = ScssParser::class;

    if (array_key_exists('customScss', $extconf) && $extconf['customScss'] === '1') {
        // Register css processing hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][PreProcessHook::class] =
         PreProcessHook::class . '->execute';
    }


})();
