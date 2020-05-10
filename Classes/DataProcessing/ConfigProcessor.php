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
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\FileRepository;


class ConfigProcessor implements DataProcessorInterface
{

	/**
	 * The content object renderer
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObjectRenderer;

	/**
	 * The contentObject configuration
	 *
	 * @var array
	 */
	protected $contentObjectConfiguration;

	/**
	 * The processor configuration
	 *
	 * @var array
	 */
	protected $processorConfiguration;

	/**
	 * processed data
	 *
	 * @var array
	 */
	protected $processedData;

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

		$this->contentObjectRenderer = $cObj;
		$this->contentObjectConfiguration = $contentObjectConfiguration;
		$this->processorConfiguration = $processorConfiguration;
		$this->processedData = $processedData;

		$frontendController = self::getFrontendController();

		// the table to query
		$tableName = 'tx_t3sbootstrap_domain_model_config';
		$this->processorConfiguration['pidInList'] = $frontendController->id;

		// execute a SQL statement to fetch the records from current page
		$records = $this->contentObjectRenderer->getRecords($tableName, $this->processorConfiguration);

		$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$this->processedData['data']['uid'])->get();

		if ( empty($records) ) {

			if ( $this->processorConfiguration['rootline'] ) {
				// config from rootline
				// unset current page
				$rlA = $rootLineArray;
				unset($rlA[count($rlA)-1]);

				foreach ($rlA as $rootline) {
					$this->processorConfiguration['pidInList'] = $rootline['uid'];
					$records = $this->contentObjectRenderer->getRecords($tableName, $this->processorConfiguration);

					if ( !empty($records) ) break;
				}
			} else {
				// config from root page
				if ( $this->processedData['data']['is_siteroot'] ) {
					$rootPageUid = $this->processedData['data']['uid'];
				} else {
					$rootPageUid = $rootLineArray[0]['uid'];
				}
				$this->processorConfiguration['pidInList'] = $rootPageUid;
				$records = $this->contentObjectRenderer->getRecords($tableName, $this->processorConfiguration);
			}
 		}

 		if ( empty($records) ) {

 			$this->processedData['noConfig'] = TRUE;

 			return $this->processedData;

 		} else {

			$this->contentObjectRenderer->start($records[0], $tableName);
			$processedRecordVariables = $this->contentDataProcessor->process($this->contentObjectRenderer, $this->processorConfiguration, $records[0]);
		}

		// override config by TS
		if ( $this->contentObjectConfiguration['settings.']['configOverride'] ) {
			foreach ( $this->contentObjectConfiguration['settings.']['override.'] as $key=>$override ) {


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
		$sysLanguageUid = $this->processedData['data']['sys_language_uid'];
		if ( $sysLanguageUid && $company ) {
			$company = $companyArr[$sysLanguageUid] ?: $company;
		} else {
			$company = $companyArr[0] ?: $company;
		}
		$this->processedData['config']['general']['company'] = trim($company);
		$this->processedData['config']['general']['homepageUid'] = $processedRecordVariables['homepage_uid'] ?: 1;
		$this->processedData['config']['general']['pageTitle'] = $processedRecordVariables['page_title'] ?: '';
		$this->processedData['config']['general']['pageTitlealign'] = $processedRecordVariables['page_titlealign'] ?: '';
		$this->processedData['config']['general']['pageTitleclass'] = $processedRecordVariables['page_titleclass'] ?: '';

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

		if ( $this->contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns'] ) {
			if ( GeneralUtility::inList('1,2,3,4,6', $this->contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns']) ) {
				$smallColumns = $this->contentObjectConfiguration['settings.']['pages.']['override.']['smallColumns'];
			} else {
				$smallColumns = 3;
			}
		}

		$this->processedData['colAside'] = $smallColumns;

		if ($currentPage['backend_layout']) {
			$threeCol = $currentPage['backend_layout'] == 'pagets__ThreeCol' ? TRUE : FALSE;
		} else {

			foreach ($rootLineArray as $subPage) {
				$bel = $subPage['backend_layout_next_level'];
				if ( !empty($subPage['backend_layout_next_level']) ) break;
			}
			$threeCol = $bel == 'pagets__ThreeCol' ? TRUE : FALSE;
		}

		switch ( $this->processedData['colAside'] ) {
			 case 1:
				$this->processedData['colMain'] = $threeCol ? 10 : 11;
			break;
			 case 2:
				$this->processedData['colMain'] = $threeCol ? 8 : 10;
			break;
			 case 3:
				$this->processedData['colMain'] = $threeCol ? 6 : 9;
			break;
			 case 4:
				$this->processedData['colMain'] = $threeCol ? 4 : 8;
			break;
			 case 6:
				$this->processedData['colMain'] = $threeCol ? 0 : 6;
			break;
				  default:
				$this->processedData['colMain'] = 9;
		}

		// image from pages media
		if ( $processedRecordVariables['pagemedia'] ) {
			$this->processedData['pagemedia'] = $processedRecordVariables['pagemedia'];
		}

		// grid breakpoint
		$this->processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';

		if ( $this->contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint'] ) {
			if ( GeneralUtility::inList('sm,md,lg,xl', $this->contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint']) ) {
				$this->processedData['gridBreakpoint'] = $this->contentObjectConfiguration['settings.']['pages.']['override.']['breakpoint'];
			} else {
				$this->processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';
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

		$this->processedData['config']['lang']['uid'] = $langUid ?: '';
		$this->processedData['config']['lang']['hreflang'] = $langHref ?: '';
		$this->processedData['config']['lang']['title'] = $langTitle ?: '';
		$this->processedData['config']['lang']['flag'] = $langFlag ?: '';

		/**
		 * Meta Navigation
		 */
		if ( $processedRecordVariables['meta_enable'] ) {
			$this->processedData['config']['meta']['align'] = $processedRecordVariables['meta_enable'];
			$this->processedData['config']['meta']['container'] = $processedRecordVariables['meta_container'] ? ' '.$processedRecordVariables['meta_container'] : '';
			$metaClass = $processedRecordVariables['meta_class'] ?: '';
			$this->processedData['config']['meta']['class'] = ' '.trim($metaClass);
			$this->processedData['config']['meta']['text'] = trim($processedRecordVariables['meta_text']);
		}

		/**
		 * Navbar
		 */
		if ( $processedRecordVariables['navbar_enable'] ) {

			switch ( $this->contentObjectConfiguration['settings.']['navbar.']['dropdownAnimate'] ) {
				 case 1:
				 	# & css
					$this->processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-1 slideIn';
				break;
				 case 2:
				 	# & inlineJS.ts - 19
					$this->processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-2';
				break;
				 case 3:
				 	# & css
					$this->processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-3';
				break;
				 case 4:
				 	# & css
					$this->processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-4';
				break;
					  default:
					$this->processedData['config']['navbar']['dropdownAnimate'] = '';
			}

			$this->processedData['config']['navbar']['enable'] = $processedRecordVariables['navbar_enable'];
			$this->processedData['config']['navbar']['sectionMenu'] = $processedRecordVariables['navbar_sectionmenu'] ? ' section-menu' : '';
			$this->processedData['config']['navbar']['brand'] = $processedRecordVariables['navbar_brand'];
			if ( $rootLineArray[1]['doktype'] == 4) {
				$this->processedData['config']['navbar']['clickableparent'] = 1;
			} else {
				$this->processedData['config']['navbar']['clickableparent'] = $processedRecordVariables['navbar_clickableparent'];
			}
			$this->processedData['config']['navbar']['image'] = $processedRecordVariables['navbar_image']
			? $processedRecordVariables['navbar_image']	: $this->contentObjectConfiguration['settings.']['navbar.']['image.']['defaultPath'];
			$this->processedData['config']['navbar']['toggler'] = $processedRecordVariables['navbar_toggler'];

			if ( $processedRecordVariables['navbar_container'] == 'none' ) {
				$this->processedData['config']['navbar']['container'] = '';
			} else {
				if ( $processedRecordVariables['navbar_container'] == 'fluid' ) {
					$this->processedData['config']['navbar']['container'] = 'container-fluid';
				} else {
					$this->processedData['config']['navbar']['containerposition'] = $processedRecordVariables['navbar_container'];
					$this->processedData['config']['navbar']['container'] = 'container';
				}
			}
			$navbarClass = 'navbar-'.$processedRecordVariables['navbar_enable'];
			$navbarClass .= $processedRecordVariables['navbar_breakpoint'] ? ' navbar-expand-'.$processedRecordVariables['navbar_breakpoint'] : ' navbar-expand-sm';
			// navbar breakpoint
			$this->processedData['navbarBreakpoint'] = $processedRecordVariables['navbar_breakpoint'] ?: 'md';
			if ( $processedRecordVariables['navbar_placement'] == 'fixed-top' && $processedRecordVariables['navbar_shrinkcolor'] ) {
				$navbarClass .= ' shrink py-'.$this->contentObjectConfiguration['settings.']['shrinkingNavPadding'];
			}
			$this->processedData['config']['navbar']['breakpoint'] = $processedRecordVariables['navbar_breakpoint'];

			$navbarClass .= $processedRecordVariables['navbar_class'] ? ' '.$processedRecordVariables['navbar_class'] : '';

			if ( $processedRecordVariables['navbar_color'] == 'color' ) {
				if ( $processedRecordVariables['navbar_background'] ) {
					$navbarStyle = 'background-color: '.$processedRecordVariables['navbar_background'].';';
					$this->processedData['config']['navbar']['style'] = $navbarStyle;
				} else {
					$this->processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbar_shrinkcolorschemes'];
					$this->processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbar_color'];
				}
			} else {
				$navbarClass .= ' bg-'.$processedRecordVariables['navbar_color'];
				$this->processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbar_shrinkcolorschemes'];
				$this->processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbar_color'];
			}

			if ( ($processedRecordVariables['navbar_placement'] == 'fixed-top' && $processedRecordVariables['navbar_shrinkcolor'])
			 && ($processedRecordVariables['navbar_enable'] == 'light' || $processedRecordVariables['navbar_enable'] == 'dark') ) {
				$this->processedData['config']['navbar']['shrinkColor'] = 'navbar-'.$processedRecordVariables['navbar_shrinkcolor'];
				$this->processedData['config']['navbar']['color'] = 'navbar-'.$processedRecordVariables['navbar_enable'];
			}

			if ($processedRecordVariables['navbar_placement']) {
				if ( $this->processedData['config']['navbar']['containerposition'] == 'outside' ) {
					$this->processedData['config']['navbar']['container'] =
					trim($this->processedData['config']['navbar']['container'].' '.$processedRecordVariables['navbar_placement']);
				} else {
					$navbarClass = $navbarClass.' '.$processedRecordVariables['navbar_placement'];
				}
			}

			$this->processedData['config']['navbar']['class'] = trim($navbarClass);
			$dropdown = $processedRecordVariables['navbar_placement'] == 'fixed-bottom' ? 'dropup' : 'dropdown';
			$this->processedData['config']['navbar']['dropdown'] = $dropdown;
			$this->processedData['config']['navbar']['spacer'] = $processedRecordVariables['navbar_includespacer'];
			$this->processedData['config']['navbar']['megamenu'] = $processedRecordVariables['navbar_megamenu'];
			$this->processedData['config']['navbar']['placement'] = $processedRecordVariables['navbar_placement'];
			$this->processedData['config']['navbar']['sticky'] = $processedRecordVariables['navbar_placement'] == 'sticky-top' ? TRUE : FALSE;
			$this->processedData['config']['navbar']['alignment'] = $processedRecordVariables['navbar_alignment'];
			$this->processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbar_alignment'] == 'right') ? ' ml-auto': '';
			if ( $this->processorConfiguration['navbarExtraRow'] ) {
				$this->processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbar_alignment'] == 'right') ? ' ml-auto': ' mr-auto';
			}
			$this->processedData['config']['navbar']['justify'] = $processedRecordVariables['navbar_justify'] ? ' nav-fill w-100' : '';
			$this->processedData['config']['navbar']['offcanvas'] = $processedRecordVariables['navbar_offcanvas'];
			if ( $processedRecordVariables['navbar_searchbox'] ) {
				$this->processedData['config']['navbar']['searchbox'] = $processedRecordVariables['navbar_searchbox'];
				$this->processedData['config']['navbar']['searchboxcolor'] = $processedRecordVariables['navbar_enable'] == 'light' ? 'dark' : 'light';
			}
		}

		/**
		 * Jumbotron
		 */
		if ( $processedRecordVariables['jumbotron_enable'] ) {
			$this->processedData['config']['jumbotron']['enable'] = $processedRecordVariables['jumbotron_enable'];
			$this->processedData['config']['jumbotron']['fluid'] = $processedRecordVariables['jumbotron_fluid'];
			$this->processedData['config']['jumbotron']['slide'] = $processedRecordVariables['jumbotron_slide'];
			$this->processedData['config']['jumbotron']['position'] = $processedRecordVariables['jumbotron_position'];
			$this->processedData['config']['jumbotron']['container'] = $processedRecordVariables['jumbotron_container'];
			$this->processedData['config']['jumbotron']['containerposition'] = $processedRecordVariables['jumbotron_containerposition'];

			$jumbotronClass = $processedRecordVariables['jumbotron_class'] ?: '';
			$jumbotronClass .= $processedRecordVariables['jumbotron_fluid'] ? ' jumbotron-fluid' : '';
			$this->processedData['config']['jumbotron']['class'] = ' '.trim($jumbotronClass);

			# Image from pages media
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
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
				$this->processedData['config']['jumbotron']['bgImage'] = $bgImage;
			} elseif ( $processedRecordVariables['jumbotron_bgimage'] == 'page' ) {
				$bgImage = $this->getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', TRUE);
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				if ($bgImage)
				$this->processedData['config']['jumbotron']['bgImage'] = $bgImage;
			}


			if ( count($fileObjects) > 1 ) {
				$this->processedData['bgSlides'] = $fileObjects;
			}
		}

		/**
		 * Background Image (body)
		 */
		if ( $this->contentObjectConfiguration['settings.']['backgroundImageEnable'] ) {

			$bgImage = $this->getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', FALSE, FALSE, [], TRUE);

			if ( empty($bgImage) && $this->contentObjectConfiguration['settings.']['backgroundImageSlide'] ) {
				foreach ($rootLineArray as $page) {
					$bgImage = $this->getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', FALSE, FALSE, [], TRUE);
					if ($bgImage) break;
				}
			}
		}

		/**
		 * Breadcrumb
		 */
		$this->processedData['config']['breadcrumb']['class'] = '';
		if ( $processedRecordVariables['breadcrumb_enable'] || $processedRecordVariables['breadcrumb_bottom'] ) {
			if ( ($processedRecordVariables['homepage_uid'] == $frontendController->id) && $processedRecordVariables['breadcrumb_notonrootpage'] ) {
				$this->processedData['config']['breadcrumb']['enable'] = FALSE;
				$this->processedData['config']['breadcrumb']['bottom'] = FALSE;
			} else {
				$this->processedData['config']['breadcrumb']['enable'] = $processedRecordVariables['breadcrumb_enable'];
				$this->processedData['config']['breadcrumb']['bottom'] = $processedRecordVariables['breadcrumb_bottom'];
				$this->processedData['config']['breadcrumb']['faicon'] = $processedRecordVariables['breadcrumb_faicon'];
				$this->processedData['config']['breadcrumb']['position'] = $processedRecordVariables['breadcrumb_position'];
				$this->processedData['config']['breadcrumb']['container'] = $processedRecordVariables['breadcrumb_container'];
				$this->processedData['config']['breadcrumb']['containerposition'] = $processedRecordVariables['breadcrumb_containerposition'];
				$this->processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumb_class']
				? ' '.$processedRecordVariables['breadcrumb_class'] : '';
				$this->processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumb_corner'] ? ' rounded-0': '';
			}
		}

		/**
		 * Sidebar / submenu
		 */
		if ( $processedRecordVariables['sidebar_enable'] ) {
			$this->processedData['config']['sidebar']['left'] = $processedRecordVariables['sidebar_enable'];
		}
		if ( $processedRecordVariables['sidebar_rightenable'] ) {
			$this->processedData['config']['sidebar']['right'] = $processedRecordVariables['sidebar_rightenable'];
		}

		/**
		 * Footer
		 */
		if ( $processedRecordVariables['footer_enable'] ) {

			$this->processedData['config']['footer']['enable'] = $processedRecordVariables['footer_enable'];
			$this->processedData['config']['footer']['sticky'] = $processedRecordVariables['footer_sticky'];
			$this->processedData['config']['footer']['fluid'] = $processedRecordVariables['footer_fluid'];
			$this->processedData['config']['footer']['slide'] = $processedRecordVariables['footer_slide'];
			$this->processedData['config']['footer']['container'] = $processedRecordVariables['footer_container'];
			$this->processedData['config']['footer']['containerposition'] = $processedRecordVariables['footer_containerposition'];

			$footerClass = $processedRecordVariables['footer_class'] ?: '';
			$footerClass .= $processedRecordVariables['footer_fluid'] ? ' jumbotron-fluid' : '';
			$footerClass .= $processedRecordVariables['footer_sticky'] ? ' footer-sticky' : '';
			$this->processedData['config']['footer']['class'] = trim($footerClass);

		}

		/**
		 * Expandedcontent Top & Bottom
		 */
		if ( $processedRecordVariables['expandedcontent_enabletop'] ) {
			$this->processedData['config']['expandedcontentTop']['enable'] = $processedRecordVariables['expandedcontent_enabletop'];
			$this->processedData['config']['expandedcontentTop']['slide'] = $processedRecordVariables['expandedcontent_slidetop'];
			$this->processedData['config']['expandedcontentTop']['container'] = $processedRecordVariables['expandedcontent_containertop'];
			$this->processedData['config']['expandedcontentTop']['containerposition'] = $processedRecordVariables['expandedcontent_containerpositiontop'];
			$this->processedData['config']['expandedcontentTop']['class'] = $processedRecordVariables['expandedcontent_classtop'];
		}
		if ( $processedRecordVariables['expandedcontent_enablebottom'] ) {
			$this->processedData['config']['expandedcontentBottom']['enable'] = $processedRecordVariables['expandedcontent_enablebottom'];
			$this->processedData['config']['expandedcontentBottom']['slide'] = $processedRecordVariables['expandedcontent_slidebottom'];
			$this->processedData['config']['expandedcontentBottom']['container'] = $processedRecordVariables['expandedcontent_containerbottom'];
			$this->processedData['config']['expandedcontentBottom']['containerposition'] = $processedRecordVariables['expandedcontent_containerpositionbottom'];
			$this->processedData['config']['expandedcontentBottom']['class'] = $processedRecordVariables['expandedcontent_classbottom'];

		}

		if (!$this->processorConfiguration['disableDefaultCss']) {
			self::includeRequiredFiles($processedRecordVariables);
		}

		return $this->processedData;
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


	/**
	 * Load required css and js files
	 *
	 * @param array $processedRecordVariables
	 *
	 * @return void
	 */
	private function includeRequiredFiles($processedRecordVariables) {

		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
		$frontendController = self::getFrontendController();
		$css = '';
		$js = '';


		##########################################################################################################
		#
		# Offcanvas Navbar
		#
		##########################################################################################################
		// offcanvasNavbar.css
		if ( $processedRecordVariables['navbar_offcanvas'] ) {

			$cssFile = 'EXT:t3sbootstrap/Resources/Public/Styles/offcanvasNavbar.css';
			$cssPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
			$style = GeneralUtility::getURL($cssPath);

			switch ($processedRecordVariables['navbar_breakpoint']) {
				   case 'sm':
					$css .= '
/*!
 * offcanvasNavbar.css
 */
@media (max-width:575px){'.$style.'}';
						break;
				   case 'md':
					$css .= '
/*!
 * offcanvasNavbar.css
 */
@media (max-width:767px){'.$style.'}';
						break;
				   case 'lg':
					$css .= '
/*!
 * offcanvasNavbar.css
 */
@media (max-width:991px){'.$style.'}';
						break;
				   case 'xl':
					$css .= '
/*!
 * offcanvasNavbar.css
 */
@media (max-width:1199px){'.$style.'}';
						break;
				   case 'no':
					$css .= '
/*!
 * offcanvasNavbar.css
 */'
.$style;
						break;
			}
		}

		##########################################################################################################
		#
		# Sticky Footer
		#
		##########################################################################################################
		if ( $processedRecordVariables['footer_sticky'] ) {
			$css .= '
/*!
 * sticky footer
 */
html{position:relative;min-height:100%}#page-footer{position:absolute;bottom:0;width:100%}
';
		}

		##########################################################################################################
		#
		# Navbar Slide
		#
		##########################################################################################################
		if ( $this->processedData['config']['navbar']['enable'] == 'slide' ) {
			$cssFile = 'EXT:t3sbootstrap/Resources/Public/Styles/slideNavbar.css';
			$cssPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
			$css .= GeneralUtility::getURL($cssPath);
			$jsFooterFile = 'EXT:t3sbootstrap/Resources/Public/Scripts/slideNavbar.js';
			$jsFooterPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);
			$js .= GeneralUtility::getURL($jsFooterPath);
		}

		##########################################################################################################
		#
		# Mega Menu
		#
		##########################################################################################################
		if ( $processedRecordVariables['navbar_megamenu'] ) {
			$cssFile = 'EXT:t3sbootstrap/Resources/Public/Styles/megaMenu.css';
			$cssPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
			$css .= GeneralUtility::getURL($cssPath);
		}

		##########################################################################################################
		#
		# t3sbootstrap default
		#
		##########################################################################################################
		$cssFile = 'EXT:t3sbootstrap/Resources/Public/Styles/t3sbootstrap.css';
		$cssPath = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
		$css .= GeneralUtility::getURL($cssPath);

		// write required files
		if ($css) {
			$customDir = 'fileadmin/T3SB/Resources/Public/CSS/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$customFileName = 't3sbProject.css';
			self::writeCustomFile($customPath, $customFileName, $css);
		}
		if ($js) {
			$customDir = 'fileadmin/T3SB/Resources/Public/JS/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$customFileName = 't3sbProject.js';
			self::writeCustomFile($customPath, $customFileName, $js);
		}

		// include required files to fileadmin
		if ($css) {
			$projectCSS = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize(
				'/fileadmin/T3SB/Resources/Public/CSS/t3sbProject.css');
			$pageRenderer->addCssFile($projectCSS);
		}
		if ($js) {
			$projectJS = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize(
				'/fileadmin/T3SB/Resources/Public/JS/t3sbProject.js');
			$pageRenderer->addJsFooterFile($projectJS);
		}

	}


	/**
	 * Write a custom css-file
	 *
	 * @param string $customPath
	 * @param string $customFileName
	 * @param string $customContent
	 *
	 * @return void
	 */
	private function writeCustomFile($customPath, $customFileName, $customContent ) {

		if ($customContent) {

			$customFile = $customPath.$customFileName;

			if (file_exists($customFile)) {
				unlink($customFile);
			}

			if (!is_dir($customPath)) {
				mkdir($customPath, 0777, true);
			}

			GeneralUtility::writeFile($customFile, trim($customContent));
		}
	}

}
