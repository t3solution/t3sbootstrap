<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\Page\PageInformation;
use T3SBS\T3sbootstrap\Helper\AssetHelper;
use T3SBS\T3sbootstrap\Helper\ClassHelper;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Helper\DefaultHelper;
use T3SBS\T3sbootstrap\Helper\MediaElementHelper;
use T3SBS\T3sbootstrap\Layouts\TwoColumns;
use T3SBS\T3sbootstrap\Layouts\ThreeColumns;
use T3SBS\T3sbootstrap\Layouts\FourColumns;
use T3SBS\T3sbootstrap\Layouts\SixColumns;
use T3SBS\T3sbootstrap\Layouts\RowColumns;
use T3SBS\T3sbootstrap\Components\Mediaobject;
use T3SBS\T3sbootstrap\Components\Card;
use T3SBS\T3sbootstrap\Components\Carousel;
use T3SBS\T3sbootstrap\Components\Button;
use T3SBS\T3sbootstrap\Components\Toast;
use T3SBS\T3sbootstrap\Wrapper\ButtonGroup;
use T3SBS\T3sbootstrap\Wrapper\BackgroundWrapper;
use T3SBS\T3sbootstrap\Wrapper\ParallaxWrapper;
use T3SBS\T3sbootstrap\Wrapper\CardWrapper;
use T3SBS\T3sbootstrap\Wrapper\CarouselContainer;
use T3SBS\T3sbootstrap\Wrapper\CollapsibleAccordion;
use T3SBS\T3sbootstrap\Wrapper\Modal;
use T3SBS\T3sbootstrap\Wrapper\TabsContainer;
use T3SBS\T3sbootstrap\Wrapper\ToastContainer;
use T3SBS\T3sbootstrap\ContentElements\Menu;
use T3SBS\T3sbootstrap\ContentElements\Table;
use T3SBS\T3sbootstrap\Wrapper\MasonryWrapper;
use T3SBS\T3sbootstrap\Wrapper\SwiperContainer;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BootstrapProcessor implements DataProcessorInterface
{
    public const TX_CONTAINER_GRID = 'two_columns,three_columns,four_columns,six_columns,row_columns';
    public const T3SBS_ELEMENTS = 't3sbs_mediaobject,t3sbs_card,t3sbs_carousel,t3sbs_button,t3sbs_fluidtemplate,t3sbs_gallery,t3sbs_toast,t3sbs_assets';
    public const TX_CONTAINER = 'button_group,background_wrapper,parallax_wrapper,autoLayout_row,container,carousel_container,collapsible_container,collapsible_accordion,modal,tabs_container,tabs_tab,listGroup_wrapper,masonry_wrapper,swiper_container,toast_container,card_wrapper';
    public const ANIMATION_PREFIX = 'tx_content_animations_';

    public function __construct(
        private readonly FileRepository $fileRepository,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly FlexFormTools $flexFormTools,
        private readonly ConnectionPool $connectionPool,
        private readonly ConfigurationManager $configurationManager,
        private readonly ClassHelper $classHelper,
        private readonly StyleHelper $styleHelper,
        private readonly Card $card,
        private readonly Mediaobject $mediaobject,
        private readonly Carousel $carousel,
        private readonly Button $button,
        private readonly Toast $toast,
        private readonly AssetHelper $assetHelper,
        private readonly TwoColumns $twoColumns,
        private readonly ThreeColumns $threeColumns,
        private readonly FourColumns $fourColumns,
        private readonly SixColumns $sixColumns,
        private readonly RowColumns $rowColumns,
        private readonly CardWrapper $cardWrapper,
        private readonly ButtonGroup $buttonGroup,
        private readonly BackgroundWrapper $backgroundWrapper,
        private readonly ParallaxWrapper $parallaxWrapper,
        private readonly CarouselContainer $carouselContainer,
        private readonly CollapsibleAccordion $collapsibleAccordion,
        private readonly Modal $modal,
        private readonly TabsContainer $tabsContainer,
        private readonly MasonryWrapper $masonryWrapper,
        private readonly SwiperContainer $swiperContainer,
        private readonly ToastContainer $toastContainer,
        private readonly Menu $menu,
        private readonly Table $table,
        private readonly MediaElementHelper $mediaElementHelper,
        private readonly DefaultHelper $defaultHelper,
    ) {}


    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array
    {

        if ( empty($processedData['data']['CType']) ) {
            return $processedData;
        }

        $request = $cObj->getRequest();
        /** @var PageInformation $pageInformation */
        $pageInformation = $request->getAttribute('frontend.page.information');
        $extConf = $this->extensionConfiguration->get('t3sbootstrap');
        $cType = $processedData['data']['CType'];
        $parentCType = '';

        $flexconf = [];
        if (!empty($processedData['t3sbFlexform'])) {
            $flexconf = $processedData['t3sbFlexform'];
        }

        $parentflexconf = [];
        $parentUid = $processedData['data']['tx_container_parent'] ?? 0;

        if (!empty($parentUid)) {
            $parentData = BackendUtility::getRecord('tt_content', $parentUid);
            $parentCType = !empty($parentData['CType']) ? $parentData['CType'] : '';
            if (!empty($parentData['tx_t3sbootstrap_flexform'])) {
                $parentflexconf = $this->flexFormTools->convertFlexFormContentToArray($parentData['tx_t3sbootstrap_flexform']);
            }
        }

        $processedData['parentCType'] = $parentCType;
        $processedData['isTxContainer'] = false;
        $processedData['dataAnimate'] = '';
        $processedData['isAnimateCss'] = false;
        $processedData['animateCssRepeat'] = false;
        $processedData['containsVideo'] = false;
        $processedData['lightBox'] = false;
        $processedData['data']['configuid'] = !empty($processorConfiguration['configuid']) ? (int)$processorConfiguration['configuid'] : 0;

        $sectionMenuClass = '';
        if (!empty($contentObjectConfiguration['settings.']['sectionMenuClass'])) {
            $sectionMenuClass = $contentObjectConfiguration['settings.']['sectionMenuClass'];
        }

        $footerPid = !empty($processorConfiguration['footerPid'])
         ? (int) $processorConfiguration['footerPid'] : 0;
        $footerContainer = !empty($processorConfiguration['footerContainer'])
         ? (string) $processorConfiguration['footerContainer'] : '';
        $jumbotronContainer = !empty($processorConfiguration['jumbotronContainer'])
         ? (string) $processorConfiguration['jumbotronContainer'] : '';
        $expandedcontentContainertop = !empty($processorConfiguration['expandedcontentContainertop'])
         ? (string) $processorConfiguration['expandedcontentContainertop'] : '';
        $expandedcontentContainerbottom = !empty($processorConfiguration['expandedcontentContainerbottom'])
         ? (string) $processorConfiguration['expandedcontentContainerbottom'] : '';

        if ($processedData['data']['CType'] === 'shortcut'
            && !empty($processedData['parentCType']) 
            && !empty($contentObjectConfiguration['settings.']['shortcutsremove']))
        {
            // empties any string (e.g. a class name) from shortcuts if in parent CE/wrapper
            $dataContainer = $processedData['data']['tx_t3sbootstrap_container'];
            $dataExtraClass = $processedData['data']['tx_t3sbootstrap_extra_class'];
            $dataFrame = $processedData['data']['frame_class'];
            $dataLayout = (string)$processedData['data']['layout'];

            $uid = GeneralUtility::trimExplode('_', $processedData['data']['records'])[2];

            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $shortcutRecord = $queryBuilder
                ->select('*')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter((int)$uid, Connection::PARAM_INT))
                )
                ->executeQuery()
                ->fetchAssociative();

            $shortcutContainer = $shortcutRecord['tx_t3sbootstrap_container'];
            $shortcutExtraClass = $shortcutRecord['tx_t3sbootstrap_extra_class'];
            $shortcutFrame = $shortcutRecord['frame_class'];
            $shortcutLayout = (string)$shortcutRecord['layout'];

            $processedData['shortcuts'] = str_replace($shortcutContainer, $dataContainer, $processedData['shortcuts']);
            $processedData['shortcuts'] = str_replace($shortcutExtraClass, $dataExtraClass, $processedData['shortcuts']);
            $processedData['shortcuts'] = str_replace($shortcutFrame, $dataFrame, $processedData['shortcuts']);
            $processedData['shortcuts'] = str_replace($shortcutLayout, $dataLayout, $processedData['shortcuts']);
        }


        // container config
        $pageRecord = $pageInformation->getPageRecord();
        $pageContainer = !empty($pageRecord) ? $pageRecord['tx_t3sbootstrap_container'] : '';
        $ts = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $pOverride = $ts['module.']['tx_t3sbootstrap.']['settings.']['pages.']['override.']['tx_t3sbootstrap_container'];
        if (!empty($pOverride)) {
            $pageContainer = !empty($pageContainer) ? $pageContainer : '';
            if ($pageContainer === 'none') {
                $pageContainer = 0;
            } else {
                $pageContainer = $pOverride;
            }
        }

        $containerConfig = [
            'footerPid' => $footerPid,
            'footerContainer' => $footerContainer,
            'jumbotronContainer' => $jumbotronContainer,
            'expandedcontentContainertop' => $expandedcontentContainertop,
            'expandedcontentContainerbottom' => $expandedcontentContainerbottom,
            'pageContainer' => !empty($pageContainer) ? true : false,
        ];

        // class
        $class = $this->classHelper->getDefaultClass($processedData['data'], $flexconf, $extConf['cTypeClass'], $sectionMenuClass);
        $processedData['class'] = !empty($processedData['class']) ? $processedData['class'].' '.$class : $class;

        // header class
        $processedData['header'] = $this->classHelper->getHeaderClass($processedData['data']);

        // style
        $processedData['style'] = $this->styleHelper->getBgColor($processedData['data']);

        // CSS-class for tx_container
        if ( GeneralUtility::inList(self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) && !($cType === 'list')) {
            $isVideo = !empty($processedData['isVideo']);
            $containerClass = $this->classHelper->getTxContainerClass($processedData['data'], $flexconf, $isVideo);
            $processedData['class'] .= $containerClass ? ' '.$containerClass : '';
            $processedData['isTxContainer'] = true;
        }

        // T3SB Elements

        if (str_contains(self::T3SBS_ELEMENTS, $cType)) {
            if ($cType === 't3sbs_mediaobject') {
                $processedData = $this->mediaobject->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 't3sbs_card') {
                $processedData = $this->card->getProcessedData($processedData, $flexconf, $parentflexconf, (bool)$processorConfiguration['minimumWidth']);
            }
            if ($cType === 't3sbs_carousel') {
                $processedData = $this->carousel->getProcessedData($processedData, $flexconf, $parentflexconf, $extConf['animateCss']);
            }
            if ($cType === 't3sbs_button') {
                $processedData = $this->button->getProcessedData($processedData, $flexconf, $parentflexconf);
            }
            if ($cType === 't3sbs_toast') {
                $processedData = $this->toast->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 't3sbs_assets') {
                if (!empty($processedData['piFlexform'])) {
                    $processedData['assets']['jquery'] = $processedData['piFlexform']['settings']['jquery'];
                    $processedData['assets']['priority'] = $processedData['piFlexform']['settings']['priority'];
                }
                if (!empty($processedData['cssfiles']) && is_array($processedData['cssfiles'])) {
                    $this->assetHelper->addCSS($processedData['cssfiles']);
                }
                if (!empty($processedData['jsfiles']) && is_array($processedData['jsfiles'])) {
                    $this->assetHelper->addJS($processedData['jsfiles'], (int) $processedData['assets']['priority']);
                }
            }
            //if ( $cType == 't3sbs_fluidtemplate' ) {}
            //if ( $cType == 't3sbs_gallery' ) {}
        }

        // Grid container

        if (str_contains(self::TX_CONTAINER_GRID, $cType)) {
            if ($cType === 'two_columns') {
                $processedData = $this->twoColumns
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']['bgMediaQueries']);
            }
            if ($cType === 'three_columns') {
                $processedData = $this->threeColumns
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'four_columns') {
                $processedData = $this->fourColumns
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'six_columns') {
                $processedData = $this->sixColumns
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'row_columns') {
                $processedData = $this->rowColumns
                ->getProcessedData($processedData, $flexconf);
            }
        }

        // Container/Wrapper

        if (str_contains(self::TX_CONTAINER, $cType) && !($cType === 'list')) {
            if ($cType === 'card_wrapper') {
                $processedData = $this->cardWrapper->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'button_group') {
                $processedData = $this->buttonGroup
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'background_wrapper') {
                $processedData = $this->backgroundWrapper
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']);
            }
            if ($cType === 'parallax_wrapper') {
                $processedData = $this->parallaxWrapper
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'collapsible_container') {
                $processedData['appearance'] = !empty($flexconf['appearance']) ? $flexconf['appearance'] : '';
                if (!empty($flexconf['appearance']) && $flexconf['appearance'] === 'accordion') {
                    $processedData['flush'] = !empty($flexconf['flush']) ? ' accordion-flush' : '';
                }
            }
            if ($cType === 'carousel_container') {
                $processedData = $this->carouselContainer
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'collapsible_accordion') {
                $processedData = $this->collapsibleAccordion
                ->getProcessedData($processedData, $flexconf, $parentflexconf);
            }
            if ($cType === 'modal') {
                $processedData = $this->modal
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'tabs_container' || $cType === 'tabs_tab') {
                $processedData = $this->tabsContainer
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'masonry_wrapper') {
                $processedData = $this->masonryWrapper
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'swiper_container') {
                $processedData = $this->swiperContainer
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'toast_container') {
                $processedData = $this->toastContainer
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']['navbarEnable']);
            }
            //if ( $cType == 'autoLayout_row' ) {}
            //if ( $cType == 'container' ) {}
            //if ( $cType == 'listGroup_wrapper' ) {}
        }

        // default content elements

        if (!str_contains(self::T3SBS_ELEMENTS.','.self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType)) {
            if (str_starts_with($cType, 'menu')) {
                $processedData = $this->menu->getProcessedData($processedData, $flexconf, $cType);
            }
            if ($cType === 'table') {
                $processedData = $this->table->getProcessedData($processedData, $flexconf);
            }
        }

        // plug-ins

        if ($cType === 'news_newsdetail') {
            $processedData['lightBox'] = true;
        }

        if ($processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] || $cType === 't3sbs_gallery') {
            $processedData = $this->mediaElementHelper->getProcessedData($processedData, $extConf, $contentObjectConfiguration['settings.']['breakpoint'], $parentflexconf);
            $fileParts = [];
            $processedData['addmedia']['ratioClass'] = 'ratio-16x9';
            $processedData['addmedia']['origImageZoom'] = $processedData['data']['tx_t3sbootstrap_zoom_orig'];

            $fileObjects = $this->fileRepository->findByRelation('tt_content', 'assets', $processedData['data']['uid']);
            foreach ($fileObjects as $key=>$fileObject) {
                    // local video
                if ($fileObject->getMimeType() === 'video/mp4' || $fileObject->getMimeType() === 'video/webm' || $fileObject->getMimeType() === 'video/wav'
                     || $fileObject->getMimeType() === 'video/ogg' || $fileObject->getMimeType() === 'video/flac' || $fileObject->getMimeType() === 'video/opus') {
 
                    $processedData['containsVideo'] = true;
                    $fileConfig = $fileObject->getStorage()->getConfiguration();
                    $filePath = substr($fileConfig['basePath'], 0, -1).explode('.', $fileObject->getIdentifier())[0];
                    $processedData['addmedia']['filePath'] = $filePath;
                    $processedData['addmedia']['extension'] = $fileObject->getExtension();

                    if (file_exists($filePath.'.png')) {
                        $fileParts[$key]['poster'] = $filePath.'.png';
                    } elseif (file_exists($filePath.'.jpg')) {
                        $fileParts[$key]['poster'] = $filePath.'.jpg';
                    } else {
                        $fileParts[$key]['poster'] = '';
                    }

                    if (array_key_exists('ratio', $extConf) && $extConf['ratio'] === '1'
                    && !empty($fileObject->getProperties()['tx_t3sbootstrap_video_ratio'])) {
                        $ratioArr = [];
                        $properties = $fileObject->getProperties();
                        if (str_contains($properties['tx_t3sbootstrap_video_ratio'], ':')) {
                            $ratioArr = explode(':', $properties['tx_t3sbootstrap_video_ratio']);
                        } elseif (str_contains($properties['tx_t3sbootstrap_video_ratio'], 'x')) {
                            $ratioArr = explode('x', $properties['tx_t3sbootstrap_video_ratio']);
                        } else {
                            $ratioArr = [];
                        }
                        if (!empty($ratioArr)) {
                            $x = $ratioArr[0].'x'.$ratioArr[1];
                            $y = $ratioArr[1].' / '.$ratioArr[0].' * 100%';
                        } else {
                            $x = '4';
                            $y = '3';
                        }
                        $processedData['addmedia']['ratioCalcCss'] = '.ratio-'.$x.'{--bs-aspect-ratio:calc('.$y.');}';
                        $processedData['addmedia']['ratioClass'] = 'ratio-'.$x;
                    }
                }
            }

            $processedData['posters'] = $fileParts;

            if (!empty($flexconf['zoom']) || !empty($parentflexconf['zoom'])) {
                $processedData['lightBox'] = true;
            }
            // lightbox
            if ($cType === 't3sbs_gallery' || !empty($processedData['data']['image_zoom'])) {
                $processedData['lightBox'] = true;
            }
        }

        // child of autoLayout_row
        if ($parentCType === 'autoLayout_row') {
            $processedData['newLine'] = !empty($flexconf['newLine']);
            $processedData['class'] .= $this->classHelper->getAutoLayoutClass($flexconf);
        }

        // child of container
        if ($parentCType === 'container') {
            $processedData['class'] .= $this->classHelper->getContainerClass($parentflexconf, $flexconf);
        }

        $processedData['dataAttr'] = '';
        if (!empty($processedData['data']['tx_content_animations_animation'])) {
            $completeAnimationSettings = $this->generateAnimationAttributeSettingsFromAnimationsArray($processedData['data']);
            $processedData['dataAttr'] = !empty($completeAnimationSettings) ? $completeAnimationSettings : '';
            $processedData['dataAnimate'] = '';
            $processedData['isAnimateCss'] = false;
            $processedData['animateCssRepeat'] = false;
        }

        // container class
        $processedData = $this->defaultHelper->getContainerClass(  $processedData,  $extConf['container'], $containerConfig);

        // defaults
        $processedData = $this->defaultHelper->getDefaults(
            $processedData,
            $flexconf,
            (int)$processorConfiguration['defaultHeaderType'],
            $processorConfiguration['contentMarginTop'],
            $extConf['animateCss'],
            $parentCType
        );

        // trim header
        $processedData['data']['header'] = !empty($processedData['data']['header']) ? trim($processedData['data']['header']) : '';

        $processedData['style'] .= ' '.$processedData['data']['tx_t3sbootstrap_extra_style'];
        $processedData['style'] = trim($processedData['style']);
        $processedData['styleAttr'] = !empty($processedData['style']) ? ' style="'.$processedData['style'].'"' : '';
        $processedData['styleInline'] = !empty($processedData['style']) ? '#c'.$processedData['data']['uid'].' {'.$processedData['style'].'}' : '';
        $processedData['trimClass'] = !empty(trim($processedData['class'])) ? trim($processedData['class']) : '';
        $processedData['class'] = !empty($processedData['trimClass']) ? ' '.$processedData['trimClass'] : '';

        $trimClass = !empty($processedData['trimClass']) ? trim($processedData['class']) : '';

        $processedData['classAttr'] = !empty($trimClass) ? ' class="'.$trimClass.'"' : '';
        $processedData['trimClass'] = $trimClass;
        
        return $processedData;
    }

    
    private function generateAnimationAttributeSettingsFromAnimationsArray(array $animationSettingsArray): string
    {
        $animation = trim((string)($animationSettingsArray[self::ANIMATION_PREFIX . 'animation'] ?? ''));
        if ($animation === '') {
            return '';
        }
    
        $boolKeys = ['once', 'mirror'];
        $attributes = ['data-aos' => $animation];
    
        foreach ($animationSettingsArray as $key => $value) {
            if (!str_starts_with($key, self::ANIMATION_PREFIX)) {
                continue;
            }
            $name = substr($key, strlen(self::ANIMATION_PREFIX));
            if ($name === 'animation' || $value === null || $value === '') {
                continue;
            }
            if (in_array($name, $boolKeys, true)) {
                if ((int)$value !== 1) {
                    continue; // AOS-Default ist false
                }
                $value = 'true';
            }
            $attributes['data-aos-' . str_replace('_', '-', $name)] = (string)$value;
        }
    
        $out = '';
        foreach ($attributes as $attr => $val) {
            $out .= $attr . '="' . htmlspecialchars($val, ENT_QUOTES) . '" ';
        }
    
        return rtrim($out);
    }


    public function removeChar(string $s, string $c): string
    {
        $s = str_replace($c, '', $s);
        if (str_contains($s, $c)) {
            $s = self::removeChar($s, $c);
        }
        return $s;
    }

}
