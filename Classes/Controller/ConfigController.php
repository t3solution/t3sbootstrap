<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use T3SBS\T3sbootstrap\Domain\Model\Config;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
final class ConfigController extends AbstractController
{

	public function __construct(
		private ModuleTemplateFactory $moduleTemplateFactory
	)
	{
	}

	 /**
		* Init all actions.
		*/
	 public function initializeAction()
	 {
		 parent::initializeAction();
	 }

	/**
	 * action list
	 */
	public function listAction(bool $deleted = FALSE, bool $created = FALSE, bool $updateSss = FALSE): ResponseInterface
	{
		if ( $this->isSiteroot && $this->rootPageId ) {
			$pidList = parent::getTreeList($this->rootPageId, 999999, 0, '1');
			$allConfig = [];
			foreach ( $this->configRepository->findAll() as $config ) {
				if (GeneralUtility::inList($pidList, $config->getPid())) {
					$page = BackendUtility::getRecord('pages',$config->getPid(),'uid,title');
					$allConfig[$page['uid']]['confUid'] = $config->getUid();
					$allConfig[$page['uid']]['title'] = $page['title'];
					$allConfig[$page['uid']]['uid'] = $page['uid'];
					$assignedOptions['compress'] = $config->getCompress();
				}
			}
			$assignedOptions['isSiteroot'] = TRUE;
			$assignedOptions['allConfig'] = $allConfig;
		}

		$assignedOptions['rootTemplate'] = TRUE;
		if (count($this->rootTemplates) === 0) {
			$assignedOptions['rootTemplate'] = FALSE;
		}
		$assignedOptions['rootConfig'] = $this->rootConfig ? TRUE : FALSE;
		$assignedOptions['config'] = $this->configRepository->findOneByPid($this->currentUid);
		$assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['customScss'] = FALSE;
		$assignedOptions['scss'] = '';
		$assignedOptions['action'] = 'list';
		$assignedOptions['updateScss'] = $updateSss;
		$assignedOptions['deleted'] = $deleted;
		$assignedOptions['created'] = $created;

		if ( !empty($this->settings['customScss']) && (int)$this->settings['customScss'] === 1 ) {
			$customScss = parent::getCustomScss('custom-variables');
			$assignedOptions['custom-variables'] = !empty($customScss['custom-variables']) ? $customScss['custom-variables'] : '';
			$customScss = parent::getCustomScss('custom');
			$assignedOptions['custom'] = !empty($customScss['custom']) ? $customScss['custom'] : '';
			$assignedOptions['customScss'] = !empty($customScss['customScss']) ? $customScss['customScss'] : '';
			if ( !empty($this->settings['enableUtilityColors']) ) {
				$assignedOptions['utilColors'] = parent::getUtilityColors();
			}
		}

		if ( !empty($this->settings['pages']['override']) ) {
			foreach ($this->settings['pages']['override'] as $field=>$override) {
				if (!empty($override)) {
					$assignedOptions['pagesOverride'][$field] = $override;
				}
			}
		}

		$assignedOptions['webpIsLoaded'] = false;
		if ( ExtensionManagementUtility::isLoaded('webp') ) {
			$assignedOptions['webpIsLoaded'] = true;
		}

		$this->view->assignMultiple($assignedOptions);
		$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$moduleTemplate->setContent($this->view->render());
		return $this->htmlResponse($moduleTemplate->renderContent());
	}


	/**
	 * action new
	 *
	 * @return void
	 */
	public function newAction(): ResponseInterface
	{
		$assignedOptions = parent::getFieldsOptions();
		$assignedOptions['pid'] = $this->currentUid;
		$assignedOptions['tcaColumns'] = parent::getTcaColumns();

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
				$assignedOptions['newConfig'] = parent::getNewConfig($rootlineConfig);
			// config from rootpage
			} else {
				$assignedOptions['newConfig'] = parent::getNewConfig($this->rootConfig);
			}

		} else {
			$newConfig = new Config();
			// some defaults
			$newConfig = parent::setDefaults($newConfig);
			$assignedOptions['newConfig'] = $newConfig;
		}

		$this->view->assignMultiple($assignedOptions);
		$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$moduleTemplate->setContent($this->view->render());
		return $this->htmlResponse($moduleTemplate->renderContent());
	}


	/**
	 * action create
	 */
	public function createAction(Config $newConfig): ResponseInterface
	{
		$newConfig->setHomepageUid($this->rootPageId);
		$newConfig->setPid($this->currentUid);
		$this->configRepository->add($newConfig);
		parent::writeConstants();
		return $this->redirect('list',NULL,Null,array('created' => TRUE));
	}


	/**
	 * action edit
	 */
	public function editAction(Config $config, bool $updated = FALSE): ResponseInterface
	{
		$assignedOptions = parent::getFieldsOptions();
		$assignedOptions['config'] = $config;
		$assignedOptions['pid'] = $this->currentUid;
		$assignedOptions['admin'] = $this->isAdmin;
		$assignedOptions['isSiteroot'] = $this->isSiteroot;
		$assignedOptions['updated'] = $updated;
		$assignedOptions['override'] = parent::overrideConfig();
		$assignedOptions['tcaColumns'] = parent::getTcaColumns();
		$assignedOptions['action'] = 'edit';
		if ( !$this->isSiteroot ) {
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

		$this->view->assignMultiple($assignedOptions);
		$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$moduleTemplate->setContent($this->view->render());
		return $this->htmlResponse($moduleTemplate->renderContent());
	}


	/**
	 * action update
	 */
	public function updateAction(Config $config): ResponseInterface
	{
		$config->setHomepageUid($this->rootPageId);
		$this->configRepository->update($config);
		parent::writeConstants();
		return $this->redirect('edit',NULL,Null,array('config' => $config, 'updated' => TRUE));
	}


	/**
	 * action delete
	 */
	public function deleteAction(Config $config): ResponseInterface
	{
		$this->configRepository->remove($config);
		parent::writeConstants();
		return $this->redirect('list',NULL,Null,array('deleted' => TRUE));
	}


	/**
	 * action dashboard
	 */
	public function dashboardAction(): ResponseInterface
	{
		if ( $this->isSiteroot ) {
			$assignedOptions['extconf'] = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		}

		$assignedOptions['action'] = 'dashboard';
		$assignedOptions['isSiteroot'] = $this->isSiteroot;
		$assignedOptions['admin'] = $this->isAdmin;

		$this->view->assignMultiple($assignedOptions);
		$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$moduleTemplate->setContent($this->view->render());
		return $this->htmlResponse($moduleTemplate->renderContent());
	}


	/**
	 * action constants
	 */
	public function constantsAction(): ResponseInterface
	{
		if ( $this->isSiteroot ) {
			$constantPath = GeneralUtility::getFileAbsFileName('fileadmin/T3SB/Configuration/TypoScript/t3sbconstants.typoscript');
			if ( file_exists($constantPath) ) {
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

		$this->view->assignMultiple($assignedOptions);
		$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$moduleTemplate->setContent($this->view->render());
		return $this->htmlResponse($moduleTemplate->renderContent());
	}

}
