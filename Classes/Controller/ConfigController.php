<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use T3SBS\T3sbootstrap\Domain\Model\Config;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Service\CacheService;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
final class ConfigController extends AbstractController
{
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {
    }

    /**
       * Init all actions.
       */
    public function initializeAction(): void
    {
        parent::initializeAction();
    }

    /**
     * action list
     */
    public function listAction(
        bool $deleted = false,
        bool $created = false,
        bool $updateSss = false
    ): ResponseInterface
    {
        $cdnHint = false;
        $file = $this->baseDir.'Resources/Public/Contrib/Bootstrap/scss/bootstrap.scss';
        if (file_exists($file) && $this->settings['cdn']['enable']) {
            $cdnHint = true;
        }

        $assignedOptions['idNull'] = FALSE;
        if ($this->request->getQueryParams()['id'] === '0') {
            $assignedOptions['idNull'] = TRUE;
        }
    
        if ($this->isSiteroot && $this->rootPageId) {
            $pidList = parent::getTreeList($this->rootPageId, 999999, 0, '1');
            $allConfig = [];
            foreach ($this->configRepository->findAll() as $config) {
                if (GeneralUtility::inList($pidList, $config->getPid())) {
                    $page = BackendUtility::getRecord('pages', $config->getPid(), 'uid,title');
                    $allConfig[$page['uid']]['confUid'] = $config->getUid();
                    $allConfig[$page['uid']]['title'] = $page['title'];
                    $allConfig[$page['uid']]['uid'] = $page['uid'];
                    $assignedOptions['compress'] = $config->getCompress();
                }
            }
            $assignedOptions['isSiteroot'] = true;
            $assignedOptions['allConfig'] = $allConfig;
        }

        $assignedOptions['rootTemplate'] = true;
        if ($this->countRootTemplates === 0) {
            $assignedOptions['rootTemplate'] = false;
        }
        $assignedOptions['rootConfig'] = $this->rootConfig ? true : false;
        $assignedOptions['config'] = $this->configRepository->findOneBy(['pid' => $this->currentUid]);
        $assignedOptions['admin'] = $this->isAdmin;
        $assignedOptions['customScss'] = false;
        $assignedOptions['action'] = 'list';
        $assignedOptions['updateScss'] = $updateSss;
        $assignedOptions['deleted'] = $deleted;
        $assignedOptions['created'] = $created;
        $assignedOptions['cdnHint'] = $cdnHint;
		$assignedOptions['settings'] = $this->settings;

        if (!empty($this->settings['customScss']) && (int)$this->settings['customScss'] === 1) {
            $customScss = parent::getCustomScss('custom-variables');
            $assignedOptions['custom-variables'] = !empty($customScss['custom-variables']) ? $customScss['custom-variables'] : '';
            $customScss = parent::getCustomScss('custom');
            $assignedOptions['custom'] = !empty($customScss['custom']) ? $customScss['custom'] : '';
            $assignedOptions['customScss'] = !empty($customScss['customScss']) ? $customScss['customScss'] : '';
            if (!empty($this->settings['enableUtilityColors'])) {
                $assignedOptions['utilColors'] = parent::getUtilityColors();
            }
        }

        if (!empty($this->settings['pages']['override'])) {
            foreach ($this->settings['pages']['override'] as $field=>$override) {
                if (!empty($override)) {
                    $assignedOptions['pagesOverride'][$field] = $override;
                }
            }
        }

        $new_raster = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/Resources/Public/Images/raster.png');
        if ( !file_exists($new_raster) ) {
            $folder = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/Resources/Public/Images/');
            if (!is_dir($folder)) {
                if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
                }
            }
            $orig_raster = GeneralUtility::getFileAbsFileName('EXT:t3sbootstrap/Resources/Public/Images/raster.png');
            copy($orig_raster, $new_raster);
        }

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple($assignedOptions);
        return $moduleTemplate->renderResponse('Config/List');
    }


    /**
     * action new
     */
    public function newAction(): ResponseInterface
    {
        $assignedOptions = parent::getFieldsOptions();
        $assignedOptions['pid'] = $this->currentUid;
        $assignedOptions['tcaColumns'] = parent::getTcaColumns();
        $assignedOptions['admin'] = $this->isAdmin;

        if ($this->rootConfig) {
            // config from rootline
            if ($this->rootConfig->getGeneralRootline()) {
                $rootLineArray = GeneralUtility::makeInstance(RootlineUtility::class, $this->currentUid)->get();
                // unset current page
                if (count($rootLineArray) > 1) {
                    unset($rootLineArray[count($rootLineArray)-1]);
                }
                foreach ($rootLineArray as $rootline) {
                    $rootlineConfig = $this->configRepository->findOneBy(['pid' => (int)$rootline['uid']]);
                    if (!empty($rootlineConfig)) {
                        break;
                    }
                }
                $assignedOptions['newConfig'] = parent::getNewConfig($rootlineConfig);
            // config from rootpage
            } else {
                $assignedOptions['newConfig'] = parent::getNewConfig($this->rootConfig);
            }
        } else {
			return $this->redirect('create');
        }

		return $this->redirect('list', null, null, ['created' => true]);
    }


    /**
     * action create
     */
    public function createAction(Config $newConfig = null): ResponseInterface
    {
		if (empty($newConfig)) {
            $newConfig = new Config();
		}
        $newConfig->setHomepageUid($this->rootPageId);
        $newConfig->setPid($this->currentUid);
        $newConfig = parent::setDefaults($newConfig);
        $this->configRepository->add($newConfig);
        parent::setDefaultBackendLayout();
        parent::writeConstants();
        return $this->redirect('list', null, null, ['created' => true]);
    }


    /**
     * action edit
     */
    public function editAction(Config $config, bool $updated = false): ResponseInterface
    {
        $assignedOptions = parent::getFieldsOptions();
        $assignedOptions['config'] = $config;
        $assignedOptions['pid'] = $this->currentUid;
        $assignedOptions['admin'] = $this->isAdmin;
        $assignedOptions['isSiteroot'] = $this->isSiteroot;
        $assignedOptions['updated'] = $updated;
        $assignedOptions['override'] = parent::overrideConfig();
        $assignedOptions['tcaColumns'] = parent::getTcaColumns();
		$assignedOptions['settings'] = $this->settings;
        $assignedOptions['action'] = 'edit';
        if (!$this->isSiteroot) {
            $assignedOptions['compare'] = parent::compareConfig($config);
        }

        if ($updated) {
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $notificationQueue = $flashMessageService->getMessageQueueByIdentifier(FlashMessageQueue::NOTIFICATION_QUEUE);
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                'The configuration was successfully updated.',
                'Record saved',
                ContextualFeedbackSeverity::OK,
            );
            $notificationQueue->enqueue($flashMessage);
        }

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple($assignedOptions);
        return $moduleTemplate->renderResponse('Config/Edit');
    }


    /**
     * action update
     */
    public function updateAction(Config $config): ResponseInterface
    {
        $config->setHomepageUid($this->rootPageId);
        $this->configRepository->update($config);
		$this->persistenceManager->persistAll();
        parent::writeConstants();
		if (!empty($this->settings['clearPageCache'])) {
	        $cacheService = GeneralUtility::makeInstance(CacheService::class);
	        $cacheService->clearPageCache();
		}

        return $this->redirect('edit', null, null, ['config' => $config, 'updated' => true]);
    }


    /**
     * action delete
     */
    public function deleteAction(Config $config): ResponseInterface
    {
        $this->configRepository->remove($config);
        parent::writeConstants();
        return $this->redirect('list', null, null, array('deleted' => true));
    }


    /**
     * action dashboard
     */
    public function dashboardAction(): ResponseInterface
    {
        if ($this->isSiteroot) {
            $assignedOptions['extconf'] = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
        }

        $assignedOptions['action'] = 'dashboard';
        $assignedOptions['isSiteroot'] = $this->isSiteroot;
        $assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['settings'] = $this->settings;

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple($assignedOptions);
        return $moduleTemplate->renderResponse('Config/Dashboard');
    }


    /**
     * action constants
     */
    public function constantsAction(): ResponseInterface
    {
        if ($this->isSiteroot) {
            $constantPath = $this->baseDir.'Configuration/TypoScript/t3sbconstants.typoscript';
            if (file_exists($constantPath)) {
                $fileGetContents = @file_get_contents($constantPath);
                $outsourcedConstantsArr = explode('[END]', trim($fileGetContents));
                $toEnd = count($outsourcedConstantsArr);
                $filecontent = '';
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

        $assignedOptions['action'] = 'constants';
        $assignedOptions['isSiteroot'] = $this->isSiteroot;
        $assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['settings'] = $this->settings;

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple($assignedOptions);
        return $moduleTemplate->renderResponse('Config/Constants');

    }
}
