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


    /**
     * init all actions
     */
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
            $notification['doktype']['title'] = 'Doktype ' . $this->doktype;
            $notification['doktype']['message'] = 'T3S Bootstrap Configurations are only provided for a page (doktype=1) checked "Use as Root Page"!';
        }

        if (empty($this->hasSet)) {
            $notification['siteset']['title'] = 'Page without a site configuration';
            $notification['siteset']['message'] = 'You need to create a site configuration in order to edit your configuration.';
        }

        if (empty($this->isSiteroot)) {
            $notification['siteroot']['title'] = 'Page is no "Root Page"';
            $notification['siteroot']['message'] = 'Page must have the “is_siteroot” flag set!';
        }

        if ($this->currentUid) {
            $hiddenPage = BackendUtility::getRecord('pages', $this->currentUid, 'hidden, deleted');
            if (in_array(1, $hiddenPage, true)) {
                $notification['hidden']['title'] = 'Page is hidden';
                $notification['hidden']['message'] = 'You cannot apply any configuration to a hidden page!';
            }
        }

        if (!empty($this->site)) {
            if (!empty($this->settings['cdn']) && $this->settings['cdn']['enable'] && $this->settings['customScss']) {
                $notification['hidden']['title'] = '"CDN" & "Custom SCSS" are activated.';
                $notification['hidden']['message'] = 'In this case, there is a fallback and the required files are loaded by CDN. 
                The Configuration is disabled!';
            }
        }

        if (empty($this->request->getArgument('id') ?? 0)) {
            $notification['idNull']['title'] = 'Page has id=0';
            $notification['idNull']['message'] = 'You cannot apply any configuration to a page with id=0';
        }

        return $notification;
    }

}
