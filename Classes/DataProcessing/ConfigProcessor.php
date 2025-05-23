<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\PageTitle\BreadcrumbProvider;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ConfigProcessor implements DataProcessorInterface
{

	public function process(
		ContentObjectRenderer $cObj,
		array $contentObjectConfiguration,
		array $processorConfiguration,
		array $processedData
	): array
	{
		/** @var \Psr\Http\Message\ServerRequestInterface $request */
		$request = $cObj->getRequest();
		$settings = $contentObjectConfiguration['settings.'];
		$frontendController = $request->getAttribute('frontend.controller');
		if (!empty($contentObjectConfiguration['settings.']['config.']['uid'])
			 && is_numeric($contentObjectConfiguration['settings.']['config.']['uid'])) {
			$processedRecordVariables = $contentObjectConfiguration['settings.']['config.'];
		} else {
			$processedData['noConfig'] = true;
			return $processedData;
		}
		$currentPageUid = $request->getAttribute('routing')->getPageId();

		/**
		 * General
		 */
		// company w/ multilingual support
		$company = $processedRecordVariables['company'];
		$companyArr = GeneralUtility::trimExplode('|', $company);
		$sysLanguageUid = $processedData['data']['sys_language_uid'];
		$backendLayout = $processedData['data']['currentValue_kidjls9dksoje'];

		if ($sysLanguageUid && $company) {
			$company = !empty($companyArr[$sysLanguageUid]) ? $companyArr[$sysLanguageUid] : $company;
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
		$rootlinePage = $pageRepository->getPage((int) $processedRecordVariables['homepageUid']);
		$smallColumnsRootline = !empty($rootlinePage['tx_t3sbootstrap_smallColumns'])
		 ? (int)$rootlinePage['tx_t3sbootstrap_smallColumns'] : 3;
		$smallColumns = $smallColumnsCurrent ?: $smallColumnsRootline;

		// global override page data
		if (!empty($contentObjectConfiguration['settings.']['pages.']['override.'])) {
			foreach ($contentObjectConfiguration['settings.']['pages.']['override.'] as $field=>$override) {
				if (!empty($override)) {
					if ($field === 'smallColumns') {
						$processedData['colAside'] = $override;
						$processedData['data'][$field] = $override;
						$smallColumns = $override;
					} elseif ($field === 'tx_t3sbootstrap_container') {
						if (($backendLayout === 'OneCol' || $backendLayout === 'OneCol_Extra') && $processedData['data']['tx_t3sbootstrap_container'] === '0') {
							// no override if container = none
						} else {
							$processedData['data']['tx_t3sbootstrap_container'] = $override;
						}
					} elseif (($field === 'tx_t3sbootstrap_titlecolor' || $field === 'tx_t3sbootstrap_subtitlecolor') && str_starts_with($override, '--bs-')) {
						$processedData['data'][$field] = 'var('.$override.')';
					} else {
						$processedData['data'][$field] = $override;
					}
				}
			}
		}

		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		// chapter section
		if (!empty($extConf['chapter'])) {
			$processedData = $this->chapterSection($processedData, $currentPageUid);
		}

		/**
		 * Backend layout & smallColumns
		 */
		if (!empty($backendLayout)) {
			$oneCol = $backendLayout === 'OneCol' || $backendLayout === 'OneCol_Extra' ? true : false;
			$threeCol = $backendLayout === 'ThreeCol' || $backendLayout === 'ThreeCol_Extra' ? true : false;
		}

		if ($oneCol === false) {
			if ($threeCol === true) {
				$smallColumns = $smallColumns < 6 ? $smallColumns : 5;
				$processedData['colMain'] = 12 - $smallColumns * 2;
			} else {
				$processedData['colMain'] = 12 - $smallColumns;
			}
			$processedData['colAside'] = $smallColumns;
		}

		// grid breakpoint
		$processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';

		/**
		 * Page title provider - BreadcrumbProvider
		 */
		$configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
		$extbaseFrameworkConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		if ($extbaseFrameworkConfiguration['config.']['pageTitleProviders.']['breadcrumb.']['provider']
		 === 'T3SBS\T3sbootstrap\PageTitle\BreadcrumbProvider') {
			$titleProvider = GeneralUtility::makeInstance(BreadcrumbProvider::class);
			$titleProvider->setTitle('');
		}

		/**
		 * Language Navigation
		 */
		if ($processedRecordVariables['navbarLangmenu']) {
			$site = $request->getAttribute('site');
			$langUid = [];
			foreach ($site->getLanguages() as $lang) {
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
		if ($processedRecordVariables['metaEnable']) {
			$processedData['config']['meta']['align'] = $processedRecordVariables['metaEnable'];
			$processedData['config']['meta']['container'] = $processedRecordVariables['metaContainer'];
			$processedData['config']['meta']['class'] = ' '.trim($processedRecordVariables['metaClass']);
			$processedData['config']['meta']['text'] = $processedRecordVariables['metaText'] ? trim($processedRecordVariables['metaText']) : '';
		}

		/**
		 * Navbar
		 */
		if ($processedRecordVariables['navbarEnable']) {
			// navbar menu
			$mainMenu = [];
			if (!empty($processedData['navbarMenu'])) {
				foreach ($processedData['navbarMenu'] as $key=>$navbarMenu) {
					$mainMenu[$key] = $navbarMenu;
					if (!empty($navbarMenu['data']['page_icon'])) {
						$mainMenu[$key]['iconPack'] = $navbarMenu['data']['page_icon'];
					}
					$mainMenu[$key]['linkTitle'] = $navbarMenu['data']['title'];
					if (!empty($settings['navbar.']['noLinkTitle'])) {
						$mainMenu[$key]['linkTitle'] = '';
					}
					if ($navbarMenu['data']['tx_t3sbootstrap_icon_only']) {
						$mainMenu[$key]['linkTitle'] = $navbarMenu['data']['title'];
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
						$mainMenu[$key]['activeClass'] = '	parent-active';
					} else {
						$mainMenu[$key]['activeClass'] = '';
					}
					if (!empty($navbarMenu['children'][0])) {
						if ($this->getChildItems($navbarMenu['children'])) {
							$mainMenu[$key]['children'] = $this->getChildItems($navbarMenu['children']);
						}
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
			if (!empty($processedRecordVariables['navbarDropdownAnimate'])) {
				$processedData['config']['navbar']['dropdownAnimate'] =
				 ' dd-animate-'.(int)$processedRecordVariables['navbarDropdownAnimate'];
				$processedData['config']['navbar']['dropdownAnimateValue'] =
				 (int)$processedRecordVariables['navbarDropdownAnimate'];
			}

			// shortcut & clickableparent
			if (!empty($frontendController->rootLine[1]) && $frontendController->rootLine[1]['doktype'] == 4
				 && empty($processedRecordVariables['navbarPlusicon'])) {
				$processedData['config']['navbar']['clickableparent'] = 1;
			} else {
				$processedData['config']['navbar']['clickableparent'] = (int) $processedRecordVariables['navbarClickableparent'];
			}

			// image
			$processedData['config']['navbar']['image'] = $processedRecordVariables['navbarImage']
			? $processedRecordVariables['navbarImage'] : $contentObjectConfiguration['settings.']['navbar.']['image.']['defaultPath'];

			// container
			$processedData['config']['navbar']['container'] = !empty($processedRecordVariables['navbarContainer'])
			 ? $processedRecordVariables['navbarContainer'] : '';
			// inner container is required
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
			if ($processedRecordVariables['navbarTransparent'] && $processedRecordVariables['navbarPlacement'] === 'fixed-top') {
				$processedData['config']['navbar']['transparent'] = true;
			} else {
				$processedData['config']['navbar']['transparent'] = false;
			}
			if ($processedRecordVariables['navbarColor'] === 'color' && !empty($processedRecordVariables['navbarBackground'])) {
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
			if ($processedRecordVariables['navbarPlacement'] === 'fixed-top' && $processedRecordVariables['navbarShrinkcolor'] ) {
				$processedData['config']['navbar']['transparent'] = false;
				$navbarClass .= ' shrink py-'.$processedRecordVariables['shrinkingNavPadding'];
				$navbarShrinkcolorschemes = $processedRecordVariables['navbarShrinkcolorschemes'];
				$navBarAttr .= ' data-shrinkcolorschemes="bg-'.$navbarShrinkcolorschemes.'"';
				$shrinkColor = $processedRecordVariables['navbarShrinkcolor'];
				$navBarAttr .= ' data-shrinkcolor="'.$shrinkColor.'"';
				$navColorArr = explode(' ', $processedRecordVariables['navbarColor']);
				if (!empty($navColorArr[1])) {
					$navbarColor = 'bg-'.$navColorArr[0];
					$processedData['config']['navbar']['gradient'] = 'bg-gradient';
				} else {
					$navbarColor = $processedRecordVariables['navbarColor'];
				}
				$navBarAttr .= ' data-colorschemes="'.$navbarColor.'"';
				$navBarAttr .= ' data-color="navbar-'.$processedRecordVariables['navbarEnable'].'"';
			}
			$processedData['config']['navbar']['dataAttr'] = $navBarAttr;

			// placement
			if ($processedRecordVariables['navbarPlacement']) {
				$processedData['config']['navbar']['placement'] = $processedRecordVariables['navbarPlacement'];

				// sticky-top & navbar container
				if ($processedRecordVariables['navbarPlacement'] === 'sticky-top'
					 && !empty($processedData['config']['navbar']['container'])) {
					$processedData['config']['navbar']['container'] = $processedData['config']['navbar']['container'].' sticky-top';
				} else {
					$navbarClass .= ' '.$processedRecordVariables['navbarPlacement'];
				}
			}
			$dropdown = $processedRecordVariables['navbarPlacement'] === 'fixed-bottom' ? 'dropup' : 'dropdown';
			$processedData['config']['navbar']['dropdown'] = $dropdown;

			// set class
			$processedData['config']['navbar']['class'] = trim($navbarClass);

			// alignment
			$processedData['config']['navbar']['alignment'] = $processedRecordVariables['navbarAlignment'];
			if ($processedRecordVariables['navbarAlignment'] === 'fill') {
				$processedData['config']['navbar']['mauto'] = ' nav-fill w-100';
			} elseif ($processedRecordVariables['navbarAlignment'] === 'justified') {
				$processedData['config']['navbar']['mauto'] = ' nav-justified w-100';
			} elseif ($processedRecordVariables['navbarAlignment'] === 'center') {
				$processedData['config']['navbar']['navbarCenter'] = ' justify-content-center';
				$processedData['config']['navbar']['mauto'] = '';
			} else {
				$processedData['config']['navbar']['mauto'] = ($processedRecordVariables['navbarAlignment'] === 'right') ? ' ms-auto' : ' me-auto';
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
				if ($processedRecordVariables['navbarEnable'] === 'dark') {
					$processedData['config']['navbar']['offcanvasTitleColor'] = 'rgba(255, 255, 255, 0.75)';
					$processedData['config']['navbar']['offcanvasCross'] = 'white';
				} else {
					$processedData['config']['navbar']['offcanvasTitleColor'] = 'rgba(0, 0, 0, 0.75)';
					$processedData['config']['navbar']['offcanvasCross'] = 'dark';
				}
				if ($processedRecordVariables['navbarAlignment'] === 'left') {
					$processedData['config']['navbar']['navbarAlignment'] = 'start';
				} elseif ($processedRecordVariables['navbarAlignment'] === 'right') {
					$processedData['config']['navbar']['navbarAlignment'] = 'end';
				} else {
					$processedData['config']['navbar']['navbarAlignment'] = 'center';
				}
				if ($processedRecordVariables['navbarToggler'] === 'left') {
					$processedData['config']['navbar']['offcanvasAlign'] = 'start';
				} else {
					$processedData['config']['navbar']['offcanvasAlign'] = 'end';
				}
				$processedData['config']['navbar']['sectionMenuDataAttr'] = ' data-bs-dismiss="offcanvas" aria-label="Close"';
			}

			// searchbox
			if ($processedRecordVariables['navbarSearchbox']) {
				$processedData['config']['navbar']['searchbox'] = $processedRecordVariables['navbarSearchbox'];
				$processedData['config']['navbar']['searchboxcolor'] = $processedRecordVariables['navbarEnable'] === 'light' ? 'dark' : 'light';
				if ($processedData['config']['navbar']['mauto'] === ' me-auto') {
					$processedData['config']['navbar']['sbmauto'] = ' ms-auto';
				}
				if ($processedData['config']['navbar']['mauto'] === ' ms-auto') {
					$processedData['config']['navbar']['sbmauto'] = ' float-end ms-3';
				}
				if ($processedData['config']['navbar']['mauto'] === 'center') {
					$processedData['config']['navbar']['sbmauto'] = '';
				}
			}

			// color
			$processedData['config']['navbar']['colorschemes'] = explode(' ', $processedRecordVariables['navbarColor'])[0];
			$processedData['config']['navbar']['gradient'] = '';
			if (!empty($processedRecordVariables['navbarColor'])) {
				if (!empty(explode(' ', $processedRecordVariables['navbarColor'])[1])) {
					$processedData['config']['navbar']['gradient'] = explode(' ', $processedRecordVariables['navbarColor'])[1];
				}
			}

			// content only on rootpage
			if (($processedRecordVariables['homepageUid'] == $frontendController->id) && $processedRecordVariables['contentOnlyOnRootpage']) {
				$processedData['config']['navbar']['enable'] = false;
			}

		}

		/**
		 * Jumbotron
		 */
		if ($processedRecordVariables['jumbotronEnable']) {
			$processedData['config']['jumbotron']['enable'] = $processedRecordVariables['jumbotronEnable'];
			$processedData['config']['jumbotron']['slide'] = $processedRecordVariables['jumbotronSlide'];
			$processedData['config']['jumbotron']['position'] = $processedRecordVariables['jumbotronPosition'];
			$processedData['config']['jumbotron']['container'] = $processedRecordVariables['jumbotronContainer'];
			$processedData['config']['jumbotron']['containerposition'] = $processedRecordVariables['jumbotronContainerposition'];
			$processedData['config']['jumbotron']['class'] = ' '.trim($processedRecordVariables['jumbotronClass']);
			$processedData['config']['jumbotron']['noBgRatio'] = true;

			# Image from pages media
			$hasBgImages = 0;
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = [];

			if ( !empty($processedRecordVariables['jumbotronAlignitem']) ) {
				$processedData['config']['jumbotron']['alignItem'] = 'd-flex mx-auto align-items-'.$processedRecordVariables['jumbotronAlignitem'];
				$processedData['config']['jumbotron']['class'] .= ' d-flex';
			} else {
				$processedData['config']['jumbotron']['alignItem'] = ' d-flex';
			}

			$processedData['config']['jumbotron']['alignment'] = $processedRecordVariables['jumbotronAlignitem'];

			if ($processedRecordVariables['jumbotronBgimage'] === 'root') {
				// slide in rootline
				foreach ($frontendController->rootLine as $page) {
					$fileObjects = $fileRepository->findByRelation('pages', 'media', $page['uid']);
					$uid = $page['uid'];
					if ($fileObjects) {
						break;
					}
				}
				$hasBgImages = count($fileObjects);
				if (count($fileObjects) > 1) {
					if (!empty($settings['multiplePagesMedia'])) {
						// background images
						$bgSlides = $this->getBackgroundImageUtility()->getBgImage(
							$uid,
							'pages',
							true,
							false,
							[],
							false,
							$processedData['data']['uid'],
							$contentObjectConfiguration['settings.']['bgMediaQueries']
						);
						$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
						$processedData['config']['jumbotron']['multiplePagesMedia'] = true;
					} else {
						// slider
						$processedData['config']['jumbotron']['alignItem'] = '';
						$bgSlides = $this->getBackgroundImageUtility()->getBgImage(
							$uid,
							'pages',
							true,
							false,
							[],
							false,
							0,
							$contentObjectConfiguration['settings.']['bgMediaQueries']
						);
						$processedData['bgSlides'] = $bgSlides;
					}
				} else {
					// background image
					$bgSlides = $this->getBackgroundImageUtility()->getBgImage(
						$uid,
						'pages',
						true,
						false,
						[],
						false,
						$processedData['data']['uid'],
						$contentObjectConfiguration['settings.']['bgMediaQueries']
					);
					$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
					if (!empty($settings['multiplePagesMedia'])) {
						$processedData['config']['jumbotron']['multiplePagesMedia'] = false;
					}
				}
			} elseif ($processedRecordVariables['jumbotronBgimage'] === 'page') {
				$fileObjects = $fileRepository->findByRelation('pages', 'media', $frontendController->id);
				$hasBgImages = count($fileObjects);
				if (count($fileObjects) > 1) {
					// slider
					$processedData['config']['jumbotron']['alignItem'] = '';
					$bgSlides = $this->getBackgroundImageUtility()->getBgImage(
						$frontendController->id,
						'pages',
						true,
						false,
						[],
						false,
						0,
						$contentObjectConfiguration['settings.']['bgMediaQueries']
					);
					$processedData['bgSlides'] = $bgSlides;
				} else {
					// background image
					$bgSlides = $this->getBackgroundImageUtility()->getBgImage(
						$frontendController->id,
						'pages',
						true,
						false,
						[],
						false,
						0,
						$contentObjectConfiguration['settings.']['bgMediaQueries']
					);
					$processedData['config']['jumbotron']['bgImage'] = $bgSlides;
				}
			}

			if ($hasBgImages && empty($currentPage['tx_t3sbootstrap_fullheightsection']) && !empty($processedRecordVariables['jumbotronBgimageratio'])) {
				$processedData['config']['jumbotron']['noBgRatio'] = false;
				$processedData['config']['jumbotron']['class'] .= ' ratio ratio-'.$processedRecordVariables['jumbotronBgimageratio'];
				$ratioArr = explode('x', $processedRecordVariables['jumbotronBgimageratio']);
				$x = $processedRecordVariables['jumbotronBgimageratio'];
				$y = $ratioArr[1].' / '.$ratioArr[0].' * 100%';
				$processedData['ratioCalcCss'] = '.ratio-'.$x.'{--bs-aspect-ratio:calc('.$y.');}';
			} else {
				if ( !empty($processedData['data']['tx_t3sbootstrap_fullheightsection']) ) {
					$processedData['config']['jumbotron']['class'] = ' ratio';
				}
			}

		}

		/**
		 * Background Image (body)
		 */
		if ($processedRecordVariables['backgroundImageEnable']) {
			$BodyBgImage = $this->getBackgroundImageUtility()->getBgImage(
				$frontendController->id,
				'pages',
				false,
				false,
				[],
				true,
				0,
				$contentObjectConfiguration['settings.']['bgMediaQueries']
			);
			$bgImage = is_array($BodyBgImage) ? $BodyBgImage[1] : '';
			if (empty($BodyBgImage) && $processedRecordVariables['backgroundImageSlide']) {
				foreach ($frontendController->rootLine as $page) {
					$BodyBgImage = $this->getBackgroundImageUtility()->getBgImage($page['uid'], 'pages', false, false, [], true, $frontendController->id);
					if ($BodyBgImage) {
						break;
					}
				}
			}
		}

		/**
		 * Breadcrumb
		 */
		$processedData['config']['breadcrumb']['class'] = '';
		if ($processedRecordVariables['breadcrumbEnable'] || $processedRecordVariables['breadcrumbBottom']) {
			if (($processedRecordVariables['homepageUid'] == $frontendController->id) && $processedRecordVariables['breadcrumbNotonrootpage']) {
				$processedData['config']['breadcrumb']['enable'] = false;
				$processedData['config']['breadcrumb']['bottom'] = false;
			} else {
				$processedData['config']['breadcrumb']['enable'] = $processedRecordVariables['breadcrumbEnable'];
				$processedData['config']['breadcrumb']['bottom'] = $processedRecordVariables['breadcrumbBottom'];
				$processedData['config']['breadcrumb']['faicon'] = $processedRecordVariables['breadcrumbFaicon'];
				$processedData['config']['breadcrumb']['position'] = $processedRecordVariables['breadcrumbPosition'];
				$processedData['config']['breadcrumb']['container'] = $processedRecordVariables['breadcrumbContainer'];
				$processedData['config']['breadcrumb']['containerposition'] = $processedRecordVariables['breadcrumbContainerposition'];
				$processedData['config']['breadcrumb']['class'] .= $processedRecordVariables['breadcrumbClass'] ?: '';
				$processedData['config']['breadcrumb']['ol-class'] = $processedRecordVariables['breadcrumbCorner'] ? ' rounded-0' : '';
			}
		}

		/**
		 * Sidebar / submenu
		 */
		if ($processedRecordVariables['sidebarEnable']) {
			$processedData['config']['sidebar']['left'] = $processedRecordVariables['sidebarEnable'];
			if ($processedRecordVariables['sidebarEnable'] === 'Section') {
				$processedData['config']['sidebar']['enable'] = true;
				$processedData['config']['sidebar']['stickTopClass'] = $processedRecordVariables['sectionmenuStickyTop'] ? ' sticky-top' : '';
				$topOffset = (int)$processedRecordVariables['sectionmenuAnchorOffset'] + (int)$processedRecordVariables['navbarHeight'];
				$processedData['config']['sidebar']['stickTopOffset'] = $topOffset ? $topOffset.'px' : 0;
				$processedData['config']['sidebar']['scrollspy'] = $processedRecordVariables['sectionmenuScrollspy'];
			} else {
				if (!empty($processedData['subNavigation']) && is_array($processedData['subNavigation'])) {
					$processedData['subNavigation'] =
					 $this->getSubNavigation($processedData['subNavigation'], (int)$processedRecordVariables['navbarClickableparent']);
				}
			}
			$processedData['config']['sidebar']['sticky'] = $processedRecordVariables['submenuSticky'];
		}
		if ($processedRecordVariables['sidebarRightenable']) {
			$processedData['config']['sidebar']['right'] = $processedRecordVariables['sidebarRightenable'];
			$processedData['config']['sidebar']['sticky'] = $processedRecordVariables['submenuSticky'];
		}

		/**
		 * Footer
		 */
		if ($processedRecordVariables['footerEnable']) {
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
		$processedData['config']['expandedcontentTop']['enable'] = $processedRecordVariables['expandedcontentEnabletop'];
		$processedData['config']['expandedcontentTop']['slide'] = $processedRecordVariables['expandedcontentSlidetop'];
		$processedData['config']['expandedcontentTop']['container'] = $processedRecordVariables['expandedcontentContainertop'];
		$processedData['config']['expandedcontentTop']['containerposition'] = $processedRecordVariables['expandedcontentContainerpositiontop'];
		$processedData['config']['expandedcontentTop']['class'] = trim($processedRecordVariables['expandedcontentClasstop'] ?? '');

		$processedData['config']['expandedcontentBottom']['enable'] = $processedRecordVariables['expandedcontentEnablebottom'];
		$processedData['config']['expandedcontentBottom']['slide'] = $processedRecordVariables['expandedcontentSlidebottom'];
		$processedData['config']['expandedcontentBottom']['container'] = $processedRecordVariables['expandedcontentContainerbottom'];
		$processedData['config']['expandedcontentBottom']['containerposition'] = $processedRecordVariables['expandedcontentContainerpositionbottom'];
		$processedData['config']['expandedcontentBottom']['class'] = trim($processedRecordVariables['expandedcontentClassbottom'] ?? '');

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
	 * Returns an array
	 */
	protected function getSubNavigation(array $subNavigation, int $navbarClickableparent): array
	{
		$res = [];
		foreach ($subNavigation as $supNav) {
			if (!empty($supNav['children']) && is_array($supNav['children'])) {
				self::getSubNavigation($supNav['children'], $navbarClickableparent);
				if ($navbarClickableparent === 0) {
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
			if (!empty($child['data']['page_icon'])) {
				$mainMenu[$cKey]['iconPack'] = $child['data']['page_icon'];
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
				$mainMenu[$cKey]['activeClass'] = '	 parent-active';
			} else {
				$mainMenu[$cKey]['activeClass'] = '';
			}
		}

		return $mainMenu;
	}



	function chapterSection(array $processedData, int $currentPageUid): array
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
			->select('uid', 'header', 'tx_t3sbootstrap_chapter')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($currentPageUid, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
			)
			->executeQuery();

		$erg = [];
		$i = -1;
		while ($row = $result->fetchAssociative()) {
			if (!empty($row['tx_t3sbootstrap_chapter'])) {

				if ($row['tx_t3sbootstrap_chapter'] === '1') {
					$i++;
					$erg[$i][$row['uid']] = $row;
				} else {
					$erg[$i][$row['uid']] = $row;
				}
			}
		}
		$processedData['chapter'] = $erg;


		return $processedData;
	}


}
