<?php
defined('TYPO3') || die();

(function () {

	/***************
	 * Register Icons
	 */
	if (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getMajorVersion() < 11) {
		
		if ( $GLOBALS['TYPO3_REQUEST'] ?? null && \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend() ) {
	
			$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
	
			// FontawesomeIconProvider
			$iconRegistry->registerIcon(
				'buttongroup',
				\TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
				['name' => 'bars']
			);
			unset($iconRegistry);
		}
	}

	/***************
	 * TsConfig
	 */
	 # Page
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:t3sbootstrap/Configuration/TSConfig/Page.tsconfig'");
	# CKEditor
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:t3sbootstrap/Configuration/TSConfig/CKEditor.tsconfig'");

	/***************
	 * Default Constants
	 */
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.form.ajax = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.typoscriptRendering = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.indexedsearch = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.news = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.codesnippet = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.linkHoverEffect = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomepagetitle = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomeCss = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cookieconsent = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.lazyLoad = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.animateCss = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.container = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.spacing = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.color = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cTypeClass = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customScss = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.editScss = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.keepVariables = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.expandedContent = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.bootswatch = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customSectionOrder = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.webp = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbconcatenate = 0');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbminify = 0');

	/***************
	 * Extension configuration
	 */
	$extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('t3sbootstrap');

	if (array_key_exists('contentconsent', $extconf) && $extconf['contentconsent'] === '1') {

		/***************
		 * plugin content consent
		 */
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
			'T3sbootstrap',
			'Pi1',
			[
				\T3SBS\T3sbootstrap\Controller\ConsentController::class => 'index',
			],
			// non-cacheable actions
			[
				\T3SBS\T3sbootstrap\Controller\ConsentController::class => '',
			]
		);
	}

	/***************
	 * Other Extensions
	 */
	# if typoscript_rendering is loaded
	if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('typoscript_rendering') ) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.typoscriptRendering = 1');
	}

	# if indexed_search is loaded
	if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('indexed_search') ) {
		 # Setup
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('t3sbootstrap',
			'setup','@import "EXT:t3sbootstrap/Resources/Private/Extensions/indexed_search/Configuration/TypoScript/setup.typoscript"','defaultContentRendering'
		);
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.indexedsearch = 1');
	}
	 # if news is loaded
	if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news') && array_key_exists('extNews', $extconf) && $extconf['extNews'] === '1' ) {
	 	# TsConfig
	 	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import "EXT:t3sbootstrap/Resources/Private/Extensions/news/Configuration/TSconfig/templateLayouts.tsconfig"');
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('t3sbootstrap',
				 'setup','@import "EXT:t3sbootstrap/Resources/Private/Extensions/news/Configuration/TypoScript/setup.typoscript"','defaultContentRendering'
		);
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.news = 1');
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.ext.newsVersion = '.(int)\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion('news'));
		# Flexform Hook
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][] = \T3SBS\T3sbootstrap\Hooks\NewsFlexFormHook::class;
	}
	// Optional flexform extend
	if (array_key_exists('flexformExtend', $extconf) && $extconf['flexformExtend'] === '1') {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
		 = \T3SBS\T3sbootstrap\Hooks\FlexFormHook::class;
	}
	// Optional modify flexform fields
	if (array_key_exists('flexformModify', $extconf) && $extconf['flexformModify'] === '1') {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\T3SBS\T3sbootstrap\Backend\FormDataProvider\FlexFormManipulation::class] = [
				 'depends' => [
				  \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class,
				 ],
				 'before' => [
				  \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class,
				 ],
		];
	}
	// Optional custom translations
	if (array_key_exists('customTranslations', $extconf) && $extconf['customTranslations'] === '1') {
		$extPath = 'EXT:t3sbootstrap/Resources/Private/Language/';
		$ctPath = 'fileadmin/T3SB/Language/';

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$extPath . 'locallang.xlf'][] = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . $ctPath . 'locallang.xlf';
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$extPath . 'locallang_m1.xlf'][] = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . $ctPath . 'locallang_m1.xlf';
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$extPath . 'locallang_db.xlf'][] = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . $ctPath . 'locallang_db.xlf';
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$extPath . 'locallang_be.xlf'][] = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . $ctPath . 'locallang_be.xlf';
	}
	// Optional CKEditor plugin "Code Snippet"
	if (array_key_exists('codesnippet', $extconf) && $extconf['codesnippet'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.codesnippet = 1');
		// if rte_ckeditor_fontawesome is loaded
		if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rte_ckeditor_fontawesome') ) {

			if (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '2') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/CodesnippetFaPro.yaml';
			} elseif (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '3') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/CodesnippetFa6.yaml';
			} elseif (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '4') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/CodesnippetFa6Pro.yaml';
			} else {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/CodesnippetFa.yaml';
			}
		} else {
			$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/Codesnippet.yaml';
		}
	} else {
		// if rte_ckeditor_fontawesome is loaded
		if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rte_ckeditor_fontawesome') ) {
			if (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '2') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/DefaultFaPro.yaml';
			} elseif (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '3') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/DefaultFa6.yaml';
			} elseif (array_key_exists('fontawesomeCss', $extconf) && $extconf['fontawesomeCss'] === '4') {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/DefaultFa6Pro.yaml';
			} else {
				$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/DefaultFa.yaml';
			}
		} else {
			$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['t3sbootstrap'] = 'EXT:t3sbootstrap/Configuration/RTE/Default.yaml';
		}
	}
	// Optional fontawesomeCss
	$fontawesomeCss = (int)$extconf['fontawesomeCss'];
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomeCss = '.$fontawesomeCss.'');
	// Optional Hover Link Effect (FAL)
	if (array_key_exists('linkHoverEffect', $extconf) && $extconf['linkHoverEffect'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.linkHoverEffect = 1');
	}
	// Optional Copyright notice (FAL)
	if (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 1');
	} elseif (array_key_exists('imgCopyright', $extconf) && $extconf['imgCopyright'] === '2') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.imgCopyright = 2');
	}
	// Optional concatenate CSS & JS files in asset collector
	if (array_key_exists('t3sbconcatenate', $extconf) && $extconf['t3sbconcatenate'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbconcatenate = 1');
	}
	// Optional minify CSS & JS with toptal.com
	if (array_key_exists('t3sbminify', $extconf) && $extconf['t3sbminify'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.t3sbminify = 1');
	}
	// Optional fontawesomepagetitle
	if (array_key_exists('fontawesomepagetitle', $extconf) && $extconf['fontawesomepagetitle'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.fontawesomepagetitle = 1');
	}
	// Optional cookieconsent
	if (array_key_exists('cookieconsent', $extconf) && $extconf['cookieconsent'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cookieconsent = 1');
	}
	// Optional lazyLoad
	if (array_key_exists('lazyLoad', $extconf)) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.lazyLoad = '.$extconf['lazyLoad']);
	}
	// Optional animateCss
	if (array_key_exists('animateCss', $extconf) && $extconf['animateCss'] > '0') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.animateCss = '.$extconf['animateCss']);
	}
	// Optional select-field for a .container or .container-fluid class in any content element
	if (array_key_exists('container', $extconf) && $extconf['container'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.container = 1');
	}
	// Optional select-fields for margin and padding in any content element
	if (array_key_exists('spacing', $extconf) && $extconf['spacing'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.spacing = 1');
	}
	// Optional "Bootstrap color palette"
	if (array_key_exists('color', $extconf) && $extconf['color'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.color = 1');
	}
	// Optional "cType in class"
	if (array_key_exists('cTypeClass', $extconf) && $extconf['cTypeClass'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.cTypeClass = 1');
	}
	// Optional "custom scss"
	if (array_key_exists('customScss', $extconf) && $extconf['customScss'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.customScss = 1');
		// Optional "bootswatch theme"
		if (array_key_exists('bootswatch', $extconf) && $extconf['bootswatch'] !== 'none') {
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.bootswatch = '.$extconf['bootswatch']);
		}
		// Edit in BE
		if (array_key_exists('editScss', $extconf) && $extconf['editScss'] === '1') {
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.editScss = 1');
		}
		if (array_key_exists('keepVariables', $extconf) && $extconf['keepVariables'] === '1') {
			\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.keepVariables = 1');
		}
	}
	// Optional "expanded content"
	if (array_key_exists('expandedContent', $extconf) && $extconf['expandedContent'] === '1') {
		# expanded content on top and bottom
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:t3sbootstrap/Configuration/TSConfig/BackendLayouts/Expanded/_main.tsconfig'");
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.expandedContent = 1');
	} else {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("@import 'EXT:t3sbootstrap/Configuration/TSConfig/BackendLayouts/Default/_main.tsconfig'");
	}
	// Optional "custom section menu order"
	if (array_key_exists('sectionOrder', $extconf) && $extconf['sectionOrder'] === '1') {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sectionOrder = tx_t3sbootstrap_sectionOrder');
	} else {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.sectionOrder = sorting');
	}
	# if webp is loaded
	if ( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('webp') ) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('bootstrap.extconf.webp = 1');
	}

	/***************
	 * Override preview of tt_content elements in page module
	 */
	if (array_key_exists('preview', $extconf) && $extconf['preview'] === '1') {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['fluidBasedPageModule'] = true;
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['previewRendererResolver'] = \TYPO3\CMS\Backend\Preview\StandardPreviewRendererResolver::class;
	}

	/***************
	 * Add RootLine Fields: keywords & description
	 */
	$rootlinefields = &$GLOBALS["TYPO3_CONF_VARS"]["FE"]["addRootLineFields"];
	if($rootlinefields != '') $rootlinefields .= ' , ';
	$rootlinefields .= 'keywords,description';

	/***************
	 * Registering wizards
	 */
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['t3sbMigrateUpdateWizard'] = \T3SBS\T3sbootstrap\Updates\T3sbMigrateUpdateWizard::class;

	/***************
	 * Parser
	 */
	// Register css processing parser
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'][\T3SBS\T3sbootstrap\Parser\ScssParser::class] = \T3SBS\T3sbootstrap\Parser\ScssParser::class;
	if (array_key_exists('customScss', $extconf) && $extconf['customScss'] === '1') {
		// Register css processing hooks
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][\T3SBS\T3sbootstrap\Hooks\PageRenderer\PreProcessHook::class]
		 = \T3SBS\T3sbootstrap\Hooks\PageRenderer\PreProcessHook::class . '->execute';
	}

})();
