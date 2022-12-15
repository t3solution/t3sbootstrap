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
use T3SBS\T3sbootstrap\Wrapper\CollapsibleContainer;
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
	const TX_CONTAINER_GRID = 'two_columns,three_columns,four_columns,six_columns,row_columns';
	const T3SBS_ELEMENTS = 't3sbs_mediaobject,t3sbs_card,t3sbs_carousel,t3sbs_button,t3sbs_fluidtemplate,t3sbs_gallery,t3sbs_toast';
	const TX_CONTAINER = 'button_group,background_wrapper,parallax_wrapper,autoLayout_row,container,carousel_container,collapsible_container,collapsible_accordion,modal,tabs_container,tabs_tab,listGroup_wrapper,masonry_wrapper,swiper_container,toast_container,card_wrapper';

	/**
	 * Process data
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration,	 array $processedData)
	{
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		$cType = $processedData['data']['CType'];
		$parentCType = '';
		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexconf = $flexFormService->convertFlexFormContentToArray($processedData['data']['tx_t3sbootstrap_flexform']);
		$parentflexconf = [];
		$parentContainer = [];
		$parentUid = $processedData['data']['tx_container_parent'];

		$t3sbsElement = FALSE;
		if ( str_contains(self::T3SBS_ELEMENTS.','.self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) && $cType !== 'list' ) {
			$t3sbsElement = TRUE;
		}

		$flexformHelper = GeneralUtility::makeInstance(FlexformHelper::class);
		if (is_string($cType)) {
			$flexconf = $flexformHelper->addMissingElements($flexconf, $cType, $t3sbsElement);
		}
		if ($parentUid) {
			$parentData = BackendUtility::getRecord('tt_content', $parentUid, 'uid, CType, tx_t3sbootstrap_flexform, tx_container_parent');
			$parentCType = $parentData['CType'];
			$parentflexconf = $flexFormService->convertFlexFormContentToArray($parentData['tx_t3sbootstrap_flexform']);
			if (is_string($parentCType)) {
				$parentflexconf = $flexformHelper->addMissingElements($parentflexconf, $parentCType, $t3sbsElement);
			}
			$parentContainer = $parentData['tx_container_parent'];
		}

		$processedData['parentCType'] = $parentCType;
		$processedData['isTxContainer'] = FALSE;
		$processedData['dataAnimate'] = '';
		$processedData['isAnimateCss'] = FALSE;
		$processedData['animateCssRepeat'] = FALSE;
		$processedData['codesnippet'] = FALSE;
		$processedData['containsVideo'] = FALSE;
		$processedData['containerError'] = FALSE;
		$processedData['data']['configuid'] = (int)$processorConfiguration['configuid'];
		$processedData['header_fontawesome'] = '';

		// class
		$classHelper = GeneralUtility::makeInstance(ClassHelper::class);
		$class = $classHelper->getDefaultClass($processedData['data'], $flexconf, $extConf['cTypeClass']);
		$processedData['class'] = !empty($processedData['class']) ? $processedData['class'].' '.$class : $class;

		// header class
		$processedData['header'] = $classHelper->getHeaderClass($processedData['data']);

		// style
		$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
		$processedData['style'] = $styleHelper->getBgColor($processedData['data']);

		if ( str_contains(self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) && $cType !== 'list' ) {
			$isVideo = !empty($processedData['isVideo']) ? TRUE : FALSE;
			$containerClass = $classHelper->getTxContainerClass($processedData['data'], $flexconf, $isVideo);
			$processedData['class'] .= $containerClass ? ' '.$containerClass : '';
			$processedData['isTxContainer'] = TRUE;
		}

		#
		# T3SB Elements
		#
		if ( str_contains(self::T3SBS_ELEMENTS, $cType) ) {
			if ( $cType == 't3sbs_mediaobject' ) {
				$processedData = GeneralUtility::makeInstance(Mediaobject::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 't3sbs_card' ) {
				$processedData = GeneralUtility::makeInstance(Card::class)
				->getProcessedData($processedData, $flexconf, $parentflexconf);
			}
			if ( $cType == 't3sbs_carousel' ) {
				$processedData = GeneralUtility::makeInstance(Carousel::class)
				->getProcessedData($processedData, $flexconf, $parentflexconf, $extConf['animateCss']);
			}
			if ( $cType == 't3sbs_button' ) {
				$processedData = GeneralUtility::makeInstance(Button::class)
				->getProcessedData($processedData, $flexconf, $parentflexconf);
			}
			if ( $cType == 't3sbs_toast' ) {
				$processedData = GeneralUtility::makeInstance(Toast::class)
				->getProcessedData($processedData, $flexconf);
			}
			#if ( $cType == 't3sbs_fluidtemplate' ) {}
			#if ( $cType == 't3sbs_gallery' ) {}
		}

		#
		# Grid container
		#
		if ( str_contains(self::TX_CONTAINER_GRID, $cType) ) {
			if ( $cType == 'two_columns' ) {
				$processedData = GeneralUtility::makeInstance(TwoColumns::class)
				->getProcessedData($processedData, $flexconf, (bool)$contentObjectConfiguration['settings.']['webp'], $contentObjectConfiguration['settings.']['bgMediaQueries']);
			}
			if ( $cType == 'three_columns' ) {
				$processedData = GeneralUtility::makeInstance(ThreeColumns::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'four_columns' ) {
				$processedData = GeneralUtility::makeInstance(FourColumns::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'six_columns' ) {
				$processedData = GeneralUtility::makeInstance(SixColumns::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'row_columns' ) {
				$processedData = GeneralUtility::makeInstance(RowColumns::class)
				->getProcessedData($processedData, $flexconf);
			}
		}

		#
		# Container/Wrapper
		#
		if ( str_contains(self::TX_CONTAINER, $cType) && $cType !== 'list' ) {
			if ( $cType == 'card_wrapper' ) {
				$processedData = GeneralUtility::makeInstance(CardWrapper::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'button_group' ) {
				$processedData = GeneralUtility::makeInstance(ButtonGroup::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'background_wrapper' ) {
				$processedData = GeneralUtility::makeInstance(BackgroundWrapper::class)
				->getProcessedData($processedData, $flexconf, (bool)$contentObjectConfiguration['settings.']['webp'], $contentObjectConfiguration['settings.']['bgMediaQueries']);
			}
			if ( $cType == 'parallax_wrapper' ) {
				$processedData = GeneralUtility::makeInstance(ParallaxWrapper::class)
				->getProcessedData($processedData, $flexconf, (bool)$contentObjectConfiguration['settings.']['webp']);
			}
			if ( $cType == 'collapsible_container' ) {
				$processedData = GeneralUtility::makeInstance(CollapsibleContainer::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'carousel_container' ) {
				$processedData = GeneralUtility::makeInstance(CarouselContainer::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'collapsible_accordion' ) {
				$processedData = GeneralUtility::makeInstance(CollapsibleAccordion::class)
				->getProcessedData($processedData, $flexconf, $parentflexconf);
			}
			if ( $cType == 'modal' ) {
				$processedData = GeneralUtility::makeInstance(Modal::class)
				->getProcessedData($processedData, $flexconf);
			}

			if ( $cType == 'tabs_container' ) {
				$processedData = GeneralUtility::makeInstance(TabsContainer::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'tabs_tab' ) {
				$processedData = GeneralUtility::makeInstance(TabsContainer::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'masonry_wrapper' ) {
				$processedData = GeneralUtility::makeInstance(MasonryWrapper::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'swiper_container' ) {
				$processedData = GeneralUtility::makeInstance(SwiperContainer::class)
				->getProcessedData($processedData, $flexconf);
			}
			if ( $cType == 'toast_container' ) {
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
		if ( !str_contains(self::T3SBS_ELEMENTS.','.self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) ) {
			if ( substr($cType, 0, 4) == 'menu' ) {
				$processedData = GeneralUtility::makeInstance(Menu::class)->getProcessedData($processedData, $flexconf, $cType);
			}
			if ( $cType == 'table' ) {
				$processedData = GeneralUtility::makeInstance(Table::class)->getProcessedData($processedData, $flexconf);
			}
		}

		#
		# plug-ins
		#
		#if ( $cType == 'list' ) {}

		// media
		if ( $processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] ) {
			$mediaElementHelper = GeneralUtility::makeInstance(MediaElementHelper::class);
			$processedData = $mediaElementHelper->getProcessedData($processedData, $extConf, $contentObjectConfiguration['settings.']['breakpoint']);
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$fileObjects = $fileRepository->findByRelation('tt_content ', 'assets', 875);
			$fileParts = [];
			foreach ($fileObjects as $key=>$fileObject) {
				if ( $fileObject->getType() === 4 ) {
					$fileConfig = $fileObject->getStorage()->getConfiguration();
					$filePath = substr($fileConfig['basePath'], 0, -1).explode('.', $fileObject->getIdentifier())[0];
					if (file_exists($filePath.'.png')) {
						$fileParts[$key]['poster'] = $filePath.'.png';
					} elseif (file_exists($filePath.'.jpg')) {
						$fileParts[$key]['poster'] = $filePath.'.jpg';
					} else {
						$fileParts[$key]['poster'] = '';
					}
				}
			}
			$processedData['posters'] = $fileParts;

			if (!empty($flexconf['zoom']) || !empty($parentflexconf['zoom'])) {
				$processedData['lightBox'] = TRUE;
			}
			// lightbox
			if ( $cType == 't3sbs_gallery' || !empty($processedData['data']['image_zoom']) ) {
				$processedData['lightBox'] = TRUE;
			}
		}

		// codesnippet
		if ( $extConf['codesnippet'] && $processedData['data']['bodytext'] ) {
			if (str_contains($processedData['data']['bodytext'], '<pre>')) {
				$processedData['codesnippet'] = TRUE;
			}
		}

		// child of autoLayout_row
		if ( $parentCType == 'autoLayout_row' ) {
			$processedData['newLine'] = $flexconf['newLine'] ? TRUE : FALSE;
			$processedData['class'] .= $classHelper->getAutoLayoutClass($flexconf);
		}

		// child of container
		if ( $parentCType === 'container' ) {
			$processedData['class'] .= $classHelper->getContainerClass($parentflexconf, $flexconf);
		}

		// container class
		$defaultHelper = GeneralUtility::makeInstance(DefaultHelper::class);
		$processedData = $defaultHelper->getContainerClass($processedData, $extConf['container']);

		// defaults
		$processedData = $defaultHelper->getDefaults($processedData, $flexconf, $extConf,
		 (int)$processorConfiguration['defaultHeaderType'], $processorConfiguration['contentMarginTop'], $extConf['animateCss'], $parentCType);

		// trim header
		$processedData['data']['header'] = !empty($processedData['data']['header']) ? trim($processedData['data']['header']) : '';

		$processedData['style'] .= ' '.$processedData['data']['tx_t3sbootstrap_extra_style'];
		$processedData['style'] = trim($processedData['style']);
		$processedData['styleAttr'] = !empty($processedData['style']) ? ' style="'.$processedData['style'].'"' : '';
		$processedData['styleInline'] = !empty($processedData['style']) ? '#c'.$processedData['data']['uid'].' {'.$processedData['style'].'}' : '';

		$processedData['trimClass'] = !empty(trim($processedData['class'])) ? trim($processedData['class']) : '';
		$processedData['class'] = !empty($processedData['trimClass']) ? ' '.$processedData['trimClass'] : '';
		$processedData['classAttr'] = !empty($processedData['trimClass']) ? ' class="'.trim($processedData['class']).'"' : '';

		if ( !empty($processedData['data']['tx_t3sbootstrap_header_fontawesome']) ) {
			$processedData['headerFontawesome'] = '<i class="'.$processedData['data']['tx_t3sbootstrap_header_fontawesome'].' me-1"></i> ';
		}

		return $processedData;
	}
}
