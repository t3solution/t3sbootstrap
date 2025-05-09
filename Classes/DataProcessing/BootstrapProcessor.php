<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use Psr\Http\Message\ServerRequestInterface;
use T3SBS\T3sbootstrap\Helper\AssetHelper;
use T3SBS\T3sbootstrap\Helper\ClassHelper;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Helper\DefaultHelper;
use T3SBS\T3sbootstrap\Helper\MediaElementHelper;
use T3SBS\T3sbootstrap\Helper\FlexformHelper;
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

    /**
     * Process data
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
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
        if ( empty($processedData['data']['tx_t3sbootstrap_flexform']) ) {
            $processedData['data']['tx_t3sbootstrap_flexform'] = $processedData['data']['pi_flexform'];
        }

        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $cObj->getRequest();
        $frontendController = $request->getAttribute('frontend.controller');
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
        $cType = $processedData['data']['CType'];
        $parentCType = '';
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = [];
        if (!empty($processedData['data']['tx_t3sbootstrap_flexform'])) {
            $flexconf = $flexFormService->convertFlexFormContentToArray($processedData['data']['tx_t3sbootstrap_flexform']);
        }
        $parentflexconf = [];
        $parentContainer = [];
        $parentUid = $processedData['data']['tx_container_parent'];
        $t3sbsElement = false;
        if (str_contains(self::T3SBS_ELEMENTS.','.self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) && $cType !== 'list') {
            $t3sbsElement = true;
        }

        $flexformHelper = GeneralUtility::makeInstance(FlexformHelper::class);
        $flexconf = $flexformHelper->addMissingElements($flexconf, $cType, $t3sbsElement);

        if (!empty($parentUid)) {
            $parentData = BackendUtility::getRecord('tt_content', $parentUid, 'uid, CType, tx_t3sbootstrap_flexform, tx_container_parent');
            $parentCType = $parentData['CType'];
			if (!empty($parentData['tx_t3sbootstrap_flexform'])) {
	            $parentflexconf = $flexFormService->convertFlexFormContentToArray($parentData['tx_t3sbootstrap_flexform']);
	            if (is_string($parentCType)) {
	                $parentflexconf = $flexformHelper->addMissingElements($parentflexconf, $parentCType, $t3sbsElement);
	            }
			}
            $parentContainer = $parentData['tx_container_parent'];
        }

        $processedData['parentCType'] = $parentCType;
        $processedData['isTxContainer'] = false;
        $processedData['dataAnimate'] = '';
        $processedData['isAnimateCss'] = false;
        $processedData['animateCssRepeat'] = false;
        $processedData['containsVideo'] = false;
        $processedData['containerError'] = false;
        $processedData['lightBox'] = false;
        $processedData['data']['configuid'] = (int)$processorConfiguration['configuid'];

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

        $containerConfig = [
            'footerPid' => $footerPid,
            'footerContainer' => $footerContainer,
            'jumbotronContainer' => $jumbotronContainer,
            'expandedcontentContainertop' => $expandedcontentContainertop,
            'expandedcontentContainerbottom' => $expandedcontentContainerbottom,
            'pageContainer' => !empty($frontendController->page['tx_t3sbootstrap_container']) ? TRUE : FALSE,
            'containerError' => $contentObjectConfiguration['settings.']['containerError']
        ];

        if (!empty($contentObjectConfiguration['settings.']['shortcutsremove'])) {
            $pageArguments = $request->getAttribute('routing');
            $currentUid = $pageArguments->getPageId();
            $removeArr = GeneralUtility::trimExplode(',', $contentObjectConfiguration['settings.']['shortcutsremove']);
            if ($processedData['data']['pid'] !== $currentUid && $processedData['data']['pid'] !== $footerPid) {
                // if contentByPid for collapsible_accordion or tabs_tab
                foreach($removeArr as $remove) {
                    if (str_contains($processedData['data']['frame_class'], substr($remove,6))) {
                        $processedData['data']['frame_class'] = 'default';
                    }
                }
            }
            if ($cType === 'shortcut' && !empty($parentCType)) {
                // remove a class or any string from shortcuts if in parent ce/wrapper
                foreach($removeArr as $remove) {
                    if (str_contains($processedData['shortcuts'], $remove)) {
                        $processedData['shortcuts'] = $this->removeChar($processedData['shortcuts'], $remove);
                    }
                }
            }
        }

        // class
        $classHelper = GeneralUtility::makeInstance(ClassHelper::class);
        $class = $classHelper->getDefaultClass($processedData['data'], $flexconf, $extConf['cTypeClass'], $sectionMenuClass);
        $processedData['class'] = !empty($processedData['class']) ? $processedData['class'].' '.$class : $class;

        // header class
        $processedData['header'] = $classHelper->getHeaderClass($processedData['data']);

        // style
        $styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
        $processedData['style'] = $styleHelper->getBgColor($processedData['data']);

        if (str_contains(self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) && $cType !== 'list') {
            $isVideo = !empty($processedData['isVideo']) ? true : false;
            $containerClass = $classHelper->getTxContainerClass($processedData['data'], $flexconf, $isVideo);
            $processedData['class'] .= $containerClass ? ' '.$containerClass : '';
            $processedData['isTxContainer'] = true;
        }

        #
        # T3SB Elements
        #
        if (str_contains(self::T3SBS_ELEMENTS, $cType)) {
            if ($cType === 't3sbs_mediaobject') {
                $processedData = GeneralUtility::makeInstance(Mediaobject::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 't3sbs_card') {
                $processedData = GeneralUtility::makeInstance(Card::class)
                ->getProcessedData($processedData, $flexconf, $parentflexconf);
            }
            if ($cType === 't3sbs_carousel') {
                $processedData = GeneralUtility::makeInstance(Carousel::class)
                ->getProcessedData($processedData, $flexconf, $parentflexconf, $extConf['animateCss']);
            }
            if ($cType === 't3sbs_button') {
                $processedData = GeneralUtility::makeInstance(Button::class)
                ->getProcessedData($processedData, $flexconf, $parentflexconf);
            }
            if ($cType === 't3sbs_toast') {
                $processedData = GeneralUtility::makeInstance(Toast::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 't3sbs_assets') {
                $pi_flexconf = $flexFormService->convertFlexFormContentToArray($processedData['data']['pi_flexform']);
                if (!empty($pi_flexconf)) {
                    $processedData['assets']['jquery'] = $pi_flexconf['settings']['jquery'];
                    $processedData['assets']['priority'] = $pi_flexconf['settings']['priority'];
                }
                if (!empty($processedData['cssfiles']) && is_array($processedData['cssfiles'])) {
                    GeneralUtility::makeInstance(AssetHelper::class)->addCSS($processedData['cssfiles']);
                }
                if (!empty($processedData['jsfiles']) && is_array($processedData['jsfiles'])) {
                    GeneralUtility::makeInstance(AssetHelper::class)->addJS($processedData['jsfiles'], (int) $processedData['assets']['priority']);
                }
            }
            #if ( $cType == 't3sbs_fluidtemplate' ) {}
            #if ( $cType == 't3sbs_gallery' ) {}
        }

        #
        # Grid container
        #
        if (str_contains(self::TX_CONTAINER_GRID, $cType)) {
            if ($cType === 'two_columns') {
                $processedData = GeneralUtility::makeInstance(TwoColumns::class)
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']['bgMediaQueries']);
            }
            if ($cType === 'three_columns') {
                $processedData = GeneralUtility::makeInstance(ThreeColumns::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'four_columns') {
                $processedData = GeneralUtility::makeInstance(FourColumns::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'six_columns') {
                $processedData = GeneralUtility::makeInstance(SixColumns::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'row_columns') {
                $processedData = GeneralUtility::makeInstance(RowColumns::class)
                ->getProcessedData($processedData, $flexconf);
            }
        }

        #
        # Container/Wrapper
        #
        if (str_contains(self::TX_CONTAINER, $cType) && $cType !== 'list') {
            if ($cType === 'card_wrapper') {
                $processedData = GeneralUtility::makeInstance(CardWrapper::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'button_group') {
                $processedData = GeneralUtility::makeInstance(ButtonGroup::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'background_wrapper') {
                $processedData = GeneralUtility::makeInstance(BackgroundWrapper::class)
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']['bgMediaQueries']);
            }
            if ($cType === 'parallax_wrapper') {
                $processedData = GeneralUtility::makeInstance(ParallaxWrapper::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'collapsible_container') {
		        $processedData['appearance'] = !empty($flexconf['appearance']) ? $flexconf['appearance'] : '';
		        if ($flexconf['appearance'] === 'accordion') {
		            $processedData['flush'] = !empty($flexconf['flush']) ? ' accordion-flush' : '';
		        }
            }
            if ($cType === 'carousel_container') {
                $processedData = GeneralUtility::makeInstance(CarouselContainer::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'collapsible_accordion') {
                $processedData = GeneralUtility::makeInstance(CollapsibleAccordion::class)
                ->getProcessedData($processedData, $flexconf, $parentflexconf);
            }
            if ($cType === 'modal') {
                $processedData = GeneralUtility::makeInstance(Modal::class)
                ->getProcessedData($processedData, $flexconf);
            }

            if ($cType === 'tabs_container') {
                $processedData = GeneralUtility::makeInstance(TabsContainer::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'tabs_tab') {
                $processedData = GeneralUtility::makeInstance(TabsContainer::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'masonry_wrapper') {
                $processedData = GeneralUtility::makeInstance(MasonryWrapper::class)
                ->getProcessedData($processedData, $flexconf);
            }

            if ($cType === 'swiper_container') {
                $processedData = GeneralUtility::makeInstance(SwiperContainer::class)
                ->getProcessedData($processedData, $flexconf);
            }
            if ($cType === 'toast_container') {
                $processedData = GeneralUtility::makeInstance(ToastContainer::class)
                ->getProcessedData($processedData, $flexconf, $contentObjectConfiguration['settings.']['navbarEnable']);
            }
            #if ( $cType == 'autoLayout_row' ) {}
            #if ( $cType == 'container' ) {}
            #if ( $cType == 'listGroup_wrapper' ) {}
        }

        #
        # default content elements
        #
        if (!str_contains(self::T3SBS_ELEMENTS.','.self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType)) {
            if (substr($cType, 0, 4) === 'menu') {
                $processedData = GeneralUtility::makeInstance(Menu::class)->getProcessedData($processedData, $flexconf, $cType);
            }
            if ($cType === 'table') {
                $processedData = GeneralUtility::makeInstance(Table::class)->getProcessedData($processedData, $flexconf);
            }
        }

        #
        # plug-ins
        #
        if ($cType === 'news_newsdetail') {
            $processedData['lightBox'] = true;
        }

        // media
        if ($processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] || $cType === 't3sbs_gallery') {
            $mediaElementHelper = GeneralUtility::makeInstance(MediaElementHelper::class);
            $processedData = $mediaElementHelper->getProcessedData($processedData, $extConf, $contentObjectConfiguration['settings.']['breakpoint'], $parentflexconf);
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $fileObjects = $fileRepository->findByRelation('tt_content ', 'assets', $processedData['data']['uid']);
            $fileParts = [];
            $processedData['addmedia']['ratioClass'] = 'ratio-16x9';
            $processedData['addmedia']['origImageZoom'] = $processedData['data']['tx_t3sbootstrap_zoom_orig'];
            foreach ($fileObjects as $key=>$fileObject) {

                if ($fileObject->getType() === 4) {
                    $fileConfig = $fileObject->getStorage()->getConfiguration();
                    $filePath = substr($fileConfig['basePath'], 0, -1).explode('.', $fileObject->getIdentifier())[0];
                    if (file_exists($filePath.'.png')) {
                        $fileParts[$key]['poster'] = $filePath.'.png';
                    } elseif (file_exists($filePath.'.jpg')) {
                        $fileParts[$key]['poster'] = $filePath.'.jpg';
                    } else {
                        $fileParts[$key]['poster'] = '';
                    }
                    if (array_key_exists('ratio', $extConf) && $extConf['ratio'] === '1'
                    && !empty($fileObject->getProperties()['tx_t3sbootstrap_video_ratio'])) {
                        $properties = $fileObject->getProperties();
                        if (str_contains($properties['tx_t3sbootstrap_video_ratio'], ':')) {
                            $ratioArr = explode(':', $properties['tx_t3sbootstrap_video_ratio']);
                        } elseif (str_contains($properties['tx_t3sbootstrap_video_ratio'], 'x')) {
                            $ratioArr = explode('x', $properties['tx_t3sbootstrap_video_ratio']);
                        }
                        $x = $ratioArr[0].'x'.$ratioArr[1];
                        $y = $ratioArr[1].' / '.$ratioArr[0].' * 100%';
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
            $processedData['newLine'] = !empty($flexconf['newLine']) ? true : false;
            $processedData['class'] .= $classHelper->getAutoLayoutClass($flexconf);
        }

        // child of container
        if ($parentCType === 'container') {
            $processedData['class'] .= $classHelper->getContainerClass($parentflexconf, $flexconf);
        }

        $processedData['dataAttr'] = '';
        if (!empty($processedData['data']['tx_content_animations_animation'])) {
            $completeAnimationSettings = $this->generateAnimationAttributeSettingsFromAnimationsArray($processedData['data']);
            $processedData['dataAttr'] = !empty($completeAnimationSettings) ? $completeAnimationSettings : '';
            $processedData['dataAnimate'] = '';
            $processedData['isAnimateCss'] = false;
            $processedData['animateCssRepeat'] = false;
            $flexconf['animate'] = '';
        }

        // container class
        $defaultHelper = GeneralUtility::makeInstance(DefaultHelper::class);
        $processedData = $defaultHelper->getContainerClass(  $processedData,  $extConf['container'],   $containerConfig);

        // defaults
        $processedData = $defaultHelper->getDefaults(
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

        // chapter
        if ( !empty($extConf['chapter']) && !empty($processedData['data']['tx_t3sbootstrap_chapter']) ) {
			$processedData = $this->getChapterIndex($processedData, $request);
            if ( $processedData['data']['tx_t3sbootstrap_chapter'] === '1' ) {
                $chapter = $trimClass.' main-chapter';
            } else {
                $chapter = $trimClass.' sub-chapter';
            }
			$chapterClass = $chapter.' chapter-indent';
            $processedData['classAttr'] = !empty($chapterClass) ? ' class="'.$chapterClass.'"' : '';
            $processedData['trimClass'] = !empty($chapterClass) ? $chapterClass : '';
        } else {
            $processedData['classAttr'] = !empty($trimClass) ? ' class="'.$trimClass.'"' : '';
            $processedData['trimClass'] = $trimClass;
        }

        return $processedData;
    }


    /**
     * @param array $animationSettingsArray
     * @return string
     */
    private function generateAnimationAttributeSettingsFromAnimationsArray(array $animationSettingsArray): string
    {
        $animationSettings = '';

        foreach ($animationSettingsArray as $key => $value) {
            if (str_starts_with($key, 'tx_content_animations_')) {
                if ($key === 'tx_content_animations_animation') {
                    $newphrase = str_replace('tx_content_animations_animation', 'data-aos', $key);
                    $animationSettings .= $newphrase . '="' . $value . '" ';
                } else {
                    $newphrase = str_replace('tx_content_animations_', 'data-aos-', $key);
                    $animationSettings .= $newphrase . '="' . $value . '" ';
                }
            }
        }
        return ' '.$animationSettings;
    }


    function removeChar(string $s, string $c): string
    {
        $s = str_replace($c, '', $s);
        if (str_contains($s, $c)) {
            self::removeChar($s, $c);
        }
        return $s;
    }


    function getChapterIndex(array $processedData, ServerRequestInterface $request): array
    {
        $pageArguments = $request->getAttribute('routing');
        $currentPageUid = $pageArguments->getPageId();

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('uid', 'header', 'tx_t3sbootstrap_chapter')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($currentPageUid, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            )
            ->executeQuery();

        $i = 0;
        $e = 0;

        while ($row = $result->fetchAssociative()) {
            if (!empty($row['tx_t3sbootstrap_chapter'])) {
                if ( $row['tx_t3sbootstrap_chapter'] == 1 ) {
                    $i++;
                    $e = 0;
                }
                $ld = '.'. $e;
                if ($e === 0) $ld = '';
                $erg[$row['uid']] = $row;
                $erg[$row['uid']]['index'] = '0.' . $i . $ld;
                $e++;
            }
        }

        $uid = $processedData['data']['uid'];

		$processedData['chapter-index'] = $erg[$uid]['index'];

        return $processedData;
    }

}
