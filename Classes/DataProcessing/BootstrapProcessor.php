<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use T3SBS\T3sbootstrap\Helper\ClassHelper;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Helper\DefaultHelper;
use T3SBS\T3sbootstrap\Helper\ContainerHelper;
use T3SBS\T3sbootstrap\Helper\ContainerGridHelper;
use T3SBS\T3sbootstrap\Helper\T3sbsElementHelper;
use T3SBS\T3sbootstrap\Helper\ContentElementHelper;
use T3SBS\T3sbootstrap\Helper\MediaElementHelper;
use T3SBS\T3sbootstrap\Helper\FlexformHelper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BootstrapProcessor implements DataProcessorInterface
{

	const TX_CONTAINER = 'card_wrapper,button_group,background_wrapper,parallax_wrapper,autoLayout_row,container,carousel_container,collapsible_container,collapsible_accordion,modal,tabs_container,tabs_tab,listGroup_wrapper,masonry_wrapper,swiper_container,toast_container';

	const TX_CONTAINER_GRID = 'two_columns,three_columns,four_columns,six_columns,row_columns';

	const T3SBS_ELEMENTS = 't3sbs_mediaobject,t3sbs_card,t3sbs_carousel,t3sbs_button,t3sbs_fluidtemplate,t3sbs_gallery,t3sbs_toast';


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
		if ( str_contains( self::TX_CONTAINER_GRID.','.self::TX_CONTAINER.','.self::T3SBS_ELEMENTS, $cType) && $cType !== 'list' ) {
			$t3sbsElement = TRUE;
		}

		$flexformHelper = GeneralUtility::makeInstance(FlexformHelper::class);
		$flexconf = $flexformHelper->addMissingElements($flexconf, $cType, $t3sbsElement);

		if ($parentUid) {
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$statement = $queryBuilder
				->select('uid', 'CType', 'tx_t3sbootstrap_flexform', 'tx_container_parent')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($parentUid, \PDO::PARAM_INT))
				)
				->execute()
				->fetch();

			$parentUid = $statement['uid'];
			$parentCType = $statement['CType'];
			$parentflexconf = $flexFormService->convertFlexFormContentToArray($statement['tx_t3sbootstrap_flexform']);
			$parentflexconf = $flexformHelper->addMissingElements($parentflexconf, $parentCType, $t3sbsElement);
			$parentContainer = $statement['tx_container_parent'];
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

		// all tx_container
		if ( str_contains(self::TX_CONTAINER_GRID.','.self::TX_CONTAINER, $cType) ) {

			// tx_container - Grid system only
			if ( str_contains(self::TX_CONTAINER_GRID, $cType) && $cType !== 'list' ) {
				$ContainerGridHelper = GeneralUtility::makeInstance(ContainerGridHelper::class);
				$processedData = $ContainerGridHelper->getProcessedData($processedData, $flexconf, (bool)$contentObjectConfiguration['settings.']['webp']);
			}

			// tx_container - NO Grid system
			if ( str_contains(self::TX_CONTAINER, $cType) && $cType !== 'list' ) {
				$containerHelper = GeneralUtility::makeInstance(ContainerHelper::class);

				$processedData = $containerHelper->getProcessedData($processedData, $flexconf, $parentflexconf, (bool)$contentObjectConfiguration['settings.']['webp'],
				 $contentObjectConfiguration['settings.']['bgMediaQueries'], $contentObjectConfiguration['settings.']['navbarEnable']);
			}

			$isVideo = !empty($processedData['isVideo']) ? TRUE : FALSE;
			$containerClass = $classHelper->getTxContainerClass($processedData['data'], $flexconf, $isVideo);
			$processedData['class'] .= $containerClass ? ' '.$containerClass : '';
			$processedData['isTxContainer'] = TRUE;

		} else {
			if ( str_contains(self::T3SBS_ELEMENTS, $cType) && $cType !== 'list' ) {
				// t3sbs_* content elements
				$t3sbsElementHelper = GeneralUtility::makeInstance(T3sbsElementHelper::class);
				$processedData = $t3sbsElementHelper->getProcessedData($processedData, $flexconf, $parentflexconf, $extConf['animateCss']);
			} else {
				// default content elements
				$contentElementHelper = GeneralUtility::makeInstance(ContentElementHelper::class);
				$processedData = $contentElementHelper->getProcessedData($processedData, $flexconf);
			}
		}

		// media
		if ( $processedData['data']['assets'] || $processedData['data']['image'] || $processedData['data']['media'] ) {
			$mediaElementHelper = GeneralUtility::makeInstance(MediaElementHelper::class);
			$processedData = $mediaElementHelper->getProcessedData($processedData, $extConf, $contentObjectConfiguration['settings.']['breakpoint']);
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
		$processedData['data']['header'] = trim($processedData['data']['header']);

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

		// EXT News
		if ( $extConf['extNews'] && $cType == 'list' && $processedData['data']['list_type'] == 'news_pi1' ) {
			$processedData['data']['style'] = $processedData['style'];
			$processedData['data']['styleAttr'] = $processedData['styleAttr'];
			$processedData['data']['class'] = $processedData['class'];
			$processedData['data']['classAttr'] = $processedData['classAttr'];
		}

		return $processedData;
	}
}
