<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\EventListener\FlexForm;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 't3sbootstrap/flexParsing',
    method: 'modifyDataStructure',
)]
final readonly class FlexformEvent
{
    
    public function __construct(
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function modifyDataStructure(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();
        if ($identifier['fieldName'] === 'tx_t3sbootstrap_flexform') {
            $extconf = $this->extensionConfiguration->get('t3sbootstrap');
            if (array_key_exists('flexformExtend', $extconf) && $extconf['flexformExtend'] === '1') {
    
                if (!empty($dataStructure['sheets']['sDEF']['ROOT']['sheetTitle'])
                && $dataStructure['sheets']['sDEF']['ROOT']['sheetTitle'] === 'Utility Settings') {
                    $this->mergeFlexFormXml(
                        GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Configuration/FlexForms/Bootstrap.xml'),
                        $dataStructure
                    );
                }
    
                $noContainerDirArr = [
                    't3sbs_assets' => 'AssetInline.xml',
                    't3sbs_button' => 'Button.xml',
                    't3sbs_card' => 'CardSetting.xml',
                    't3sbs_carousel' => 'Carousel.xml',
                    't3sbs_mediaobject' => 'Mediaobject.xml',
                    't3sbs_toast' => 'ToastSetting.xml',
                ];
    
                $inContainerDirArr = [
                    'autoLayout_row' => 'AutolayoutRow.xml',
                    'background_wrapper' => 'BackgroundWrapper.xml',
                    'button_group' => 'ButtonGroup.xml',
                    'card_wrapper' => 'CardWrapper.xml',
                    'carousel_container' => 'CarouselContainer.xml',
                    'collapsible_accordion' => 'CollapsibleAccordion.xml',
                    'collapsible_container' => 'CollapsibleContainer.xml',
                    'container' => 'Container.xml',
                    'four_columns' => 'FourColumns.xml',
                    'masonry_wrapper' => 'MasonryWrapper.xml',
                    'modal' => 'Modal.xml',
                    'parallax_wrapper' => 'ParallaxWrapper.xml',
                    'row_columns' => 'RowColumns.xml',
                    'six_columns' => 'SixColumns.xml',
                    'swiper_container' => 'SwiperContainer.xml',
                    'tabs_container' => 'TabsContainer.xml',
                    'tabs_tab' => 'TabsTab.xml',
                    'three_columns' => 'ThreeColumns.xml',
                    'toast_container' => 'ToastContainer.xml',
                    'two_columns' => 'TwoColumns.xml',
                ];
    
                $key = $identifier['dataStructureKey'];
                if (isset($noContainerDirArr[$key])) {
                    $this->mergeFlexFormXml(
                        GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Configuration/FlexForms/'.$noContainerDirArr[$key]),
                        $dataStructure
                    );
                }
                if (isset($inContainerDirArr[$key])) {
                    $this->mergeFlexFormXml(
                        GeneralUtility::getFileAbsFileName('EXT:t3sb_package/Configuration/FlexForms/Container/'.$inContainerDirArr[$key]),
                        $dataStructure
                    );
                }
            }
        }
    
        $event->setDataStructure($dataStructure);
    }
    
    private function mergeFlexFormXml(string $xmlFile, array &$dataStructure): void
    {
        if (file_exists($xmlFile)) {
            $content = file_get_contents($xmlFile);
            if ($content) {
                $extraDataStructure = [];
                $extraDataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
                ArrayUtility::mergeRecursiveWithOverrule($dataStructure, $extraDataStructure);
            }
        }
    }

}
