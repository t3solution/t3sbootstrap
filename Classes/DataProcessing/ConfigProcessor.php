<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\PageTitle\BreadcrumbProvider;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConfigProcessor implements DataProcessorInterface
{

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
		$request = $GLOBALS['TYPO3_REQUEST'];
		$settings = $contentObjectConfiguration['settings.'];
		$frontendController = $request->getAttribute('frontend.controller');
		if (!$frontendController) {
			$frontendController = self::getFrontendController();
		}
		$webp = (bool)$settings['webp'];

		if ( is_numeric($contentObjectConfiguration['settings.']['config.']['uid']) ) {
			$processedRecordVariables = $contentObjectConfiguration['settings.']['config.'];
		} else {
			$processedData['noConfig'] = TRUE;
			return $processedData;
		}

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
		$processedData['config']['general']['company'] = !empty($company) ? trim($company) : 'Company Name';
		$processedData['config']['general']['homepageUid'] = $processedRecordVariables['homepageUid'] ?: 1;
		$processedData['config']['general']['pageTitle'] = $processedRecordVariables['pageTitle'] ?: '';
		$processedData['config']['general']['pageTitlealign'] = $processedRecordVariables['pageTitlealign'] ?: '';
		$processedData['config']['general']['pageTitleclass'] = $processedRecordVariables['pageTitleclass'] ?: '';

		// flexible small columns
		$currentPage = $frontendController->page;
		$smallColumnsCurrent = (int)$currentPage['tx_t3sbootstrap_smallColumns'];
		$pageRepository = GeneralUtility::makeInstance(PageRepository::class);
		$rootlinePage = $pageRepository->getPage($processedRecordVariables['homepageUid']);
		$smallColumnsRootline = (int)$rootlinePage['tx_t3sbootstrap_smallColumns'];
		$smallColumns = $smallColumnsCurrent ?: $smallColumnsRootline;

		// global override page data
		if ( !empty($contentObjectConfiguration['settings.']['pages.']['override.']) ) {
			foreach ( $contentObjectConfiguration['settings.']['pages.']['override.'] as $field=>$override ) {
				if ( ($field === 'tx_t3sbootstrap_titlecolor' || $field === 'tx_t3sbootstrap_subtitlecolor') && str_starts_with($override, '--bs-')) {
					$processedData['data'][$field] = 'var('.$override.')';
				} else {
					$processedData['data'][$field] = $override;
				}
			}
		}

		/**
		 * Backend layout
		 */
		if (!empty($processedData['data']['backend_layout'])) {
			$oneCol = $processedData['data']['backend_layout'] == 'pagets__OneCol' ? TRUE : FALSE;
			$threeCol = $processedData['data']['backend_layout'] == 'pagets__ThreeCol' ? TRUE : FALSE;
		} else {
			$backendLayoutNextLevel = FALSE;
			foreach ($frontendController->rootLine as $subPage) {
				$backendLayoutNextLevel = $subPage['backend_layout_next_level'];
				if ( !empty($subPage['backend_layout_next_level']) ) break;
			}
			$oneCol = $backendLayoutNextLevel == 'pagets__OneCol' ? TRUE : FALSE;
			$threeCol = $backendLayoutNextLevel == 'pagets__ThreeCol' ? TRUE : FALSE;
		}
		if ($oneCol === FALSE) {
			$smallColumns = (int)$processedData['data']['tx_t3sbootstrap_smallColumns'];
			$processedData['colAside'] = $smallColumns;
			if ($threeCol === TRUE) {
				$smallColumns = $smallColumns < 6 ? $smallColumns : 5;
				$processedData['colMain'] = 12 - $smallColumns * 2;
			} else {
				$processedData['colMain'] = 12 - $smallColumns;
			}
			$processedData['colAside'] = 12 - $processedData['colMain'];
		}

		// grid breakpoint
		$processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';

		/**
		 * Page title provider - BreadcrumbProvider
		 */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\Extbase\\Object\\ObjectManager');
		$configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$extbaseFrameworkConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		if ($extbaseFrameworkConfiguration['config.']['pageTitleProviders.']['breadcrumb.']['provider']
		 === 'T3SBS\T3sbootstrap\PageTitle\BreadcrumbProvider') {
			$titleProvider = GeneralUtility::makeInstance(BreadcrumbProvider::class);
			$titleProvider->setTitle('');
		}

		/**
		 * Language Navigation
		 */
		if( $processedRecordVariables['navbarEnable'] && $processedRecordVariables['navbarLangmenu'] ) {
			$site = $request->getAttribute('site');
			$langUid = [];
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
		}

		/**
		 * Meta Navigation
		 */
		if ( $processedRecordVariables['metaEnable'] ) {
			$processedData['config']['meta']['align'] = $processedRecordVariables['metaEnable'];
			$processedData['config']['meta']['container'] = $processedRecordVariables['metaContainer'];
			$processedData['config']['meta']['class'] = ' '.trim($processedRecordVariables['metaClass']);
			$processedData['config']['meta']['text'] = $processedRecordVariables['metaText'] ? trim($processedRecordVariables['metaText']) : '';
		}

		/**
		 * Navbar
		 */
		if ( $processedRecordVariables['navbarEnable'] ) {

			// navbar menu
			foreach ($processedData['navbarMenu'] as $key=>$navbarMenu) {
				$mainMenu[$key] = $navbarMenu;
				if (!empty($navbarMenu['data']['tx_t3sbootstrap_fontawesome_icon'])) {
					$mainMenu[$key]['faIcon'] = '<i class="'.$navbarMenu['data']['tx_t3sbootstrap_fontawesome_icon'].'"></i> ';
				}
				if ($navbarMenu['data']['tx_t3sbootstrap_icon_only']) {
					$mainMenu[$key]['title'] = '';
				}
				$mainMenu[$key]['target'] = $navbarMenu['data']['target'] ? $navbarMenu['data']['target'] : '_self';
				if (!empty($navbarMenu['data']['tx_t3sbootstrap_dropdownRight'])) {
					$mainMenu[$key]['dropdownRightClass'] = ' dropdown-menu-end';
				}
				if (!empty($navbarMenu['current']) && !empty($navbarMenu['active'])) {
					$mainMenu[$key]['active'] = 0;
					$mainMenu[$key]['activeClass'] = ' active';
				} elseif (!empty($navbarMenu['active'])) {
					$mainMenu[$key]['activeClass'] = '  parent-active';
				} else {
					$mainMenu[$key]['activeClass'] = '';
				}
				if (!empty($navbarMenu['children'][0])) {	
					if (self::getChildItems($navbarMenu['children'])) {
						$mainMenu[$key]['children'] = self::getChildItems($navbarMenu['children']);
					}
				}
			}
			$processedData['navbarMenu'] = $mainMenu;

			$processedData['config']['navbar']['enable'] = $processedRecordVariables['navbarEnable'];
			$processedData['config']['navbar']['sectionMenu'] = $processedRecordVariables['navbarSectionmenu'] ? ' section-menu' : '';
			$processedData['config']['navbar']['hover'] = $processedRecordVariables['navbarHover'] ? ' dropdown-hover' : '';
			$processedData['config']['navbar']['spacer'] = $processedRecordVariables['navbarIncludespacer'];
			$processedData['config']['navbar']['megamenu'] = $processedRecordVariables['navbarMegamenu'];
			$processedData['config']['navbar']['dataToggle'] = 'collapse';

			// dropdown animate			
			if ( !empty( $processedRecordVariables['navbarDropdownAnimate'] ) ) {
				$processedData['config']['navbar']['dropdownAnimate'] =
				 ' dd-animate-'.(int)$processedRecordVariables['navbarDropdownAnimate'];
				 $processedData['config']['navbar']['dropdownAnimateValue'] = 
				 (int)$contentObjectConfiguration['settings.']['config.']['navbarDropdownAnimate'];
			}

			// shortcut & clickableparent
			if (!empty($frontendController->rootLine[1]) && $frontendController->rootLine[1]['doktype'] == 4 && empty($processedRecordVariables['navbarPlusicon'])) {
				$processedData['config']['navbar']['clickableparent'] = 1;
			} else {
				$processedData['config']['navbar']['clickableparent'] = (int) $processedRecordVariables['navbarClickableparent'];
			}

			// image
			$processedData['config']['navbar']['image'] = $processedRecordVariables['navbarImage']
			? $processedRecordVariables['navbarImage']	: $contentObjectConfiguration['settings.']['navbar.']['image.']['defaultPath'];

			// container
			if ( !$processedRecordVariables['navbarContainer'] ) {
				$processedData['config']['navbar']['container'] = '';
			} else {
				$processedData['config']['navbar']['containerposition'] = $processedRecordVariables['navbarContainer'];
				$processedData['config']['navbar']['container'] = 'container';
			}
			$processedData['config']['navbar']['innercontainer'] = $processedRecordVariables['navbarInnercontainer'] ?: 'container';

			// brand
			$processedData['config']['navbar']['brand'] = $processedRecordVariables['navbarBrand'];
			$processedData['config']['navbar']['brandAlignment'] = $processedRecordVariables['navbarbrandAlignment'];
			if ($processedRecordVariables['navbarBrand'] === 'imgText' && !empty($processedRecordVariables['company'])) {
				$processedData['config']['navbar']['brandClass'] = ' d-inline-block me-2';
			}
			// toggler
			$processedData['config']['navbar']['toggler'] = $processedRecordVariables['navbarToggler'];
			$processedData['config']['navbar']['animatedToggler'] = $processedRecordVariables['navbarAnimatedtoggler'];

			// breakpoint
			$processedData['navbarBreakpoint'] = $processedRecordVariables['navbarBreakpoint'] ?: 'md';
			$processedData['config']['navbar']['breakpoint'] = $processedRecordVariables['navbarBreakpoint'];

			// class ff
			$navbarClass = 'navbar-'.$processedRecordVariables['navbarEnable'];
			$navbarClass .= $processedRecordVariables['navbarBreakpoint']
			 ? ' navbar-expand-'.$processedRecordVariables['navbarBreakpoint'] : ' navbar-expand-sm';
			$navbarClass .= $processedRecordVariables['navbarClass'] ? ' '.$processedRecordVariables['navbarClass'] : '';
			$navbarClass .= $processedRecordVariables['navbarSectionmenu'] ? ' sectionMenu' : '';
			$navbarClass .= $processedRecordVariables['navbarClickableparent'] ? ' clickableparent' : '';
			$navbarClass .= $processedRecordVariables['navbarHover'] ? ' navbarHover' : '';

			// transparent
			if ( $processedRecordVariables['navbarTransparent'] && $processedRecordVariables['navbarPlacement'] === 'fixed-top') {
				$processedData['config']['navbar']['transparent'] = TRUE;
			} else {
				$processedData['config']['navbar']['transparent'] = FALSE;			
			}
			if ( $processedRecordVariables['navbarColor'] === 'color' && !empty($processedRecordVariables['navbarBackground']) ) {
				$navbarStyle = 'background-color: '.$processedRecordVariables['navbarBackground'].';';
				$processedData['config']['navbar']['styleAttr'] = ' style="'.$navbarStyle.'"';
			} else {
				if (!$processedData['config']['navbar']['transparent']) {
					$navbarClass .= ' bg-'.$processedRecordVariables['navbarColor'];				
				}
			}

			// plusicon
			$processedData['config']['navbar']['bstoggle'] = 'dropdown';
			if (!empty($processedRecordVariables['navbarPlusicon'])) {
				$processedData['config']['navbar']['navbarPlusicon'] = $processedRecordVariables['navbarPlusicon'];
				$processedData['config']['navbar']['bstoggle'] = 'none';
				$navbarClass .= ' navplusicon';
				$processedData['config']['navbar']['hover'] = '';		
			}

			// shrinking navbar on scrolling
			$navBarAttr = '';
			if ( $processedRecordVariables['navbarPlacement'] == 'fixed-top' && $processedRecordVariables['navbarShrinkcolor'] ) {
				$processedData['config']['navbar']['transparent'] = FALSE;
				$navbarClass .= ' shrink py-'.$contentObjectConfiguration['settings.']['config.']['shrinkingNavPadding'];
				$navbarShrinkcolorschemes = $processedRecordVariables['navbarShrinkcolorschemes'];
				$navBarAttr .= ' data-shrinkcolorschemes="bg-'.$navbarShrinkcolorschemes.'"';
				$shrinkColor = $processedRecordVariables['navbarShrinkcolor'];
				$navBarAttr .= ' data-shrinkcolor="'.$shrinkColor.'"';
				$navColorArr = explode(' ', $processedRecordVariables['navbarColor']);
				if ( !empty($navColorArr[1]) ) {
					$navbarColor = 'bg-'.$navColorArr[0];
					$processedData['config']['navbar']['gradient'] = 'bg-gradient';
				} else {
					$navbarColor = $processedRecordVariables['navbarColor'];
				}
				$navBarAttr .= ' data-colorschemes="'.'bg-'.$navbarColor.'"';
				$navBarAttr .= ' data-color="navbar-'.$processedRecordVariables['navbarEnable'].'"';
			}

			// sticky-top
			if ($processedRecordVariables['navbarPlacement'] === 'sticky-top') {
				$navBarAttr .= ' data-bs-toggle="sticky-onscroll"';
			}
			$processedData['config']['navbar']['dataAttr'] = $navBarAttr;

			// placement
			if ($processedRecordVariables['navbarPlacement']) {
				$processedData['config']['navbar']['placement'] = $processedRecordVariables['navbarPlacement'];
				if ( !empty($processedData['config']['navbar']['containerposition']) && $processedData['config']['navbar']['containerposition'] == 'outside' ) {
					$processedData['config']['navbar']['container'] =
					trim($processedData['config']['navbar']['container'].' '.$processedRecordVariables['navbarPlacement']);
				} else {
					$navbarClass .= ' '.$processedRecordVariables['navbarPlacement'];
				}
			}
			$dropdown = $processedRecordVariables['navbarPlacement'] == 'fixed-bottom' ? 'dropup' : 'dropdown';
			$processedData['config']['navbar']['dropdown'] = $dropdown;

			// set class
			$processedData['config']['navbar']['class'] = trim($navbarClass);

			// alignment
			$processedData['config']['navbar']['alignment'] = $processedRecordVariables['navbarAlignment'];
			if ($processedRecordVariables['navbarAlignment'] == 'fill') {
				$processedData['config']['navbar']['mauto'] = ' nav-fill w-100';
			} elseif ($processedRecordVariables['navbarAlignment'] == 'justified') {
				$processedData['config']['navbar']['mauto'] = ' nav-justified w-100';
			} elseif ($processedRecordVariables['navbarAlignment'] == 'center') {
				$processedData['config']['navbar']['navbarCenter'] = ' justify-content-center';
				$processedData['config']['navbar']['mauto'] = '';
			} else {
				$processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbarAlignment'] == 'right') ? ' ms-auto': ' me-auto';
			}

			// extra row
			if ($processedRecordVariables['navbarExtraRow']) {
				$processedData['config']['navbar']['navbarExtraRow'] = ' flex-column';
			}

			// offcanvas
			if ($processedRecordVariables['navbarOffcanvas']) {
				$processedData['config']['navbar']['offcanvas'] = $processedRecordVariables['navbarOffcanvas'];
				$processedData['config']['navbar']['dataToggle'] = 'offcanvas';
				$processedData['config']['navbar']['offcanvasBgColorClass'] = 'bg-'.$processedRecordVariables['navbarColor'];
				if ($processedRecordVariables['navbarEnable'] == 'dark') {
					$processedData['config']['navbar']['offcanvasTitleColor'] = 'rgba(255, 255, 255, 0.75)';
					$processedData['config']['navbar']['offcanvasCross'] = 'white';
				} else {
					$processedData['config']['navbar']['offcanvasTitleColor'] = 'rgba(0, 0, 0, 0.75)';
					$processedData['config']['navbar']['offcanvasCross'] = 'dark';
				}
				if ($processedRecordVariables['navbarAlignment'] == 'left') {
					$processedData['config']['navbar']['navbarAlignment'] = 'start';
				} elseif ($processedRecordVariables['navbarAlignment'] == 'right') {
					$processedData['config']['navbar']['navbarAlignment'] = 'end';
				} else {
					$processedData['config']['navbar']['navbarAlignment'] = 'center';
				}
				if ($processedRecordVariables['navbarToggler'] == 'left') {
					$processedData['config']['navbar']['offcanvasAlign'] = 'start';
				} else {
					$processedData['config']['navbar']['offcanvasAlign'] = 'end';
				}
				$processedData['config']['navbar']['sectionMenuDataAttr'] = ' data-bs-dismiss="offcanvas" aria-label="Close"';
			}

			// searchbox
			if ( $processedRecordVariables['navbarSearchbox'] ) {
				$processedData['config']['navbar']['searchbox'] = $processedRecordVariables['navbarSearchbox'];
				$processedData['config']['navbar']['searchboxcolor'] = $processedRecordVariables['navbarEnable'] == 'light' ? 'dark' : 'light';
				if ( $processedData['config']['navbar']['mauto'] == ' me-auto' ) {
					$processedData['config']['navbar']['sbmauto'] = ' ms-auto';
				}
				if ( $processedData['config']['navbar']['mauto'] == ' ms-auto' ) {
					$processedData['config']['navbar']['sbmauto'] = ' float-right ms-3';
				}
				if ( $processedData['config']['navbar']['mauto'] == 'center' ) {
					$processedData['config']['navbar']['sbmauto'] = '';
				}
			}

			// content only on rootpage
			if ( ($processedRecordVariables['homepageUid'] == $frontendController->id) && $processedRecordVariables['contentOnlyOnRootpage'] ) {
				$processedData['config']['navbar']['enable'] = FALSE;
			}

			// navigation color
			$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
			if ( $extConf['navigationColor'] ) {
				$processedData['config']['navbar']['navColorCSS'] = self::getNavigationColor((int)$processedData['data']['sys_language_uid']);
			}
		}

		/**
		 * Jumbotron
		 */
		if ( $processedRecordVariables['jumbotronEnable'] ) {
			$processedData['config']['jumbotron']['enable'] = $processedRecordVariables['jumbotronEnable'];
			$processedData['config']['jumbotron']['slide'] = $processedRecordVariables['jumbotronSlide'];
			$processedData['config']['jumbotron']['position'] = $processedRecordVariables['jumbotronPosition'];
			$processedData['config']['jumbotron']['container'] = $processedRecordVariables['jumbotronContainer'];
			$processedData['config']['jumbotron']['containerposition'] = $processedRecordVariables['jumbotronContainerposition'];
			$processedData['config']['jumbotron']['class'] = ' '.trim($processedRecordVariables['jumbotronClass']);
			$processedData['config']['jumbotron']['noBgRatio'] = TRUE;

			# Image from pages media
			$hasBgImages = 0;
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);


			$fileObjects = [];
			$processedData['config']['jumbotron']['alignItem'] = 'd-flex align-items-'.$processedRecordVariables['jumbotronAlignitem'];
			$processedData['config']['jumbotron']['alignment'] = $processedRecordVariables['jumbotronAlignitem'];
			if ( $processedRecordVariables['jumbotronBgimage'] == 'root' ) {
				// slide in rootline
				foreach ($frontendController->rootLine as $page) {
					$fileObjects = $fileRepository->findByRelation('pages', 'media', $page['uid']);
					$uid = $page['uid'];
					if ($fileObjects) break;
				}
				$hasBgImages = count($fileObjects);
				if ( count($fileObjects) > 1 ) {
					if (!empty($settings['multiplePagesMedia'])) {
						// background images
						$bgSlides = self::getBackgroundImageUtility()->getBgImage($uid, 'pages', TRUE, FALSE, [], FALSE,
						$processedData['data']['uid'], $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
						$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
						$processedData['config']['jumbotron']['multiplePagesMedia'] = TRUE;
					} else {
						// slider
						$processedData['config']['jumbotron']['alignItem'] = '';
						$bgSlides = self::getBackgroundImageUtility()->getBgImage($uid, 'pages', TRUE, FALSE, [], FALSE, 0,
						$webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
						$processedData['bgSlides'] = $bgSlides;
					}
				} else {
					// background image
					$bgSlides = self::getBackgroundImageUtility()->getBgImage($uid, 'pages', TRUE, FALSE, [], FALSE,
					$processedData['data']['uid'], $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
					$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
					if (!empty($settings['multiplePagesMedia'])) {
						$processedData['config']['jumbotron']['multiplePagesMedia'] = FALSE;
					}
				}
			} elseif ( $processedRecordVariables['jumbotronBgimage'] == 'page' ) {
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				$hasBgImages = count($fileObjects);
				if ( count($fileObjects) > 1 ) {
					// slider
					$processedData['config']['jumbotron']['alignItem'] = '';
					$bgSlides = self::getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', TRUE, FALSE, [], FALSE, 0,
					  $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
					$processedData['bgSlides'] = $bgSlides;
				} else {
					// background image
					$bgSlides = self::getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', TRUE, FALSE, [], FALSE, 0,
					 $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
					$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
				}
			}
			if ($hasBgImages && !empty($processedRecordVariables['jumbotronBgimageratio'])) {
				$processedData['config']['jumbotron']['noBgRatio'] = FALSE;
				$processedData['config']['jumbotron']['class'] .= ' ratio ratio-'.$processedRecordVariables['jumbotronBgimageratio'];
			}
		}

		/**
		 * Background Image (body)
		 */
		if ( $contentObjectConfiguration['settings.']['config.']['backgroundImageEnable'] ) {

			$BodyBgImage = self::getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', FALSE, FALSE, [], TRUE, 0,
			 $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
			 $bgImage = is_array($BodyBgImage) ? $BodyBgImage[1] : '';
			if ( empty($BodyBgImage) && $contentObjectConfiguration['settings.']['config.']['backgroundImageSlide'] ) {
				foreach ($frontendController->rootLine as $page) {
					$BodyBgImage = self::getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', FALSE, FALSE, [], TRUE, $frontendController->id, $webp);
					if ($BodyBgImage) break;
				}
			}
		}

		/**
		 * Breadcrumb
		 */
		$processedData['config']['breadcrumb']['class'] = '';
		if ( $processedRecordVariables['breadcrumbEnable'] || $processedRecordVariables['breadcrumbBottom'] ) {

			if ( ($processedRecordVariables['homepageUid'] == $frontendController->id) && $processedRecordVariables['breadcrumbNotonrootpage'] ) {
				$processedData['config']['breadcrumb']['enable'] = FALSE;
				$processedData['config']['breadcrumb']['bottom'] = FALSE;
			} else {
				$processedData['config']['breadcrumb']['enable'] = $processedRecordVariables['breadcrumbEnable'];
				$processedData['config']['breadcrumb']['bottom'] = $processedRecordVariables['breadcrumbBottom'];
				$processedData['config']['breadcrumb']['faicon'] = $processedRecordVariables['breadcrumbFaicon'];
				$processedData['config']['breadcrumb']['position'] = $processedRecordVariables['breadcrumbPosition'];
				$processedData['config']['breadcrumb']['container'] = $processedRecordVariables['breadcrumbContainer'];
				$processedData['config']['breadcrumb']['containerposition'] = $processedRecordVariables['breadcrumbContainerposition'];
				$processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumbClass'] ?: '';
				$processedData['config']['breadcrumb']['ol-class'] = $processedRecordVariables['breadcrumbCorner'] ? ' rounded-0': '';
			}
		}

		/**
		 * Sidebar / submenu
		 */
		if ( $processedRecordVariables['sidebarEnable'] ) {
			$processedData['config']['sidebar']['left'] = $processedRecordVariables['sidebarEnable'];
			if ($processedRecordVariables['sidebarEnable'] == 'Section') {
				$processedData['config']['sidebar']['enable'] = TRUE;
				$processedData['config']['sidebar']['stickTopClass'] = $processedRecordVariables['sectionmenuStickyTop'] ? ' sticky-top': '';
				$topOffset = (int)$processedRecordVariables['sectionmenuAnchorOffset'] + (int)$processedRecordVariables['navbarHeight'];
				$processedData['config']['sidebar']['stickTopOffset'] = $topOffset ? $topOffset.'px' : 0;
				$processedData['config']['sidebar']['scrollspyOffset'] = $processedRecordVariables['sectionmenuScrollspyOffset'];
				$processedData['config']['sidebar']['scrollspy'] = $processedRecordVariables['sectionmenuScrollspy'];
			} else {
				if (!empty($processedData['subNavigation']) && is_array($processedData['subNavigation'])) {
					$processedData['subNavigation'] =
					 self::getSubNavigation($processedData['subNavigation'], (int)$processedRecordVariables['navbarClickableparent']);
				}
			}
			$processedData['config']['sidebar']['sticky'] = $processedRecordVariables['submenuSticky'];
		}
		if ( $processedRecordVariables['sidebarRightenable'] ) {
			$processedData['config']['sidebar']['right'] = $processedRecordVariables['sidebarRightenable'];
			$processedData['config']['sidebar']['sticky'] = $processedRecordVariables['submenuSticky'];
		}

		/**
		 * Footer
		 */
		if ( $processedRecordVariables['footerEnable'] ) {

			$processedData['config']['footer']['enable'] = $processedRecordVariables['footerEnable'];
			$processedData['config']['footer']['sticky'] = $processedRecordVariables['footerSticky'];
			$processedData['config']['footer']['slide'] = $processedRecordVariables['footerSlide'];
			$processedData['config']['footer']['container'] = $processedRecordVariables['footerContainer'];
			$processedData['config']['footer']['containerposition'] = $processedRecordVariables['footerContainerposition'];
			$footerClass = $processedRecordVariables['footerClass'] ?: '';
			$footerClass .= $processedRecordVariables['footerSticky'] ? ' footer-sticky' : '';
			$processedData['config']['footer']['class'] = trim($footerClass);
		}

		/**
		 * Expandedcontent Top & Bottom
		 */
		if ( $processedRecordVariables['expandedcontentEnabletop'] ) {
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$numberOfTop = $queryBuilder
				->count('uid')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(20, \PDO::PARAM_INT))
				)
			->execute()
			->fetchColumn();
			$processedData['config']['expandedcontentTop']['enable'] = $numberOfTop ? $processedRecordVariables['expandedcontentEnabletop'] : 0;
			$processedData['config']['expandedcontentTop']['slide'] = $processedRecordVariables['expandedcontentSlidetop'];
			$processedData['config']['expandedcontentTop']['container'] = $processedRecordVariables['expandedcontentContainertop'];
			$processedData['config']['expandedcontentTop']['containerposition'] = $processedRecordVariables['expandedcontentContainerpositiontop'];
			$processedData['config']['expandedcontentTop']['class'] = trim($processedRecordVariables['expandedcontentClasstop']);
		}
		if ( $processedRecordVariables['expandedcontentEnablebottom'] ) {
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$numberOfBottom = $queryBuilder
				->count('uid')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(21, \PDO::PARAM_INT))
				)
			->execute()
			->fetchColumn();
			$processedData['config']['expandedcontentBottom']['enable'] = $numberOfBottom ? $processedRecordVariables['expandedcontentEnablebottom'] : 0;
			$processedData['config']['expandedcontentBottom']['slide'] = $processedRecordVariables['expandedcontentSlidebottom'];
			$processedData['config']['expandedcontentBottom']['container'] = $processedRecordVariables['expandedcontentContainerbottom'];
			$processedData['config']['expandedcontentBottom']['containerposition'] = $processedRecordVariables['expandedcontentContainerpositionbottom'];
			$processedData['config']['expandedcontentBottom']['class'] = trim($processedRecordVariables['expandedcontentClassbottom']);
		}

		return $processedData;
	}


	/**
	 * Returns an instance of the rbackground image utility
	 */
	protected function getBackgroundImageUtility(): BackgroundImageUtility
	{
		return GeneralUtility::makeInstance(BackgroundImageUtility::class);
	}


	/**
	 * Generate CSS for navigation color
	 */
	protected function getNavigationColor(int $languageUid): string
	{
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
		$result = $queryBuilder
			 ->select('uid','tx_t3sbootstrap_navigationcolor', 'tx_t3sbootstrap_navigationactivecolor', 'tx_t3sbootstrap_navigationhover','tx_t3sbootstrap_navigationbgcolor')
			 ->from('pages')
			 ->where(
				$queryBuilder->expr()->orX(
					$queryBuilder->expr()->neq('tx_t3sbootstrap_navigationcolor', $queryBuilder->createNamedParameter('')),
					$queryBuilder->expr()->neq('tx_t3sbootstrap_navigationactivecolor', $queryBuilder->createNamedParameter('')),
					$queryBuilder->expr()->neq('tx_t3sbootstrap_navigationhover', $queryBuilder->createNamedParameter('')),
					$queryBuilder->expr()->neq('tx_t3sbootstrap_navigationbgcolor', $queryBuilder->createNamedParameter(''))
				)
			 )
			->andWhere(
				$queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($languageUid, \PDO::PARAM_INT))
			)
			 ->execute();

		$navbarColors = $result->fetchAll();
		$navbarColorCSS = '';

		if (is_array($navbarColors)) {

			foreach($navbarColors as $navbarColor) {

				if (is_integer($navbarColor['uid'])) {

					$treePages = explode(',', self::getTreeList((int)$navbarColor['uid'], 99));

					foreach($treePages as $treepageUid) {

						if ($navbarColor['uid'] == $treepageUid) {

							$item = '#nav-item-'.(int)$treepageUid;

							if ($navbarColor['tx_t3sbootstrap_navigationactivecolor']) {
								$navbarColorCSS .= $item.'.active .nav-link{color:'.$navbarColor['tx_t3sbootstrap_navigationbgcolor'].' !important}';
							}

						} else {

							$item = '.dropdown-item-'.(int)$treepageUid;

							if ($navbarColor['tx_t3sbootstrap_navigationcolor']) {
								$navbarColorCSS .= $item.'{color:'.$navbarColor['tx_t3sbootstrap_navigationcolor'].' !important}';
							}
							if ($navbarColor['tx_t3sbootstrap_navigationactivecolor']) {
								$navbarColorCSS .= $item.'.active{color:'.$navbarColor['tx_t3sbootstrap_navigationactivecolor'].' !important}';
							}
							if ($navbarColor['tx_t3sbootstrap_navigationbgcolor']) {
								$navbarColorCSS .= $item.'.active{background:'.$navbarColor['tx_t3sbootstrap_navigationbgcolor'].' !important}';
								$navbarColorCSS .= $item.':hover,'.$item.':focus{background:'.$navbarColor['tx_t3sbootstrap_navigationbgcolor'].' !important}';
							}
							if ($navbarColor['tx_t3sbootstrap_navigationhover']) {
								$navbarColorCSS .= $item.':hover,'.$item.':focus{color:'.$navbarColor['tx_t3sbootstrap_navigationhover'].' !important}';
								$navbarColorCSS .= $item.'.active:hover,'.$item.'.active:focus{color:'.$navbarColor['tx_t3sbootstrap_navigationhover'].' !important}';
							}
						}
					}
				}
			}
		}
		return $navbarColorCSS;
	}


	/**
	 * Returns an string
	 */
	protected function getTreeList(int $id, int $depth, int $begin = 0, string $permsClause = ''): string
	{
		if ($id < 0) {
			$id = abs($id);
		}
		if ($begin == 0) {
			$theList = $id;
		} else {
			$theList = '';
		}
		if ($id && $depth > 0) {
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
			$queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
			$statement = $queryBuilder->select('uid')
				->from('pages')
				->where(
					$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
					$queryBuilder->expr()->eq('sys_language_uid', 0),
					QueryHelper::stripLogicalOperatorPrefix($permsClause)
				)
				->execute();
			while ($row = $statement->fetch()) {
				if ($begin <= 0) {
					$theList .= ',' . $row['uid'];
				}
				if ($depth > 1) {
					$theSubList = $this->getTreeList($row['uid'], $depth - 1, $begin - 1, $permsClause);
					if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
						$theList .= ',';
					}
					$theList .= $theSubList;
				}
			}
		}

		return (string)$theList;
	}


	/**
	 * Returns an array
	 */
	protected function getSubNavigation(array $subNavigation, int $navbarClickableparent): array
	{
		$res = [];
		foreach ($subNavigation as $supNav ) {
			if ( is_array($supNav['children'])) {
				self::getSubNavigation($supNav['children'], $navbarClickableparent);
				if ( $navbarClickableparent === 0 ) {
					$supNav['link'] = '#';
				}
			}

			$res[] = $supNav;
		}

		return $res;
	}


	/**
	 * Returns an array
	 */
	protected function getChildItems($children)
	{
		$mainMenu = $children;
		foreach ($children as $cKey=>$child) {
			if (!empty($child['data']['tx_t3sbootstrap_fontawesome_icon'])) {
				$mainMenu[$cKey]['faIcon'] = '<i class="'.$child['data']['tx_t3sbootstrap_fontawesome_icon'].'"></i> ';				
			}
			if (!empty($child['data']['tx_t3sbootstrap_icon_only'])) {
				$mainMenu[$cKey]['title'] = '';
			}
			$mainMenu[$cKey]['target'] = $child['target'] ? $child['target'] : '_self';
			if (!empty($child['current'])) {
				$mainMenu[$cKey]['active'] = 0;
			}
			if (!empty($child['children'][0]) && !empty(self::getChildItems($child['children']))) {
				$mainMenu[$cKey]['children'] = $child;
				$mainMenu[$cKey]['children'] = self::getChildItems($child['children']);
			}
			if (!empty($child['current']) && !empty($child['active'])) {
				$mainMenu[$cKey]['active'] = 0;
				$mainMenu[$cKey]['activeClass'] = ' active';
			} elseif (!empty($child['active'])) {
				$mainMenu[$cKey]['activeClass'] = '  parent-active';
			} else {
				$mainMenu[$cKey]['activeClass'] = '';
			}
		}

		return $mainMenu;
	}


	/**
	 * Returns $typoScriptFrontendController TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}

}
