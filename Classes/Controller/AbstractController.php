<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use T3SBS\T3sbootstrap\Domain\Model\Config;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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
    protected $rootTemplates;
    protected $persistenceManager;
    protected $countRootTemplates;
    protected $baseDir;


    /**
     * init all actions
     */
    public function initializeAction(): void
    {
        $site = $this->request->getAttribute('site');
        $this->rootPageId = $site->getRootPageId();
        $this->currentUid = (int) $this->request->getQueryParams()['id'];
        $this->isSiteroot = $this->rootPageId === $this->currentUid ? true : false;
        $this->tcaColumns = $GLOBALS['TCA']['tx_t3sbootstrap_domain_model_config']['columns'];
        $this->isAdmin = $GLOBALS['BE_USER']->isAdmin();
        $this->configRepository = GeneralUtility::makeInstance(ConfigRepository::class);
        $this->rootConfig = $this->configRepository->findOneBy(['pid' => $this->rootPageId]);
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');
        $this->countRootTemplates = $queryBuilder
            ->count('uid')
            ->from('sys_template')
            ->where(
                $queryBuilder->expr()->eq('root', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT))
            )
            ->executeQuery()->fetchOne();
        if (empty($this->settings['sitepackage'])) {
            $this->baseDir = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/');
        } else {
            if (ExtensionManagementUtility::isLoaded('t3sb_package')) {
                $this->baseDir = GeneralUtility::getFileAbsFileName("EXT:t3sb_package/");
            } else {
                throw new \InvalidArgumentException('Your t3sb_package is not loaded!', 1657464787);
            }
        }

    }


    /**
     * SCSS in the BE
     */
    protected function getCustomScss(string $file): array
    {
        $assignedOptions = [];

        if (!empty($this->settings['sitepackage'])) {
            $customScssDir = 'EXT:t3sb_package/Resources/Public/T3SB-SCSS/';
        } else {
            $customScssDir = 'fileadmin/T3SB/Resources/Public/T3SB-SCSS/';
        }
        $customScssFilePath = GeneralUtility::getFileAbsFileName($customScssDir);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $result = $queryBuilder
             ->select('*')
             ->from('pages')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', 0),
                 $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT))
             )
             ->executeQuery();

        $siteroots = $result->fetchAllAssociative();
        $customScssFileName = '';

        foreach ($siteroots as $key=>$siteroot) {
            if ($siteroot['uid'] == $this->currentUid) {
                if ($key === 0) {
                    $customScssFileName = $file.'.scss';
                } else {
                    $customScssFileName = $file.'-'.$siteroot['uid'].'.scss';
                }
            }
        }

        $customScssFile = $customScssFilePath.$customScssFileName;

        if (file_exists($customScssFile)) {
            if ($file == 'custom') {
                $assignedOptions['customScss'] = true;
            }
            $arguments = $this->request->getArguments();
            if (!empty($arguments['t3sbootstrap'][$file])) {
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
                    $assignedOptions[$file] = fread($handle, filesize($customScssFile));
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
        foreach ($this->tcaColumns as $field=>$columns) {
            // is select-field
            if ($columns['config']['type'] == 'select' && $columns['config']['renderType'] == 'selectSingle') {
                $var = GeneralUtility::underscoredToLowerCamelCase($field).'Options';
                foreach ($columns['config']['items'] as $key=>$entry) {
                    $option = new \stdClass();
                    $option->key = $entry['value'];
                    $option->value = $entry['label'];
                    $fieldsOptions[$var][$entry['value']] = $option;
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
        foreach ($this->tcaColumns as $field=>$columns) {
            $var = GeneralUtility::underscoredToUpperCamelCase($field);
            $set = 'set'.$var;
            $get = 'get'.$var;
            $newConfig->$set($rootConfig->$get());
        }
		$this->configRepository->add($newConfig);

        return $newConfig;
    }


    /**
     * compare config with rootconfig
     */
    protected function compareConfig(Config $config): array
    {
        $compare = [];

        foreach ($this->tcaColumns as $field=>$columns) {
            $filedsArr[] = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
        }
        foreach ($filedsArr as $field) {
            if ($config->$field() !== $this->rootConfig->$field()) {
                $key = lcfirst(substr($field, 3));
                $rootConfigField = $this->rootConfig->$field();
                if ($rootConfigField === true) {
                    $compare[$key] = 'enabled';
                } elseif ($rootConfigField === false) {
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

        foreach ($this->tcaColumns as $field=>$columns) {
            $filedsArr[$field] = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
        }

        foreach ($filedsArr as $fKey=>$field) {
            $fKey = GeneralUtility::underscoredToLowerCamelCase($fKey);
            if (!empty($ts['page.']['10.']['settings.']['config.'][$fKey])) {
                $tsField = $ts['page.']['10.']['settings.']['config.'][$fKey];
                if ($tsField != $this->rootConfig->$field() &&	str_starts_with((string)$tsField, '{$bootstrap.config.') != true) {
                    if ($this->rootConfig->$field() === true) {
                        $override[$fKey] = 'enabled';
                    } elseif ($this->rootConfig->$field() === false) {
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
        if ($this->countRootTemplates) {
            $configRepository = $this->configRepository->findOneBy(['pid' => $this->rootPageId]);
            $navbarBreakpoint = $configRepository->getNavbarBreakpoint();
            $breakpointWidth = $navbarBreakpoint == 'no' ? '' : $this->settings['breakpoint'][$navbarBreakpoint];
            $siteroots = [];
            $filecontent = '';
            foreach ($this->configRepository->findAll() as $config) {
                $page = GeneralUtility::makeInstance(PageRepository::class)->getPage($config->getPid());
	            if (!empty($page['uid']) && $page['hidden'] === 0 && $page['deleted'] === 0) {
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
            foreach ($this->objectToArr($model) as $key=>$value) {
                if (!str_starts_with($key, '_')) {
                    $setup .= 'page.10.settings.config.'.$key.' = {$bootstrap.config.'.$key.'}'.PHP_EOL;
                }
            }
            $setup .= 'page.10.settings.config.navbarBreakpointWidth = {$bootstrap.config.navbarBreakpointWidth}'.PHP_EOL;

            // outsourced constants
            foreach ($configurations as $config) {
                if ($config->getPid() == $config->getHomepageUid()) {
                    // is root page
                    if (count($siteroots) === 1) {
                        $filecontent .= self::getConstants($config, true);
                        $filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
                    } else {
                        $filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
                        $filecontent .= self::getConstants($config, true);
                        $filecontent .= 'bootstrap.config.navbarBreakpointWidth = '.$breakpointWidth.PHP_EOL;
                        $filecontent .= '[END]'.PHP_EOL.PHP_EOL;
                    }
                } else {
                    if ($config->getGeneralRootline()) {
                        $filecontent .= '['.$config->getPid().' in tree.rootLineIds]'.PHP_EOL;
                    } else {
                        $filecontent .= '[traverse(page, "uid") == '.$config->getPid().']'.PHP_EOL;
                    }
                    if ($config->getGeneralOverride()) {
                        $filecontent .= self::getConstants($config, true);
                    } else {
                        $filecontent .= self::getConstants($config, false);
                    }
                    $filecontent .= '[END]'.PHP_EOL.PHP_EOL;
                }
            }
            $customPath = $this->baseDir.'Configuration/TypoScript/';
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

        foreach ($this->tcaColumns as $field=>$columns) {
            $field = GeneralUtility::underscoredToLowerCamelCase($field);
            $var = str_replace(' ', '_', $field);
            $getField = 'get'.GeneralUtility::underscoredToUpperCamelCase($field);
            $value = $config->$getField() == '' ? 0 : $config->$getField();
            if ($var == 'jumbotronCarouselPause' && $value == 1) {
                $value = 'hover';
            } elseif ($var == 'jumbotronCarouselPause' && $value == 0) {
                $value = '';
            }
            if ($isRoot) {
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
        foreach ($this->tcaColumns as $key=>$column) {
            if (!empty($column['accordion_id'])) {
                if (!empty($column['accordion_sub'])) {
                    $sub = $column['accordion_sub'];
                    $tca[$column['accordion_id']][$sub][$key] = $column;
                    $tca[$column['accordion_id']][$sub][$key]['property'] = GeneralUtility::underscoredToLowerCamelCase($key);
                    $tca[$column['accordion_id']][$sub][$key]['type'] = ucfirst($column['config']['type']);
                    $tca[$column['accordion_id']][$sub][$key]['noSub'] = false;
                } else {
                    $tca[$column['accordion_id']][$key] = $column;
                    $tca[$column['accordion_id']][$key]['property'] = GeneralUtility::underscoredToLowerCamelCase($key);
                    $tca[$column['accordion_id']][$key]['type'] = ucfirst($column['config']['type']);
                    $tca[$column['accordion_id']][$key]['noSub'] = true;
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

        if (!empty($customScss['custom-variables']) && str_starts_with($custom_variables, $default) == false) {
            foreach (GeneralUtility::trimExplode(';', $customScss['custom-variables']) as $customvariables) {
                $scsscolor = GeneralUtility::trimExplode(':', $customvariables);
                if (str_starts_with((string)$customvariables, '$') && GeneralUtility::inList($defaultUtilColorsList, $scsscolor[0])) {
                    $scsscolor = GeneralUtility::trimExplode(':', $customvariables);
                    $customScssArr[$scsscolor[0]] = $scsscolor[1];
                }
            }
            foreach ($customScssArr as $key=>$customvariables) {
                if (str_starts_with((string)$customvariables, '$')) {
                    if (!empty($customScssArr[$customvariables]) && $customScssArr[$customvariables]) {
                        $utilityColors[$key] = $customScssArr[$customvariables];
                    }
                } elseif (str_starts_with((string)$customvariables, '#')) {
                    if ($customvariables) {
                        $utilityColors[$key] = $customvariables;
                    }
                }
            }
        }

        if (empty($this->settings['sitepackage'])) {
            $variablesSCSS = 'fileadmin/T3SB/Resources/Public/Contrib/Bootstrap/scss/_variables.scss';
        } else {
            $variablesSCSS = 'EXT:t3sb_package/Resources/Public/Contrib/Bootstrap/scss/_variables.scss';
        }

        $variablesSCSS = GeneralUtility::getFileAbsFileName($variablesSCSS);
        $variablesSCSS = GeneralUtility::getURL($variablesSCSS);

        if (!empty($variablesSCSS)) {
            foreach (GeneralUtility::trimExplode(';', $variablesSCSS) as $defaultVariables) {
                $defaultScssColor = GeneralUtility::trimExplode(':', $defaultVariables);
                if (str_starts_with((string)$defaultVariables, '$') && GeneralUtility::inList($defaultUtilColorsList, $defaultScssColor[0])
                     && (str_starts_with((string)$defaultScssColor[1], '$') || str_starts_with((string)$defaultScssColor[1], '#'))) {
                    $scsscolor = GeneralUtility::trimExplode(':', $defaultVariables);
                    $defaultUtilColors[$scsscolor[0]] = substr($scsscolor[1], 0, -9);
                }
            }
            foreach ($defaultUtilColors as $key=>$customvariables) {
                if (str_starts_with((string)$customvariables, '$')) {
                    if (!empty($customScssArr[$customvariables]) && $customScssArr[$customvariables]) {
                        $defaultUtilityColors[$key] = $customScssArr[$customvariables];
                    }
                } elseif (str_starts_with((string)$customvariables, '#')) {
                    if ($customvariables) {
                        $defaultUtilityColors[$key] = $customvariables;
                    }
                }
            }
        }
        if (is_array($utilityColors) && is_array($defaultUtilityColors)) {
            if (!empty($utilityColors)) {
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
        $newConfig->setPageTitle('jumbotron');
        $newConfig->setPageTitlealign('center');
        $newConfig->setNavbarImage($defaultNavbarImagePath);
        $newConfig->setNavbarEnable('light');
        $newConfig->setNavbarLevels(4);
        $newConfig->setNavbarbrandAlignment('left');
        $newConfig->setNavbarColor('warning');
        $newConfig->setNavbarAlignment('left');
        $newConfig->setNavbarBrand('imgText');
        $newConfig->setNavbarContainer('inside');
        $newConfig->setNavbarClass('');
        $newConfig->setJumbotronEnable(1);
        $newConfig->setJumbotronSlide(0);
        $newConfig->setJumbotronPosition('below');
        $newConfig->setJumbotronContainer('container');
        $newConfig->setJumbotronContainerposition('Inside');
        $newConfig->setJumbotronCarouselInterval(5000);
        $newConfig->setJumbotronCarouselPause(0);
        $newConfig->setJumbotronClass('p-5 mb-4 bg-light rounded-0');
        $newConfig->setBreadcrumbEnable(1);
        $newConfig->setBreadcrumbCorner(1);
        $newConfig->setBreadcrumbPosition('belowJum');
        $newConfig->setBreadcrumbContainer('container');
        $newConfig->setSidebarLevels(4);
        $newConfig->setFooterEnable(1);
        $newConfig->setFooterSlide(0);
        $newConfig->setFooterContainer('container');
        $newConfig->setFooterSticky(1);
        $newConfig->setFooterContainerposition('inside');
        $newConfig->setFooterClass('bg-dark text-light py-4');
        $newConfig->setStickyFooterExtraPadding(100);
        $newConfig->setCompress(1);
        $newConfig->setDisablePrefixComment(1);
        $newConfig->setGlobalPaddingTop('pt-5');
        $newConfig->setLoadingSpinnerColor('primary');
        $newConfig->setLightboxSelection(1);
        $newConfig->setShrinkingNavPadding('5');
        $newConfig->setSidebarMenuPosition('above');
        $newConfig->setLangMenuWithFaIcon(1);
        $newConfig->setDateFormat('d.m.Y');
        $newConfig->setSubheaderColor('secondary');
        $newConfig->setSectionmenuAnchorOffset(29);
        $newConfig->setSectionmenuScrollspy(1);
        $newConfig->setNavbarLangFlags(1);
        $newConfig->setSectionmenuScrollspyThreshold('0.1, 0.5, 1');
        $newConfig->setSectionmenuScrollspyRootMargin('0px 0px -75%');

        return $newConfig;
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
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0),
                    QueryHelper::stripLogicalOperatorPrefix($permsClause)
                )
                ->executeQuery();
            while ($row = $statement->fetchAssociative()) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }

                if ($depth > 1) {
                    $theSubList = self::getTreeList($row['uid'], $depth - 1, $begin - 1, $permsClause);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                        $theList .= ',';
                    }
                    $theList .= $theSubList;
                }
            }
        }

        return (string)$theList;
    }


    protected function setDefaultBackendLayout() {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder
            ->update('pages')
            ->where(
                $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('backend_layout', $queryBuilder->createNamedParameter('', Connection::PARAM_STR)),
                $queryBuilder->expr()->eq('backend_layout_next_level', $queryBuilder->createNamedParameter('', Connection::PARAM_STR))
            )
            ->set('backend_layout', 'pagets__OneCol')
            ->set('backend_layout_next_level', 'pagets__OneCol')
            ->executeStatement();
    }

}
