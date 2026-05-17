<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

#[AsEventListener(
	identifier: 't3sbootstrap/page-info',
	method: 'showPageInfo',
)]
final readonly class PageInfo
{
	public function __construct(
		private ViewFactoryInterface $viewFactory,
		private ExtensionConfiguration $extensionConfiguration,
		private ConnectionPool $connectionPool,
	) {
	}

	public function showPageInfo(ModifyPageLayoutContentEvent $event): void
	{
		$extconf = $this->extensionConfiguration->get('t3sbootstrap');
		$request = $event->getRequest();
		$currentUid = (int)($request->getQueryParams()['id'] ?? 0);

		$assignedOptions = ['isPageInfo' => false];

		if ($currentUid <= 0) {
			$this->renderView($event, $assignedOptions);
			return;
		}

		$page = BackendUtility::getRecord('pages', $currentUid, '*') ?? [];

		if ((int)($page['doktype'] ?? 0) !== 1 || empty($extconf['pageInfo']) || $extconf['pageInfo'] !== '1') {
			$this->renderView($event, $assignedOptions);
			return;
		}

		$assignedOptions['isPageInfo'] = true;

		// Config für aktuelle Seite holen, sonst Rootline durchgehen
		$config = $this->findConfig($currentUid);

		$site = $request->getAttribute('site');
		$siteConfiguration = $site?->getConfiguration() ?? [];

		// Pages Overrides from Site Config
		$assignedOptions['infoOverride'] = [];
		$assignedOptions['countPagesOverrides'] = 0;
		$pagesOverrides = $siteConfiguration['settings']['bootstrap']['pages']['override'] ?? [];

		if (!empty($pagesOverrides)) {
			$pagesOverrideArr = array_filter($pagesOverrides);
			$infoOverride = [
				'backToTopForAllPages' => $siteConfiguration['settings']['bootstrap']['backToTopForAllPages'] ?? 0,
				'container' => $pagesOverrides['container'] ?? 0,
				'breakpoint' => $pagesOverrides['breakpoint'] ?? 0,
				'smallColumns' => $pagesOverrides['smallColumns'] ?? 0,
				'mobileOrder' => $pagesOverrides['mobileOrder'] ?? 0,
				'titlecolor' => $pagesOverrides['titlecolor'] ?? 0,
				'subtitlecolor' => $pagesOverrides['subtitlecolor'] ?? 0,
				'dropdownRight' => $pagesOverrides['dropdownRight'] ?? 0,
			];

			if ($infoOverride['backToTopForAllPages']) {
				$pagesOverrideArr['backToTopForAllPages'] = $siteConfiguration['settings']['bootstrap']['backToTopForAllPages'];
			}

			$assignedOptions['countPagesOverrides'] = count($pagesOverrideArr);
			$assignedOptions['infoOverride'] = $infoOverride;
		}

		// Column 1: Page-specific settings
		$assignedOptions['infoColOne'] = [
			'container' => $this->resolveContainer($page['tx_t3sbootstrap_container'] ?? 0),
			'linkToTop' => $page['tx_t3sbootstrap_linkToTop'] ?? 0,
			'smallColumns' => $page['tx_t3sbootstrap_smallColumns'] ?? 0,
			'breakpoint' => $page['tx_t3sbootstrap_breakpoint'] ?? 0,
			'fullHeightSection' => $page['tx_t3sbootstrap_fullheightsection'] ?? 0,
		];

		// Column 2: Optional page settings
		$infoColTwo = [];
		if (ExtensionManagementUtility::isLoaded('iconpack') && !empty($page['page_icon'])) {
			$infoColTwo['pageIcon'] = $page['page_icon']; 
			if (!empty($page['tx_t3sbootstrap_icon_only'])) {
				$infoColTwo['iconOnly'] = $page['tx_t3sbootstrap_icon_only'] ?? 0;
			}
		}
		if (!empty($page['tx_t3sbootstrap_dropdownRight'])) {
			$infoColTwo['dropdownRight'] = $page['tx_t3sbootstrap_dropdownRight'];
		}
		
		$assignedOptions['infoColTwo'] = $infoColTwo;

		// Column 3 T3SB config
		$infoConfig = [];
		if (empty($config['jumbotron_enable'])) {
			$infoConfig['jumbotronDisabled'] = true;
		}
		if (empty($config['footer_enable'])) {
			$infoConfig['footerDisabled'] = true;
		}

		if (!empty($config['expandedcontent_enabletop']) || !empty($config['expandedcontent_enablebottom'])) {
			$infoConfig['expandedContentEnabled'] = true;
		}
		$assignedOptions['infoConfig'] = $infoConfig;

		$this->renderView($event, $assignedOptions);
	}

	/**
	 * First, look for the config on the current page, then work your way up the root line.
	 */
	private function findConfig(int $currentUid): array
	{
		$config = $this->fetchConfig($currentUid);

		if (!empty($config)) {
			return $config;
		}

		$rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentUid)->get();

		foreach ($rootline as $rlpage) {
			$config = $this->fetchConfig((int)$rlpage['uid']);
			if (!empty($config)) {
				return $config;
			}
		}

		return [];
	}

	private function fetchConfig(int $pid): array
	{
		$queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');

		$result = $queryBuilder
			->select('jumbotron_enable', 'footer_enable', 'expandedcontent_enabletop', 'expandedcontent_enablebottom')
			->from('tx_t3sbootstrap_domain_model_config')
			->where(
				$queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
			)
			->setMaxResults(1)
			->executeQuery()
			->fetchAssociative();

		return is_array($result) ? $result : [];
	}

	private function resolveContainer(string $container): string
	{
		if ($container === '') {
			return 'FALSE';
		}
		if ($container === 'none') {
			return 'FALSE - even if pages override';
		}
		return $container;
	}

	/**
	 * @param array<string, mixed> $assignedOptions
	 */
	private function renderView(ModifyPageLayoutContentEvent $event, array $assignedOptions): void
	{
		$viewFactoryData = new ViewFactoryData(
			templateRootPaths: ['EXT:t3sbootstrap/Resources/Private/Backend/Templates'],
			request: $event->getRequest(),
		);
		$view = $this->viewFactory->create($viewFactoryData);
		$view->assignMultiple($assignedOptions);
		$event->addHeaderContent($view->render('PageInfo'));
	}
}
