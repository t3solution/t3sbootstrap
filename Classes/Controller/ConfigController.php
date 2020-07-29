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

			$configRepository = $this->configRepository->findAll();

			foreach ( $configRepository as $key => $config ) {
				$page = $pageRepository->getPage($config->getPid());
				$allConfig[$page['uid']]['title'] = $page['title'];
				$allConfig[$page['uid']]['uid'] = $page['uid'];
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
			if ( $this->settings['t3sbConfig']['rootline'] ) {

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
		$this->addFlashMessage('The new configuration was created.');

		if ( self::isSiteroot() ) {
			$homepageUid = (int)$_GET['id'];
		} else {

			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

			$homepageUid = $rootLineArray[0]['uid'];
		}

		$newConfig->setHomepageUid($homepageUid);
		$newConfig->setPid((int)$_GET['id']);
		$this->configRepository->add($newConfig);

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
		$this->addFlashMessage('The configuration was updated.');

		if ( self::isSiteroot() ) {
			$homepageUid = (int)$_GET['id'];
		} else {

			$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, (int)$_GET['id'])->get();

			$homepageUid = $rootLineArray[0]['uid'];
		}

		$config->setHomepageUid($homepageUid);

		$this->configRepository->update($config);
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
		$this->addFlashMessage('The object was deleted.');
		$this->configRepository->remove($config);
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

		$newConfig->setexpandedcontentEnablebottom( $rootConfig->getexpandedcontentEnablebottom() );
		$newConfig->setExpandedcontentSlidebottom( $rootConfig->getExpandedcontentSlidebottom() );
		$newConfig->setExpandedcontentContainerpositionbottom( $rootConfig->getExpandedcontentContainerpositionbottom() );
		$newConfig->setExpandedcontentContainerbottom( $rootConfig->getExpandedcontentContainerbottom() );
		$newConfig->setExpandedcontentClassbottom( $rootConfig->getExpandedcontentClassbottom() );

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

		$fileds = 'getCompany, getHomepageUid, getPageTitle, getPageTitlealign, getPageTitlecontainer, getPageTitleclass, getMetaEnable, getMetaValue, getMetaContainer, getMetaClass, getMetaText, getNavbarEnable, getNavbarEntrylevel, getNavbarLevels, getNavbarExcludeuiduist, getNavbarIncludespacer, getNavbarJustify, getNavbarSectionmenu, getNavbarMegamenu, getNavbarHover, getNavbarClickableparent, getNavbarBrand, getNavbarImage, getNavbarColor, getNavbarShrinkcolorschemes, getNavbarShrinkcolor, getNavbarBackground, getNavbarContainer, getNavbarPlacement, getNavbarAlignment, getNavbarClass, getNavbarToggler, getNavbarBreakpoint, getNavbarOffcanvas, getNavbarHeight, getNavbarSearchbox, getNavbarLangmenu, getJumbotronEnable, getJumbotronBgimage, getJumbotronFluid, getJumbotronSlide, getJumbotronPosition, getJumbotronContainer, getJumbotronContainerposition, getJumbotronClass, getBreadcrumbEnable, getBreadcrumbNotonrootpage, getBreadcrumbFaicon, getBreadcrumbCorner, getBreadcrumbBottom, getBreadcrumbPosition, getBreadcrumbContainer, getBreadcrumbContainerposition, getBreadcrumbClass, getSidebarEnable, getSidebarRightenable, , getSidebarEntrylevel, getSidebarLevels, getSidebarExcludeuiduist, getSidebarIncludespacer, getFooterEnable, getFooterFluid, getFooterSlide, getFooterSticky, getFooterContainer, getFooterContainerposition, getFooterClass, getFooterPid, getExpandedcontentEnabletop, getExpandedcontentSlidetop, getExpandedcontentContainerpositiontop, getExpandedcontentContainertop, getExpandedcontentClasstop, getexpandedcontentEnablebottom, getExpandedcontentSlidebottom, getExpandedcontentContainerpositionbottom, getExpandedcontentContainerbottom, getExpandedcontentClassbottom';

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

}
