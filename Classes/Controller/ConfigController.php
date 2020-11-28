<?php
namespace T3SBS\T3sbootstrap\Controller;

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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Backend\View\BackendTemplateView;


/**
 * ConfigController
 */
class ConfigController extends ActionController
{
	/**
	 * configRepository
	 *
	 * @var \T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository
	 */
	protected $configRepository;


	/**
	 * Backend Template Container (build the header in the BE)
	 *
	 * @var string
	 */
	protected $defaultViewObjectName = BackendTemplateView::class;

	/**
	 * TYPO3 version
	 *
	 * @var int
	 */
	protected $version;



	/**
	 * Inject a configRepository repository to enable DI
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository $configRepository
	 */
	public function injectConfigRepository(\T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository $configRepository)
	{
		$this->configRepository = $configRepository;
	}


	public function initializeAction()
	{
		$typo3Version = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
		$this->version = (int)$typo3Version->getVersion();
	}


	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
	{
		$isSiteroot = self::isSiteroot();

		if ( $isSiteroot ) {

			if ($this->version == 10) {
				$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
			} else {
				$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
			}

			$childPages = $pageRepository->getMenu((int)$_GET['id'], 'uid');
			$childUids = array_keys($childPages);
			$subChildPages = $pageRepository->getMenu($childUids, 'uid');
			$allPages = array_merge($childPages, $subChildPages);
			foreach ( $allPages as $page ) {
				$pageList .= $page['uid'].',';
			}
			$pageList = rtrim($pageList, ',');

			$configRepository = $this->configRepository->findAll();

			foreach ( $configRepository as $key => $config ) {

				if (\TYPO3\CMS\Core\Utility\GeneralUtility::inList($pageList, $config->getPid())) {

					$page = $pageRepository->getPage($config->getPid());

					$allConfig[$page['uid']]['title'] = $page['title'];
					$allConfig[$page['uid']]['uid'] = $page['uid'];

				}
			}

			$assignedOptions['allConfig'] = $allConfig;
		}

		$rootConfig = $this->configRepository->findOneByPid(self::getRootPageUid());

		$assignedOptions['isSiteroot'] = $isSiteroot;
		$assignedOptions['rootConfig'] = $rootConfig ? TRUE : FALSE;
		$assignedOptions['config'] = $this->configRepository->findOneByPid((int)$_GET['id']);
		$assignedOptions['admin'] = $GLOBALS['BE_USER']->isAdmin();

		$assignedOptions['customScss'] = FALSE;
		$assignedOptions['scss'] = '';

		if ( (int)$this->settings['customScss'] === 1 && (int)$this->settings['wsScss'] === 1 ) {

			$customScss = self::getCustomScss('custom-variables');
			$assignedOptions['custom-variables'] = $customScss['custom-variables'];

			$customScss = self::getCustomScss('custom');
			$assignedOptions['custom'] = $customScss['custom'];
			$assignedOptions['customScss'] = $customScss['customScss'];
		}

		$assignedOptions['version'] = $this->version;

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action new
	 *
	 * @return void
	 */
	public function newAction()
	{
		$assignedOptions = self::getFieldsOptions();

		$rootConfig = $this->configRepository->findOneByPid(self::getRootPageUid());

		if ( $rootConfig ) {
			// config from rootline
			if ( $rootConfig->getGeneralRootline() ) {

				$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

				// unset current page
				if (count($rootLineArray) > 1)
				unset($rootLineArray[count($rootLineArray)-1]);

				foreach ($rootLineArray as $rootline) {
					$rootlineConfig = $this->configRepository->findOneByPid((int)$rootline['uid']);
					if ( !empty($rootlineConfig) ) break;
				}
				$assignedOptions['newConfig'] = self::getNewConfig($rootlineConfig);

			// config from rootpage
			} else {
				$assignedOptions['newConfig'] = self::getNewConfig($rootConfig);
			}

		} else {
			$newConfig = new \T3SBS\T3sbootstrap\Domain\Model\Config();
			// some defaults
			$newConfig->setHomepageUid((int)$_GET['id']);
			$newConfig->setPageTitle( 'jumbotron' );
			$newConfig->setPageTitlealign( 'center' );
			$newConfig->setNavbarImage($this->settings['defaultNavbarImagePath']);
			$newConfig->setNavbarEnable( 'light' );
			$newConfig->setNavbarLevels( 4 );
			$newConfig->setNavbarColor( 'warning' );
			$newConfig->setNavbarAlignment( 'left' );
			$newConfig->setNavbarBrand( 'imgText' );
			$newConfig->setNavbarContainer( 'inside' );
			$newConfig->setJumbotronEnable( 1 );
			$newConfig->setJumbotronFluid( 1 );
			$newConfig->setJumbotronSlide( 0 );
			$newConfig->setJumbotronPosition( 'below' );
			$newConfig->setJumbotronContainer( 'container' );
			$newConfig->setJumbotronContainerposition( 'Inside' );
			$newConfig->setBreadcrumbEnable( 1 );
			$newConfig->setBreadcrumbCorner( 1 );
			$newConfig->setBreadcrumbPosition( 'belowJum' );
			$newConfig->setBreadcrumbContainer( 'container' );
			$newConfig->setSidebarLevels( 4 );
			$newConfig->setFooterEnable( 1 );
			$newConfig->setFooterFluid( 1 );
			$newConfig->setFooterSlide( 0 );
			$newConfig->setFooterContainer( 'container' );
			$newConfig->setFooterSticky( 1 );
			$newConfig->setFooterContainerposition( 'inside' );
			$newConfig->setFooterClass( 'bg-dark text-light' );
			$assignedOptions['newConfig'] = $newConfig;
		}

		$assignedOptions['pid'] = (int)$_GET['id'];
		$assignedOptions['version'] = $this->version;

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action create
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	 * @return void
	 */
	public function createAction(\T3SBS\T3sbootstrap\Domain\Model\Config $newConfig)
	{
		$this->addFlashMessage('The new configuration was created.', '', FlashMessage::OK);

		if ( self::isSiteroot() ) {
			$homepageUid = (int)$_GET['id'];
		} else {

			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

			$homepageUid = $rootLineArray[0]['uid'];
		}

		$newConfig->setHomepageUid($homepageUid);
		$newConfig->setPid((int)$_GET['id']);
		$this->configRepository->add($newConfig);
		$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
		$persistenceManager->persistAll();
		self::writeConstants();

		parent::redirect('list');
	}


	/**
	 * action edit
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return void
	 */
	public function editAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config)
	{
		$assignedOptions = self::getFieldsOptions();
		$assignedOptions['config'] = $config;
		$assignedOptions['pid'] = (int)$_GET['id'];
		$assignedOptions['admin'] = $GLOBALS['BE_USER']->isAdmin();

		$ts = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

		if (is_array($ts['page.']['10.']['settings.']['override.'])) {
			$overrideArr = [];
			foreach ( $ts['page.']['10.']['settings.']['override.'] as $key=>$tsOverride ) {
				if ( !empty($tsOverride)) {
					if ( $tsOverride === 'TRUE' ) {
						$overrideArr[lcfirst(GeneralUtility::underscoredToUpperCamelCase($key))] = 'enabled';
					} elseif ( $tsOverride === 'FALSE' ) {
						$overrideArr[lcfirst(GeneralUtility::underscoredToUpperCamelCase($key))] = 'disabled';
					} else {
						$overrideArr[lcfirst(GeneralUtility::underscoredToUpperCamelCase($key))] = $tsOverride ?: 'not set';
					}
				}
			}
			$assignedOptions['override'] = $overrideArr;
		}

		if ( !self::isSiteroot() ) {
			$compare = self::compareConfig($config);
			$assignedOptions['compare'] = $compare;
		}

		$assignedOptions['version'] = $this->version;

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action update
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return void
	 */
	public function updateAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config)
	{
		$this->addFlashMessage('The configuration was updated.', '', FlashMessage::OK);

		if ( self::isSiteroot() ) {
			$homepageUid = (int)$_GET['id'];
		} else {

			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

			$homepageUid = $rootLineArray[0]['uid'];
		}

		$config->setHomepageUid($homepageUid);

		$this->configRepository->update($config);
		$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
		$persistenceManager->persistAll();
		self::writeConstants();

		parent::redirect('edit',NULL,Null,array('config' => $config));
	}


	/**
	 * action delete
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return void
	 */
	public function deleteAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config)
	{
		$this->addFlashMessage('The object was deleted.', '', FlashMessage::INFO);
		$this->configRepository->remove($config);
		$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
		$persistenceManager->persistAll();
		self::writeConstants();

		parent::redirect('list');
	}


	/**
	* prepare options for select fields
	*
	* @return array
	*/
	public function getFieldsOptions()
	{

		$fields = [
			'pageTitleOptions' => 'page_title',
			'pageTitlealignOptions' => 'page_titlealign',
			'pageTitlecontainerOptions' => 'page_titlecontainer',
			'metaEnableOptions' => 'meta_enable',
			'metaContainerOptions' => 'meta_container',
			'navbarEnableOptions' => 'navbar_enable',
			'navbarBrandOptions' => 'navbar_brand',
			'navbarColorOptions' => 'navbar_color',
			'navbarShrinkcolorOptions' => 'navbar_shrinkcolor',
			'navbarShrinkcolorschemesOptions' => 'navbar_shrinkcolorschemes',
			'navbarContainerOptions' => 'navbar_container',
			'navbarPlacementOptions' => 'navbar_placement',
			'navbarAlignmentOptions' => 'navbar_alignment',
			'navbarTogglerOptions' => 'navbar_toggler',
			'navbarSearchboxOptions' => 'navbar_searchbox',
			'navbarBreakpointOptions' => 'navbar_breakpoint',
			'jumbotronPositions' => 'jumbotron_position',
			'jumbotronBgImageOptions' => 'jumbotron_bgimage',
			'jumbotronContainerOptions' => 'jumbotron_container',
			'jumbotronContainerpositionOptions' => 'jumbotron_containerposition',
			'breadcrumbPositions' => 'breadcrumb_position',
			'breadcrumbContainerOptions' => 'breadcrumb_container',
			'breadcrumbContainerpositionOptions' => 'breadcrumb_containerposition',
			'sidebarLeftOptions' => 'sidebar_enable',
			'sidebarRightOptions' => 'sidebar_rightenable',
			'footerContainerOptions' => 'footer_container',
			'footerContainerpositionOptions' => 'footer_containerposition',
			'expandedcontentContainertopOptions' => 'expandedcontent_containertop',
			'expandedcontentContainerpositiontopOptions' => 'expandedcontent_containerpositiontop',
			'expandedcontentContainerbottomOptions' => 'expandedcontent_containerbottom',
			'expandedcontentContainerpositionbottomOptions' => 'expandedcontent_containerpositionbottom'
		];

		foreach ($fields as $fieldKey=>$field) {

			$tcaItems[$fieldKey] = $GLOBALS['TCA']['tx_t3sbootstrap_domain_model_config']['columns'][$field]['config']['items'];

			foreach ($tcaItems[$fieldKey] as $key=>$value) {
				$entries[$fieldKey][$value[1]] = $value[0];
			}

				 $options = [];

				 foreach ($entries[$fieldKey] as $key=>$entry) {
				  $option = new \stdClass();
				  $option->key = $key;
				#$option->value = LocalizationUtility::translate('option.' . $entry, 't3sbootstrap');
				  $option->value = $entry;
				  $options[$key] = $option;
				 }

			$fieldsOptions[$fieldKey] = $options;
		}

		 return $fieldsOptions;
	}


	/**
	* take over $rootConfig settings
	*
	* @param \T3SBS\T3sbootstrap\Domain\Model\Config $rootConfig
	* @return \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	*/
	public function getNewConfig(\T3SBS\T3sbootstrap\Domain\Model\Config $rootConfig)
	{

		$newConfig = new \T3SBS\T3sbootstrap\Domain\Model\Config();
		$newConfig->setCompany( $rootConfig->getCompany() );
		$newConfig->setHomepageUid( $rootConfig->getHomepageUid() );
		$newConfig->setPageTitle( $rootConfig->getPageTitle() );
		$newConfig->setPageTitlecontainer( $rootConfig->getPageTitlecontainer() );
		$newConfig->setPageTitleclass( $rootConfig->getPageTitleclass() );
		$newConfig->setPageTitlealign( $rootConfig->getPageTitlealign() );
		$newConfig->setMetaEnable( $rootConfig->getMetaEnable() );
		$newConfig->setMetaValue( $rootConfig->getMetaValue() );
		$newConfig->setMetaContainer( $rootConfig->getMetaContainer() );
		$newConfig->setMetaClass( $rootConfig->getMetaClass() );
		$newConfig->setMetaText( $rootConfig->getMetaText() );

		$newConfig->setNavbarEnable( $rootConfig->getNavbarEnable() );
		$newConfig->setNavbarLevels( $rootConfig->getNavbarLevels() );
		$newConfig->setNavbarEntrylevel( $rootConfig->getNavbarEntrylevel() );
		$newConfig->setNavbarExcludeuiduist( $rootConfig->getNavbarExcludeuiduist() );
		$newConfig->setNavbarIncludespacer( $rootConfig->getNavbarIncludespacer() );
		$newConfig->setNavbarJustify( $rootConfig->getNavbarJustify() );
		$newConfig->setNavbarSectionmenu( $rootConfig->getNavbarSectionmenu() );
		$newConfig->setNavbarMegamenu( $rootConfig->getNavbarMegamenu() );
		$newConfig->setNavbarHover( $rootConfig->getNavbarHover() );
		$newConfig->setNavbarClickableparent( $rootConfig->getNavbarClickableparent() );
		$newConfig->setNavbarBrand( $rootConfig->getNavbarBrand() );
		$newConfig->setNavbarImage( $rootConfig->getNavbarImage() );
		$newConfig->setNavbarColor( $rootConfig->getNavbarColor() );
		$newConfig->setNavbarShrinkcolor( $rootConfig->getNavbarShrinkcolor() );
		$newConfig->setNavbarShrinkcolorschemes( $rootConfig->getNavbarShrinkcolorschemes() );
		$newConfig->setNavbarContainer( $rootConfig->getNavbarContainer() );
		$newConfig->setNavbarPlacement( $rootConfig->getNavbarPlacement() );
		$newConfig->setNavbarAlignment( $rootConfig->getNavbarAlignment() );
		$newConfig->setNavbarToggler( $rootConfig->getNavbarToggler() );
		$newConfig->setNavbarBreakpoint( $rootConfig->getNavbarBreakpoint() );
		$newConfig->setNavbarOffcanvas( $rootConfig->getNavbarOffcanvas() );
		$newConfig->setNavbarBackground( $rootConfig->getNavbarBackground() );
		$newConfig->setNavbarClass( $rootConfig->getNavbarClass() );
		$newConfig->setNavbarHeight( $rootConfig->getNavbarHeight() );
		$newConfig->setNavbarSearchbox( $rootConfig->getNavbarSearchbox() );
		$newConfig->setNavbarLangmenu( $rootConfig->getNavbarLangmenu() );

		$newConfig->setJumbotronEnable( $rootConfig->getJumbotronEnable() );
		$newConfig->setJumbotronBgimage( $rootConfig->getJumbotronBgimage() );
		$newConfig->setJumbotronFluid( $rootConfig->getJumbotronFluid() );
		$newConfig->setJumbotronSlide( $rootConfig->getJumbotronSlide() );
		$newConfig->setJumbotronPosition( $rootConfig->getJumbotronPosition() );
		$newConfig->setJumbotronContainer( $rootConfig->getJumbotronContainer() );
		$newConfig->setJumbotronContainerposition( $rootConfig->getJumbotronContainerposition() );
		$newConfig->setJumbotronClass( $rootConfig->getJumbotronClass() );

		$newConfig->setBreadcrumbEnable( $rootConfig->getBreadcrumbEnable() );
		$newConfig->setBreadcrumbNotonrootpage( $rootConfig->getBreadcrumbNotonrootpage() );
		$newConfig->setBreadcrumbFaicon( $rootConfig->getBreadcrumbFaicon() );
		$newConfig->setBreadcrumbCorner( $rootConfig->getBreadcrumbCorner() );
		$newConfig->setBreadcrumbBottom( $rootConfig->getBreadcrumbBottom() );
		$newConfig->setBreadcrumbPosition( $rootConfig->getBreadcrumbPosition() );
		$newConfig->setBreadcrumbContainer( $rootConfig->getBreadcrumbContainer() );
		$newConfig->setBreadcrumbContainerposition( $rootConfig->getBreadcrumbContainerposition() );
		$newConfig->setBreadcrumbClass( $rootConfig->getBreadcrumbClass() );

		$newConfig->setSidebarEnable( $rootConfig->getSidebarEnable() );
		$newConfig->setSidebarRightenable( $rootConfig->getSidebarRightenable() );
		$newConfig->setSidebarEntrylevel( $rootConfig->getSidebarEntrylevel() );
		$newConfig->setSidebarLevels( $rootConfig->getSidebarLevels() );
		$newConfig->setSidebarExcludeuiduist( $rootConfig->getSidebarExcludeuiduist() );
		$newConfig->setSidebarIncludespacer( $rootConfig->getSidebarIncludespacer() );

		$newConfig->setFooterEnable( $rootConfig->getFooterEnable() );
		$newConfig->setFooterFluid( $rootConfig->getFooterFluid() );
		$newConfig->setFooterSlide( $rootConfig->getFooterSlide() );
		$newConfig->setFooterSticky( $rootConfig->getFooterSticky() );
		$newConfig->setFooterContainer( $rootConfig->getFooterContainer() );
		$newConfig->setFooterContainerposition( $rootConfig->getFooterContainerposition() );
		$newConfig->setFooterClass( $rootConfig->getFooterClass() );
		$newConfig->setFooterPid( $rootConfig->getFooterPid() );

		$newConfig->setExpandedcontentEnabletop( $rootConfig->getExpandedcontentEnabletop() );
		$newConfig->setExpandedcontentSlidetop( $rootConfig->getExpandedcontentSlidetop() );
		$newConfig->setExpandedcontentContainerpositiontop( $rootConfig->getExpandedcontentContainerpositiontop() );
		$newConfig->setExpandedcontentContainertop( $rootConfig->getExpandedcontentContainertop() );
		$newConfig->setExpandedcontentClasstop( $rootConfig->getExpandedcontentClasstop() );

		$newConfig->setExpandedcontentEnablebottom( $rootConfig->getExpandedcontentEnablebottom() );
		$newConfig->setExpandedcontentSlidebottom( $rootConfig->getExpandedcontentSlidebottom() );
		$newConfig->setExpandedcontentContainerpositionbottom( $rootConfig->getExpandedcontentContainerpositionbottom() );
		$newConfig->setExpandedcontentContainerbottom( $rootConfig->getExpandedcontentContainerbottom() );
		$newConfig->setExpandedcontentClassbottom( $rootConfig->getExpandedcontentClassbottom() );

		$newConfig->setGeneralRootline( $rootConfig->getGeneralRootline() );

		return $newConfig;
	}


	/**
	 * Returns isSiteroot
	 *
	 * @return boolean
	 */
	protected function isSiteroot()
	{
		if ($this->version == 10) {
			$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
		} else {
			$pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
		}

		$page = $pageRepository->getPage((int)$_GET['id']);

		if ( $page['is_siteroot'] ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	/**
	 * Returns the RootPageUid
	 *
	 * @return array
	 */
	protected function getRootPageUid()
	{
		if ( self::isSiteroot() ) {
			$rootPageUid = (int)$_GET['id'];
		} else {

			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

			$rootPageUid = $rootLineArray[0]['uid'];
		}

		return $rootPageUid;
	}


	/**
	 * Compare config with rootconfig
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return array
	 */
	protected function compareConfig($config)
	{
		$compare = [];
		$rootConfig = $this->configRepository->findOneByPid(self::getRootPageUid());

		$fileds = 'getCompany, getHomepageUid, getPageTitle, getPageTitlealign, getPageTitlecontainer, getPageTitleclass, getMetaEnable, getMetaValue, getMetaContainer, getMetaClass, getMetaText, getNavbarEnable, getNavbarEntrylevel, getNavbarLevels, getNavbarExcludeuiduist, getNavbarIncludespacer, getNavbarJustify, getNavbarSectionmenu, getNavbarMegamenu, getNavbarHover, getNavbarClickableparent, getNavbarBrand, getNavbarImage, getNavbarColor, getNavbarShrinkcolorschemes, getNavbarShrinkcolor, getNavbarBackground, getNavbarContainer, getNavbarPlacement, getNavbarAlignment, getNavbarClass, getNavbarToggler, getNavbarBreakpoint, getNavbarOffcanvas, getNavbarHeight, getNavbarSearchbox, getNavbarLangmenu, getJumbotronEnable, getJumbotronBgimage, getJumbotronFluid, getJumbotronSlide, getJumbotronPosition, getJumbotronContainer, getJumbotronContainerposition, getJumbotronClass, getBreadcrumbEnable, getBreadcrumbNotonrootpage, getBreadcrumbFaicon, getBreadcrumbCorner, getBreadcrumbBottom, getBreadcrumbPosition, getBreadcrumbContainer, getBreadcrumbContainerposition, getBreadcrumbClass, getSidebarEnable, getSidebarRightenable, , getSidebarEntrylevel, getSidebarLevels, getSidebarExcludeuiduist, getSidebarIncludespacer, getFooterEnable, getFooterFluid, getFooterSlide, getFooterSticky, getFooterContainer, getFooterContainerposition, getFooterClass, getFooterPid, getExpandedcontentEnabletop, getExpandedcontentSlidetop, getExpandedcontentContainerpositiontop, getExpandedcontentContainertop, getExpandedcontentClasstop, getExpandedcontentEnablebottom, getExpandedcontentSlidebottom, getExpandedcontentContainerpositionbottom, getExpandedcontentContainerbottom, getExpandedcontentClassbottom, getGeneralRootline';

		$filedsArr = GeneralUtility::trimExplode(',', $fileds, 1);

		foreach ($filedsArr as $field) {
			if ( $config->$field() !== $rootConfig->$field() ) {

				$key = lcfirst(substr($field,3));
				$rootConfigField = $rootConfig->$field();

				if ( $rootConfigField === TRUE ) {

					$compare[$key] = 'enabled';

				} elseif ( $rootConfigField === FALSE ) {

					$compare[$key] = 'disabled';

				} else {

					$compare[$key] = $rootConfigField ?: 'not set';
				}
			}
		}

		return $compare;
	}


	/**
	 * SCSS in the BE
	 *
	 * @return array
	 */
	public function getCustomScss( $file ) {

		$customScssDir = $this->settings['customScssPath'] ? $this->settings['customScssPath'] : 'fileadmin/T3SB/SCSS/';
		$customScssFilePath = GeneralUtility::getFileAbsFileName($customScssDir);
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
		$result = $queryBuilder
			 ->select('*')
			 ->from('pages')
			 ->where(
				$queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
			 )
			 ->execute();
		$siteroots = $result->fetchAll();
		foreach ($siteroots as $key=>$siteroot) {
			if ( $siteroot['uid'] == $_GET['id'] ) {
				if ( $key === 0 ) {
					$customScssFileName = $file.'.scss';
				} else {
					$customScssFileName = $file.'-'.$siteroot['uid'].'.scss';
				}
			}
		}

		$customScssFile = $customScssFilePath.$customScssFileName;

		if ( file_exists($customScssFile) ) {

			if ($file == 'custom') {
				$assignedOptions['customScss'] = TRUE;
			}
			$arguments = $this->request->getArguments();

			if ( $arguments['t3sbootstrap'][$file] ) {
				$scss = $arguments['t3sbootstrap'][$file];
			} else {
				$scss = $arguments[$file] ? $arguments[$file] : '';
			}

			if ($scss) {
				$assignedOptions[$file] = $scss;
				$handle = fopen($customScssFile, 'w') or die('Cannot open file:	 '.$customScssFile);
				fwrite($handle, $scss);
				fclose($handle);
				if ($file == 'custom') {
					// clean typo3temp/ws_scss/
					if ( ExtensionManagementUtility::isLoaded('ws_scss') ) {
						$tempDir = 'typo3temp/var/cache/data/ws_scss/';
					} else {
						$tempDir = '';
					}
					$tempPath = GeneralUtility::getFileAbsFileName($tempDir);
					self::deleteFilesFromDirectory($tempPath);
				}
			} else {
				$handle = fopen($customScssFile, 'r');
				if (filesize($customScssFile)) {
					$assignedOptions[$file] = fread($handle,filesize($customScssFile));
				} else {
					$assignedOptions[$file] = '// empty file';
				}
				fclose($handle);
			}
		}

		return $assignedOptions;
	}


	/**
	 * Delete files from directory
	 *
	 * @return void
	 */
	public function deleteFilesFromDirectory($directory){
		if (is_dir($directory)) {
			if ($dh = opendir($directory)) {
				while (($file = readdir($dh)) !== false) {
					if ($file!='.' && $file !='..' && $file[0] != '_') {
						unlink(''.$directory.''.$file.'');
					}
				}
				closedir($dh);
			}
		}
	}


	/**
	 * Write data from DB to constant file and import in sys_template
	 *
	 * @return void
	 */
	public function writeConstants() {

		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');
		$rootTemplates = $queryBuilder
			 ->select('uid','pid', 'constants')
			 ->from('sys_template')
			 ->where(
				$queryBuilder->expr()->eq('root', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
			 )
			 ->execute()->fetchAll();


		if ( count($rootTemplates) ) {

			$configRepository = $this->configRepository->findAll();

			foreach ($rootTemplates as $key=>$rootTemplate) {

				$import = "@import 'fileadmin/T3SB/Configuration/TypoScript/t3sbconstants-".$rootTemplate['pid'].".typoscript'";

				$pos = strpos($rootTemplate['constants'], $import);

				if ($pos === FALSE) {

					$setConstants = "# Please do not delete or comment out the following line".PHP_EOL;
					$setConstants .= $import.PHP_EOL;
					$setConstants .= $rootTemplate['constants'];

					$update = $queryBuilder
						->update('sys_template')
						->where(
							$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($rootTemplate['uid'], \PDO::PARAM_INT))
						)
						->set('constants', $setConstants)
						->execute();
				}

				$filecontent = '';

				foreach ( $configRepository as $config ) {

					if ( $config->getPid() == $rootTemplate['pid'] ) {
						$filecontent .= self::getContents($config).PHP_EOL;

					} else {

						$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, $config->getPid())->get();

						if ($rootLineArray[0]['uid'] == $rootTemplate['pid'] ){

							if ($config->getGeneralRootline() || $config->getNavbarMegamenu()) {
								$filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
							} else {
								$filecontent .= '[page["uid"] == '.$config->getPid().']'.PHP_EOL;
							}

							$filecontent .= self::getContents($config);
							$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
						}
					}
				}

				$customDir = 'fileadmin/T3SB/Configuration/TypoScript/';
				$customPath = GeneralUtility::getFileAbsFileName($customDir);
				$customFileName = 't3sbconstants-'.$rootTemplate['pid'].'.typoscript';
				$customFile = $customPath.$customFileName;

				if (file_exists($customFile)) {
					unlink($customFile);
				}
				if (!is_dir($customPath)) {
					mkdir($customPath, 0777, true);
				}

				GeneralUtility::writeFile($customFile, $filecontent);

			}
		}

	}


	/**
	 * Get the data from DB
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return string
	 */
	private function getContents(\T3SBS\T3sbootstrap\Domain\Model\Config $config) {

		$constants = 'bootstrap.db.uid = '.$config->getUid() .PHP_EOL;
		$constants .= 'bootstrap.db.company = '.$config->getCompany() .PHP_EOL;
		$constants .= 'bootstrap.db.homepage_uid = '.$config->getHomepageUid() .PHP_EOL;

		$constants .= 'bootstrap.db.page_title = '.$config->getPageTitle() .PHP_EOL;
		$constants .= 'bootstrap.db.page_titlealign = '.$config->getPageTitlealign() .PHP_EOL;
		$constants .= 'bootstrap.db.page_titlecontainer = '.$config->getPageTitlecontainer() .PHP_EOL;
		$constants .= 'bootstrap.db.page_titleclass = '.$config->getPageTitleclass() .PHP_EOL;

		$constants .= 'bootstrap.db.meta_enable = '.$config->getMetaEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.meta_value = '.$config->getMetaValue() .PHP_EOL;
		$constants .= 'bootstrap.db.meta_container = '.$config->getMetaContainer() .PHP_EOL;
		$constants .= 'bootstrap.db.meta_class = '.$config->getMetaClass() .PHP_EOL;
		$constants .= 'bootstrap.db.meta_text = '.$config->getMetaText() .PHP_EOL;

		$constants .= 'bootstrap.db.navbar_enable = '.$config->getNavbarEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_entrylevel = '.$config->getNavbarEntrylevel() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_levels = '.$config->getNavbarLevels() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_excludeuiduist = '.$config->getNavbarExcludeuiduist() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_includespacer = '.$config->getNavbarIncludespacer() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_justify = '.$config->getNavbarJustify() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_sectionmenu = '.$config->getNavbarSectionmenu() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_megamenu = '.$config->getNavbarMegamenu() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_hover = '.$config->getNavbarHover() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_clickableparent = '.$config->getNavbarClickableparent() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_brand = '.$config->getNavbarBrand() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_image = '.$config->getNavbarImage() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_color = '.$config->getNavbarColor() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_background = '.$config->getNavbarBackground() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_container = '.$config->getNavbarContainer() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_placement = '.$config->getNavbarPlacement() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_alignment = '.$config->getNavbarAlignment() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_class = '.$config->getNavbarClass() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_toggler = '.$config->getNavbarToggler() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_breakpoint = '.$config->getNavbarBreakpoint() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_offcanvas = '.$config->getNavbarOffcanvas() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_height = '.$config->getNavbarHeight() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_searchbox = '.$config->getNavbarSearchbox() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_langmenu = '.$config->getNavbarLangmenu() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_shrinkcolorschemes = '.$config->getNavbarShrinkcolorschemes() .PHP_EOL;
		$constants .= 'bootstrap.db.navbar_shrinkcolor = '.$config->getNavbarShrinkcolor() .PHP_EOL;

		$constants .= 'bootstrap.db.jumbotron_enable = '.$config->getJumbotronEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_bgimage = '.$config->getJumbotronBgimage() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_fluid = '.$config->getJumbotronFluid() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_slide = '.$config->getJumbotronSlide() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_position = '.$config->getJumbotronPosition() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_container = '.$config->getJumbotronContainer() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_containerposition = '.$config->getJumbotronContainerposition() .PHP_EOL;
		$constants .= 'bootstrap.db.jumbotron_class = '.$config->getJumbotronClass() .PHP_EOL;

		$constants .= 'bootstrap.db.breadcrumb_enable = '.$config->getBreadcrumbEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_notonrootpage = '.$config->getBreadcrumbNotonrootpage() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_faicon = '.$config->getBreadcrumbFaicon() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_corner = '.$config->getBreadcrumbCorner() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_bottom = '.$config->getBreadcrumbBottom() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_position = '.$config->getBreadcrumbPosition() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_container = '.$config->getBreadcrumbContainer() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_containerposition = '.$config->getBreadcrumbContainerposition() .PHP_EOL;
		$constants .= 'bootstrap.db.breadcrumb_class = '.$config->getBreadcrumbClass() .PHP_EOL;

		$constants .= 'bootstrap.db.sidebar_enable = '.$config->getSidebarEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.sidebar_rightenable = '.$config->getSidebarRightenable() .PHP_EOL;
		$constants .= 'bootstrap.db.sidebar_levels = '.$config->getSidebarEntrylevel() .PHP_EOL;
		$constants .= 'bootstrap.db.sidebar_entrylevel = '.$config->getSidebarEntrylevel() .PHP_EOL;
		$constants .= 'bootstrap.db.sidebar_excludeuiduist = '.$config->getSidebarExcludeuiduist() .PHP_EOL;
		$constants .= 'bootstrap.db.sidebar_includespacer = '.$config->getSidebarIncludespacer() .PHP_EOL;

		$constants .= 'bootstrap.db.footer_enable = '.$config->getFooterEnable() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_fluid = '.$config->getFooterFluid() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_slide = '.$config->getFooterSlide() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_sticky = '.$config->getFooterSticky() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_container = '.$config->getFooterContainer() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_containerposition = '.$config->getFooterContainerposition() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_class = '.$config->getFooterClass() .PHP_EOL;
		$constants .= 'bootstrap.db.footer_pid = '.$config->getFooterPid() .PHP_EOL;

		$constants .= 'bootstrap.db.expandedcontent_enabletop = '.$config->getexpandedcontentEnabletop() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_slidetop = '.$config->getExpandedcontentSlidetop() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_containerpositiontop = '.$config->getExpandedcontentContainerpositiontop() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_containertop = '.$config->getExpandedcontentContainertop() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_classtop = '.$config->getExpandedcontentClasstop() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_enablebottom = '.$config->getExpandedcontentEnablebottom() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_slidebottom = '.$config->getExpandedcontentSlidebottom() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_containerpositionbottom = '.$config->getExpandedcontentContainerpositionbottom() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_containerbottom = '.$config->getExpandedcontentContainerbottom() .PHP_EOL;
		$constants .= 'bootstrap.db.expandedcontent_classbottom = '.$config->getExpandedcontentClassbottom() .PHP_EOL;

		$constants .= 'bootstrap.db.general_rootline = '.$config->getGeneralRootline() .PHP_EOL;

		return $constants;
	}


}
