<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use T3SBS\T3sbootstrap\Domain\Repository\ConfigRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
 
#[AsController]
abstract class AbstractController extends ActionController
{

    protected ?SiteInterface $site = null;
    protected ?ConfigRepository $configRepository= null;
    protected bool $isSiteroot = false;
    protected int $rootPageId = 0;
    protected int $currentUid = 0;
    protected bool $isAdmin = false;
    protected mixed $rootConfig = null;
    protected string $baseDir = '';
    protected array $currentPage = [];
    protected int $doktype = 0;
    protected bool $hasSet = false;
    protected mixed $sysTemplate = null;


    public function initializeAction(): void
    { 
        if (!empty($this->request->getArgument('id') ?? 0)) {
            $this->site = $this->request->getAttribute('site');
            $this->rootPageId = $this->site->getRootPageId();
            $this->currentUid = !empty($this->request->getQueryParams()['id']) ? (int) $this->request->getQueryParams()['id'] : 0;
            $this->currentPage = BackendUtility::getRecord('pages', $this->currentUid, 'uid, is_siteroot, doktype, title');
            $this->doktype = $this->currentPage['doktype'];
            $this->isSiteroot = (bool) $this->currentPage['is_siteroot'];
            $this->isAdmin = $GLOBALS['BE_USER']->isAdmin();
            $this->configRepository = GeneralUtility::makeInstance(ConfigRepository::class);
            $this->rootConfig = $this->configRepository->findOneBy(['pid' => $this->rootPageId]);
            $this->baseDir = GeneralUtility::getFileAbsFileName("EXT:t3sb_package/Configuration/");
            if ($this->currentPage['uid'] === $this->rootPageId) {
                $this->hasSet = true;
            } else {
                $this->hasSet = false;
            }
        }
    }


    public function getNotifications(array $assignedOptions): array 
    {
        $notification = [];

        if ($this->doktype > 1) {
            $notification['doktype']['title'] = LocalizationUtility::translate('notificationtitle_1','t3sbootstrap').' '. $this->doktype;
            $notification['doktype']['message'] = LocalizationUtility::translate('notificationmessage_1','t3sbootstrap');
        }

        if (empty($this->hasSet)) {
            $notification['siteset']['title'] = LocalizationUtility::translate('notificationtitle_2','t3sbootstrap');
            $notification['siteset']['message'] = LocalizationUtility::translate('notificationmessage_2','t3sbootstrap');
        }

        if (empty($this->isSiteroot)) {
            $notification['siteroot']['title'] = LocalizationUtility::translate('notificationtitle_3','t3sbootstrap');
            $notification['siteroot']['message'] = LocalizationUtility::translate('notificationmessage_3','t3sbootstrap');
        }

        if ($this->currentUid) {
            $hiddenPage = BackendUtility::getRecord('pages', $this->currentUid, 'hidden, deleted');
            if (in_array(1, $hiddenPage, true)) {
                $notification['hidden']['title'] = LocalizationUtility::translate('notificationtitle_4','t3sbootstrap');
                $notification['hidden']['message'] = LocalizationUtility::translate('notificationmessage_4','t3sbootstrap');
            }
        }

        if (!empty($this->site)) {
            if (!empty($this->settings['cdn']) && $this->settings['cdn']['enable'] && $this->settings['customScss']) {
                $notification['hidden']['title'] = LocalizationUtility::translate('notificationtitle_5','t3sbootstrap');
                $notification['hidden']['message'] = LocalizationUtility::translate('notificationmessage_5','t3sbootstrap');
            }
        }

        if (empty($this->request->getArgument('id') ?? 0)) {
            $notification['idNull']['title'] = LocalizationUtility::translate('notificationtitle_6','t3sbootstrap');
            $notification['idNull']['message'] = LocalizationUtility::translate('notificationmessage_6','t3sbootstrap');
        }

        return $notification;
    }

}
