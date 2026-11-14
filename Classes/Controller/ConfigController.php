<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use T3SBS\T3sbootstrap\Service\AssetPathService;
use T3SBS\T3sbootstrap\Service\ConfigTransferService;
use T3SBS\T3sbootstrap\Service\SystemTransferService;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder as BeUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;

#[AsController]
final class ConfigController extends AbstractController
{

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly IconFactory $iconFactory,
        private readonly BeUriBuilder $beUriBuilder,
        protected readonly ComponentFactory $componentFactory,
        private readonly AssetPathService $assetPathService,
        private readonly ConfigTransferService $configTransferService,
        private readonly SystemTransferService $systemTransferService,
    ) {
    }


    /**
     * Holds the system part of an uploaded export between the preview and the
     * confirmation, so the file does not have to be uploaded twice.
     */
    private const TRANSFER_SESSION_KEY = 't3sbootstrapTransfer';

    /**
     * Columns of the configuration record that can be edited, resolved once per request.
     *
     * @var string[]
     */
    private array $editableColumns = [];

    public const T3SBCONSTANTSPATH = 'TypoScript/t3sbconstants.typoscript';
    public const RASTERPATH = 'fileadmin/T3SB/Resources/Public/Images/raster.png';
    public const RASTERSOURCE = 'EXT:t3sbootstrap/Resources/Public/Images/raster.png';


    public function initializeAction(): void
    {
        parent::initializeAction();
    }


    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        if ($this->request->hasArgument('id') && !empty($this->request->getArgument('id'))) {

            $this->setUpDocHeader($moduleTemplate);
        }

        $assignedOptions = [];
        $assignedOptions['notifications'] = $this->getNotifications($assignedOptions);

        if (!empty($assignedOptions['notifications'])) {
            $moduleTemplate->assignMultiple($assignedOptions);
            return $moduleTemplate->renderResponse('Config/NotificationPage');
        }

        $assignedOptions['rootPageId'] = $this->rootPageId;
        $assignedOptions['isSiteroot'] = $this->isSiteroot;
        $assignedOptions['title'] = $this->currentPage['title'] ?? '';

        // Outsourced constants 
        $constantPath = $this->baseDir.self::T3SBCONSTANTSPATH;

        if (file_exists($constantPath)) {
            $fileGetContents = file_get_contents($constantPath);
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
            $assignedOptions['constantBlocks'] = $this->buildConstantBlocks($filecontent);
        }

        if (empty($this->settings['cdn']['enable']) && !empty($this->settings['bootswatch'])) {
            if ( !empty($this->settings['customScss'])) {
                $customVariablesFile = 'custom-variables-'.$this->rootPageId.'.scss';
                $customVariablesPath = $this->assetPathService->getScssPath().$customVariablesFile;
                if (!file_exists($customVariablesPath)) {
                    $assignedOptions['executeTask'] = true;
                }
            } else {
                $assignedOptions['customScssCdnDisabled'] = true;
            }
        }

        // Config transfer: export/import of this root page's configuration
        // only offer the pages this user is actually allowed to see
        $siteRootPages = array_values(array_filter(
            $this->configTransferService->getSiteRootPages(),
            fn(array $page): bool => $this->hasPageAccess((int)$page['uid'], Permission::PAGE_SHOW)
        ));
        $assignedOptions['transfer'] = [
            'pageOptions' => $this->buildPageOptions($siteRootPages),
            'currentPid' => $this->rootPageId,
            'modeOptions' => [
                ConfigTransferService::MODE_UPDATE => LocalizationUtility::translate('transfer.mode.update', 't3sbootstrap'),
                ConfigTransferService::MODE_REPLACE => LocalizationUtility::translate('transfer.mode.replace', 't3sbootstrap'),
                ConfigTransferService::MODE_ADD => LocalizationUtility::translate('transfer.mode.add', 't3sbootstrap'),
            ],
            // the system part writes settings.php and config/sites/, so it is offered to
            // admins only - not $this->isAdmin, which stays false without a usable "id"
            'admin' => (bool)$GLOBALS['BE_USER']->isAdmin(),
        ];

        // What the export would carry besides the record. Only for admins, who are the
        // only ones who get those parts anyway, and it reads every package's set
        // definitions - no reason to do that for an editor opening the module.
        if ($GLOBALS['BE_USER']->isAdmin()) {
            $environment = $this->systemTransferService->exportEnvironment();
            $sets = $this->systemTransferService->describeSetsForPage($this->rootPageId);
            $settings = $this->systemTransferService->getSettingsForPage($this->rootPageId);
            $configuration = $this->systemTransferService->getExportedExtensionConfiguration();
            $ownSets = array_filter($sets, static fn(array $set): bool => (bool)$set['own']);

            $assignedOptions['transfer']['inventory'] = [
                'typo3' => $environment['typo3'],
                'php' => $environment['php'],
                'extensions' => $environment['extensions'],
                'extensionCount' => count($environment['extensions']),
                'composer' => $this->systemTransferService->buildComposerRequire($environment['extensions']),
                'site' => $this->systemTransferService->getSiteIdentifierForPage($this->rootPageId),
                'sets' => $sets,
                'setCount' => count($sets),
                'ownSetCount' => count($ownSets),
                'settings' => $settings,
                'settingCount' => count($settings),
                'configuration' => $configuration,
                'configurationCount' => count($configuration),
                // QueryResult is countable, so this does not hydrate the records
                'recordCount' => (int)($this->configRepository?->findBy(['pid' => $this->rootPageId])?->count() ?? 0),
                'extensionConfigurationWritable' => $this->systemTransferService->isExtensionConfigurationWritable(),
            ];
        }

        // The description points at the record history for stepping back through single
        // changes - without a link that is a dead end, the module has no other way there.
        $assignedOptions['transfer']['historyUri'] = $this->buildHistoryUri();

        $assignedOptions['rootConfig'] = (bool)$this->rootConfig;
        $assignedOptions['config'] = $this->configRepository->findOneBy(['pid' => $this->currentUid]);
        $assignedOptions['admin'] = $this->isAdmin;
        $assignedOptions['settings'] = $this->settings;
        $assignedOptions['currentUid'] = $this->currentUid;

        if (!empty($this->settings['pages']['override'])) {
            foreach ($this->settings['pages']['override'] as $field=>$override) {
                if (!empty($override)) {
                    $assignedOptions['pagesOverride'][$field] = $override;
                }
            }
        }

        $rasterPath = !empty($this->settings['rasterPath']) ? $this->settings['rasterPath'] : self::RASTERPATH;

        $new_raster = GeneralUtility::getFileAbsFileName($rasterPath);
        if ( !file_exists($new_raster) ) {
            $folder = dirname($new_raster);
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            $orig_raster = GeneralUtility::getFileAbsFileName(self::RASTERSOURCE);
            copy($orig_raster, $new_raster);
        }
        
        $moduleTemplate->assignMultiple($assignedOptions);
        return $moduleTemplate->renderResponse('Config/List');
    }


    private function setUpDocHeader(ModuleTemplate $moduleTemplate): void {
        $config = $this->configRepository?->findOneBy(['pid' => $this->currentUid]);
        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $returnUrl = $this->beUriBuilder->buildUriFromRequest($this->request, ['id' => $this->currentUid]);
        
        // Edit page
        $editPageUri = $this->beUriBuilder->buildUriFromRoutePath(
            '/record/edit',
            [
                'edit' => [
                    'pages' => [
                        $this->currentUid => 'edit',
                    ],
                ],
                'returnUrl' => (string)$returnUrl
            ],
        );

        $rootButton = $this->componentFactory->createLinkButton()
            ->setHref((string)$editPageUri)
            ->setTitle((string)LocalizationUtility::translate('editpage', 't3sbootstrap'))
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-file-edit', IconSize::SMALL));

        $buttonBar->addButton($rootButton, ButtonBar::BUTTON_POSITION_LEFT, 3);

        // View page
        $previewDataAttributes = PreviewUriBuilder::create($this->rootPageId)
            ->withRootLine(BackendUtility::BEgetRootLine($this->rootPageId))
            ->buildDispatcherDataAttributes();
        
        $viewButton = $this->componentFactory->createLinkButton()
            ->setHref('#')
            ->setDataAttributes($previewDataAttributes ?? [])
            ->setDisabled($previewDataAttributes === null)
            ->setTitle($this->getLanguageService()->sL(
                'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.showPage'
            ))
            ->setIcon($this->iconFactory->getIcon('actions-view-page', IconSize::SMALL))
            ->setShowLabelText(true);
        
        $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 2);   

        // Edit T3SB Configuration
        if (!empty($config)) {
            $uriConfig = $this->beUriBuilder->buildUriFromRoute('record_edit', [
                'edit' => [
                    'tx_t3sbootstrap_domain_model_config' => [$config->getUid() => 'edit'],
                ],
                'id' => $this->currentUid,
                'returnUrl' => (string)$returnUrl
            ]);

            $currentAction = $this->componentFactory->createLinkButton()
                ->setHref((string)$uriConfig)
                ->setTitle(LocalizationUtility::translate('editconfig','t3sbootstrap'))
                ->setShowLabelText(true)
                ->setIcon($this->iconFactory->getIcon('bootstraplogo', IconSize::SMALL));
            $buttonBar->addButton($currentAction, ButtonBar::BUTTON_POSITION_LEFT, 1);
        }
    }
    
    
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }


    /**
     * True when the current backend user may act on that page.
     *
     * Admins always may. For everybody else the page has to exist and be covered
     * by their permissions - doesUserHaveAccess() also honours the DB mounts.
     */
    private function hasPageAccess(int $pid, int $permission): bool
    {
        // not $this->isAdmin: initializeAction() leaves that false when the request
        // carries no usable "id", and an admin would then be locked out
        if ($GLOBALS['BE_USER']->isAdmin()) {
            return true;
        }

        $page = BackendUtility::getRecord('pages', $pid);

        if (!is_array($page)) {
            return false;
        }

        return (bool)$GLOBALS['BE_USER']->doesUserHaveAccess($page, $permission);
    }

    private function denyTransfer(): ResponseInterface
    {
        $this->addFlashMessage(
            (string)(LocalizationUtility::translate('transfer.error.access', 't3sbootstrap')
                ?? 'You are not allowed to access the configuration of that page.'),
            '',
            ContextualFeedbackSeverity::ERROR
        );

        return $this->redirect('list', null, null, ['id' => $this->currentUid]);
    }

    /**
     * Exports the configuration records of a root page as a portable json file. The payload
     * carries neither uid nor pid, only field values and FAL references as combined
     * identifiers, so it can be imported into an installation with a different page tree.
     */
    public function exportAction(int $exportPid = 0): ResponseInterface
    {
        $pid = $exportPid;

        // The module is registered with 'access' => 'user', so editors reach this action
        // too. Without the check any pid in the URL would hand out a foreign site's
        // configuration, custom_scss included. A missing pid means "every record".
        if ($pid <= 0 ? !$GLOBALS['BE_USER']->isAdmin() : !$this->hasPageAccess($pid, Permission::PAGE_SHOW)) {
            return $this->denyTransfer();
        }

        try {
            $payload = $this->configTransferService->export($pid > 0 ? $pid : null);
            // the system part is the same for the whole installation, so only an admin gets
            // it - an editor exporting their own page must not take settings.php with them
            if ($GLOBALS['BE_USER']->isAdmin()) {
                $payload += $this->systemTransferService->export();
            }
        } catch (\Throwable $e) {
            $this->addFlashMessage($e->getMessage(), '', ContextualFeedbackSeverity::ERROR);
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        $json = (string)json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $filename = sprintf('t3sbootstrap-config-%s-pid%d.json', date('Ymd-His'), $pid);

        $response = new Response();
        $response->getBody()->write($json);

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Content-Length', (string)strlen($json));
    }

    /**
     * Imports a json export into the selected root page. The configuration record is written
     * right away - that is what the three modes are for. The system part of a format 2 export
     * is not: extension configuration and site files are shown as a diff first and only
     * written after a second confirmation, see transferPreviewAction().
     */
    public function importAction(
        int $targetPid = 0,
        string $importMode = ConfigTransferService::MODE_UPDATE,
        bool $importRecords = true,
        // the form has this ticked for an admin; the default stays false for anyone else,
        // who never sees the field and must not import it through a hand-made request
        bool $importSystem = false,
    ): ResponseInterface
    {
        // Writing action - the target page has to be editable for this user.
        // A missing pid is rejected by the service itself with its own message.
        if ($targetPid > 0 && !$this->hasPageAccess($targetPid, Permission::PAGE_EDIT)) {
            return $this->denyTransfer();
        }

        $mode = $importMode;

        if (!in_array($mode, [
            ConfigTransferService::MODE_UPDATE,
            ConfigTransferService::MODE_REPLACE,
            ConfigTransferService::MODE_ADD,
        ], true)) {
            $mode = ConfigTransferService::MODE_UPDATE;
        }
        $uploadedFile = $this->findUploadedFile($this->request->getUploadedFiles(), 'configFile');

        if ($uploadedFile === null || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $this->addFlashMessage(
                LocalizationUtility::translate('transfer.error.nofile', 't3sbootstrap') ?? 'No file uploaded.',
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        if (!$importRecords && !$importSystem) {
            $this->addFlashMessage(
                (string)(LocalizationUtility::translate('transfer.error.nothingselected', 't3sbootstrap')
                    ?? 'Nothing selected to import.'),
                '',
                ContextualFeedbackSeverity::WARNING
            );
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        try {
            $payload = (array)json_decode((string)$uploadedFile->getStream()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $result = $importRecords
                ? $this->configTransferService->import($payload, $targetPid, $mode)
                : ['imported' => 0, 'messages' => [], 'errors' => []];
        } catch (\Throwable $e) {
            $this->addFlashMessage($e->getMessage(), '', ContextualFeedbackSeverity::ERROR);
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        foreach ($result['errors'] ?? [] as $error) {
            $this->addFlashMessage($error, '', ContextualFeedbackSeverity::ERROR);
        }
        foreach ($result['messages'] ?? [] as $message) {
            $this->addFlashMessage($message, '', ContextualFeedbackSeverity::INFO);
        }

        if ($importRecords) {
            $this->addFlashMessage(
                sprintf(
                    (string)(LocalizationUtility::translate('transfer.imported', 't3sbootstrap')
                        ?? 'Configuration records written on page %2$d: %1$d'),
                    (int)($result['imported'] ?? 0),
                    $targetPid
                ),
                '',
                ContextualFeedbackSeverity::OK
            );
        }

        if ($importSystem) {
            return $this->prepareSystemTransfer($payload, $targetPid);
        }

        return $this->redirect('list', null, null, ['id' => $this->currentUid]);
    }

    /**
     * Shows what the system part of the uploaded export would change, nothing is written.
     * The payload itself comes from the backend session, where importAction() left it.
     */
    public function transferPreviewAction(): ResponseInterface
    {
        $stored = $this->getStoredTransfer();

        if ($stored === []) {
            return $this->denyExpiredTransfer();
        }
        if (!$GLOBALS['BE_USER']->isAdmin()) {
            return $this->denyTransfer();
        }

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->assignMultiple([
            'currentUid' => $this->currentUid,
            'transfer' => $this->buildSystemTransferReport($stored, true),
        ]);

        return $moduleTemplate->renderResponse('Config/TransferPreview');
    }

    /**
     * Writes the system part that transferPreviewAction() showed.
     */
    public function transferApplyAction(): ResponseInterface
    {
        $stored = $this->getStoredTransfer();

        if ($stored === []) {
            return $this->denyExpiredTransfer();
        }
        if (!$GLOBALS['BE_USER']->isAdmin()) {
            return $this->denyTransfer();
        }

        $report = $this->buildSystemTransferReport($stored, false);
        $this->clearStoredTransfer();

        foreach ($report['flash'] as $severity => $messages) {
            foreach ($messages as $message) {
                $this->addFlashMessage($message, '', ContextualFeedbackSeverity::from((int)$severity));
            }
        }

        return $this->redirect('list', null, null, ['id' => $this->currentUid]);
    }

    /**
     * The preview keeps the payload in the backend session. When it is gone - a second tab,
     * a new login, a backend session that was renewed - there is nothing left to write.
     * Saying so beats redirecting to a page that looks as if the click did nothing.
     */
    private function denyExpiredTransfer(): ResponseInterface
    {
        $this->addFlashMessage(
            (string)(LocalizationUtility::translate('transfer.preview.expired', 't3sbootstrap')
                ?? 'The preview is no longer available. Please upload the file again.'),
            '',
            ContextualFeedbackSeverity::WARNING
        );

        return $this->redirect('list', null, null, ['id' => $this->currentUid]);
    }

    /**
     * Keeps the system part of the payload for the confirmation step and forwards to the
     * preview. Only an admin gets this far - these are repository files, not records, and
     * the module is readable for editors as well.
     *
     * @param array<string, mixed> $payload
     */
    private function prepareSystemTransfer(array $payload, int $targetPid): ResponseInterface
    {
        if (!$GLOBALS['BE_USER']->isAdmin()) {
            $this->addFlashMessage(
                (string)(LocalizationUtility::translate('transfer.system.adminonly', 't3sbootstrap')
                    ?? 'Only an administrator can import the system configuration.'),
                '',
                ContextualFeedbackSeverity::WARNING
            );
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        $extensionConfiguration = (array)($payload['extensionConfiguration'] ?? []);
        $sites = (array)($payload['sites'] ?? []);

        if ($extensionConfiguration === [] && $sites === []) {
            $this->addFlashMessage(
                (string)(LocalizationUtility::translate('transfer.system.notinfile', 't3sbootstrap')
                    ?? 'The file carries no system configuration - it was written by an older version.'),
                '',
                ContextualFeedbackSeverity::INFO
            );
            return $this->redirect('list', null, null, ['id' => $this->currentUid]);
        }

        $siteIdentifier = $this->systemTransferService->getSiteIdentifierForPage($targetPid);

        if ($sites !== [] && $siteIdentifier === '') {
            $this->addFlashMessage(
                (string)(LocalizationUtility::translate('transfer.system.nosite', 't3sbootstrap')
                    ?? 'The target page belongs to no site, so sets and site settings are skipped.'),
                '',
                ContextualFeedbackSeverity::WARNING
            );
        }

        $GLOBALS['BE_USER']->setAndSaveSessionData(self::TRANSFER_SESSION_KEY, [
            'extensionConfiguration' => $extensionConfiguration,
            'hostSpecific' => (array)($payload['hostSpecific'] ?? []),
            'environment' => (array)($payload['environment'] ?? []),
            'site' => $siteIdentifier === '' ? [] : $this->pickSitePayload($sites, $siteIdentifier),
            'siteIdentifier' => $siteIdentifier,
            'source' => (string)($payload['meta']['source'] ?? ''),
        ]);

        return $this->redirect('transferPreview', null, null, ['id' => $this->currentUid]);
    }

    /**
     * Runs both imports and collects what they report. $dryRun decides whether anything is
     * written - preview and confirmation take the same path, so they cannot drift apart.
     *
     * @param array<string, mixed> $stored
     * @return array<string, mixed>
     */
    private function buildSystemTransferReport(array $stored, bool $dryRun): array
    {
        $extension = $this->systemTransferService->importExtensionConfiguration(
            (array)($stored['extensionConfiguration'] ?? []),
            $dryRun,
        );

        $site = [];
        $siteIdentifier = (string)($stored['siteIdentifier'] ?? '');
        if ($siteIdentifier !== '' && (array)($stored['site'] ?? []) !== []) {
            $site = $this->systemTransferService->importSite($siteIdentifier, (array)$stored['site'], $dryRun);
        }

        $flash = [ContextualFeedbackSeverity::OK->value => [], ContextualFeedbackSeverity::ERROR->value => []];

        if (!$dryRun) {
            if ($extension['written']) {
                $flash[ContextualFeedbackSeverity::OK->value][] = sprintf(
                    (string)(LocalizationUtility::translate('transfer.system.extwritten', 't3sbootstrap')
                        ?? '%d value(s) written to the extension configuration.'),
                    count($extension['changes'])
                );
            }
            if (!empty($site['written'])) {
                $flash[ContextualFeedbackSeverity::OK->value][] = sprintf(
                    (string)(LocalizationUtility::translate('transfer.system.sitewritten', 't3sbootstrap')
                        ?? 'Site %s: %d set(s) and %d setting(s) written.'),
                    $site['site'],
                    count($site['setsMissing']),
                    count($site['changes'])
                );
            }
            foreach ((array)($site['errors'] ?? []) as $error) {
                $flash[ContextualFeedbackSeverity::ERROR->value][] = $error;
            }
        }

        // the target is missing these - the require line for exactly them is what the
        // administrator of the target needs, not the one for the whole source
        $environment = $this->systemTransferService->checkEnvironment(
            (array)($stored['environment'] ?? []),
            (array)($stored['site']['allSets'] ?? []),
        );
        $environment['composer'] = $this->systemTransferService->buildComposerRequire($environment['missingExtensions']);

        return [
            'extension' => $extension,
            'site' => $site,
            'hostSpecific' => (array)($stored['hostSpecific'] ?? []),
            'environment' => $environment,
            'source' => (string)($stored['source'] ?? ''),
            'flash' => $flash,
        ];
    }

    /**
     * The entry of the export that belongs to the target site: its identifier if the export
     * holds it, the entry with the same root page next, and with a single site in the file
     * that one - a renamed site identifier is the normal case when moving an installation.
     *
     * @param array<string, mixed> $sites
     * @return array<string, mixed>
     */
    private function pickSitePayload(array $sites, string $siteIdentifier): array
    {
        if (isset($sites[$siteIdentifier]) && is_array($sites[$siteIdentifier])) {
            return $sites[$siteIdentifier];
        }

        foreach ($sites as $site) {
            if (is_array($site) && (int)($site['rootPageId'] ?? 0) === $this->rootPageId) {
                return $site;
            }
        }

        if (count($sites) === 1) {
            $single = reset($sites);
            if (is_array($single)) {
                return $single;
            }
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTransfer(): array
    {
        $stored = $GLOBALS['BE_USER']->getSessionData(self::TRANSFER_SESSION_KEY);

        return is_array($stored) ? $stored : [];
    }

    private function clearStoredTransfer(): void
    {
        $GLOBALS['BE_USER']->setAndSaveSessionData(self::TRANSFER_SESSION_KEY, null);
    }

    /**
     * Finds an uploaded file by its form field name. Extbase prefixes form fields with the
     * plugin namespace, so the file sits one level below tx_t3sbootstrap_web_t3sbootstrap
     * instead of at the top of getUploadedFiles(); searching recursively survives renaming.
     *
     * @param array<mixed> $files
     */
    private function findUploadedFile(array $files, string $name): ?UploadedFileInterface
    {
        foreach ($files as $key => $value) {
            if ($key === $name && $value instanceof UploadedFileInterface) {
                return $value;
            }
            if (is_array($value)) {
                $found = $this->findUploadedFile($value, $name);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Turns the generated constants file into rows that can be read without knowing the
     * table: the constant, its value and the label of the field behind it. Every constant
     * except uid and navbarBreakpointWidth is a column of the configuration table, so the
     * labels come from the TCA and are already translated.
     *
     * Condition blocks are kept - with more than one site root the file carries one block
     * per root page, and a value only says something together with its condition.
     *
     * @return array<int, array{condition: string, rows: array<int, array{constant: string, value: string, label: string, description: string}>}>
     */
    private function buildConstantBlocks(string $filecontent): array
    {
        $languageService = $this->getLanguageService();

        // Built the same way round as the generator builds the file: column name to camel
        // case, never back. One column is spelled card_flipper_on_Click, so converting the
        // constant back to a column name would miss it.
        $fields = [];
        foreach ($GLOBALS['TCA'][ConfigTransferService::TABLE]['columns'] ?? [] as $column => $configuration) {
            $fields[GeneralUtility::underscoredToLowerCamelCase($column)] = [
                'column' => $column,
                'label' => $this->translateTcaLabel($languageService, (string)($configuration['label'] ?? '')),
                'description' => $this->translateTcaLabel($languageService, (string)($configuration['description'] ?? '')),
            ];
        }

        // two constants the generator adds itself, they have no column behind them
        $fields['uid'] ??= ['column' => '', 'label' => $this->translate('constants.uid'), 'description' => ''];
        // derived from navbar_breakpoint and the site settings - the pencil leads to that field
        $fields['navbarBreakpointWidth'] ??= ['column' => 'navbar_breakpoint', 'label' => $this->translate('constants.breakpointwidth'), 'description' => ''];

        $blocks = [];
        $current = ['condition' => '', 'uid' => 0, 'lines' => []];

        foreach (explode("\n", $filecontent) as $line) {
            $line = trim($line);

            if ($line === '' || $line === '[END]') {
                continue;
            }

            if (str_starts_with($line, '[')) {
                // a new condition starts, keep what was collected so far
                if ($current['lines'] !== []) {
                    $blocks[] = $current;
                }
                $current = ['condition' => $line, 'uid' => 0, 'lines' => []];
                continue;
            }

            [$constant, $value] = array_pad(explode('=', $line, 2), 2, '');
            $constant = trim($constant);
            if (!str_starts_with($constant, 'bootstrap.config.')) {
                continue;
            }

            $field = substr($constant, strlen('bootstrap.config.'));
            $value = trim($value);

            // every block carries the uid of the record it was generated from, and that
            // is what the edit links of this block have to point at
            if ($field === 'uid') {
                $current['uid'] = (int)$value;
            }
            // the record type is the view the record is currently switched to, not a
            // property of these constants - it says nothing about the block and is skipped
            if ($field === 'recordType') {
                continue;
            }

            $current['lines'][] = ['constant' => $constant, 'field' => $field, 'value' => $value];
        }

        if ($current['lines'] !== []) {
            $blocks[] = $current;
        }

        $returnUrl = (string)$this->request->getAttribute('normalizedParams')?->getRequestUri();
        $result = [];

        foreach ($blocks as $block) {
            $editable = $this->getEditableColumns();
            $rows = [];

            foreach ($block['lines'] as $line) {
                $field = $fields[$line['field']] ?? ['column' => '', 'label' => '', 'description' => ''];
                $column = (string)$field['column'];

                $rows[] = [
                    'constant' => $line['constant'],
                    'value' => $line['value'],
                    'label' => $field['label'],
                    'description' => $field['description'],
                    'editUri' => in_array($column, $editable, true)
                        ? $this->buildFieldEditUri($column, (int)$block['uid'], $returnUrl)
                        : '',
                ];
            }

            $result[] = [
                'condition' => $block['condition'],
                'editableCount' => count(array_filter($rows, static fn(array $row): bool => $row['editUri'] !== '')),
                'rowCount' => count($rows),
                'rows' => $rows,
            ];
        }

        return $result;
    }

    /**
     * The columns that can be edited at all - the union of every record type's showitem,
     * not filtered by the type the record currently shows: the types are a view switch on
     * one and the same record, and ShowColumnsOfAnyType widens the form for the single
     * field the pencil asks for.
     *
     * A column that appears in no showitem has no form of its own anywhere and is left out.
     * homepage_uid is the case in point: it is maintained by the extension and decides
     * whether a record produces constants at all - a hand edited value breaks exactly that.
     * Left out as well: the type field itself, read only columns and the technical types.
     *
     * @return string[]
     */
    private function getEditableColumns(): array
    {
        if ($this->editableColumns !== []) {
            return $this->editableColumns;
        }

        $tca = $GLOBALS['TCA'][ConfigTransferService::TABLE] ?? [];
        $typeField = (string)($tca['ctrl']['type'] ?? '');
        $columns = [];

        foreach ($tca['types'] ?? [] as $type) {
            foreach ($this->expandShowitem((string)($type['showitem'] ?? ''), $tca) as $column) {
                if ($column === '' || $column === $typeField || !isset($tca['columns'][$column])) {
                    continue;
                }
                $config = $tca['columns'][$column]['config'] ?? [];
                if (!empty($config['readOnly']) || in_array($config['type'] ?? '', ['passthrough', 'none'], true)) {
                    continue;
                }
                $columns[] = $column;
            }
        }

        return $this->editableColumns = array_values(array_unique($columns));
    }

    /**
     * Field names out of a showitem string, with palettes resolved. Everything that is not
     * a field - dividers, line breaks, labels after a semicolon - falls away.
     *
     * @param array<string, mixed> $tca
     * @return string[]
     */
    private function expandShowitem(string $showitem, array $tca): array
    {
        $fields = [];

        foreach (GeneralUtility::trimExplode(',', $showitem, true) as $item) {
            $parts = GeneralUtility::trimExplode(';', $item);
            $name = $parts[0] ?? '';

            if ($name === '--div--' || $name === '--linebreak--') {
                continue;
            }

            if ($name === '--palette--') {
                $palette = (string)($tca['palettes'][$parts[2] ?? '']['showitem'] ?? '');
                foreach ($this->expandShowitem($palette, $tca) as $field) {
                    $fields[] = $field;
                }
                continue;
            }

            $fields[] = $name;
        }

        return $fields;
    }

    /**
     * Opens the configuration record with this single field shown - columnsOnly turns the
     * edit form into exactly the one setting behind the constant, and the return url leads
     * back into this module afterwards. Empty for the two constants that have no field.
     */
    private function buildFieldEditUri(string $column, int $uid, string $returnUrl): string
    {
        if ($column === '' || $uid <= 0) {
            return '';
        }

        try {
            return (string)$this->beUriBuilder->buildUriFromRoute('record_edit', [
                'edit' => [ConfigTransferService::TABLE => [$uid => 'edit']],
                'columnsOnly' => [ConfigTransferService::TABLE => [$column]],
                'returnUrl' => $returnUrl,
            ]);
        } catch (\Throwable) {
            return '';
        }
    }

    private function translate(string $key): string
    {
        return (string)(LocalizationUtility::translate($key, 't3sbootstrap') ?? '');
    }

    /**
     * Resolves an LLL reference, and leaves an empty or unresolvable one empty rather than
     * printing the reference itself into the table.
     */
    private function translateTcaLabel(LanguageService $languageService, string $label): string
    {
        if ($label === '') {
            return '';
        }

        // sL() resolves "LLL:EXT:..." as well as the v14 translation domain shorthand
        // "<extension>.<domain>:<key>" that this extension's TCA uses - so everything goes
        // through it, and only what comes back unresolved is dropped.
        $translated = (string)$languageService->sL($label);

        $unresolved = $translated === $label && str_contains($translated, ':');

        return ($translated === '' || $unresolved) ? '' : $translated;
    }

    /**
     * Link to the history of the configuration record of the current page, empty when the
     * page has none yet. The history is a backend route of its own; from this module there
     * is otherwise no way to reach it.
     */
    private function buildHistoryUri(): string
    {
        $config = $this->configRepository?->findOneBy(['pid' => $this->currentUid]);
        $uid = $config?->getUid() ?? 0;

        if ($uid <= 0) {
            return '';
        }

        try {
            return (string)$this->beUriBuilder->buildUriFromRoute('record_history', [
                'element' => ConfigTransferService::TABLE . ':' . $uid,
                'returnUrl' => (string)$this->request->getAttribute('normalizedParams')?->getRequestUri(),
            ]);
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Root pages as uid => label, ready for f:form.select.
     *
     * @param array<int, array{uid: int, title: string}> $pages
     * @return array<int, string>
     */
    private function buildPageOptions(array $pages): array
    {
        $options = [];

        foreach ($pages as $page) {
            $options[$page['uid']] = $page['title'] . ' [' . $page['uid'] . ']';
        }

        return $options;
    }
}
