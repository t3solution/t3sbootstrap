<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\QueryGenerator;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;


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

		$settings = $contentObjectConfiguration['settings.'];
		$frontendController = self::getFrontendController();
		$webp = $settings['webp'];

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
		$processedData['config']['general']['company'] = trim($company);
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

			foreach ($frontendController->rootLine as $subPage) {
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
		$site = self::getCurrentSite();

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
		if ( $processedRecordVariables['metaEnable'] ) {
			$processedData['config']['meta']['align'] = $processedRecordVariables['metaEnable'];
			$processedData['config']['meta']['container'] = $processedRecordVariables['metaContainer']
			 ? ' '.$processedRecordVariables['metaContainer'] : '';
			$metaClass = $processedRecordVariables['metaClass'] ?: '';
			$processedData['config']['meta']['class'] = ' '.trim($metaClass);
			$processedData['config']['meta']['text'] = trim($processedRecordVariables['metaText']);
		}

		/**
		 * Navbar
		 */
		if ( $processedRecordVariables['navbarEnable'] ) {
			switch ( (int)$contentObjectConfiguration['settings.']['config.']['navbarDropdownAnimate'] ) {
				 case 1:
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-1';
				break;
				 case 2:
					$processedData['config']['navbar']['dropdownAnimate'] = ' dd-animate-2';
				break;
				 default:
					$processedData['config']['navbar']['dropdownAnimate'] = '';
			}

			$processedData['config']['navbar']['dropdownAnimateValue'] = (int)$contentObjectConfiguration['settings.']['config.']['navbarDropdownAnimate'];
			$processedData['config']['navbar']['enable'] = $processedRecordVariables['navbarEnable'];
			$processedData['config']['navbar']['sectionMenu'] = $processedRecordVariables['navbarSectionmenu'] ? ' section-menu' : '';
			$processedData['config']['navbar']['brand'] = $processedRecordVariables['navbarBrand'];
			$processedData['config']['navbar']['brandAlignment'] = $processedRecordVariables['navbarbrandAlignment'];
			$processedData['config']['navbar']['hover'] = $processedRecordVariables['navbarHover'] ? ' dropdown-hover' : '';

			if ( $frontendController->rootLine[1]['doktype'] == 4) {
				$processedData['config']['navbar']['clickableparent'] = 1;
			} else {
				$processedData['config']['navbar']['clickableparent'] = $processedRecordVariables['navbarClickableparent'];
			}
			if ( $processedData['config']['navbar']['clickableparent'] && $processedRecordVariables['navbarPlusicon']) {
				$processedData['config']['navbar']['navbarPlusicon'] = 0;
			} else {
				$processedData['config']['navbar']['navbarPlusicon'] = $processedRecordVariables['navbarPlusicon'];
			}
			$processedData['config']['navbar']['image'] = $processedRecordVariables['navbarImage']
			? $processedRecordVariables['navbarImage']	: $contentObjectConfiguration['settings.']['navbar.']['image.']['defaultPath'];

			$processedData['config']['navbar']['toggler'] = $processedRecordVariables['navbarToggler'];

			if ( !$processedRecordVariables['navbarContainer'] ) {
				$processedData['config']['navbar']['container'] = '';
			} else {
				if ( $processedRecordVariables['navbarContainer'] == 'fluid' ) {
					$processedData['config']['navbar']['container'] = 'container-fluid';
				} else {
					$processedData['config']['navbar']['containerposition'] = $processedRecordVariables['navbarContainer'];
					$processedData['config']['navbar']['container'] = 'container';
				}
			}

			$processedData['config']['navbar']['innercontainer'] = $processedRecordVariables['navbarInnercontainer'] ?: 'container';

			$navbarClass = 'navbar-'.$processedRecordVariables['navbarEnable'];
			$navbarClass .= $processedRecordVariables['navbarBreakpoint']
			 ? ' navbar-expand-'.$processedRecordVariables['navbarBreakpoint'] : ' navbar-expand-sm';
			// navbar breakpoint
			$processedData['navbarBreakpoint'] = $processedRecordVariables['navbarBreakpoint'] ?: 'md';
			if ( $processedRecordVariables['navbarPlacement'] == 'fixed-top' && $processedRecordVariables['navbarShrinkcolor'] ) {
				$navbarClass .= ' shrink py-'.$contentObjectConfiguration['settings.']['config.']['shrinkingNavPadding'];
			}
			$processedData['config']['navbar']['breakpoint'] = $processedRecordVariables['navbarBreakpoint'];

			$navbarClass .= $processedRecordVariables['navbarClass'] ? ' '.$processedRecordVariables['navbarClass'] : '';

			if ( $processedRecordVariables['navbarTransparent'] && $processedRecordVariables['navbarPlacement'] == 'fixed-top') {
				if ( $processedRecordVariables['navbarColor'] == 'color' && $processedRecordVariables['navbarBackground'] ) {
					$processedData['config']['navbar']['colorschemes'] = $processedRecordVariables['navbarBackground'];
				} else {
					$navColorArr = explode(' ', $processedRecordVariables['navbarColor']);
					if ( $navColorArr[1] ) {
						$processedData['config']['navbar']['colorschemes'] = 'bg-'.$navColorArr[0];
						$processedData['config']['navbar']['gradient'] = 'bg-gradient';
					} else {
						$processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbarColor'];
					}
				}
				$processedData['config']['navbar']['transparent'] = true;
			} else {

				if ( $processedRecordVariables['navbarColor'] == 'color' ) {
					if ( $processedRecordVariables['navbarBackground'] ) {
						$navbarStyle = 'background-color: '.$processedRecordVariables['navbarBackground'].';';
						$processedData['config']['navbar']['style'] = $navbarStyle;
					} else {
						$processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbarShrinkcolorschemes'];
						$processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbarColor'];
					}
				} else {
					$navbarClass .= ' bg-'.$processedRecordVariables['navbarColor'];
					$processedData['config']['navbar']['shrinkColorschemes'] = 'bg-'.$processedRecordVariables['navbarShrinkcolorschemes'];
					$processedData['config']['navbar']['colorschemes'] = 'bg-'.$processedRecordVariables['navbarColor'];
				}
			}

			if ( ($processedRecordVariables['navbarPlacement'] == 'fixed-top' && $processedRecordVariables['navbarShrinkcolor'])
			 && ($processedRecordVariables['navbarEnable'] == 'light' || $processedRecordVariables['navbarEnable'] == 'dark') ) {
				$processedData['config']['navbar']['shrinkColor'] = 'navbar-'.$processedRecordVariables['navbarShrinkcolor'];
				$processedData['config']['navbar']['color'] = 'navbar-'.$processedRecordVariables['navbarEnable'];
			}

			$navbarClass .= $processedRecordVariables['navbarClickableparent'] ? ' clickableparent' : '';
			$navbarClass .= $processedRecordVariables['navbarHover'] ? ' navbarHover' : '';

			if ($processedRecordVariables['navbarPlacement']) {
				if ( $processedData['config']['navbar']['containerposition'] == 'outside' ) {
					$processedData['config']['navbar']['container'] =
					trim($processedData['config']['navbar']['container'].' '.$processedRecordVariables['navbarPlacement']);
				} else {
					$navbarClass = $navbarClass.' '.$processedRecordVariables['navbarPlacement'];
				}
			}

			$navbarClass .= $processedRecordVariables['navbarSectionmenu'] ? ' sectionMenu' : '';
			$processedData['config']['navbar']['class'] = trim($navbarClass);
			$dropdown = $processedRecordVariables['navbarPlacement'] == 'fixed-bottom' ? 'dropup' : 'dropdown';
			$processedData['config']['navbar']['dropdown'] = $dropdown;
			$processedData['config']['navbar']['spacer'] = $processedRecordVariables['navbarIncludespacer'];
			$processedData['config']['navbar']['megamenu'] = $processedRecordVariables['navbarMegamenu'];
			$processedData['config']['navbar']['placement'] = $processedRecordVariables['navbarPlacement'];
			$processedData['config']['navbar']['sticky'] = $processedRecordVariables['navbarPlacement'] == 'sticky-top' ? TRUE : FALSE;
			$processedData['config']['navbar']['alignment'] = $processedRecordVariables['navbarAlignment'];

			if ($processedRecordVariables['navbarAlignment'] == 'fill') {
				$processedData['config']['navbar']['mauto'] = ' nav-fill w-100';
			} else {
				$processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbarAlignment'] == 'right') ? ' ms-auto': ' me-auto';
			}

			if ($processedRecordVariables['navbarAlignment'] == 'center') {
				$processedData['config']['navbar']['navbarCenter'] = ' justify-content-center';
				$processedData['config']['navbar']['mauto'] = '';
			}

			$processedData['config']['navbar']['offcanvas'] = $processedRecordVariables['navbarOffcanvas'];

			// Offcanvas
			if ($processedRecordVariables['navbarOffcanvas']) {
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
			}

			$processedData['config']['navbar']['animatedToggler'] = $processedRecordVariables['navbarAnimatedtoggler'];

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

			$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
			if ( $extConf['navigationColor'] ) {
				$processedData['config']['navbar']['navColorCSS'] = self::getNavigationColor();
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
			$jumbotronClass = $processedRecordVariables['jumbotronClass'] ?: '';
			$processedData['config']['jumbotron']['class'] = ' '.trim($jumbotronClass);

			# Image from pages media
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = [];

			if ( $processedRecordVariables['jumbotronBgimage'] == 'root' ) {
				// slide in rootline
				foreach ($frontendController->rootLine as $page) {
					$fileObjects = $fileRepository->findByRelation('pages', 'media', $page['uid']);
					$uid = $page['uid'];
					if ($fileObjects) break;
				}
				if ( count($fileObjects) > 1 ) {
					// slider
					$bgSlides = self::getBackgroundImageUtility()->getBgImage($uid, 'pages', TRUE, FALSE, [], FALSE, 0,
					  $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
					  $processedData['bgSlides'] = $bgSlides;
				} else {
					// background image
					$bgSlides = self::getBackgroundImageUtility()->getBgImage($uid, 'pages', TRUE, FALSE, [], FALSE,
					  $processedData['data']['uid'], $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
					  $processedData['config']['jumbotron']['bgImage'] = $bgSlides;
				}

			} elseif ( $processedRecordVariables['jumbotronBgimage'] == 'page' ) {
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				if ( count($fileObjects) > 1 ) {
					// slider
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
		}

		/**
		 * Background Image (body)
		 */
		if ( $contentObjectConfiguration['settings.']['config.']['backgroundImageEnable'] ) {

			$BodyBgImage = self::getBackgroundImageUtility()->getBgImage($frontendController->id, 'pages', FALSE, FALSE, [], TRUE, 0,
			 $webp, $contentObjectConfiguration['settings.']['bgMediaQueries']);
			 $bgImage = $BodyBgImage[1];
			if ( empty($bgImage) && $contentObjectConfiguration['settings.']['config.']['backgroundImageSlide'] ) {
				foreach ($frontendController->rootLine as $page) {
					$BodyBgImage = self::getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', FALSE, FALSE, [], TRUE, $frontendController->id, $webp);
					$bgImage = $BodyBgImage[1];
					if ($bgImage) break;
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
			}
		}
		if ( $processedRecordVariables['sidebarRightenable'] ) {
			$processedData['config']['sidebar']['right'] = $processedRecordVariables['sidebarRightenable'];
		}

		/**
		 * Footer
		 */
		if ( $processedRecordVariables['footerEnable'] ) {

			$processedData['config']['footer']['enable'] = $processedRecordVariables['footerEnable'];
			$processedData['config']['footer']['sticky'] = $processedRecordVariables['footerSticky'];
			$processedData['config']['footer']['fluid'] = $processedRecordVariables['footerFluid'];
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
	 * Returns the currently configured "site" if a site is configured (= resolved) in the current request.
	 *
	 * @return SiteInterface
	 */
	protected function getCurrentSite(): SiteInterface
	{
		$matcher = GeneralUtility::makeInstance(SiteMatcher::class);
		return $matcher->matchByPageId((int)self::getFrontendController()->id);
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}


	/**
	 * Returns an instance of the rbackground image utility
	 *
	 * @return BackgroundImageUtility
	 */
	protected function getBackgroundImageUtility(): BackgroundImageUtility
	{
		return GeneralUtility::makeInstance(BackgroundImageUtility::class);
	}


	/**
	 * Generate CSS for navigation color
	 *
	 * @return string
	 */
	protected function getNavigationColor(): string
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
			 ->execute();

		$navbarColors = $result->fetchAll();
		$navbarColorCSS = '';

		if (is_array($navbarColors)) {

			foreach($navbarColors as $navbarColor) {

				if (is_integer($navbarColor['uid'])) {

					$treePages = $this->getTreePids($navbarColor['uid']);

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


	function getTreePids($parent = 0): array
	{
		$queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);

		 $childPids = $queryGenerator->getTreeList($parent, 999999, 0, 1);
		 $childPids = explode(',',(string) $childPids );

		 return $childPids;
	}

}
