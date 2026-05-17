<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Domain\Event\RecordCreationEvent;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use T3SBS\T3sbootstrap\Domain\Model\Config;
use TYPO3\CMS\Extbase\Service\CacheService;
use TYPO3\CMS\Core\Site\SiteFinder;

#[AsEventListener(
	identifier: 't3sbootstrap/backend/write-files',
	method: 'writeOutsourcedFiles',
)]
final readonly class OutsourcedFiles
{

	public function __construct(
		private readonly CacheService $cacheService,
		private readonly ConfigRepository $configRepository,
		private readonly ConfigurationManager $configurationManager,
		private readonly Config $config,
		private readonly FlashMessageService $flashMessageService,
		private readonly SiteFinder $siteFinder
	) {}

	public function writeOutsourcedFiles(RecordCreationEvent $event): void
	{
		if (!empty($event->getRawRecord()->getMainType())
			 && $event->getRawRecord()->getMainType() === 'tx_t3sbootstrap_domain_model_config') 
		{
			$settings = $this->configurationManager->getConfiguration(
				ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
				't3sbootstrap'
			);
			$currentUid = $event->getRawRecord()->getPid();		
			$rootConfig = $this->configRepository->findOneBy(['pid' => $currentUid]);
			
			$site = $this->siteFinder->getSiteByPageId($currentUid);
			$siteConfig = $site-> getConfiguration();
			$t3sbSettings = $siteConfig['settings']['bootstrap'];

			if ($rootConfig->getNavbarBreakpoint() !== 'no') {
				$breakpointWidth = $t3sbSettings['navbar']['breakpoint'][$rootConfig->getNavbarBreakpoint()];
			} else {
				$breakpointWidth = '';
			}

			$siterootConfig = [];
			$configurations = [];

			foreach ($this->configRepository->findAll() as $config) {
				$page = BackendUtility::getRecord('pages', $config->getPid() , '*');
				if (!empty($page['uid']) && $page['hidden'] === 0 && $page['deleted'] === 0 && !empty($page['is_siteroot'])) {
					$siterootConfig[$config->getUid()] = $page['is_siteroot'];
					$configurations[$config->getPid()] = $config;
				}
			}

			$setup = '';
			
			// @extensionScannerIgnoreLine
			foreach ($this->objectToArr($this->config) as $key=>$value) {
				if (!str_starts_with($key, '_')) {
					$setup .= 'page.10.settings.config.'.$key.' = {$bootstrap.config.'.$key.'}'.PHP_EOL;
				}
			}
			
			$setup .= 'page.10.settings.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;

			// outsourced constants
			$filecontent = '';
			foreach ($configurations as $config) {
				if (count($siterootConfig) && $config->getPid() === $config->getHomepageUid()) {
					if (count($siterootConfig) === 1) {
						$filecontent .= $this->getConstants($config, true);
						$filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
					} else {
						$filecontent .= '[site("rootPageId") == ' . $config->getPid() . ']'.PHP_EOL;
						$filecontent .= $this->getConstants($config, true);
						$filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
						$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
					}
				}
			}

			$baseDir = GeneralUtility::getFileAbsFileName("EXT:t3sb_package/Configuration/");
			$customPath = $baseDir.'TypoScript/';
			$customFileName = 't3sbconstants.typoscript';
			$customFile = $customPath.$customFileName;

			if (file_exists($customFile)) {
				unlink($customFile);
			}
			if (!is_dir($customPath)) {
				if (!mkdir($customPath, 0755, true) && !is_dir($customPath)) {
					$this->addFlashMessage(sprintf('Directory "%s" was not created', $customPath), 'ERROR');
				}
			}

			// write outsourced constants
			GeneralUtility::writeFile($customFile, $filecontent);
			
			// Outsourced setup
			$customFileName = 't3sbsetup.typoscript';
			$customFile = $customPath.$customFileName;

			if (file_exists($customFile)) {
				unlink($customFile);
			}
			if (!is_dir($customPath)) {
				if (!mkdir($customPath, 0755, true) && !is_dir($customPath)) {
					$this->addFlashMessage(sprintf('Directory "%s" was not created', $customPath), 'ERROR');
				}
			}
			
			// write outsourced setup
			GeneralUtility::writeFile($customFile, $setup);

			// Update outsourced custom SCSS
			if (!empty($rootConfig->getCustomVariablesScss()) || !empty($rootConfig->getCustomScss())) { 
				// update custom scss
				$baseDir = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Resources/');
				$customPath = $baseDir.'Public/T3SB-SCSS/';

				if (!is_dir($customPath)) {
					if (!mkdir($customPath, 0755, true) && !is_dir($customPath)) {
						$this->addFlashMessage(sprintf('Directory "%s" was not created', $customPath), 'ERROR');
					}
				}

				$customFileName = 'custom-variables-'.$currentUid.'.scss';
				$customFileNameOverride = 'custom-'.$currentUid.'.scss';
				
				$this->writeCustomFile($rootConfig, $customPath, $customFileName, '_variables');
				$this->writeCustomFile($rootConfig, $customPath, $customFileNameOverride, '_bootswatch');

				$this->cacheService->clearPageCache();
			}
		}
	}
	
	
	private function objectToArr(Config $obj): array
	{
		$result = [];
		$cls = new \ReflectionClass($obj);
		$props = $cls->getProperties();
		foreach ($props as $key=>$prop) {
			$result[$prop->getName()] = 'get'.ucfirst($prop->getName());
		}
	
		return $result;
	}
	
	
	/**
	 * Get the data from DB
	 */
	protected function getConstants(Config $config, bool $isRoot): string
	{
		$constants = 'bootstrap.config.uid = '.$config->getUid() .PHP_EOL;	
		$tcaColumns = $GLOBALS['TCA']['tx_t3sbootstrap_domain_model_config']['columns'];

		foreach ($tcaColumns as $f=>$columns) {
			$field = GeneralUtility::underscoredToLowerCamelCase($f);
			$var = str_replace(' ', '_', $field);
			$getField = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
			$value = $config->$getField() === '' ? 0 : $config->$getField();

			if ($field === 'customScss' || $field === 'customVariablesScss') {
				if (!empty($value)) {
					$value = 1;
				} else {
					$value = 0;
				}
			}
			if ($var === 'jumbotronCarouselPause' && $value === 1) {
				$value = 'hover';
			} elseif ($var === 'jumbotronCarouselPause' && $value === 0) {
				$value = '';
			}
			if ($isRoot) {
				$constants .= 'bootstrap.config.'.$var.' = '.$value .PHP_EOL;
			}

		}

		return $constants;
	}
	
	
	private function writeCustomFile(Config $rootConfig, string $customPath, string $customFileName, string $name): void
	{
		$customFile = $customPath.$customFileName;

		if ($name === '_variables') {
			$customContent = $rootConfig->getCustomVariablesScss();
		} else {
			$customContent = $rootConfig->getCustomScss();
		}

		if (file_exists($customFile)) {
				unlink($customFile);
		}
		
		GeneralUtility::writeFile($customFile, $customContent);
	}


	private function addFlashMessage(string $text, string $header): void
	{
		$message = GeneralUtility::makeInstance(
			FlashMessage::class,
			$text,
			$header,
			ContextualFeedbackSeverity::ERROR
		);
		$defaultFlashMessageQueue = $this->flashMessageService->getMessageQueueByIdentifier();
		$defaultFlashMessageQueue->enqueue($message);
	}

}
