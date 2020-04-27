<?php
namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ConfigProcessor implements DataProcessorInterface
{
	/**
	 * @var ContentDataProcessor
	 */
	protected $contentDataProcessor;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->contentDataProcessor = GeneralUtility::makeInstance(ContentDataProcessor::class);
	}


	/**
	 * Fetches records from the database as an array
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 *
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
	{

		$frontendController = self::getFrontendController();

		// the table to query
		$tableName = 'tx_t3sbootstrap_domain_model_config';
		$processorConfiguration['pidInList'] = $frontendController->id;

		// execute a SQL statement to fetch the records from current page
		$records = $cObj->getRecords($tableName, $processorConfiguration);

		$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$processedData['data']['uid'])->get();

		if ( empty($records) ) {

			if ( $processorConfiguration['rootline'] ) {
				// config from rootline
				// unset current page
				$rlA = $rootLineArray;
				unset($rlA[count($rlA)-1]);

				foreach ($rlA as $rootline) {
					$processorConfiguration['pidInList'] = $rootline['uid'];
					$records = $cObj->getRecords($tableName, $processorConfiguration);

					if ( !empty($records) ) break;
				}
			} else {
				// config from root page
				if ( $processedData['data']['is_siteroot'] ) {
					$rootPageUid = $processedData['data']['uid'];
				} else {
					$rootPageUid = $rootLineArray[0]['uid'];
				}
				$processorConfiguration['pidInList'] = $rootPageUid;
				$records = $cObj->getRecords($tableName, $processorConfiguration);
			}
 		}

 		if ( empty($records) ) {

 			$processedData['noConfig'] = TRUE;

 			return $processedData;

 		} else {

			$cObj->start($records[0], $tableName);
			$processedRecordVariables = $this->contentDataProcessor->process($cObj, $processorConfiguration, $records[0]);
		}

		// override config by TS
		if ( $contentObjectConfiguration['settings.']['configOverride'] ) {
			foreach ( $contentObjectConfiguration['settings.']['override.'] as $key=>$override ) {


				if ( ($override || $override === '0' || $override === 'false')	&& $override[1] != '$' ) {

					if ( $override === 'none' ) {
						$processedRecordVariables[$key] = '';
					} else {
						$processedRecordVariables[$key] = $override;
					}
				}
			}
		}

#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($processedRecordVariables, '$processedRecordVariables');

		/**
		 * General
		 */
		// company w/ multilingual support
		$company = $processedRecordVariables['company'];
		$companyArr = GeneralUtility::trimExplode('|', $company);
		$sysLanguageUid = $processedData['data']['sys_language_uid'];
		if ( $sysLanguageUid && $company ) {
			$company = $companyArr[$sysLanguageUid] ?: $company;
		} else {
			$company = $companyArr[0] ?: $company;
		}
		$processedData['config']['general']['company'] = trim($company);
		$processedData['config']['general']['homepageUid'] = $processedRecordVariables['homepage_uid'] ?: 1;
		$processedData['config']['general']['pageTitle'] = $processedRecordVariables['page_title'] ?: '';
		$processedData['config']['general']['pageTitlealign'] = $processedRecordVariables['page_titlealign'] ?: '';
		$processedData['config']['general']['pageTitleclass'] = $processedRecordVariables['page_titleclass'] ?: '';

		// flexible small columns
		$currentPage = $frontendController->page;
		$smallColumnsCurrent = (int)$currentPage['tx_t3sbootstrap_smallColumns'];

		if (version_compare(TYPO3_branch, '10.0', '>=')) {
			$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
		} else {
			$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
		}

		$rootlinePage = $pageRepository->getPage($frontendController->rootLine[0]['uid']);

		$smallColumnsRootline = (int)$rootlinePage['tx_t3sbootstrap_smallColumns'];
		$smallColumns = $smallColumnsCurrent ?: $smallColumnsRootline;

		if ( $contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns'] ) {
			if ( GeneralUtility::inList('1,2,3,4,6', $contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns']) ) {
				$smallColumns = $contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns'];
			} else {
				$smallColumns = 3;
			}
		}

		$processedData['colAside'] = $smallColumns;

		if ($currentPage['backend_layout']) {
			$threeCol = $currentPage['backend_layout'] == 'pagets__ThreeCol' ? TRUE : FALSE;
		} else {

			foreach ($rootLineArray as $subPage) {
				$bel = $subPage['backend_layout_next_level'];
				if ( !empty($subPage['backend_layout_next_level']) ) break;
			}
			$threeCol = $bel == 'pagets__ThreeCol' ? TRUE : FALSE;
		}

		switch ( $processedData['colAside'] ) {
			 case 1:
				$processedData['colMain'] = $threeCol ? 10 : 11;
			break;
			 case 2:
				$processedData['colMain'] = $threeCol ? 8 : 10;
			break;
			 case 3:
				$processedData['colMain'] = $threeCol ? 6 : 9;
			break;
			 case 4:
				$processedData['colMain'] = $threeCol ? 4 : 8;
			break;
			 case 6:
				$processedData['colMain'] = $threeCol ? 0 : 6;
			break;
				  default:
				$processedData['colMain'] = 9;
		}

		// image from pages media
		if ( $processedRecordVariables['pagemedia'] ) {
			$processedData['pagemedia'] = $processedRecordVariables['pagemedia'];
		}

		// grid breakpoint
		$processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';

		if ( $contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint'] ) {
			if ( GeneralUtility::inList('sm,md,lg,xl', $contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint']) ) {
				$processedData['gridBreakpoint'] = $contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint'];
			} else {
				$processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';
			}
		}

		/**
		 * Language Navigation
		 */

		$site = $this->getCurrentSite();

		foreach ($site->getLanguages() as $lang ) {
			$langUid[$lang->getLanguageId()] = $lang->getLanguageId();
			$langTitle[$lang->getLanguageId()] = $lang->getNavigationTitle();
			$langHref[$lang->getLanguageId()] = $lang->getHreflang();
			$langFlag[$lang->getLanguageId()] = $lang->getFlagIdentifier();
		}

		$processedData['config']['lang']['uid'] = $langUid ?: '';
		$processedData['config']['lang']['hreflang'] = $langHref ?: '';
		$processedData['config']['lang']['title'] = $langTitle ?: '';
		$processedData['config']['lang']['flag'] = $langFlag ?: '';

		/**
		 * Meta Navigation
		 */
		if ( $processedRecordVariables['meta_enable'] ) {
			$processedData['config']['meta']['align'] = $processedRecordVariables['meta_enable'];
			$processedData['config']['meta']['container'] = $processedRecordVariables['meta_container'] ? ' '.$processedRecordVariables['meta_container'] : '';
			$metaClass = $processedRecordVariables['meta_class'] ?: '';
			$processedData['config']['meta']['class'] = ' '.trim($metaClass);
			$processedData['config']['meta']['text'] = trim($processedRecordVariables['meta_text']);
		}

		/**
		 * Navbar
		 */
		if ( $processedRecordVariables['navbar_enable'] ) {

			switch ( $contentObjectConfiguration['settings.']['navbar.']['dropdownAnimate'] ) {
				 case 1:
				 	# & css
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-1 slideIn';
				break;
				 case 2:
				 	# & inlineJS.ts - 19
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-2';
				break;
				 case 3:
				 	# & css
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-3';
				break;
				 case 4:
				 	# & css
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-4';
				break;
					  default:
					$processedData['config']['navbar']['dropdownAnimate'] = '';
			}

			$processedData['config']['navbar']['enable'] = $processedRecordVariables['navbar_enable'];
			$processedData['config']['navbar']['sectionMenu'] = $processedRecordVariables['navbar_sectionmenu'] ? ' section-menu' : '';
			$processedData['config']['navbar']['brand'] = $processedRecordVariables['navbar_brand'];
			if ( $rootLineArray[1]['doktype'] == 4) {
				$processedData['config']['navbar']['clickableparent'] = 1;
			} else {
				$processedData['config']['navbar']['clickableparent'] = $processedRecordVariables['navbar_clickableparent'];
			}
			$processedData['config']['navbar']['image'] = $processedRecordVariables['navbar_image']
			? $processedRecordVariables['navbar_image']	: $contentObjectConfiguration['settings.']['navbar.']['image.']['defaultPath'];
			$processedData['config']['navbar']['toggler'] = $processedRecordVariables['navbar_toggler'];

			if ( $processedRecordVariables['navbar_container'] == 'none' ) {
				$processedData['config']['navbar']['container'] = '';
			} else {
				if ( $processedRecordVariables['navbar_container'] == 'fluid' ) {
					$processedData['config']['navbar']['container'] = 'container-fluid';
				} else {
					$processedData['config']['navbar']['containerposition'] = $processedRecordVariables['navbar_container'];
					$processedData['config']['navbar']['container'] = 'container';
				}
			}
			$navbarClass = 'navbar-'.$processedRecordVariables['navbar_enable'];
			$navbarClass .= $processedRecordVariables['navbar_breakpoint'] ? ' navbar-expand-'.$processedRecordVariables['navbar_breakpoint'] : ' navbar-expand-sm';
			// navbar breakpoint
			$processedData['navbarBreakpoint'] = $processedRecordVariables['navbar_breakpoint'] ?: 'md';
			if ( $processedRecordVariables['navbar_placement'] == 'fixed-top' && $processedRecordVariables['navbar_shrinkcolor'] ) {
				$navbarClass .= ' shrink py-'.$contentObjectConfiguration['settings.']['shrinkingNavPadding'];
			}
			$processedData['config']['navbar']['breakpoint'] = $processedRecordVariables['navbar_breakpoint'];

			$navbarClass .= $processedRecordVariables['navbar_class'] ? ' '.$processedRecordVariables['navbar_class'] : '';

			if ( $processedRecordVariables['navbar_color'] == 'color' ) {
				if ( $processedRecordVariables['navbar_background'] ) {
					$navbarStyle = 'background-color: '.$processedRecordVariables['navbar_background'].';';
					$processedData['config']['navbar']['style'] = $navbarStyle;
				} else {
					$processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbar_shrinkcolorschemes'];
					$processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbar_color'];
				}
			} else {
				$navbarClass .= ' bg-'.$processedRecordVariables['navbar_color'];
				$processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbar_shrinkcolorschemes'];
				$processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbar_color'];
			}

			if ( ($processedRecordVariables['navbar_placement'] == 'fixed-top' && $processedRecordVariables['navbar_shrinkcolor'])
			 && ($processedRecordVariables['navbar_enable'] == 'light' || $processedRecordVariables['navbar_enable'] == 'dark') ) {
				$processedData['config']['navbar']['shrinkColor'] = 'navbar-'.$processedRecordVariables['navbar_shrinkcolor'];
				$processedData['config']['navbar']['color'] = 'navbar-'.$processedRecordVariables['navbar_enable'];
			}

			if ($processedRecordVariables['navbar_placement']) {
				if ( $processedData['config']['navbar']['containerposition'] == 'outside' ) {
					$processedData['config']['navbar']['container'] =
					trim($processedData['config']['navbar']['container'].' '.$processedRecordVariables['navbar_placement']);
				} else {
					$navbarClass = $navbarClass.' '.$processedRecordVariables['navbar_placement'];
				}
			}

			$processedData['config']['navbar']['class'] = trim($navbarClass);
			$dropdown = $processedRecordVariables['navbar_placement'] == 'fixed-bottom' ? 'dropup' : 'dropdown';
			$processedData['config']['navbar']['dropdown'] = $dropdown;
			$processedData['config']['navbar']['spacer'] = $processedRecordVariables['navbar_includespacer'];
			$processedData['config']['navbar']['megamenu'] = $processedRecordVariables['navbar_megamenu'];
			$processedData['config']['navbar']['placement'] = $processedRecordVariables['navbar_placement'];
			$processedData['config']['navbar']['sticky'] = $processedRecordVariables['navbar_placement'] == 'sticky-top' ? TRUE : FALSE;
			$processedData['config']['navbar']['alignment'] = $processedRecordVariables['navbar_alignment'];
			$processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbar_alignment'] == 'right') ? ' ml-auto': '';
			if ( $processorConfiguration['navbarExtraRow'] ) {
				$processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbar_alignment'] == 'right') ? ' ml-auto': ' mr-auto';
			}
			$processedData['config']['navbar']['justify'] = $processedRecordVariables['navbar_justify'] ? ' nav-fill w-100' : '';
			$processedData['config']['navbar']['offcanvas'] = $processedRecordVariables['navbar_offcanvas'];
			if ( $processedRecordVariables['navbar_searchbox'] ) {
				$processedData['config']['navbar']['searchbox'] = $processedRecordVariables['navbar_searchbox'];
				$processedData['config']['navbar']['searchboxcolor'] = $processedRecordVariables['navbar_enable'] == 'light' ? 'dark' : 'light';
			}
		}

		/**
		 * Jumbotron
		 */
		if ( $processedRecordVariables['jumbotron_enable'] ) {
			$processedData['config']['jumbotron']['enable'] = $processedRecordVariables['jumbotron_enable'];
			$processedData['config']['jumbotron']['fluid'] = $processedRecordVariables['jumbotron_fluid'];
			$processedData['config']['jumbotron']['slide'] = $processedRecordVariables['jumbotron_slide'];
			$processedData['config']['jumbotron']['position'] = $processedRecordVariables['jumbotron_position'];
			$processedData['config']['jumbotron']['container'] = $processedRecordVariables['jumbotron_container'];
			$processedData['config']['jumbotron']['containerposition'] = $processedRecordVariables['jumbotron_containerposition'];

			$jumbotronClass = $processedRecordVariables['jumbotron_class'] ?: '';
			$jumbotronClass .= $processedRecordVariables['jumbotron_fluid'] ? ' jumbotron-fluid' : '';
			$processedData['config']['jumbotron']['class'] = ' '.trim($jumbotronClass);

			# Image from pages media
			$fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
			$fileObjects = [];
			if ( $processedRecordVariables['jumbotron_bgimage'] == 'root' ) {
				$bgImage = $this->getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', TRUE);
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				if ( empty($bgImage) ) {
					$currentUid = $rootLineArray[count($rootLineArray)-1]['uid'];
					foreach ($rootLineArray as $page) {
						$bgImage = $this->getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', TRUE, FALSE, [], FALSE, $currentUid);
						$fileObjects = $fileRepository->findByRelation('pages', 'media', $page['uid']);
						if ($bgImage) break;
					}
				}
				if ($bgImage)
				$processedData['config']['jumbotron']['bgImage'] = $bgImage;
			} elseif ( $processedRecordVariables['jumbotron_bgimage'] == 'page' ) {
				$bgImage = $this->getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', TRUE);
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				if ($bgImage)
				$processedData['config']['jumbotron']['bgImage'] = $bgImage;
			}


			if ( count($fileObjects) > 1 ) {
				$processedData['bgSlides'] = $fileObjects;
			}
		}

		/**
		 * Background Image (body)
		 */
		if ( $contentObjectConfiguration['settings.']['backgroundImageEnable'] ) {

			$bgImage = $this->getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', FALSE, FALSE, [], TRUE);

			if ( empty($bgImage) && $contentObjectConfiguration['settings.']['backgroundImageSlide'] ) {
				foreach ($rootLineArray as $page) {
					$bgImage = $this->getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', FALSE, FALSE, [], TRUE);
					if ($bgImage) break;
				}
			}
		}

		/**
		 * Breadcrumb
		 */
		$processedData['config']['breadcrumb']['class'] = '';
		if ( $processedRecordVariables['breadcrumb_enable'] || $processedRecordVariables['breadcrumb_bottom'] ) {
			if ( ($processedRecordVariables['homepage_uid'] == $frontendController->id) && $processedRecordVariables['breadcrumb_notonrootpage'] ) {
				$processedData['config']['breadcrumb']['enable'] = FALSE;
				$processedData['config']['breadcrumb']['bottom'] = FALSE;
			} else {
				$processedData['config']['breadcrumb']['enable'] = $processedRecordVariables['breadcrumb_enable'];
				$processedData['config']['breadcrumb']['bottom'] = $processedRecordVariables['breadcrumb_bottom'];

				$processedData['config']['breadcrumb']['faicon'] = $processedRecordVariables['breadcrumb_faicon'];
				$processedData['config']['breadcrumb']['position'] = $processedRecordVariables['breadcrumb_position'];
				$processedData['config']['breadcrumb']['container'] = $processedRecordVariables['breadcrumb_container'];
				$processedData['config']['breadcrumb']['containerposition'] = $processedRecordVariables['breadcrumb_containerposition'];
				$processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumb_class']
				? ' '.$processedRecordVariables['breadcrumb_class'] : '';
				$processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumb_corner'] ? ' rounded-0': '';
			}
		}

		/**
		 * Sidebar / submenu
		 */
		if ( $processedRecordVariables['sidebar_enable'] ) {
			$processedData['config']['sidebar']['left'] = $processedRecordVariables['sidebar_enable'];
		}
		if ( $processedRecordVariables['sidebar_rightenable'] ) {
			$processedData['config']['sidebar']['right'] = $processedRecordVariables['sidebar_rightenable'];
		}

		/**
		 * Footer
		 */
		if ( $processedRecordVariables['footer_enable'] ) {

			$processedData['config']['footer']['enable'] = $processedRecordVariables['footer_enable'];
			$processedData['config']['footer']['sticky'] = $processedRecordVariables['footer_sticky'];
			$processedData['config']['footer']['fluid'] = $processedRecordVariables['footer_fluid'];
			$processedData['config']['footer']['slide'] = $processedRecordVariables['footer_slide'];
			$processedData['config']['footer']['container'] = $processedRecordVariables['footer_container'];
			$processedData['config']['footer']['containerposition'] = $processedRecordVariables['footer_containerposition'];

			$footerClass = $processedRecordVariables['footer_class'] ?: '';
			$footerClass .= $processedRecordVariables['footer_fluid'] ? ' jumbotron-fluid' : '';
			$footerClass .= $processedRecordVariables['footer_sticky'] ? ' footer-sticky' : '';
			$processedData['config']['footer']['class'] = trim($footerClass);

		}

		/**
		 * Expandedcontent Top & Bottom
		 */
		if ( $processedRecordVariables['expandedcontent_enabletop'] ) {
			$processedData['config']['expandedcontentTop']['enable'] = $processedRecordVariables['expandedcontent_enabletop'];
			$processedData['config']['expandedcontentTop']['slide'] = $processedRecordVariables['expandedcontent_slidetop'];
			$processedData['config']['expandedcontentTop']['container'] = $processedRecordVariables['expandedcontent_containertop'];
			$processedData['config']['expandedcontentTop']['containerposition'] = $processedRecordVariables['expandedcontent_containerpositiontop'];
			$processedData['config']['expandedcontentTop']['class'] = $processedRecordVariables['expandedcontent_classtop'];
		}
		if ( $processedRecordVariables['expandedcontent_enablebottom'] ) {
			$processedData['config']['expandedcontentBottom']['enable'] = $processedRecordVariables['expandedcontent_enablebottom'];
			$processedData['config']['expandedcontentBottom']['slide'] = $processedRecordVariables['expandedcontent_slidebottom'];
			$processedData['config']['expandedcontentBottom']['container'] = $processedRecordVariables['expandedcontent_containerbottom'];
			$processedData['config']['expandedcontentBottom']['containerposition'] = $processedRecordVariables['expandedcontent_containerpositionbottom'];
			$processedData['config']['expandedcontentBottom']['class'] = $processedRecordVariables['expandedcontent_classbottom'];

		}

		/**
		 * CSS & JS
		 */
		$animateCSS = false;
		$repeatCSS = false;
		$navSlide = $processedData['config']['navbar']['enable'] == 'slide' ? true : false;

		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		# ... if needed only
		if ( $extConf['animateCss'] == 1 ) {
			$processorConfiguration['pidInList'] = $frontendController->id;
			$contents = $cObj->getRecords('tt_content', $processorConfiguration);
			$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
			foreach ($contents as $content) {
				$flexconf = $flexFormService->convertFlexFormContentToArray($content['tx_t3sbootstrap_flexform']);
				// carousel container w/ animated captions
				if ( $flexconf['animate'] && !$animateCSS ) {
					$animateCSS = true;
				}
				if ( $content['tx_t3sbootstrap_animateCss'] && !$animateCSS ) {
					$animateCSS = true;
				}
				if ( $content['tx_t3sbootstrap_animateCssRepeat'] && !$repeatCSS ) {
					$repeatCSS = true;
				}
			}
		}

		if ( $navSlide || $animateCSS ) {

			$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

			if ( $navSlide ) {
				$cssFile = 'EXT:t3sbootstrap/Resources/Public/Styles/slideNavbar.css';
				$cssFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
				$jsFooterFile = 'EXT:t3sbootstrap/Resources/Public/Scripts/slideNavbar.js';
				$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);
				$pageRenderer->addCssFile($cssFile);
				$pageRenderer->addJsFooterFile($jsFooterFile);
			}
			if ( $animateCSS ) {
				if ($processorConfiguration['cdnEnable']) {
					$cssFile = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/'.$processorConfiguration['cdnAnimate'].'/animate.min.css';
					$pageRenderer->addCssFile($cssFile);
				} else {
					$cssFile = 'fileadmin/T3SB/Resources/Public/CSS/animate.min.css';
					$cssFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
					$pageRenderer->addCssFile($cssFile);
				}
			}
			if ( $repeatCSS ) {

				if ($processorConfiguration['cdnEnable']) {
					$jsFooterFile = 'https://cdnjs.cloudflare.com/ajax/libs/jQuery-viewport-checker/'.$processorConfiguration['cdnViewportchecker'].'/jquery.viewportchecker.min.js';
					$pageRenderer->addJsFooterFile($jsFooterFile);
				} else {
					$jsFooterFile = 'fileadmin/T3SB/Resources/Public/JS/jquery.viewportchecker.min.js';
					$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);
					$pageRenderer->addJsFooterFile($jsFooterFile);
				}

				$animateCssInlineJs = $processorConfiguration['animateCssInlineJs'] ?: "classToAdd: 'bt_visible',classToRemove: 'bt_hidden',offset: 0";
				if ( $animateCssInlineJs ) {
					$inlineJS = 'jQuery(function(){$( \'.animated\' ).each(function() {$(this).viewportChecker({'.$processorConfiguration['animateCssInlineJs'].'});});});';
					$pageRenderer->addJsFooterInlineCode(' Viewport checker ',$inlineJS,'FALSE');
				}
			}
		}


		return $processedData;
	}


	/**
	 * Returns the currently configured "site" if a site is configured (= resolved) in the current request.
	 *
	 * @return SiteInterface
	 */
	protected function getCurrentSite(): SiteInterface
	{
		$matcher = GeneralUtility::makeInstance(SiteMatcher::class);
		return $matcher->matchByPageId((int)$this->getFrontendController()->id);
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}


	/**
	 * Returns an instance of the rbackground image utility
	 *
	 * @return BackgroundImageUtility
	 */
	protected function getBackgroundImageUtility()
	{
		return GeneralUtility::makeInstance(BackgroundImageUtility::class);
	}


}
