<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class ConfigProcessor implements DataProcessorInterface
{
	
	public function __construct(
		private readonly FileRepository $fileRepository,
		private readonly BackgroundImageUtility $backgroundImageUtility,
	) {}
	

	public function process(
		ContentObjectRenderer $cObj,
		array $contentObjectConfiguration,
		array $processorConfiguration,
		array $processedData
	): array {
		$processedData['customScss-cdn-dupe-set'] = false;
		
		if (!empty($contentObjectConfiguration['settings.']['customScss'])
			&& !empty($contentObjectConfiguration['settings.']['cdn'])
		) {
			$processedData['customScss-cdn-dupe-set'] = true;
			return $processedData;
		}

		if (empty($contentObjectConfiguration['settings.']['config.']['uid'])
			|| !is_numeric($contentObjectConfiguration['settings.']['config.']['uid'])
		) {
			$processedData['noConfig'] = true;
			return $processedData;
		}

		$settings             = $contentObjectConfiguration['settings.'];
		$request              = $cObj->getRequest();
		$pageInformation      = $request->getAttribute('frontend.page.information');
		$site                 = $processedData['site'];
		$siteSettings         = $site->getConfiguration()['settings']['bootstrap'];
		$processedRecordVars  = $contentObjectConfiguration['settings.']['config.'];

		// Type Normalization (TypoScript/DB returns strings)
		$processedRecordVars['homepageUid'] = (int)($processedRecordVars['homepageUid'] ?? 0) ?: 1;
			
		$currentPage          = $pageInformation->getPageRecord();
		$backendLayout        = $processedData['data']['currentValue_kidjls9dksoje'];

		$processedData = $this->processExpandedContent($processedData, $processedRecordVars, $currentPage);
		$processedData = $this->processGeneral($processedData, $processedRecordVars, $pageInformation, $siteSettings, $backendLayout, $settings);
		$processedData = $this->processNavbar($processedData, $processedRecordVars, $pageInformation, $settings, $request);
		$processedData = $this->processJumbotron($processedData, $processedRecordVars, $pageInformation, $currentPage, $settings, $request);
		$processedData = $this->processBackgroundImage($processedData, $processedRecordVars, $pageInformation, $settings);
		$processedData = $this->processBreadcrumb($processedData, $processedRecordVars, $pageInformation);
		$processedData = $this->processSidebar($processedData, $processedRecordVars);
		$processedData = $this->processFooter($processedData, $processedRecordVars);

		return $processedData;
	}

	// ─── General ─────────────────────────────────────────────────────────────

	private function processGeneral(
		array $processedData,
		array $vars,
		object $pageInformation,
		array $siteSettings,
		string $backendLayout,
		array $settings
	): array {
		$company    = $vars['company'] ?? '';
		$companyArr = GeneralUtility::trimExplode('|', $company);
		$langUid = (int)($processedData['data']['sys_language_uid'] ?? 0);

		if ($langUid && !empty($company)) {
			$company = !empty($companyArr[$langUid]) ? $companyArr[$langUid] : $company;
		} else {
			$company = $companyArr[0] ?: $company;
		}

		$processedData['config']['general']['company']        = !empty($company) ? trim($company) : 'Company Name';
		$processedData['config']['general']['homepageUid']    = $vars['homepageUid']     ?: 1;
		$processedData['config']['general']['pageTitle']      = $vars['pageTitle']        ?: '';
		$processedData['config']['general']['pageTitlealign'] = $vars['pageTitlealign']   ?: '';
		$processedData['config']['general']['pageTitleclass'] = $vars['pageTitleclass']   ?: '';

		$currentPage          = $pageInformation->getPageRecord();
		$smallColumnsCurrent  = (int)$currentPage['tx_t3sbootstrap_smallColumns'];
		$smallColumnsRootline = !empty($pageInformation->getRootLine()[0]['tx_t3sbootstrap_smallColumns'])
			? (int)$pageInformation->getRootLine()[0]['tx_t3sbootstrap_smallColumns'] : 3;
		$smallColumns = $smallColumnsCurrent ?: $smallColumnsRootline;

		if (ExtensionManagementUtility::isLoaded('indexed_search') && $currentPage['doktype']) {
			$processedData['config']['general']['pageTitleSearch'] = true;
		}

		if (!empty($siteSettings['pages']['override'])) {
			foreach ($siteSettings['pages']['override'] as $field => $override) {
				if (empty($override)) {
					continue;
				}
				if ($field === 'smallColumns') {
					$processedData['colAside']  = $override;
					$processedData['data'][$field] = $override;
					$smallColumns = $override;
				} elseif ($field === 'container') {
					$isOneCol = in_array($backendLayout, ['OneCol', 'OneCol_Extra'], true);
					if (!($isOneCol && $processedData['data']['tx_t3sbootstrap_container'] === 'none')) {
						$processedData['data']['tx_t3sbootstrap_container'] = $override;
					}
				} elseif (
					in_array($field, ['tx_t3sbootstrap_titlecolor', 'tx_t3sbootstrap_subtitlecolor'], true)
					&& str_starts_with($override, '--bs-')
				) {
					$processedData['data'][$field] = 'var(' . $override . ')';
				} else {
					$processedData['data'][$field] = $override;
				}
			}
		}

		$oneCol   = in_array($backendLayout, ['OneCol', 'OneCol_Extra'], true);
		$threeCol = in_array($backendLayout, ['ThreeCol', 'ThreeCol_Extra'], true);

		if (!$oneCol) {
			if ($threeCol) {
				$smallColumns = $smallColumns < 6 ? $smallColumns : 5;
				$processedData['colMain'] = 12 - $smallColumns * 2;
			} else {
				$processedData['colMain'] = 12 - $smallColumns;
			}
			$processedData['colAside'] = $smallColumns;
		}

		$processedData['gridBreakpoint'] = $currentPage['tx_t3sbootstrap_breakpoint'] ?: 'md';

		return $processedData;
	}

	// ─── Language Navigation ──────────────────────────────────────────────────

	private function processNavbar(
		array $processedData,
		array $vars,
		object $pageInformation,
		array $settings,
		object $request
	): array {
		if (!$vars['navbarLangmenu'] && !$vars['navbarEnable']) {
			return $processedData;
		}

		$site = $processedData['site'];

		if ($vars['navbarLangmenu']) {
			$langUid = $langTitle = $langHref = $langFlag = [];
			foreach ($site->getLanguages() as $lang) {
				// @extensionScannerIgnoreLine
				$id = $lang->getLanguageId();
				$langUid[$id]   = $id;
				// @extensionScannerIgnoreLine
				$langTitle[$id] = $lang->getNavigationTitle();
				// @extensionScannerIgnoreLine
				$langHref[$id]  = $lang->getHreflang();
				// @extensionScannerIgnoreLine
				$langFlag[$id]  = $lang->getFlagIdentifier();
			}
			$processedData['config']['lang'] = [
				'uid'      => $langUid      ?: '',
				'hreflang' => $langHref     ?: '',
				'title'    => $langTitle    ?: '',
				'flag'     => $langFlag     ?: '',
			];
		}

		if (!$vars['navbarEnable']) {
			return $processedData;
		}

		// Menu items
		$mainMenu = [];
		foreach ($processedData['navbarMenu'] ?? [] as $key => $navbarMenu) {
			$item              = $navbarMenu;
			$item['iconPack']  = $navbarMenu['data']['page_icon'] ?? '';
			$item['linkTitle'] = !empty($navbarMenu['data']['title']) ? $navbarMenu['data']['title'] : '';

			if (!empty($settings['navbar.']['noLinkTitle'])) {
				$item['linkTitle'] = '';
			}
			if ($navbarMenu['data']['tx_t3sbootstrap_icon_only']) {
				$item['linkTitle'] = !empty($navbarMenu['data']['title']) ? $navbarMenu['data']['title'] : '';
				$item['title']     = '';
			}

			$item['target']           = $navbarMenu['data']['target'] ?: '_self';
			$item['dropdownRightClass'] = !empty($navbarMenu['data']['tx_t3sbootstrap_dropdownRight'])
				? ' dropdown-menu-end' : '';

			$item['activeClass'] = match(true) {
				!empty($navbarMenu['current']) && !empty($navbarMenu['active']) => ' active',
				!empty($navbarMenu['active'])                                   => ' parent-active',
				default                                                         => '',
			};
			if (!empty($navbarMenu['current']) && !empty($navbarMenu['active'])) {
				$item['active'] = 0;
			}

			if (!empty($navbarMenu['children'][0])) {
				$childItems = $this->getChildItems($navbarMenu['children']);
				if ($childItems) {
					$item['children'] = $childItems;
				}
			}

			$mainMenu[$key] = $item;
		}
		$processedData['navbarMenu'] = $mainMenu;

		$cfg = &$processedData['config']['navbar'];

		$cfg['enable']        = $vars['navbarEnable'];
		$cfg['sectionMenu']   = $vars['navbarSectionmenu']  ? ' section-menu'      : '';
		$cfg['hover']         = $vars['navbarHover']        ? ' dropdown-hover'    : '';
		$cfg['spacer']        = $vars['navbarIncludespacer'];
		$cfg['megamenu']      = $vars['navbarMegamenu'];
		$cfg['dataToggle']    = 'collapse';
		$cfg['bstoggle']      = 'dropdown';

		if (!empty($vars['navbarDropdownAnimate'])) {
			$cfg['dropdownAnimate']      = ' dd-animate-' . (int)$vars['navbarDropdownAnimate'];
			$cfg['dropdownAnimateValue'] = (int)$vars['navbarDropdownAnimate'];
		}

		$rootLine = $pageInformation->getRootLine();
		$cfg['clickableparent'] = (!empty($rootLine[1]) && $rootLine[1]['doktype'] === 4 && empty($vars['navbarPlusicon']))
			? 1 : (int)$vars['navbarClickableparent'];

		$cfg['clickableparent'] = (!empty($rootLine[1]) && (int)($rootLine[1]['doktype'] ?? 0) === 4 && empty($vars['navbarPlusicon']))
			? 1 : (int)$vars['navbarClickableparent'];
			
		$cfg['image']          = $vars['navbarImage'] ?: ($settings['navbar.']['image.']['defaultPath'] ?? '');
		$cfg['container']      = $vars['navbarContainer']     ?? '';
		$cfg['innercontainer'] = $vars['navbarInnercontainer'] ?: 'container';
		$cfg['brand']          = $vars['navbarBrand'];
		$cfg['brandAlignment'] = $vars['navbarbrandAlignment'];

		if ($vars['navbarBrand'] === 'imgText' && !empty($vars['company'])) {
			$cfg['brandClass'] = ' d-inline-block me-2';
		}

		$cfg['toggler']         = $vars['navbarToggler'];
		$cfg['animatedToggler'] = $vars['navbarAnimatedtoggler'];
		$cfg['breakpoint']      = $vars['navbarBreakpoint'];
		$processedData['navbarBreakpoint'] = $vars['navbarBreakpoint'] ?: 'md';

		$navbarClass  = 'navbar-' . $vars['navbarEnable'];
		$navbarClass .= $vars['navbarBreakpoint'] ? ' navbar-expand-' . $vars['navbarBreakpoint'] : ' navbar-expand-sm';
		$navbarClass .= $vars['navbarClass']        ? ' ' . $vars['navbarClass']  : '';
		$navbarClass .= $vars['navbarSectionmenu']  ? ' sectionMenu'              : '';
		$navbarClass .= $vars['navbarClickableparent'] ? ' clickableparent'        : '';
		$navbarClass .= $vars['navbarHover']        ? ' navbarHover'              : '';

		$cfg['transparent'] = $vars['navbarTransparent'] && $vars['navbarPlacement'] === 'fixed-top';

		if ($vars['navbarColor'] === 'color' && !empty($vars['navbarBackground'])) {
			$cfg['styleAttr'] = ' style="background-color: ' . $vars['navbarBackground'] . ';"';
		} elseif (!$cfg['transparent']) {
			$navbarClass .= ' bg-' . $vars['navbarColor'];
		}

		if (!empty($vars['navbarPlusicon'])) {
			$cfg['navbarPlusicon'] = $vars['navbarPlusicon'];
			$cfg['bstoggle']      = 'none';
			$cfg['hover']         = '';
			$navbarClass         .= ' navplusicon';
		}

		$navBarAttr = '';
		if ($vars['navbarPlacement'] === 'fixed-top' && $vars['navbarShrinkcolor']) {
			$cfg['transparent'] = false;
			$navbarClass       .= ' shrink py-' . $vars['shrinkingNavPadding'];
			$navColorParts      = explode(' ', $vars['navbarColor']);
			$navBarAttr        .= ' data-shrinkcolorschemes="bg-' . $vars['navbarShrinkcolorschemes'] . '"';
			$navBarAttr        .= ' data-shrinkcolor="' . $vars['navbarShrinkcolor'] . '"';
			$navBarAttr        .= ' data-colorschemes="' . ($navColorParts[1] ? 'bg-' . $navColorParts[0] : $vars['navbarColor']) . '"';
			$navBarAttr        .= ' data-color="navbar-' . $vars['navbarEnable'] . '"';
			if (!empty($navColorParts[1])) {
				$cfg['gradient'] = 'bg-gradient';
			}
		}
		$cfg['dataAttr'] = $navBarAttr;

		if ($vars['navbarPlacement']) {
			$cfg['placement'] = $vars['navbarPlacement'];
			if ($vars['navbarPlacement'] === 'sticky-top' && !empty($cfg['container'])) {
				$cfg['container'] .= ' sticky-top';
			} else {
				$navbarClass .= ' ' . $vars['navbarPlacement'];
			}
		}
		$cfg['dropdown'] = $vars['navbarPlacement'] === 'fixed-bottom' ? 'dropup' : 'dropdown';
		$cfg['class']    = trim($navbarClass);

		$cfg['alignment'] = $vars['navbarAlignment'];
		$cfg['mauto'] = match ($vars['navbarAlignment']) {
			'fill'      => ' nav-fill w-100',
			'justified' => ' nav-justified w-100',
			'right'     => ' ms-auto',
			'center'    => '',
			default     => ' me-auto',
		};
		if ($vars['navbarAlignment'] === 'center') {
			$cfg['navbarCenter'] = ' justify-content-center';
		}

		if ($vars['navbarExtraRow']) {
			$cfg['navbarExtraRow'] = ' flex-column';
		}

		if ($vars['navbarOffcanvas']) {
			$cfg['offcanvas']           = $vars['navbarOffcanvas'];
			$cfg['dataToggle']          = 'offcanvas';
			$cfg['offcanvasBgColorClass'] = 'bg-' . $vars['navbarColor'];
			$isDark = $vars['navbarEnable'] === 'dark';
			$cfg['offcanvasTitleColor'] = $isDark ? 'rgba(255, 255, 255, 0.75)' : 'rgba(0, 0, 0, 0.75)';
			$cfg['offcanvasCross']      = $isDark ? 'white' : 'dark';
			$cfg['navbarAlignment']     = match ($vars['navbarAlignment']) {
				'left'  => 'start',
				'right' => 'end',
				default => 'center',
			};
			$cfg['offcanvasAlign']           = $vars['navbarToggler'] === 'left' ? 'start' : 'end';
			$cfg['sectionMenuDataAttr']       = ' data-bs-dismiss="offcanvas" aria-label="Close"';
		}

		if ($vars['navbarSearchbox']) {
			$cfg['searchbox']      = $vars['navbarSearchbox'];
			$cfg['searchboxcolor'] = $vars['navbarEnable'] === 'light' ? 'dark' : 'light';
			$cfg['sbmauto'] = match ($cfg['mauto']) {
				' me-auto' => ' ms-auto',
				' ms-auto' => ' float-end ms-3',
				default    => '',
			};
		}

		$colorParts = explode(' ', $vars['navbarColor']);
		$cfg['colorschemes'] = $colorParts[0];
		$cfg['gradient']     = $cfg['gradient'] ?? ($colorParts[1] ?? '');

		if ((int)$vars['homepageUid'] === (int)$pageInformation->getId() && $vars['contentOnlyOnRootpage']) {
			$cfg['enable'] = false;
		}

		unset($cfg);

		return $processedData;
	}

	// ─── Jumbotron ────────────────────────────────────────────────────────────

	private function processJumbotron(
		array $processedData,
		array $vars,
		object $pageInformation,
		array $currentPage,
		array $settings,
		object $request
	): array {
		if (!$vars['jumbotronEnable']) {
			return $processedData;
		}

		$cfg = &$processedData['config']['jumbotron'];
		$cfg['enable']             = $vars['jumbotronEnable'];
		$cfg['slide']              = $vars['jumbotronSlide'];
		$cfg['position']           = $vars['jumbotronPosition'];
		$cfg['container']          = $vars['jumbotronContainer'];
		$cfg['containerposition']  = $vars['jumbotronContainerposition'];
		$cfg['class']              = ' ' . trim($vars['jumbotronClass']);
		$cfg['noBgRatio']          = true;
		$cfg['alignment']          = $vars['jumbotronAlignitem'];

		if (!empty($vars['jumbotronAlignitem'])) {
			$cfg['alignItem'] = 'd-flex mx-auto align-items-' . $vars['jumbotronAlignitem'];
			$cfg['class']    .= ' d-flex';
		} else {
			$cfg['alignItem'] = ' d-flex';
		}

		$bgMediaQueries = $settings['bgMediaQueries'];
		$hasBgImages    = 0;
		$bgSlides       = [];

		if ($vars['jumbotronBgimage'] === 'root') {
			$fileObjects = [];
			$uid         = 0;
			foreach ($pageInformation->getRootLine() as $page) {
				$found = $this->fileRepository->findByRelation('pages', 'media', $page['uid']);
				if (!empty($found)) {
					$fileObjects = $found;
					$uid         = $page['uid'];
					break;
				}
			}

			$hasBgImages = count($fileObjects);
			if ($hasBgImages > 1) {
				$cfg['alignItem'] = '';
				$bgSlides = $this->backgroundImageUtility->getJumbotronBgSlider(
					$uid, $fileObjects, $bgMediaQueries, $pageInformation->getId()
				);
			} elseif ($hasBgImages === 1) {
				$bgSlides[0] = $this->backgroundImageUtility->getJumbotronBgImage(
					$uid, $fileObjects, $bgMediaQueries
				);
			}
			$processedData['bgSlides'] = $bgSlides;

		} elseif ($vars['jumbotronBgimage'] === 'page') {
			$fileObjects         = $this->fileRepository->findByRelation('pages', 'media', $pageInformation->getId());
			$hasBgImages         = count($fileObjects);
			$localFullHeightBgVideo = !empty($fileObjects[0])
				&& $fileObjects[0]->getOriginalFile()->getMimeType() === 'video/mp4';

			if ($localFullHeightBgVideo) {
				$processedData['localFullHeightBgVideo'] = true;
			}

			if ($hasBgImages > 1 && !$localFullHeightBgVideo) {
				$cfg['alignItem'] = '';
				$bgSlides = $this->backgroundImageUtility->getJumbotronBgSlider(
					$pageInformation->getId(), $fileObjects, $bgMediaQueries, $pageInformation->getId()
				);
				$processedData['bgSlides'] = $bgSlides;
			} else {
				if ($localFullHeightBgVideo) {
					$bgSlides = $this->backgroundImageUtility->getJumbotronBgImage(
						$pageInformation->getId(), $fileObjects, $bgMediaQueries
					);
					$serverParams            = $request->getServerParams();
					$processedData['baseUri'] = ($serverParams['REQUEST_SCHEME'] ?? 'https')
						. '://' . ($serverParams['HTTP_HOST'] ?? '');
				} else {
					$bgSlides[0] = $this->backgroundImageUtility->getJumbotronBgImage(
						$pageInformation->getId(), $fileObjects, $bgMediaQueries
					);
				}
				$cfg['bgImage']            = $bgSlides;
				$processedData['bgSlides'] = $bgSlides;
			}
		}

		$ratio = $this->normalizeRatio($vars['jumbotronBgimageratio'] ?? '', '37x9');

		if ($hasBgImages && empty($currentPage['tx_t3sbootstrap_fullheightsection'])) {
			$cfg['noBgRatio'] = false;
			$cfg['class']    .= ' ratio ratio-' . $ratio;
			$ratioArr         = explode('x', $ratio);
			$processedData['ratioCalcCss'] = '.ratio-' . $ratio
				. '{--bs-aspect-ratio:calc(' . $ratioArr[1] . ' / ' . $ratioArr[0] . ' * 100%);}';
		} elseif (!empty($processedData['data']['tx_t3sbootstrap_fullheightsection'])) {
			$cfg['class'] = ' ratio';
		}

		unset($cfg);

		return $processedData;
	}

	// ─── Background Image ─────────────────────────────────────────────────────

	private function processBackgroundImage(
		array $processedData,
		array $vars,
		object $pageInformation,
		array $settings
	): array {
		if (!$vars['backgroundImageEnable']) {
			return $processedData;
		}

		if ($vars['backgroundImageSlide'] && !empty($processedData['rootFiles'])) {
			$this->backgroundImageUtility->getBgImage(
				$pageInformation->getId(),
				$processedData['rootFiles'][0],
				$settings['bgMediaQueries']
			);
		} elseif (!empty($processedData['pagesMedia'])) {
			$this->backgroundImageUtility->getBgImage(
				$pageInformation->getId(),
				$processedData['pagesMedia'][0],
				$settings['bgMediaQueries']
			);
		}

		return $processedData;
	}

	// ─── Breadcrumb ───────────────────────────────────────────────────────────

	private function processBreadcrumb(
		array $processedData,
		array $vars,
		object $pageInformation
	): array {
		$processedData['config']['breadcrumb']['class'] = '';

		if (!$vars['breadcrumbEnable'] && !$vars['breadcrumbBottom']) {
			return $processedData;
		}

		if ((int)$vars['homepageUid'] === (int)$pageInformation->getId() && $vars['breadcrumbNotonrootpage']) {
			$processedData['config']['breadcrumb']['enable'] = false;
			$processedData['config']['breadcrumb']['bottom'] = false;
			return $processedData;
		}

		$processedData['config']['breadcrumb'] = array_merge(
			$processedData['config']['breadcrumb'],
			[
				'enable'             => $vars['breadcrumbEnable'],
				'bottom'             => $vars['breadcrumbBottom'],
				'faicon'             => $vars['breadcrumbFaicon'],
				'position'           => $vars['breadcrumbPosition'],
				'container'          => $vars['breadcrumbContainer'],
				'containerposition'  => $vars['breadcrumbContainerposition'],
				'class'              => $vars['breadcrumbClass'] ?: '',
				'ol-class'           => $vars['breadcrumbCorner'] ? ' rounded-0' : '',
			]
		);

		return $processedData;
	}

	// ─── Sidebar ──────────────────────────────────────────────────────────────

	private function processSidebar(array $processedData, array $vars): array
	{
		if ($vars['sidebarEnable']) {
			$processedData['config']['sidebar']['left'] = $vars['sidebarEnable'];

			if ($vars['sidebarEnable'] === 'Section') {
				$topOffset = (int)$vars['sectionmenuAnchorOffset'] + (int)$vars['navbarHeight'];
				$processedData['config']['sidebar'] = array_merge(
					$processedData['config']['sidebar'] ?? [],
					[
						'enable'          => true,
						'stickTopClass'   => $vars['sectionmenuStickyTop'] ? ' sticky-top' : '',
						'stickTopOffset'  => $topOffset ? $topOffset . 'px' : 0,
						'scrollspy'       => $vars['sectionmenuScrollspy'],
					]
				);
			} elseif (!empty($processedData['subNavigation']) && is_array($processedData['subNavigation'])) {
				$processedData['subNavigation'] = $this->getSubNavigation(
					$processedData['subNavigation'],
					(int)$vars['navbarClickableparent']
				);
			}

			$processedData['config']['sidebar']['sticky'] = $vars['submenuSticky'];
		}

		if ($vars['sidebarRightenable']) {
			$processedData['config']['sidebar']['right']  = $vars['sidebarRightenable'];
			$processedData['config']['sidebar']['sticky'] = $vars['submenuSticky'];
		}

		return $processedData;
	}

	// ─── Footer ───────────────────────────────────────────────────────────────

	private function processFooter(array $processedData, array $vars): array
	{
		if (!$vars['footerEnable']) {
			return $processedData;
		}

		$footerClass  = $vars['footerClass'] ?: '';
		$footerClass .= $vars['footerSticky'] ? ' footer-sticky' : '';

		$processedData['config']['footer'] = [
			'enable'             => $vars['footerEnable'],
			'sticky'             => $vars['footerSticky'],
			'slide'              => $vars['footerSlide'],
			'container'          => $vars['footerContainer'],
			'containerposition'  => $vars['footerContainerposition'],
			'class'              => trim($footerClass),
		];

		return $processedData;
	}

	// ─── Expanded Content ────────────────────────────────────────────────────

	private function processExpandedContent(array $processedData, array $vars, array $currentPage): array
	{
		foreach (['Top' => 'top', 'Bottom' => 'bottom'] as $key => $suffix) {
			$processedData['config']['expandedcontent' . $key] = [
				'enable'             => $vars['expandedcontentEnable'    . $suffix],
				'slide'              => $vars['expandedcontentSlide'     . $suffix],
				'container'          => $vars['expandedcontentContainer' . $suffix],
				'containerposition'  => $vars['expandedcontentContainerposition' . $suffix],
				'class'              => trim($vars['expandedcontentClass' . $suffix] ?? ''),
			];
		}
		// is no longer needed
		unset($processedData['config']['expandedcontentBottom']['enable']);

		if (!empty($processedData['config']['expandedcontentTop']['enable'])) {
			$processedData['config']['expandedcontentTop']['hasContent'] = $this->hasContent($currentPage['uid'], 20);
			$processedData['config']['expandedcontentBottom']['hasContent'] = $this->hasContent($currentPage['uid'], 21);
		}

		return $processedData;
	}

	// ─── Hilfsmethoden ───────────────────────────────────────────────────────

	/**
	 * Normalisiert Seitenverhältnisse auf "WxH"-Format.
	 */
	private function normalizeRatio(string $raw, string $fallback = '16x9'): string
	{
		if (empty($raw)) {
			return $fallback;
		}

		$normalized = str_replace([':', '/', 'by'], 'x', $raw);

		return str_contains($normalized, 'x') ? $normalized : $fallback;
	}

	protected function getSubNavigation(array $subNavigation, int $navbarClickableparent): array
	{
		$res = [];
		foreach ($subNavigation as $supNav) {
			if (!empty($supNav['children']) && is_array($supNav['children'])) {
				$supNav['children'] = $this->getSubNavigation($supNav['children'], $navbarClickableparent);
				if ($navbarClickableparent === 0) {
					$supNav['link'] = '#';
				}
			}
			$res[] = $supNav;
		}

		return $res;
	}

	protected function getChildItems(array $children): array
	{
		foreach ($children as $cKey => $child) {
			$children[$cKey]['iconPack']   = $child['data']['page_icon'] ?? '';
			$children[$cKey]['title']      = !empty($child['data']['tx_t3sbootstrap_icon_only']) ? '' : $child['title'];
			$children[$cKey]['target']     = $child['target'] ?: '_self';

			if (!empty($child['current'])) {
				$children[$cKey]['active'] = 0;
			}

			$children[$cKey]['activeClass'] = match(true) {
				!empty($child['current']) && !empty($child['active']) => ' active',
				!empty($child['active'])                              => ' parent-active',
				default                                               => '',
			};

			if (!empty($child['children'][0])) {
				$nested = $this->getChildItems($child['children']);
				if ($nested) {
					$children[$cKey]['children'] = $nested;
				}
			}
		}

		return $children;
	}
	
	
	private function hasContent(int $pageUid, int $colPos): bool
	{		
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
			->getQueryBuilderForTable('tt_content');
		
		$queryBuilder->getRestrictions()
			->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class))
			->add(GeneralUtility::makeInstance(HiddenRestriction::class));
		
		$hasContent = (bool)$queryBuilder
			->count('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter($colPos, Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchOne();

		return $hasContent;
	}


}
