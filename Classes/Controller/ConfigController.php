<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Controller;

use Psr\Http\Message\ResponseInterface;
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
    ) {
    }


    public const T3SBCONSTANTSPATH = 'TypoScript/t3sbconstants.typoscript';


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
                $customVariablesPath = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Resources/Public/T3SB-SCSS/custom-variables-'.$this->rootPageId.'.scss');
                if (!file_exists($customVariablesPath)) {
                    $assignedOptions['executeTask'] = true;
                }
            } else {
                $assignedOptions['customScssCdnDisabled'] = true;
            }
        }

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

        $new_raster = GeneralUtility::getFileAbsFileName($this->settings['rasterPath']);
        if ( !file_exists($new_raster) ) {
            $folder = dirname($new_raster);
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            $orig_raster = GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Resources/Public/Images/raster.png');
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

}
