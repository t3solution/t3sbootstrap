<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Information\Typo3Version;

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
	 * is siteroot
	 *
	 * @var boolean
	 */
	protected $isSiteroot;

	/**
	 * rootpage Id
	 *
	 * @var int
	 */
	protected $rootPageId;

	/**
	 * current uid
	 *
	 * @var int
	 */
	protected $currentUid;

	/**
	 * columns from TCA tx_t3sbootstrap_domain_model_config
	 *
	 * @var array
	 */
	protected $tcaColumns;

	/**
	 * is admin
	 *
	 * @var bool
	 */
	protected $isAdmin;

	/**
	 * Root configuration
	 *
	 * @var \T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository
	 */
	protected $rootConfig;

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
		$site = self::getCurrentSite();
		$this->rootPageId = $site->getRootPageId();
		$this->currentUid = (int) GeneralUtility::_GET('id');
		$this->isSiteroot = $this->rootPageId === $this->currentUid ? TRUE : FALSE;
		$this->tcaColumns = $GLOBALS['TCA']['tx_t3sbootstrap_domain_model_config']['columns'];
		$this->isAdmin = $GLOBALS['BE_USER']->isAdmin();
		$this->rootConfig = $this->configRepository->findOneByPid($this->rootPageId);
 		$typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
		$this->version = (int)$typo3Version->getVersion();

		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
		$pageRenderer->loadRequireJsModule(
			 'TYPO3/CMS/T3sbootstrap/T3sBootstrap',
			 'function() { console.log("Loaded own module."); }'
		);

		if ($this->version === 11) {
			$pageRenderer->addCssFile('/typo3conf/ext/t3sbootstrap/Resources/Public/Backend/bestyles11.css');
		}
	}


	/**
	 * action list
	 *
	 * @param bool $deleted
	 * @param bool $created
	 * @param bool $updateSss
	 * @return void
	 */
	public function listAction($deleted = FALSE, $created = FALSE, $updateSss = FALSE): void
	{
		if ( $this->isSiteroot && $this->rootPageId ) {

			$pidList = $this->getTreeList($this->rootPageId, 999999, 0, '1');

			foreach ( $this->configRepository->findAll() as $config ) {
				if (GeneralUtility::inList($pidList, $config->getPid())) {
					$page = BackendUtility::getRecord('pages',$config->getPid(),'uid,title');
					$allConfig[$page['uid']]['confUid'] = $config->getUid();
					$allConfig[$page['uid']]['title'] = $page['title'];
					$allConfig[$page['uid']]['uid'] = $page['uid'];
				}
			}

			$assignedOptions['isSiteroot'] = TRUE;
			$assignedOptions['allConfig'] = $allConfig;
		}

		$assignedOptions['t3version'] = $this->version;
		$assignedOptions['rootConfig'] = $this->rootConfig ? TRUE : FALSE;
		$assignedOptions['config'] = $this->configRepository->findOneByPid($this->currentUid);
		$assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['customScss'] = FALSE;
		$assignedOptions['scss'] = '';
		$assignedOptions['action'] = 'list';
		$assignedOptions['updateScss'] = $updateSss;
		$assignedOptions['deleted'] = $deleted;
		$assignedOptions['created'] = $created;

		if ( (int)$this->settings['customScss'] === 1 && (int)$this->settings['wsScss'] === 1 ) {
			$customScss = self::getCustomScss('custom-variables');
			$assignedOptions['custom-variables'] = $customScss['custom-variables'];
			$customScss = self::getCustomScss('custom');
			$assignedOptions['custom'] = $customScss['custom'];
			$assignedOptions['customScss'] = $customScss['customScss'];
			if ( $this->settings['enableUtilityColors'] ) {
				$assignedOptions['utilColors'] = self::getUtilityColors();
			}
		}

		if ($this->settings['wizardsMarkedUndone']) {
			self::wizardsMarkedUndone();
		}

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action new
	 *
	 * @return void
	 */
	public function newAction(): void
	{
		$assignedOptions = self::getFieldsOptions();
		$assignedOptions['pid'] = $this->currentUid;
		$assignedOptions['tcaColumns'] = self::getTcaColumns();

		if ( $this->rootConfig ) {
			// config from rootline
			if ( $this->rootConfig->getGeneralRootline() ) {
				$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, $this->currentUid)->get();
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
				$assignedOptions['newConfig'] = self::getNewConfig($this->rootConfig);
			}

		} else {
			$newConfig = new \T3SBS\T3sbootstrap\Domain\Model\Config();
			// some defaults
			$newConfig = self::setDefaults($newConfig);
			$assignedOptions['newConfig'] = $newConfig;
		}

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action create
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	 * @return void
	 */
	public function createAction(\T3SBS\T3sbootstrap\Domain\Model\Config $newConfig): void
	{
		$newConfig->setHomepageUid($this->rootPageId);
		$newConfig->setPid($this->currentUid);
		$this->configRepository->add($newConfig);
		self::writeConstants();

		parent::redirect('list',NULL,Null,array('created' => TRUE));
	}


	/**
	 * action edit
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @param bool $updated
	 * @return void
	 */
	public function editAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config, $updated = FALSE): void
	{
		$assignedOptions = self::getFieldsOptions();
		$assignedOptions['t3version'] = $this->version;
		$assignedOptions['config'] = $config;
		$assignedOptions['pid'] = $this->currentUid;
		$assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['isSiteroot'] = $this->isSiteroot;
		$assignedOptions['updated'] = $updated;
		$assignedOptions['override'] = self::overrideConfig();
		$assignedOptions['tcaColumns'] = self::getTcaColumns();
		$assignedOptions['action'] = 'edit';
		if ( !$this->isSiteroot ) {
			$assignedOptions['compare'] = self::compareConfig($config);
		}

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action update
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return void
	 */
	public function updateAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config): void
	{
		$config->setHomepageUid($this->rootPageId);
		$this->configRepository->update($config);
		self::writeConstants();

		parent::redirect('edit',NULL,Null,array('config' => $config, 'updated' => TRUE));
	}


	/**
	 * action delete
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return void
	 */
	public function deleteAction(\T3SBS\T3sbootstrap\Domain\Model\Config $config): void
	{
		$this->configRepository->remove($config);
		self::writeConstants();

		parent::redirect('list',NULL,Null,array('deleted' => TRUE));
	}


	/**
	 * action dashboard
	 *
	 * @return void
	 */
	public function dashboardAction(): void
	{
		if ( $this->isSiteroot ) {
			$assignedOptions['extconf'] = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
			$assignedOptions['isLoaded']['ws_scss'] = ExtensionManagementUtility::isLoaded('ws_scss');
		}

		$assignedOptions['t3version'] = $this->version;
		$assignedOptions['action'] = 'dashboard';
		$assignedOptions['isSiteroot'] = $this->isSiteroot;
		$assignedOptions['admin'] = $this->isAdmin;

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	 * action constants
	 *
	 * @return void
	 */
	public function constantsAction(): void
	{
		if ( $this->isSiteroot ) {
			$constantPath = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/Configuration/TypoScript/t3sbconstants.typoscript');
			if ( file_exists($constantPath) ) {
				$fileGetContents = file_get_contents(GeneralUtility::getFileAbsFileName($constantPath));
				$outsourcedConstantsArr = explode('[END]', trim($fileGetContents));
				$toEnd = count($outsourcedConstantsArr);
				foreach ($outsourcedConstantsArr as $outsourcedConstants) {
					if (0 === --$toEnd) {
						$filecontent .= trim($outsourcedConstants).PHP_EOL.PHP_EOL;
					} else {
						$filecontent .= trim($outsourcedConstants).PHP_EOL . '[END]'.PHP_EOL.PHP_EOL;
					}
				}
				$assignedOptions['filecontent'] = $filecontent;
			}
		}

		$assignedOptions['t3version'] = $this->version;
		$assignedOptions['action'] = 'constants';
		$assignedOptions['isSiteroot'] = $this->isSiteroot;
		$assignedOptions['admin'] = $this->isAdmin;

		$this->view->assignMultiple($assignedOptions);
	}


	/**
	* prepare options for select fields
	*
	* @return array
	*/
	public function getFieldsOptions(): array
	{
		foreach ( $this->tcaColumns as $field=>$columns ) {
			// is select-field
			if ( $columns['config']['type'] == 'select' && $columns['config']['renderType'] == 'selectSingle' ) {
				$var = str_replace(' ', '_', $field);
				$var = GeneralUtility::underscoredToLowerCamelCase($field);
				$var = $var.'Options';
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
	*
	* @param \T3SBS\T3sbootstrap\Domain\Model\Config $rootConfig
	* @return \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	*/
	public function getNewConfig(\T3SBS\T3sbootstrap\Domain\Model\Config $rootConfig): \T3SBS\T3sbootstrap\Domain\Model\Config
	{
		$newConfig = new \T3SBS\T3sbootstrap\Domain\Model\Config();

		foreach ( $this->tcaColumns as $field=>$columns ) {
			$var = str_replace(' ', '_', $field);
			$var = GeneralUtility::underscoredToUpperCamelCase($field);
			$set = 'set'.$var;
			$get = 'get'.$var;
			$newConfig->$set( $rootConfig->$get() );
		}

		return $newConfig;
	}


	/**
	 * Compare config with rootconfig
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return array
	 */
	protected function compareConfig(\T3SBS\T3sbootstrap\Domain\Model\Config $config): array
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
	 *
	 * @return array
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
			$tsField = $ts['page.']['10.']['settings.']['config.'][$fKey];

			if ( $tsField != $this->rootConfig->$field() &&	GeneralUtility::isFirstPartOfStr($tsField, '{$bootstrap.config.') != TRUE ) {
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

		return $override;
	}


	/**
	 * SCSS in the BE
	 *
	 * @param string $file
	 * @return array
	 */
	public function getCustomScss( string $file ): array
	{
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
	 * @param string $directory
	 * @return void
	 */
	public function deleteFilesFromDirectory(string $directory): void
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
	 *
	 * @return void
	 */
	public function writeConstants(): void
	{
		$persistenceManager = $this->objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
		$persistenceManager->persistAll();

		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');
		$rootTemplates = $queryBuilder
			 ->select('uid','pid', 'constants')
			 ->from('sys_template')
			 ->where(
				$queryBuilder->expr()->eq('root', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
			 )
			 ->execute()->fetchAll();

		if ( count($rootTemplates) ) {
			$filecontent = '';
			foreach ($rootTemplates as $key=>$rootTemplate) {
				foreach ( $this->configRepository->findAll() as $config ) {
					$rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, $config->getPid())->get();
					if ( $config->getPid() == $rootTemplate['pid'] ) {

						if ( count($rootTemplates) == 1 ) {
							$filecontent .= self::getConstants($config, TRUE).PHP_EOL;
						} else {
							if ($rootLineArray[0]['uid'] == $rootTemplate['pid'] ){
								$filecontent .= '['.$rootTemplate['pid'].' in tree.rootLineIds]'.PHP_EOL;
								$filecontent .= self::getConstants($config, TRUE);
								$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
							}
						}
					} else {
						if ( count($rootTemplates) == 1 ) {
							if ($rootLineArray[0]['uid'] == $rootTemplate['pid'] ){
								if ($config->getGeneralRootline() || $config->getNavbarMegamenu()) {
									$filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
								} else {
									$filecontent .= '[page["uid"] == '.$config->getPid().']'.PHP_EOL;
								}
								$filecontent .= self::getConstants($config, FALSE);
								$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
							}
						} else {

							if ($rootLineArray[0]['uid'] == $rootTemplate['pid'] ){

								if ($config->getGeneralRootline() || $config->getNavbarMegamenu()) {
									$filecontent .= '['.$rootTemplate['pid'].' in tree.rootLineIds && '.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
								} else {
									$filecontent .= '['.$rootTemplate['pid'].' in tree.rootLineIds && page["uid"] == '.$config->getPid().']'.PHP_EOL;
								}
								$filecontent .= self::getConstants($config, FALSE);
								$filecontent .= '[END]'.PHP_EOL.PHP_EOL;
							}
						}
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

				GeneralUtility::writeFile($customFile, $filecontent);
			}
		}
	}


	/**
	 * Get the data from DB
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @param bool $isRoot
	 * @return string
	 */
	 private function getConstants(\T3SBS\T3sbootstrap\Domain\Model\Config $config, $isRoot): string
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
	 *
	 * @param \T3SBS\T3sbootstrap\Domain\Model\Config $config
	 * @return array
	 */
	 private function getTcaColumns(): array
	 {
		foreach ( $this->tcaColumns as $key=>$column ) {
			if ($column['accordion_id']) {
				if ( $column['accordion_sub'] ) {
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
	 *
	 * @return array
	 */
	 private function getUtilityColors(): array
	 {
		$defaultUtilColorsList = '$white,$gray-100,$gray-200,$gray-300,$gray-400,$gray-500,$gray-600,$gray-700,$gray-800,$gray-900,$black,$blue,$indigo,$purple,$pink,$red,$orange,$yellow,$green,$teal,$cyan,$primary,$secondary,$success,$info,$warning,$danger,$light,$dark';
		$utilityColors = [];
		$colors = [];
		$customScss = self::getCustomScss('custom-variables');
		if ($customScss['custom-variables']) {
			$customScssArr = GeneralUtility::trimExplode(';', $customScss['custom-variables']);
			foreach( $customScssArr as $customvariables ) {
				$scsscolor = GeneralUtility::trimExplode(':', $customvariables);
				if ( GeneralUtility::isFirstPartOfStr($scsscolor[1], '$')
				 && GeneralUtility::inList($defaultUtilColorsList, $scsscolor[0]) ) {
					$utilColors[$scsscolor[0]] = $scsscolor[1];
				} elseif (GeneralUtility::isFirstPartOfStr($scsscolor[1], '#')) {
					if (GeneralUtility::isFirstPartOfStr($scsscolor[0], '$')) {
						$colors[$scsscolor[0]] = $scsscolor[1];
					}
				}
			}
			if (is_array($utilColors)) {
				foreach($utilColors as $key=>$utiColor) {
					if ( GeneralUtility::isFirstPartOfStr($utiColor, '$') ) {
						$utilityColors[$key] = $colors[$utiColor];
					}
				}
			}
		}

		$variablesSCSS = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/_variables.scss';
		$variablesSCSS = GeneralUtility::getFileAbsFileName($variablesSCSS);
		$variablesSCSS = GeneralUtility::getURL($variablesSCSS);
		$defaultUtilityColors = [];
		$defaultcolors = [];

		foreach ( GeneralUtility::trimExplode(';', $variablesSCSS) as $defaultVariables) {

			$defaultScssColor = GeneralUtility::trimExplode(':', $defaultVariables);
			if ($defaultScssColor[1] && GeneralUtility::inList($defaultUtilColorsList, trim($defaultScssColor[0]))) {
				if ( GeneralUtility::isFirstPartOfStr($defaultScssColor[1], '$')) {
					// variable has variable
				 	$defaultUtilColors[$defaultScssColor[0]] = trim(rtrim($defaultScssColor[1], '!default'));
				} elseif (GeneralUtility::isFirstPartOfStr($defaultScssColor[1], '#')) {
					if (GeneralUtility::isFirstPartOfStr($defaultScssColor[0], '$')) {
						$defaultcolors[$defaultScssColor[0]] = trim(rtrim($defaultScssColor[1], '!default'));
					}
				}
			}
		}

		if (is_array($defaultUtilColors)) {
			foreach($defaultUtilColors as $key=>$defaultUtiColor) {
				$defaultUtilityColors[$key] = $defaultcolors[$defaultUtiColor];
			}
		}

		if ( is_array($utilityColors) && is_array($colors) ) {
			$colorArr = array_merge($defaultUtilityColors, $defaultcolors, $utilityColors, $colors);
			ksort($colorArr);
			return $colorArr;
		} else {
			return [];
		}
	}


	/**
	 * Mark the Upgrade Wizard as undone
	 *
	 * @return void
	 */
	protected function wizardsMarkedUndone(): void
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
		$statements = $queryBuilder
				 ->select('uid')
				 ->from('tx_t3sbootstrap_domain_model_config')
				 ->execute()
				 ->fetchAll();

		foreach ($statements as $statement) {
			$recordId = (int)$statement['uid'];
			$queryBuilder
				  ->update('tx_t3sbootstrap_domain_model_config')
				  ->where(
					 $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
				  )
				  ->set('updated', 0)
				  ->set('gridupdated', 0)

				  ->execute();
		}
	}


	/**
	 * Returns some default settings for new root configuration
	 *
	* @param \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	* @return \T3SBS\T3sbootstrap\Domain\Model\Config $newConfig
	 */
	protected function setDefaults($newConfig): \T3SBS\T3sbootstrap\Domain\Model\Config
	{
		$newConfig->setHomepageUid($this->currentUid);
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
		$newConfig->setJumbotronCarouselInterval(5000);
		$newConfig->setJumbotronCarouselPause(0);
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
		$newConfig->setCompress( 1 );
		$newConfig->setDisablePrefixComment(1);
		$newConfig->setGlobalPaddingTop( 'pt-5' );
		$newConfig->setLoadingSpinnerColor( 'primary' );
		$newConfig->setLightboxSelection(1);
		$newConfig->setShrinkingNavPadding( '5' );
		$newConfig->setSidebarMenuPosition( 'above' );
		$newConfig->setLangMenuWithFaIcon( 1 );
		$newConfig->setDateFormat( 'd.m.Y' );
		$newConfig->setSubheaderColor( 'secondary' );
		$newConfig->setFaLinkIcons( 1 );
		$newConfig->setSectionmenuAnchorOffset(29);
		$newConfig->setSectionmenuScrollspyOffset(130);
		$newConfig->setUpdated(1);
		$newConfig->setGridupdated(1);
		$newConfig->setNavbarLangFlags(1);

		return $newConfig;
	}


	/**
	 * Returns the currently configured "site" if a site is configured (= resolved) in the current request.
	 *
	 * @return SiteInterface
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
