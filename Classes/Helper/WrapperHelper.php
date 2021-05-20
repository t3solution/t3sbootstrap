<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use T3SBS\T3sbootstrap\Utility\YouTubeRenderer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\PageRenderer;


class WrapperHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 * @param boolean	$cdnEnable
	 *
	 * @return array
	 */
	public function getBackgroundWrapper($processedData, $flexconf, $cdnEnable=null, $webp=FALSE): array
	{
		// autoheight
		$processedData['enableAutoheight'] = $flexconf['enableAutoheight'] ? TRUE : FALSE;
		$processedData['addHeight'] = $flexconf['addHeight'];
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];

		// media
		if ( $file ) {
			// VIDEO type
			if ( $file->getType() === 4 ) {
				// youtube
				if ( $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube' ) {
					$processedData['youtube'] = TRUE;
					$processedData['isVideo'] = TRUE;

					$processedData['contentCenter'] = $flexconf['contentCenter'] ? TRUE : FALSE;

					$filter = $flexconf['grayscale'] ? 'grayscale: '.(int)$flexconf['grayscale'].', ' : '';
					$filter .= $flexconf['huerotate'] ? 'hue_rotate: '.(int)$flexconf['huerotate'].', ' : '';
					$filter .= $flexconf['invert'] ? 'invert: '.(int)$flexconf['invert'].', ' : '';
					$filter .= $flexconf['opacity'] ? 'opacity: '.(int)$flexconf['opacity'].', ' : '';
					$filter .= $flexconf['saturate'] ? 'saturate: '.(int)$flexconf['saturate'].', ' : '';
					$filter .= $flexconf['sepia'] ? 'sepia: '.(int)$flexconf['sepia'].', ' : '';
					$filter .= $flexconf['brightness'] ? 'brightness: '.(int)$flexconf['brightness'].', ' : '';
					$filter .= $flexconf['contrast'] ? 'contrast: '.(int)$flexconf['contrast'].', ' : '';
					$filter .= $flexconf['blur'] ? 'blur: '.(int)$flexconf['blur'].', ' : '';

					// if youtube filter
					if ( $filter ) {
						$filter = substr(trim($filter), 0, -1);
						$addFilters = ' var filters = {'.$filter.'}; jQuery(\'.player'.$processedData['data']['uid'].'\').YTPApplyFilters(filters);';
					} else {
						$addFilters = '';
					}

					if ( $cdnEnable ) {
						$cssFile = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/3.3.1/css/jquery.mb.YTPlayer.min.css';
					} else {
						$cssFile = 'fileadmin/T3SB/Resources/Public/CSS/jquery.mb.YTPlayer.min.css';
						$cssFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);
					}

					if ( $flexconf['videoRatio'] == '16/9' || $flexconf['videoRatio'] == 'auto' ) {
						$pH = ((int)$flexconf['bgHeight'] / 16) * 9;
					} else {
						$pH = ((int)$flexconf['bgHeight'] / 4) * 3;
					}

					if ( $cdnEnable ) {
						$jsFooterFile = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/3.3.1/jquery.mb.YTPlayer.min.js';
					} else {
						$jsFooterFile = 'fileadmin/T3SB/Resources/Public/JS/jquery.mb.YTPlayer.min.js';
						$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);
					}

					if ( $flexconf['bgHeight'] ) {
					$inlineJS = '
// Background-video-'.$processedData['data']['uid'].'
$(document).ready(function(){
	jQuery(\'.player'.$processedData['data']['uid'].'\').YTPlayer({ realfullscreen: true, onReady: function(event) {
		$(\'body\').addClass(\'video-loaded\');
	}});'.$addFilters.'
	jQuery(\'.player'.$processedData['data']['uid'].'\').css("padding-bottom", "'.$pH.'%")
});';
					} else {

					$inlineJS = '
$(document).ready(function(){
	jQuery(\'.player'.$processedData['data']['uid'].'\').YTPlayer({ realfullscreen: true, onReady: function(event) {
		$(\'body\').addClass(\'video-loaded\');
	}});'.
	$addFilters.'
});';
					}
					if ($cssFile) {
						$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
						$pageRenderer->addCssFile($cssFile);
					}

					if ($jsFooterFile)
					GeneralUtility::makeInstance(AssetCollector::class)
						->addJavaScript('ytplayerjs', $jsFooterFile);
					if ($inlineJS)
					GeneralUtility::makeInstance(AssetCollector::class)
						->addInlineJavaScript('background-video-'.$processedData['data']['uid'], $inlineJS);
					
					
					$events = $flexconf;
					$events['videoAutoPlay'] = $file->getProperties()['autoplay'];
					$events['uid'] = $processedData['data']['uid'];

					$processedData['youtubeProperty'] = GeneralUtility::makeInstance(YouTubeRenderer::class)->render($file, $events);

				} else {

					if ( $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo' ) {
					// vimeo video
						$processedData['vimeo'] = TRUE;
					} else {
					// local video
						$processedData['file'] = $file;
						// align content items
						$processedData['alignItem'] = $flexconf['alignVideoItem'] != 'none' ? ' '.$flexconf['alignVideoItem'] :'';
						// aspect ratio
						$processedData['aspectRatio'] = $flexconf['aspectRatio'];
						// prepare needed javascript
						$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
						$overlayChild = $queryBuilder
							 ->count('uid')
							 ->from('tt_content')
							 ->where(
								$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
								 )
							 ->execute()
							 ->fetchColumn(0);
						$autoplay = $file->getProperties()['autoplay'];
						$controls = $autoplay ? $flexconf['controls'] : true;
						$loop = $flexconf['loop'];
						$mute = $autoplay ? true : $flexconf['mute'];

						$inlineJS = '
// Background-video-'.$file->getUid().'
var videoElement = document.querySelector(\'#video-'.$file->getUid().' video\');
videoElement.muted = '.$mute.';
videoElement.loop = '.$loop.';';
						if ( $autoplay ) {
							$inlineJS .= '
videoElement.pause();
videoElement.currentTime = 0;
videoElement.play();
$(\'#video-'.$file->getUid().' video\').attr(\'playsinline\', \'\');';
						}

						if ( $controls ) {
							if ( $overlayChild )
							$inlineJS .= '
$(\'#s-'.$processedData['data']['uid'].' .card-img-overlay\').css(\'bottom\', \'20px\').css(\'top\', \'20px\');';
						} else {
							$inlineJS .= '
$(videoElement).removeAttr("controls");';
						}
						if($inlineJS)
						GeneralUtility::makeInstance(AssetCollector::class)
							 ->addInlineJavaScript('background-video-'.$file->getUid(), $inlineJS);
					}
				}

			} elseif ( $file->getType() === 2 ) {
			// IMAGE
				// orig. image option in flexform
				if ($flexconf['origImage']) {
					$processedData['file'] = $file;
				} else {
					$processedData['bgImage'] = GeneralUtility::makeInstance(BackgroundImageUtility::class)
						->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, TRUE, $flexconf, FALSE, 0, $webp);
					if ($flexconf['paddingTopBottom']) {
						$processedData['style'] .= ' padding: '.$flexconf['paddingTopBottom'].'rem 1rem;';
					}
				}
				// align content items
				$processedData['alignItem'] = $flexconf['alignItem'] ? ' '.$flexconf['alignItem'] :'';

				// image raster
				$processedData['imageRaster'] = $flexconf['imageRaster'] ? ' multiple-' : ' ';

				// Text color - overlay (
				if ( $processedData['data']['tx_t3sbootstrap_textcolor'] ) {
					$processedData['overlayClass'] = ' text-'.$processedData['data']['tx_t3sbootstrap_textcolor'];
				}

				$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
				$processedData['bgColorOverlay'] = $styleHelper->getBgColor($processedData['data'], FALSE);

				$filter = $flexconf['imgGrayscale'] ? ' grayscale('.$flexconf['imgGrayscale'].'%) ' : '';
				$filter .= $flexconf['imgSepia'] ? ' sepia('.$flexconf['imgSepia'].'%) ' : '';
				$filter .= $flexconf['imgOpacity'] ? ' opacity('.$flexconf['imgOpacity'].'%) ' : '';
				if ($filter)
				$processedData['style'] .= 'filter: '.trim($filter).';';

			} else {
				// do nothing - audio file
			}

		} else {
		// NO file - background color only
			// Padding Top & Bottom if no media - add to style
			if ($flexconf['noMediaPaddingTopBottom']) {
				$processedData['style'] .= ' padding: '.$flexconf['noMediaPaddingTopBottom'].'rem 1rem;';
			}
		}

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 *
	 * @return array
	 */
	public function getCardWrapper($processedData, $flexconf): array
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

		$children = $queryBuilder
			->select('*')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
			)
			->orderBy('sorting')
			->execute()
			->fetchAll();

		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

		$processedData['cropMaxCharacters'] = $flexconf['cropMaxCharacters'];

		if (count($children)) {

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);

			foreach ( $children as $key=>$child ) {
				$fileObjects = $fileRepository->findByRelation('tt_content', 'assets', $child['uid']);
				$children[$key] = $flexFormService->convertFlexFormContentToArray($child['pi_flexform']);
				$children[$key]['imgwidth'] = $child['imagewidth'] ?: 576;
				if ($flexconf['card_wrapper'] == 'flipper'){
					$children[$key]['hFa'] = $child['tx_t3sbootstrap_header_fontawesome']
					 ? '<i class="'.$child['tx_t3sbootstrap_header_fontawesome'].' mr-1"></i> ' : '';
					$children[$key]['file'] = $fileObjects;
					$children[$key]['backheader'] = $children[$key]['header']['text'];
					$children[$key]['header'] = $child['header'];
				} else {
					$children[$key]['file'] = $fileObjects[0];
					$children[$key]['header'] = $child['header'];
				}
				$children[$key]['ratio'] = $child['tx_t3sbootstrap_image_ratio'] ?: '0';
				$children[$key]['uid'] = $child['uid'];

				$children[$key]['subheader'] = $child['subheader'];
				$children[$key]['header_link'] = $child['header_link'];
				$children[$key]['header_position'] = $child['header_position'] ? ' text-'.$child['header_position'] :'';
				$children[$key]['tx_t3sbootstrap_header_display'] = $child['tx_t3sbootstrap_header_display'];
				$children[$key]['tx_t3sbootstrap_header_class'] = $child['tx_t3sbootstrap_header_class'];
				$children[$key]['tx_t3sbootstrap_header_fontawesome'] = $child['tx_t3sbootstrap_header_fontawesome'];

				$children[$key]['settings'] = $flexFormService->convertFlexFormContentToArray($child['tx_t3sbootstrap_flexform']);
			}
			$processedData['cards'] = $children;

			if ($flexconf['card_wrapper'] == 'flipper') {

				switch ( count($children) ) {
					 case 1:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-12 col-md-12';
					break;
					 case 2:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-6';
					break;
					 case 3:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
					break;
					 case 4:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-3';
					break;
					 case 6:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-2';
					break;
							 default:
						$processedData['flipper']['class'] = 'col-xs-12 col-sm-6 col-md-4';
				}
			}

			if ($flexconf['card_wrapper'] == 'slider') {
				$processedData['class'] .= ' mx-n3';
				$processedData['visibleCards'] = (int)$flexconf['visibleCards'] ?: 3;
				$processedData['cols'] = floor(12 / $processedData['visibleCards']);
			}
		}
		$processedData['card_wrapper_layout'] = $flexconf['card_wrapper'] ?: '';

		return $processedData;
	}



	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 *
	 * @return array
	 */
	public function getCarouselContainer($processedData, $flexconf): array
	{

		if ( $flexconf['multislider'] ) {

			$cssFile = 'EXT:t3sbootstrap/Resources/Public/Contrib/Multislider/multislider.css';
			$cssFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($cssFile);

			$jsFooterFile = 'EXT:t3sbootstrap/Resources/Public/Contrib/Multislider/multislider.min.js';
			$jsFooterFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($jsFooterFile);

			if ( $flexconf['interval'] ) {
				$options = 'interval: '.intval($flexconf['interval']).', ';
			} else {
				$options = 'interval: 2000, ';
			}
			if ( $flexconf['sliding'] == 'all' ) {
				$options .= 'slideAll: true, ';
			} else {
				if ( $flexconf['sliding'] == 'continuous' ) {
					// disable interval
					$options = 'continuous: true, ';
					$processedData['continuous'] = TRUE;
				} else {
					$options .= '';
				}
			}

			$duration = $flexconf['duration'] ? intval($flexconf['duration']) : 500;
			$options .= 'duration: '.$duration;

			$inlineJS = '
	$(\'#multiSlider-'.$processedData['data']['uid'].'\').multislider({'.$options.'});';

			$block = '#multiSlider-'.$processedData['data']['uid'].' .MS-content .item {width: '.$flexconf['number'].'}';

			if($cssFile) {
				$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
				$pageRenderer->addCssFile($cssFile);
			}
			if($block) {
				$pageRenderer->addCssInlineBlock ('multislider-'.$processedData['data']['uid'], $block);
			}

			if($jsFooterFile)
			GeneralUtility::makeInstance(AssetCollector::class)
				  ->addJavaScript('multisliderjs', $jsFooterFile);
			if($inlineJS)
			GeneralUtility::makeInstance(AssetCollector::class)
				  ->addInlineJavaScript('multisliderinlinejs-'.$processedData['data']['uid'], $inlineJS);

			$processedData['multislider'] = TRUE;

		}

		$processedData['maxWidth'] = $flexconf['width'].'px';
		$processedData['interval'] = $flexconf['interval'];
		$processedData['carouselFade'] = $flexconf['carouselFade'] ? ' carousel-fade': '';

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 *
	 * @return array
	 */
	public function getParallaxWrapper($processedData, $flexconf, $webp=FALSE): array
	{
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];

		if ( $file ) {
			if ( $file->getType() === 4 ) {
				$processedData['video'] = TRUE;
			} else {
				$processedData['parallaxImage'] =
				 GeneralUtility::makeInstance(BackgroundImageUtility::class)
				 ->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE, [], FALSE, FALSE, $webp);
				$processedData['speedFactor'] = $flexconf['speedFactor'];
				$processedData['imageRaster'] = $flexconf['imageRaster'] ? ' multiple-' : ' ';
			}
		}

		if ($flexconf['paddingTopBottom']) {
			$processedData['style'] .= ' padding: '.$flexconf['paddingTopBottom'].'rem 1rem;';
		}

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 *
	 * @return array
	 */
	public function getCollapsible($processedData, $flexconf, $parentflexconf): array
	{
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];

		$processedData['appearance'] = $parentflexconf['appearance'];
		$processedData['show'] = $flexconf['active'] ? ' show' : '';
		$processedData['expanded'] = $flexconf['active'] ? 'true' : 'false';
		$processedData['buttonstyle'] = $flexconf['style'] ? $flexconf['style'] : 'primary';
		$processedData['collapsibleByPid'] = $flexconf['collapsibleByPid'] ?: '';
		$processedData['media'] = $file ? $file : '';

		return $processedData;
	}

}
