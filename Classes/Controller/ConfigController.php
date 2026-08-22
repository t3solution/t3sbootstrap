<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use T3SBS\T3sbootstrap\Service\AssetPathService;
use T3SBS\T3sbootstrap\Service\ConfigTransferService;
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
    ) {
    }


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

        // Config Transfer: Export/Import der Konfiguration dieser Root-Seite
        $siteRootPages = $this->configTransferService->getSiteRootPages();
        $assignedOptions['transfer'] = [
            'pageOptions' => $this->buildPageOptions($siteRootPages),
            'currentPid' => $this->rootPageId,
            'modeOptions' => [
                ConfigTransferService::MODE_UPDATE => LocalizationUtility::translate('transfer.mode.update', 't3sbootstrap'),
                ConfigTransferService::MODE_REPLACE => LocalizationUtility::translate('transfer.mode.replace', 't3sbootstrap'),
                ConfigTransferService::MODE_ADD => LocalizationUtility::translate('transfer.mode.add', 't3sbootstrap'),
            ],
        ];

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
        $config = $this->configRepository->findOneBy(['pid' => $this->currentUid]);
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
     * Exports the configuration records of a root page as a portable json file.
     *
     * The payload carries neither uid nor pid, only field values and FAL
     * references as combined identifiers, so it can be imported into an
     * installation with a completely different page tree.
     */
    public function exportAction(int $exportPid = 0): ResponseInterface
    {
        $pid = $exportPid;

        try {
            $payload = $this->configTransferService->export($pid > 0 ? $pid : null);
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
     * Imports a json export into the selected root page.
     */
    public function importAction(int $targetPid = 0, string $importMode = ConfigTransferService::MODE_UPDATE): ResponseInterface
    {
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

        try {
            $payload = json_decode((string)$uploadedFile->getStream()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $result = $this->configTransferService->import((array)$payload, $targetPid, $mode);
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

        $this->addFlashMessage(
            sprintf(
                (string)(LocalizationUtility::translate('transfer.imported', 't3sbootstrap') ?? '%d record(s) imported on page %d.'),
                (int)($result['imported'] ?? 0),
                $targetPid
            ),
            '',
            ContextualFeedbackSeverity::OK
        );

        return $this->redirect('list', null, null, ['id' => $this->currentUid]);
    }

    /**
     * Finds an uploaded file by its form field name.
     *
     * Extbase prefixes form fields with the plugin namespace, so the file does
     * not sit at the top level of getUploadedFiles() but one level below
     * tx_t3sbootstrap_web_t3sbootstrap. Searching recursively keeps this working
     * no matter how the form is namespaced.
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
