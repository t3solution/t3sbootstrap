<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\RootlineUtility;

#[AsEventListener(
	identifier: 't3sbootstrap/page-info',
	method: 'showPageInfo',
)]
final readonly class PageInfo
{
	public function __construct(
		private ViewFactoryInterface $viewFactory,
	) {}

	public function showPageInfo(ModifyPageLayoutContentEvent $event): void
	{
		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		$request = $event->getRequest();
		$currentUid = !empty($request->getQueryParams()['id']) ? (int) $request->getQueryParams()['id'] : 0;
		$page = BackendUtility::getRecord('pages', $currentUid, 'doktype');
		$assignedOptions = [];

		if ($page['doktype'] === 1 && array_key_exists('pageInfo', $extconf) && $extconf['pageInfo'] === '1') {
			$assignedOptions['isPageInfo'] = true;
		
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
			$config = $queryBuilder
				->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop')
				->from('tx_t3sbootstrap_domain_model_config')
				->where(
					$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($currentUid, Connection::PARAM_INT))
				)
				->setMaxResults(1)
				->executeQuery()
				->fetchAssociative();
	
			if (empty($config)) {
	
				$rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentUid);
				$rootLineArray = $rootline->get();
			
				foreach ($rootLineArray as $rlpage) {
					$config = $queryBuilder
						->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop')
							->from('tx_t3sbootstrap_domain_model_config')
						->where(
							$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rlpage['uid'], Connection::PARAM_INT))
						)
						->setMaxResults(1)
						->executeQuery()
						->fetchAssociative();
			
					if (!empty($config)) {
						break;
					}
				}
			}

			$site = $event->getRequest()->getAttribute('site');
	
			$infoOverride = [];
			$pagesOverrideArr = [];
			if (!empty($site->getConfiguration()['settings']['bootstrap']['pages']['override'])) {

				$pagesOverrides = $site->getConfiguration()['settings']['bootstrap']['pages']['override'];
				foreach ($pagesOverrides as $pagesOverride) {
					if (!empty($pagesOverride)) {
						$pagesOverrideArr[] = $pagesOverride;
					}
				}

				$infoOverride['Back to top icon for all pages'] = 'FALSE';
				if (!empty($site->getConfiguration()['settings']['bootstrap']['backToTopForAllPages'])) {
					$pagesOverrideArr['backToTopForAllPages'] = $site->getConfiguration()['settings']['bootstrap']['backToTopForAllPages'];
					$infoOverride['Back to top icon for all pages'] = 'TRUE';
				}

				$assignedOptions['countPagesOverrides'] = count($pagesOverrideArr);

				$infoOverride['Container'] = $pagesOverrides['container'];
				$infoOverride['Breakpoint'] = $pagesOverrides['breakpoint'];
				$infoOverride['Aside columns width'] = $pagesOverrides['smallColumns'];
				$infoOverride['Aside order on mobile'] = $pagesOverrides['mobileOrder'];
				$infoOverride['Page Title Color'] = $pagesOverrides['titlecolor'];
				$infoOverride['Subtitle Color'] = $pagesOverrides['subtitlecolor'];
				$infoOverride['Dropdown menu right'] = $pagesOverrides['dropdownRight'] ? 'TRUE' : 'FALSE';
			}

			$assignedOptions['infoOverride'] = $infoOverride;

			$page = BackendUtility::getRecord('pages', $currentUid, '*');	
			$container = $page['tx_t3sbootstrap_container'];
			if (!empty($container)) {
				if ($container === 'none') {
					$container = 'FALSE - even if pages override';
				}
			} else {
				$container = 'FALSE';
			}

			$infoColOne['Container'] = $container;
			$infoColOne['Link to top'] = $page['tx_t3sbootstrap_linkToTop'] ? 'TRUE' : 'FALSE';
			$infoColOne['Aside columns width'] = $page['tx_t3sbootstrap_smallColumns'];
			$infoColOne['Aside order on mobile'] = $page['tx_t3sbootstrap_mobileOrder'];
			$infoColOne['Breakpoint'] = $page['tx_t3sbootstrap_breakpoint'];
			$infoColOne['Full height section'] = $page['tx_t3sbootstrap_fullheightsection'] ? 'TRUE' : 'FALSE';
			$assignedOptions['infoColOne'] = $infoColOne;
			
			$infoColTwo = [];
			if (!empty($page['tx_t3sbootstrap_dropdownRight'])) {
				$infoColTwo['Dropdown menu right'] = $page['tx_t3sbootstrap_dropdownRight'];
			}
			if (!empty($page['tx_t3sbootstrap_megamenu'])) {
				$infoColTwo['Mega menu'] = $page['tx_t3sbootstrap_megamenu'];
			}
			if (ExtensionManagementUtility::isLoaded('iconpack') && !empty($page['page_icon'])) {
				$infoColTwo['Page icon'] =  $page['page_icon'];
				if (!empty($page['tx_t3sbootstrap_icon_only'])) {
					$infoColTwo['Icon only'] =  $page['tx_t3sbootstrap_icon_only'];
				}
			}
			if (!empty($page['tx_t3sbootstrap_titlecolor'])) {
				$infoColTwo['Page Title Color'] = $page['tx_t3sbootstrap_titlecolor'];
			}
			if (!empty($page['tx_t3sbootstrap_subtitlecolor'])) {
				$infoColTwo['Subtitle Color'] = $page['tx_t3sbootstrap_subtitlecolor'];
			}
			$assignedOptions['infoColTwo'] = $infoColTwo;
	
			$infoConfig = [];
			foreach ($config as $key=>$conf) {
				if ($key === 'jumbotron_enable' && empty($conf)) {
					$infoConfig['Jumbotron disabled!'] = '1';
				}
				if ($key === 'footer_enable' && empty($conf)) {
					$infoConfig['Footer disabled!'] = '1';
				}
				if ($key === 'expandedcontent_enabletop' && empty($conf)) {
					$infoConfig['Expanded Content disabled!'] = '1';
				}
			}
			$assignedOptions['infoConfig'] = $infoConfig;

		} else {
			$assignedOptions['isPageInfo'] = false;
		}

		$viewFactoryData = new ViewFactoryData(
			templateRootPaths: ['EXT:t3sbootstrap/Resources/Private/Backend/Templates'],
			request: $event->getRequest(),
		);
		$view = $this->viewFactory->create($viewFactoryData);
		$view->assignMultiple($assignedOptions);
		$event->addHeaderContent($view->render('PageInfo'));
	}
}
