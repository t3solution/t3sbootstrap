<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Hooks;

use T3SBS\T3sbootstrap\Domain\Model\Config;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\CacheService;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;


/**
 * Writes outsourced TypoScript constants/setup and SCSS files when a t3sbootstrap
 * configuration record is created or updated in the backend.
 *
 * Registered via:
 *   $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
 *       = \T3SBS\T3sbootstrap\Backend\Hook\OutsourcedFiles::class;
 */
final readonly class OutsourcedFiles
{
	private const TABLE = 'tx_t3sbootstrap_domain_model_config';

	public function __construct(
		private CacheService $cacheService,
		private ConfigRepository $configRepository,
		private FlashMessageService $flashMessageService,
		private SiteFinder $siteFinder,
		private ReflectionService $reflectionService,
	) {
	}

	/**
	 * DataHandler hook entry point: fires after a record has been written to the DB.
	 */
	public function processDatamap_afterDatabaseOperations(
		string $status,
		string $table,
		int|string $id,
		array $fieldArray,
		DataHandler $dataHandler,
	): void {
		if ($table !== self::TABLE) {
			return;
		}

		// Resolve UID for both new (NEW...) and existing records
		$uid = (int)(is_string($id) && str_starts_with($id, 'NEW')
			? ($dataHandler->substNEWwithIDs[$id] ?? 0)
			: $id);

		if ($uid <= 0) {
			return;
		}

		$record = BackendUtility::getRecord(self::TABLE, $uid);
		if ($record === null) {
			return;
		}

		$pid = (int)$record['pid'];
		$rootConfig = $this->configRepository->findOneBy(['pid' => $pid]);
		if (!$rootConfig instanceof Config) {
			return;
		}

		$this->writeOutsourcedFiles($rootConfig, $pid);
	}

	private function writeOutsourcedFiles(Config $rootConfig, int $currentUid): void
	{
		$breakpointWidth = $this->resolveBreakpointWidth($rootConfig, $currentUid);
		[$configurations, $siterootCount] = $this->collectConfigurations();

		$setup = $this->buildSetup($rootConfig, $breakpointWidth);
		$constants = $this->buildConstants($configurations, $siterootCount, $breakpointWidth);

		$baseDir = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Configuration/');
		$customPath = $baseDir . 'TypoScript/';

		$this->writeFile($customPath, 't3sbconstants.typoscript', $constants);
		$this->writeFile($customPath, 't3sbsetup.typoscript', $setup);

		// Update outsourced custom SCSS (if present)
		if (!empty($rootConfig->getCustomVariablesScss()) || !empty($rootConfig->getCustomScss())) {
			$scssPath = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Resources/Public/T3SB-SCSS/');
			$this->ensureDirectoryExists($scssPath);

			$this->writeCustomScssFile(
				$scssPath,
				'custom-variables-' . $currentUid . '.scss',
				(string)$rootConfig->getCustomVariablesScss(),
			);
			$this->writeCustomScssFile(
				$scssPath,
				'custom-' . $currentUid . '.scss',
				(string)$rootConfig->getCustomScss(),
			);

			$this->cacheService->clearPageCache();
		}
	}

	private function resolveBreakpointWidth(Config $rootConfig, int $currentUid): string
	{
		if ($rootConfig->getNavbarBreakpoint() === 'no') {
			return '';
		}

		try {
			$site = $this->siteFinder->getSiteByPageId($currentUid);
		} catch (SiteNotFoundException $e) {
			$this->addFlashMessage(
				sprintf('No site config found for page %d: %s', $currentUid, $e->getMessage()),
				'Warning'
			);
			return '';
		}
		
		$siteConfig = $site->getConfiguration();
		$breakpoints = $siteConfig['settings']['bootstrap']['navbar']['breakpoint'] ?? [];
		$key = $rootConfig->getNavbarBreakpoint();

		return (string)($breakpoints[$key] ?? '');
	}

	/**
	 * @return array{0: array<int, Config>, 1: int}
	 */
	private function collectConfigurations(): array
	{
		$configurations = [];
		$siterootCount = 0;

		foreach ($this->configRepository->findAll() as $config) {
			$page = BackendUtility::getRecord('pages', $config->getPid());
			if ($page === null) {
				continue;
			}

			// tinyint values may arrive as int OR string depending on the driver
			$isHidden = !empty($page['hidden']);
			$isDeleted = !empty($page['deleted']);
			$isSiteroot = !empty($page['is_siteroot']);

			if ($isHidden || $isDeleted || !$isSiteroot) {
				continue;
			}

			$configurations[$config->getPid()] = $config;
			$siterootCount++;
		}

		return [$configurations, $siterootCount];
	}

	private function buildSetup(Config $rootConfig, string $breakpointWidth): string
	{
		$setup = '';

		foreach ($this->getConfigPropertyNames($rootConfig) as $key) {
			if (str_starts_with($key, '_')) {
				continue;
			}
			$setup .= 'page.10.settings.config.' . $key . ' = {$bootstrap.config.' . $key . '}' . PHP_EOL;
		}

		$setup .= 'page.10.settings.config.navbarBreakpointWidth = ' . $breakpointWidth . PHP_EOL;

		return $setup;
	}

	/**
	 * @param array<int, Config> $configurations
	 */
	private function buildConstants(array $configurations, int $siterootCount, string $breakpointWidth): string
	{
		$filecontent = '';

		foreach ($configurations as $config) {
			if ($siterootCount === 0 || (int)$config->getPid() !== (int)$config->getHomepageUid()) {
				continue;
			}

			if ($siterootCount === 1) {
				$filecontent .= $this->getConstants($config);
				$filecontent .= 'bootstrap.config.navbarBreakpointWidth = ' . $breakpointWidth . PHP_EOL;
			} else {
				$filecontent .= '[site("rootPageId") == ' . $config->getPid() . ']' . PHP_EOL;
				$filecontent .= $this->getConstants($config);
				$filecontent .= 'bootstrap.config.navbarBreakpointWidth = ' . $breakpointWidth . PHP_EOL;
				$filecontent .= '[END]' . PHP_EOL . PHP_EOL;
			}
		}

		return $filecontent;
	}

	private function getConstants(Config $config): string
	{
		$constants = 'bootstrap.config.uid = ' . $config->getUid() . PHP_EOL;
		$tcaColumns = $GLOBALS['TCA'][self::TABLE]['columns'] ?? [];

		foreach (array_keys($tcaColumns) as $tcaField) {
			$field = GeneralUtility::underscoredToLowerCamelCase($tcaField);
			$getter = 'get' . GeneralUtility::underscoredToUpperCamelCase($field);

			if (!method_exists($config, $getter)) {
				continue;
			}

			$rawValue = $config->$getter();
			$value = empty($rawValue) && $rawValue !== 0 && $rawValue !== '0' ? 0 : $rawValue;

			if ($field === 'customScss' || $field === 'customVariablesScss') {
				$value = !empty($rawValue) ? 1 : 0;
			}

			if (is_scalar($value)) {
				$constants .= 'bootstrap.config.' . $field . ' = ' . $value . PHP_EOL;
			}

		}

		return $constants;
	}

	/**
	 * Return the property names of the Config model via Reflection.
	 *
	 * @return list<string>
	 */
	private function getConfigPropertyNames(Config $config): array
	{
		$reflection = $this->reflectionService->getClassSchema(Config::class);
			
		$names = [];
		foreach ($reflection->getProperties() as $property) {
			$names[] = $property->getName();
		}

		return $names;
	}

	private function writeFile(string $path, string $filename, string $content): void
	{
		$this->ensureDirectoryExists($path);
		$fullPath = $path . $filename;

		if (file_exists($fullPath)) {
			@unlink($fullPath);
		}

		$success = GeneralUtility::writeFile($fullPath, $content);
		if ($success === false) {
			$this->addFlashMessage(sprintf('File "%s" could not be written.', $fullPath), 'ERROR');
		}
	}

	private function writeCustomScssFile(string $path, string $filename, string $content): void
	{
		$fullPath = $path . $filename;

		if (file_exists($fullPath)) {
			@unlink($fullPath);
		}

		$success = GeneralUtility::writeFile($fullPath, $content);
		if ($success === false) {
			$this->addFlashMessage(sprintf('SCSS file "%s" could not be written.', $fullPath), 'ERROR');
		}
	}

	private function ensureDirectoryExists(string $path): void
	{
		if (is_dir($path)) {
			return;
		}

		if (!mkdir($path, 0755, true) && !is_dir($path)) {
			$this->addFlashMessage(sprintf('Directory "%s" was not created', $path), 'ERROR');
		}
	}

	private function addFlashMessage(string $text, string $header): void
	{
		$message = GeneralUtility::makeInstance(
			FlashMessage::class,
			$text,
			$header,
			ContextualFeedbackSeverity::ERROR,
			true,
		);
		$this->flashMessageService->getMessageQueueByIdentifier()->enqueue($message);
	}
}
