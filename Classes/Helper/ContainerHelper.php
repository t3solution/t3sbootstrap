<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use T3SBS\T3sbootstrap\Helper\WrapperHelper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ContainerHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf, bool $webp, string $bgMediaQueries, string $navbarEnable): array
	{

		$wrapperHelper = GeneralUtility::makeInstance(WrapperHelper::class);
		$cType = $processedData['data']['CType'];
		$filesFromRepository = [];

		/**
		 * Card Wrapper
		 */
		if ( $cType == 'card_wrapper' ) {
			$processedData = $wrapperHelper->getCardWrapper($processedData, $flexconf);
			$processedData['visibleCards'] = !empty($flexconf['visibleCards']) ? (int)$flexconf['visibleCards'] : 4;
			$processedData['gutter'] = !empty($flexconf['gutter']) ? (int)$flexconf['gutter'] : 0;
		}

		/**
		 * Button group
		 */
		if ( $cType == 'button_group' ) {
			$processedData['class'] .= !empty($flexconf['vertical']) ? ' btn-group-vertical' : ' btn-group';
			$processedData['class'] .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] != 'default' ? ' '.$flexconf['btnsize']: '';
			$processedData['buttonGroupClass'] = !empty($flexconf['align']) ? $flexconf['align'] : '';

			$processedData['visiblePart'] = '';
			if ( !empty($flexconf['fixedPosition']) ) {
				$processedData['buttonGroupClass'] .= ' d-none fixedGroupButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
				$processedData['class'] .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['class'] .= !empty($flexconf['vertical']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['fixedButton'] = TRUE;
				if ( $flexconf['slideIn'] && $flexconf['vertical'] && $flexconf['fixedPosition'] == 'right' ) {
					$processedData['class'] .= ' slideInButton';
					$processedData['visiblePart'] = $flexconf['visiblePart'] ? (int)$flexconf['visiblePart'] : 33;
				}
			}
		}

		/**
		 * Background Wrapper
		 */
		if ( $cType == 'background_wrapper') {

			$processedData = $wrapperHelper->getBackgroundWrapper($processedData, $flexconf, $webp, $bgMediaQueries);
			$vMute = !empty($flexconf['videoMute']) ? $flexconf['videoMute'] : 0;
			$mute = !empty($processedData['videoAutoPlay']) ? 1 : $vMute;
			if (!empty($flexconf['videoControls'])) {
				$processedData['controlStyle'] = '';
			} elseif ( empty($processedData['videoAutoPlay']) ) {
				$processedData['controlStyle'] = '';
			} else {
				$processedData['controlStyle'] = ' pointer-events:none;';
			}

			if ( !empty($processedData['videoId']) ) {
				$params = '?autoplay='.$processedData['videoAutoPlay'].'&loop='.$flexconf['videoLoop'].'&playlist='.
				$processedData['videoId'].'&mute='.$mute.'&rel=0&showinfo=0&controls='.$flexconf['videoControls'].'&modestbranding='.$flexconf['videoControls'];
				$processedData['youtubeParams'] = $params;
			}
		}

		/**
		 * Parallax Wrapper
		 */
		if ( $cType == 'parallax_wrapper' && $processedData['data']['assets'] ) {
			$processedData = $wrapperHelper->getParallaxWrapper($processedData, $flexconf, $webp);
		}

		/**
		 * Carousel container
		 */
		if ( $cType == 'carousel_container' ) {
			$processedData = $wrapperHelper->getCarouselContainer($processedData, $flexconf);
		}

		/**
		 * Swiper container
		 */
		if ( $cType == 'swiper_container' ) {
			$processedData['swiperCss'] = !empty($flexconf['swiperCss']) ? $flexconf['swiperCss'] : '';
			$processedData['swiperJs'] = $flexconf['swiperJs'] ?? '';
			$processedData['sliderStyle'] = $flexconf['sliderStyle'];
			$processedData['width'] = $flexconf['width'];
			$processedData['ratio'] = $flexconf['ratio'];
			$processedData['slidesPerView'] = (int)$flexconf['slidesPerView'] ?: 4;
			$processedData['breakpoints576'] = (int)$flexconf['breakpoints576'] ?: 2;
			$processedData['breakpoints768'] = (int)$flexconf['breakpoints768'] ?: 3;
			$processedData['breakpoints992'] = (int)$flexconf['breakpoints992'] ?: 4;
			$processedData['slidesPerGroup'] = (int)$flexconf['slidesPerGroup'];
			$processedData['spaceBetween'] = (int)$flexconf['spaceBetween'];
			$processedData['loop'] = (int)$flexconf['loop'];
			$processedData['navigation'] = (int)$flexconf['navigation'];
			$processedData['pagination'] = (int)$flexconf['pagination'];
			$processedData['autoplay'] = (int)$flexconf['autoplay'];
			$processedData['delay'] = !empty($flexconf['autoplay']) ? (int)$flexconf['delay'] : 99999999;
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$statement = $queryBuilder
				->select('uid')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($processedData['data']['sys_language_uid'], \PDO::PARAM_INT)),
					$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
				)
				->execute()
				->fetchAll();
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			foreach($statement as $element) {
				$filesFromRepository[$element['uid']] = $fileRepository->findByRelation('tt_content', 'assets', $element['uid']);
			}
			$processedData['swiperSlides'] = $filesFromRepository;
		}

		/**
		 * Collapse Container
		 */
		if ( $cType == 'collapsible_container' ) {
			$processedData['appearance'] = $flexconf['appearance'];
			if ($flexconf['appearance'] == 'accordion') {
				$processedData['flush'] = $flexconf['flush'] ? ' accordion-flush' : '';
			}
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$statements = $queryBuilder
				->select('tx_t3sbootstrap_flexform', 'tx_t3sbootstrap_header_fontawesome', 'tx_t3sbootstrap_header_class')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($processedData['data']['sys_language_uid'], \PDO::PARAM_INT)),
					$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
				)
				->execute()
				->fetchAll();

			$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
			foreach ($statements as $key=>$statement) {
				$flexformArr[$key] = $flexFormService->convertFlexFormContentToArray($statement['tx_t3sbootstrap_flexform']);
				$headerExtraClassArr[$key] = !empty($statement['tx_t3sbootstrap_header_class']) ? $statement['tx_t3sbootstrap_header_class'] : '';
				$headerFontawesomeArr[$key] = !empty($statement['tx_t3sbootstrap_header_fontawesome'])
				 ? '<i class="'.$statement['tx_t3sbootstrap_header_fontawesome'].'"></i> ' : '';
			}
			$processedData['flexformArr'] = $flexformArr;
			$processedData['headerExtraClassArr'] = $headerExtraClassArr;
			$processedData['headerFontawesomeArr'] = $headerFontawesomeArr;
		}


		/**
		 * Collapsible
		 */
		if ( $cType == 'collapsible_accordion' ) {
			$processedData = $wrapperHelper->getCollapsible($processedData, $flexconf, $parentflexconf);
			$processedData['appearance'] = $parentflexconf['appearance'];
		}


		/**
		 * Modal
		 */
		if ( $cType == 'modal' ) {
			$processedData['modal']['animation'] = $flexconf['animation'];
			$processedData['modal']['size'] = $flexconf['size'];
			$processedData['modal']['button'] = $flexconf['button'];
			$processedData['modal']['style'] = $flexconf['style'];
			if ( $flexconf['buttonText'] ) {
				$processedData['modal']['buttonText'] = $flexconf['buttonText'];
			} elseif ( $processedData['data']['header'] ) {
				$processedData['modal']['buttonText'] = $processedData['data']['header'];
			} else {
				$processedData['modal']['buttonText'] = $processedData['modal']['button'] ? 'Modal-Button' :'Modal-Link';
			}
			if ( $flexconf['fixedPosition'] ) {
				$processedData['modal']['fixedClass'] = 'fixedModalButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
				$processedData['class'] .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['modal']['fixedButton'] = TRUE;
			}
			if ($processedData['data']['header_position']) {
				$headerPosition = $processedData['data']['header_position'];
				if ( $headerPosition == 'left' ) $headerPosition = 'start';
				if ( $headerPosition == 'right' ) $headerPosition = 'end';
				$processedData['class'] .= ' text-'.$headerPosition;
			}

		}

		/**
		 * Tabs / Pills
		 */
		if ( $cType == 'tabs_container'
		 || $cType == 'tabs_tab' ) {

			if ( !empty($flexconf['display_type']) && $flexconf['display_type'] == 'verticalpills') {
				$processedData['pill']['asideWidth'] = (int)$flexconf['aside_width'];
				$processedData['pill']['mainWidth'] = $flexconf['aside_width'] ? 12 - (int)$flexconf['aside_width'] : 9;
			}
			$processedData['tab']['displayType'] = $flexconf['display_type'];
			$processedData['tab']['switchEffect'] =	 $parentflexconf['switch_effect'];
			$processedData['tab']['contentByPid'] =	 $flexconf['contentByPid'];
			$processedData['tab']['fill'] =	 $flexconf['fill'] ? ' '.$flexconf['fill']: '';
		}

		/**
		 * Masonry (masonry_layout)
		 */
		if ( $cType == 'masonry_wrapper' ) {
			$processedData['masonryClass'] = $flexconf['colclass'];
		}

		/**
		 * Toast Container
		 */
		if ( $cType == 'toast_container' ) {
			$processedData['animation'] = $flexconf['animation'] ? 'true' : 'false';
			$processedData['autohide'] = $flexconf['autohide'] ? 'true' : 'false';
			$processedData['delay'] = $flexconf['delay'];
			$processedData['style'] .= !empty($flexconf['toastwidth']) ? ' width:'.$flexconf['toastwidth'].'px;' : '';
			$processedData['cookie'] = $flexconf['cookie'];
			$processedData['expires'] = !empty($flexconf['expires']) ? $flexconf['expires'] : '';
			$processedData['multipleToast'] = $flexconf['multipleToast'];
			$processedData['style'] .= ' z-index:1;';

			if ( $navbarEnable ) {
				if ($flexconf['placement'] && str_starts_with($flexconf['placement'], 'top-0')) {
					$processedData['placement'] = ' '.str_replace('top-0', 'top-70', $flexconf['placement']);
				} else {
					$processedData['placement'] = ' '.$flexconf['placement'];
				}
			}
		}

		return $processedData;
	}

}
