<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Psr\Http\Message\ResponseInterface;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use T3SBS\T3sbootstrap\Domain\Model\Config;

/**
 * Abstract controller.
 */
abstract class AbstractController extends ActionController
{

	protected $configRepository;
	protected $isSiteroot;
	protected $rootPageId;
	protected $currentUid;
	protected $tcaColumns;
	protected $isAdmin;
	protected $rootConfig;
	protected $version;
	protected $rootTemplates;
	protected $persistenceManager;

	/**
	 * Init all actions.
	 */
	public function initializeAction()
	{
		$site = self::getCurrentSite();
		$this->rootPageId = $site->getRootPageId();
		$this->currentUid = (int) GeneralUtility::_GET('id');
		$this->isSiteroot = $this->rootPageId === $this->currentUid ? TRUE : FALSE;
		$this->tcaColumns = $GLOBALS['TCA']['tx_t3sbootstrap_domain_model_config']['columns'];
		$this->isAdmin = $GLOBALS['BE_USER']->isAdmin();
		$this->rootConfig = $this->configRepository->findOneByPid($this->rootPageId);
 		$typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
		$this->version = (int)$typo3Version->getVersion();
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');
		$this->rootTemplates = $queryBuilder
			 ->select('uid','pid', 'constants')
			 ->from('sys_template')
			 ->where(
				$queryBuilder->expr()->eq('root', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
			 )
			 ->execute()->fetchAll();

		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
		if ($typo3Version->getMajorVersion() === 11) {
			$pageRenderer->addCssFile('EXT:t3sbootstrap/Resources/Public/Backend/bestyles-v11.css');
		} else {
			$pageRenderer->addCssFile('EXT:t3sbootstrap/Resources/Public/Backend/bestyles-v10.css');
		}
		$pageRenderer->loadRequireJsModule(
			 'TYPO3/CMS/T3sbootstrap/Bootstrap',
			 'function() { console.log("Loaded bootstrap.js by t3sbootstrap!"); }'
		);
	}


	/**
	 * SCSS in the BE
	 */
	protected function getCustomScss( string $file ): array
	{
		$assignedOptions = [];
		$customScssDir = !empty($this->settings['customScssPath']) ? $this->settings['customScssPath'] : 'fileadmin/T3SB/Resources/Public/SCSS/';
		$customScssFilePath = GeneralUtility::getFileAbsFileName($customScssDir);
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
		$result = $queryBuilder
			 ->select('*')
			 ->from('pages')
			 ->where(
			 	$queryBuilder->expr()->eq('sys_language_uid', 0),
				$queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
			 )
			 ->execute();

		$siteroots = $result->fetchAll();
		$customScssFileName = '';

		foreach ($siteroots as $key=>$siteroot) {
			if ( $siteroot['uid'] == $this->currentUid ) {
				if ( $key === 0 ) {
					$customScssFileName = $file.'.scss';
				} else {
					$customScssFileName = $file.'-'.$siteroot['uid'].'.scss';
				}
			}
		}

		$customScssFile = $customScssFilePath.$customScssFileName;

		if ( file_exists($customScssFile) ) {

			$customScssFile = $customScssFilePath.$customScssFileName;
			if ($file == 'custom') {
				$assignedOptions['customScss'] = TRUE;
			}
			$arguments = $this->request->getArguments();
			if ( !empty($arguments['t3sbootstrap'][$file]) ) {
				$scss = $arguments['t3sbootstrap'][$file];
			} else {
				$scss = !empty($arguments[$file]) ? $arguments[$file] : '';
			}
			if ($scss) {
				$assignedOptions[$file] = $scss;
				$handle = fopen($customScssFile, 'w') or die('Cannot open file:	 '.$customScssFile);
				fwrite($handle, $scss);
				fclose($handle);
				if ($file == 'custom') {
					// clean typo3temp/assets/t3sbootstrap/css/
					$tempPath = GeneralUtility::getFileAbsFileName('typo3temp/assets/t3sbootstrap/css/');
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

		return (array)$assignedOptions;
	}



	/**
	* prepare options for select fields
	*/
	protected function getFieldsOptions(): array
	{
		foreach ( $this->tcaColumns as $field=>$columns ) {
			// is select-field
			if ( $columns['config']['type'] == 'select' && $columns['config']['renderType'] == 'selectSingle' ) {
				$var = GeneralUtility::underscoredToLowerCamelCase($field).'Options';
				foreach ( $columns['config']['items'] as $key=>$entry ) {
					$option = new \stdClass();
					$option->key = $entry[1];
					$option->value = $entry[0];
					$fieldsOptions[$var][$entry[1]] = $option;
				}
			}
		}

		return $fieldsOptions;
	}


	/**
	* take over $rootConfig settings
	*/
	protected function getNewConfig(Config $rootConfig): Config
	{
		$newConfig = new Config();

		foreach ( $this->tcaColumns as $field=>$columns ) {
			$var = GeneralUtility::underscoredToUpperCamelCase($field);
			$set = 'set'.$var;
			$get = 'get'.$var;
			$newConfig->$set( $rootConfig->$get() );
		}

		return $newConfig;
	}


	/**
	 * Compare config with rootconfig
	 */
	protected function compareConfig(Config $config): array
	{
		$compare = [];

		foreach ( $this->tcaColumns as $field=>$columns ) {
			$filedsArr[] = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
		}
		foreach ($filedsArr as $field) {
			if ( $config->$field() !== $this->rootConfig->$field() ) {
				$key = lcfirst(substr($field,3));
				$rootConfigField = $this->rootConfig->$field();
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
	 * override config - info in BE Module
	 */
	protected function overrideConfig(): array
	{
		$override = [];
		$ts = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

		foreach ( $this->tcaColumns as $field=>$columns ) {
			$filedsArr[$field] = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
		}

		foreach ($filedsArr as $fKey=>$field) {
			$fKey = GeneralUtility::underscoredToLowerCamelCase($fKey);
			if ( !empty($ts['page.']['10.']['settings.']['config.'][$fKey]) ) {
				$tsField = $ts['page.']['10.']['settings.']['config.'][$fKey];
				if ( $tsField != $this->rootConfig->$field() &&	str_starts_with((string)$tsField, '{$bootstrap.config.') != TRUE ) {
					if ( $this->rootConfig->$field() === TRUE ) {
						$override[$fKey] = 'enabled';
					} elseif ( $this->rootConfig->$field() === FALSE ) {
						$override[$fKey] = 'disabled';
					} elseif ($this->rootConfig->$field() == '') {
						$override[$fKey] = 'not set';
					} else {
						$override[$fKey] = $tsField;
					}
				}
			}
		}

		return $override;
	}


	/**
	 * Delete files from directory
	 */
	protected function deleteFilesFromDirectory(string $directory): void
	{
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
	 */
	protected function writeConstants(): void
	{
		$this->persistenceManager->persistAll();
		if ( count($this->rootTemplates) ) {
			$configRepository = $this->configRepository->findOneByPid($this->rootPageId);
			$navbarBreakpoint = $configRepository->getNavbarBreakpoint();
			$breakpointWidth = $this->settings['breakpoint'][$navbarBreakpoint];
			$siteroots = [];
			$filecontent = '';

			foreach( $this->configRepository->findAll() as $config ) {
				$page = GeneralUtility::makeInstance(PageRepository::class)->getPage($config->getPid());
				if ( $page['hidden'] === 0 && $page['deleted'] === 0 ) {
					if (!empty($page['is_siteroot'])) {
						$siteroots[$config->getUid()] = $page['is_siteroot'];
					}
					$pages[$config->getPid()] = $page;
					$configurations[$config->getPid()] = $config;
				}
			}

			// outsourced setup
			$setup = '';
			$model = GeneralUtility::makeInstance(Config::class);
			foreach ( $this->objectToArr($model) as $key=>$value) {
				if ( !str_starts_with($key, '_') ) {
					$setup .= 'page.10.settings.config.'.$key.' = {$bootstrap.config.'.$key.'}'.PHP_EOL;
				}
			}
			$setup .= 'page.10.settings.config.navbarBreakpointWidth = {$bootstrap.config.navbarBreakpointWidth}'.PHP_EOL;

			// outsourced constants
			foreach ( $configurations as $config ) {
				if ($config->getPid() == $config->getHomepageUid()) {
					// is root page
					if ( count($siteroots) === 1 ) {
						$filecontent .= self::getConstants($config, TRUE);
						$filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
					} else {
						$filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
						$filecontent .= self::getConstants($config, TRUE);
						$filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
						$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
					}
				} else {
					if ($config->getGeneralRootline()) {
						$filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
					} else {
						$filecontent .= '[page["uid"] == '.$config->getPid().']'.PHP_EOL;
					}
					if ($config->getGeneralOverride()) {
						$filecontent .= self::getConstants($config, TRUE);
					} else {
						$filecontent .= self::getConstants($config, FALSE);
					}
					$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
				}
			}
			$customDir = 'fileadmin/T3SB/Configuration/TypoScript/';
			$customPath = GeneralUtility::getFileAbsFileName($customDir);
			$customFileName = 't3sbconstants.typoscript';
			$customFile = $customPath.$customFileName;

			if (file_exists($customFile)) {
				unlink($customFile);
			}
			if (!is_dir($customPath)) {
				mkdir($customPath, 0777, true);
			}
			// constants
			GeneralUtility::writeFile($customFile, $filecontent);

			$customFileName = 't3sbsetup.typoscript';
			$customFile = $customPath.$customFileName;

			if (file_exists($customFile)) {
				unlink($customFile);
			}
			if (!is_dir($customPath)) {
				mkdir($customPath, 0777, true);
			}
			// setup
			GeneralUtility::writeFile($customFile, $setup);

		}
	}


	private function objectToArr($obj)
	{
		$result = [];
		$cls = new \ReflectionClass($obj);
		$props = $cls->getProperties();
		foreach ($props as $key=>$prop)
		{
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

		foreach ( $this->tcaColumns as $field=>$columns ) {
			$field = GeneralUtility::underscoredToLowerCamelCase($field);
			$var = str_replace(' ', '_', $field);
			$getField = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
			$value = $config->$getField() == '' ? 0 : $config->$getField();
			if ( $var == 'jumbotronCarouselPause' && $value == 1 ) {
				$value = 'hover';
			} elseif ( $var == 'jumbotronCarouselPause' && $value == 0 ) {
				$value = '';
			}
			if ($isRoot){
				$constants .= 'bootstrap.config.'.$var.' = '.$value .PHP_EOL;
			} else {
				if ($config->$getField() != $this->rootConfig->$getField()) {
					$constants .= 'bootstrap.config.'.$var.' = '.$value .PHP_EOL;
				}
			}
		}

		return $constants;
	}


	/**
	 * Get the Tca Columns
	 */
	 protected function getTcaColumns(): array
	 {
		foreach ( $this->tcaColumns as $key=>$column ) {
			if (!empty($column['accordion_id'])) {
				if ( !empty($column['accordion_sub']) ) {
					$sub = $column['accordion_sub'];
					$tca[$column['accordion_id']][$sub][$key] = $column;
					$tca[$column['accordion_id']][$sub][$key]['property'] = GeneralUtility::underscoredToLowerCamelCase($key);
					$tca[$column['accordion_id']][$sub][$key]['type'] = ucfirst($column['config']['type']);
					$tca[$column['accordion_id']][$sub][$key]['noSub'] = FALSE;
				} else {
					$tca[$column['accordion_id']][$key] = $column;
					$tca[$column['accordion_id']][$key]['property'] = GeneralUtility::underscoredToLowerCamelCase($key);
					$tca[$column['accordion_id']][$key]['type'] = ucfirst($column['config']['type']);
					$tca[$column['accordion_id']][$key]['noSub'] = TRUE;
				}
			}
		}

		return $tca;
	}


	/**
	 * Get the Utility Colors
	 */
	 protected function getUtilityColors(): array
	 {
	 	$defaultUtilColorsList = '$white,$gray-100,$gray-200,$gray-300,$gray-400,$gray-500,$gray-600,$gray-700,$gray-800,$gray-900,$black,$blue,$indigo,$purple,$pink,$red,$orange,$yellow,$green,$teal,$cyan,$primary,$secondary,$success,$info,$warning,$danger,$light,$dark';
		$utilityColors = [];
		$colors = [];
		$customScssArr = [];
		$defaultUtilityColors = [];
		$defaultcolors = [];

		$customScss = self::getCustomScss('custom-variables');
		$custom_variables = empty($customScss['custom-variables']) ? '' : preg_replace('/\s+/', ' ', trim($customScss['custom-variables']));
		$default = '// Overrides Bootstrap variables // $enable-shadows: true; // $enable-gradients: true; // $enable-negative-margins: true;';

		if ( !empty($customScss['custom-variables']) && str_starts_with($custom_variables, $default) == FALSE ) {
			foreach( GeneralUtility::trimExplode(';', $customScss['custom-variables']) as $customvariables ) {
				$scsscolor = GeneralUtility::trimExplode(':', $customvariables);
				if ( str_starts_with((string)$customvariables, '$') && GeneralUtility::inList($defaultUtilColorsList, $scsscolor[0]) ) {
					$scsscolor = GeneralUtility::trimExplode(':', $customvariables);
					$customScssArr[$scsscolor[0]] = $scsscolor[1];
				}
			}
			foreach( $customScssArr as $key=>$customvariables ) {
				if (str_starts_with((string)$customvariables, '$')) {
					if (!empty($customScssArr[$customvariables]) && $customScssArr[$customvariables])
					$utilityColors[$key] = $customScssArr[$customvariables];
				} elseif ( str_starts_with((string)$customvariables, '#') ) {
					if ($customvariables)
					$utilityColors[$key] = $customvariables;
				}
			}
		}

		$variablesSCSS = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/_variables.scss';
		$variablesSCSS = GeneralUtility::getFileAbsFileName($variablesSCSS);
		$variablesSCSS = GeneralUtility::getURL($variablesSCSS);

		if (!empty($variablesSCSS)) {
			foreach ( GeneralUtility::trimExplode(';', $variablesSCSS) as $defaultVariables) {
				$defaultScssColor = GeneralUtility::trimExplode(':', $defaultVariables);
				if ( str_starts_with((string)$defaultVariables, '$') && GeneralUtility::inList($defaultUtilColorsList, $defaultScssColor[0])
					 && ( str_starts_with((string)$defaultScssColor[1], '$') || str_starts_with((string)$defaultScssColor[1], '#') ) ) {
					$scsscolor = GeneralUtility::trimExplode(':', $defaultVariables);
					$defaultUtilColors[$scsscolor[0]] = substr($scsscolor[1], 0, -9);
				}
			}
			foreach( $defaultUtilColors as $key=>$customvariables ) {
				if (str_starts_with((string)$customvariables, '$')) {
					if ( !empty($customScssArr[$customvariables]) && $customScssArr[$customvariables])
					$defaultUtilityColors[$key] = $customScssArr[$customvariables];
				} elseif ( str_starts_with((string)$customvariables, '#') ) {
					if ($customvariables)
					$defaultUtilityColors[$key] = $customvariables;
				}
			}
		}
		if ( is_array($utilityColors) && is_array($defaultUtilityColors) ) {

			if ( !empty($utilityColors) ) {
				$colorArr = array_merge($defaultUtilityColors, $utilityColors);
				ksort($colorArr);
				return $colorArr;
			} else {
				ksort($defaultUtilityColors);
				return $defaultUtilityColors;
			}
		} else {
			return [];
		}
	}


	/**
	 * Returns some default settings for new root configuration
	 */
	protected function setDefaults(Config $newConfig): Config
	{
		$defaultNavbarImagePath = isset($this->settings['defaultNavbarImagePath']) ? $this->settings['defaultNavbarImagePath'] : '';
		$newConfig->setHomepageUid($this->currentUid);
		$newConfig->setPageTitle( 'jumbotron' );
		$newConfig->setPageTitlealign( 'center' );
		$newConfig->setNavbarImage($defaultNavbarImagePath);
		$newConfig->setNavbarEnable( 'light' );
		$newConfig->setNavbarLevels(4);
		$newConfig->setNavbarbrandAlignment( 'left' );
		$newConfig->setNavbarColor( 'warning' );
		$newConfig->setNavbarAlignment( 'left' );
		$newConfig->setNavbarBrand( 'imgText' );
		$newConfig->setNavbarContainer( 'inside' );
		$newConfig->setNavbarClass('');
		$newConfig->setJumbotronEnable(1);
		$newConfig->setJumbotronSlide(0);
		$newConfig->setJumbotronPosition( 'below' );
		$newConfig->setJumbotronContainer( 'container' );
		$newConfig->setJumbotronContainerposition( 'Inside' );
		$newConfig->setJumbotronCarouselInterval(5000);
		$newConfig->setJumbotronCarouselPause(0);
		$newConfig->setJumbotronClass( 'p-5 mb-4 bg-light rounded-0' );
		$newConfig->setBreadcrumbEnable(1);
		$newConfig->setBreadcrumbCorner(1);
		$newConfig->setBreadcrumbPosition( 'belowJum' );
		$newConfig->setBreadcrumbContainer( 'container' );
		$newConfig->setSidebarLevels(4);
		$newConfig->setFooterEnable(1);
		$newConfig->setFooterSlide(0);
		$newConfig->setFooterContainer( 'container' );
		$newConfig->setFooterSticky(1);
		$newConfig->setFooterContainerposition( 'inside' );
		$newConfig->setFooterClass( 'bg-dark text-light py-4' );
		$newConfig->setStickyFooterExtraPadding(100);
		$newConfig->setCompress(1);
		$newConfig->setDisablePrefixComment(1);
		$newConfig->setGlobalPaddingTop( 'pt-5' );
		$newConfig->setLoadingSpinnerColor( 'primary' );
		$newConfig->setLightboxSelection(1);
		$newConfig->setShrinkingNavPadding( '5' );
		$newConfig->setSidebarMenuPosition( 'above' );
		$newConfig->setLangMenuWithFaIcon(1);
		$newConfig->setDateFormat( 'd.m.Y' );
		$newConfig->setSubheaderColor( 'secondary' );
		$newConfig->setFaLinkIcons(1);
		$newConfig->setSectionmenuAnchorOffset(29);
		$newConfig->setSectionmenuScrollspyOffset(130);
		$newConfig->setSectionmenuScrollspy(1);
		$newConfig->setNavbarLangFlags(1);

		return $newConfig;
	}


	/**
	 * Returns the currently configured "site" if a site is configured (= resolved) in the current request.
	 */
	protected function getCurrentSite(): SiteInterface
	{
		$matcher = GeneralUtility::makeInstance(SiteMatcher::class);
		return $matcher->matchByPageId((int) GeneralUtility::_GET('id'));
	}


	protected function getTreeList($id, $depth, $begin = 0, $permsClause = ''): string
	{
		$depth = (int)$depth;
		$begin = (int)$begin;
		$id = (int)$id;
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


}