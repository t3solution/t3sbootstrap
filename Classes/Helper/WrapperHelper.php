<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Resource\FileRepository;
use T3SBS\T3sbootstrap\Utility\YouTubeRenderer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class WrapperHelper implements SingletonInterface
{
	/**
	 * Returns the $processedData
	 */
	public function getBackgroundWrapper(array $processedData, array $flexconf, bool $webp=FALSE, string $bgMediaQueries='2560,1920,1200,992,768,576'): array
	{
		// autoheight
		$processedData['enableAutoheight'] = !empty($flexconf['enableAutoheight']) ? TRUE : FALSE;
		$processedData['addHeight'] = !empty($flexconf['addHeight']) ? (int)$flexconf['addHeight'] : 0;
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$files = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid']);
		$file = !empty($files) && is_array($files) ? $files[0] : '';

		// media
		if ( $file ) {
			// VIDEO type
			if ( $file->getType() === 4 ) {
				// youtube
				if ( $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube' ) {
					$processedData['youtube'] = TRUE;
					$processedData['isVideo'] = TRUE;
					$processedData['contentPosition'] = $flexconf['contentPosition'];
					$processedData['ytVideo']['bgHeight'] = $flexconf['bgHeight'];
					$processedData['ytVideo']['ytshift'] = $flexconf['ytshift'];
					$processedData['videoAutoPlay'] = $file->getProperties()['autoplay'];
					$youTubeRenderer = GeneralUtility::makeInstance(YouTubeRenderer::class);
					$processedData['videoId'] = $youTubeRenderer->render($file);
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
						$processedData['shift'] = $flexconf['shift'];
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
						$loop = $flexconf['loop'];
						$mute = $autoplay ? true : $flexconf['mute'];

						$processedData['localVideo']['overlayChild'] = $overlayChild;
						$processedData['localVideo']['autoplay'] = $autoplay;
						$processedData['localVideo']['loop'] = $loop;
						$processedData['localVideo']['mute'] = $mute;
					}
				}

			} elseif ( $file->getType() === 2 ) {
			// IMAGE
				// orig. image option in flexform
				if ($flexconf['origImage']) {
					$processedData['file'] = $file;
				} else {
					$bgImage = GeneralUtility::makeInstance(BackgroundImageUtility::class)
						->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, TRUE, $flexconf, FALSE, 0, $webp, $bgMediaQueries);
					$processedData['bgImage'] = $bgImage;
					if ($flexconf['paddingTopBottom']) {
						$processedData['style'] .= ' padding: '.$flexconf['paddingTopBottom'].'rem 0;';
					}
				}
				// align content items
				$processedData['alignItem'] = $flexconf['alignItem'] ? ' '.$flexconf['alignItem'] :'';

				// image raster
				$processedData['imageRaster'] = $flexconf['imageRaster'] ? 'multiple-' : '';

				// Text color - overlay (
				if ( $processedData['data']['tx_t3sbootstrap_textcolor'] ) {
					$processedData['overlayClass'] = ' text-'.$processedData['data']['tx_t3sbootstrap_textcolor'];
				}

				$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
				$processedData['bgColorOverlay'] = $styleHelper->getBgColor($processedData['data'], FALSE);

				$filter = $flexconf['imgGrayscale'] ? ' grayscale('.$flexconf['imgGrayscale'].'%) ' : '';
				$filter .= $flexconf['imgSepia'] ? ' sepia('.$flexconf['imgSepia'].'%) ' : '';
				$filter .= $flexconf['imgOpacity'] && $flexconf['imgOpacity'] != 100 ? ' opacity('.$flexconf['imgOpacity'].'%) ' : '';

				if ($filter)
				$processedData['style'] .= 'filter: '.trim($filter).';';

			} else {
				// do nothing - audio file
			}

		} else {
		// NO file - background color only
			// Padding Top & Bottom if no media - add to style
			if (!empty($flexconf['noMediaPaddingTopBottom'])) {
				$processedData['style'] .= ' padding: '.$flexconf['noMediaPaddingTopBottom'].'rem 0;';
			}
		}

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 */
	public function getCardWrapper(array $processedData, array $flexconf): array
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
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
					 ? '<i class="'.$child['tx_t3sbootstrap_header_fontawesome'].' me-1"></i> ' : '';
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
			// swiperjs
			if ($flexconf['card_wrapper'] == 'slider') {
				$processedData['visibleCards'] = (int)$flexconf['visibleCards'] ?: 3;
				$processedData['cols'] = floor(12 / $processedData['visibleCards']);
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
				$processedData['delay'] = $flexconf['autoplay'] ? (int)$flexconf['delay'] : 99999999;
			}
		}
		$processedData['card_wrapper_layout'] = $flexconf['card_wrapper'] ?: '';

		return $processedData;
	}



	/**
	 * Returns the $processedData
	 */
	public function getCarouselContainer(array $processedData, array $flexconf): array
	{
		$processedData['maxWidth'] = $flexconf['width'] ? $flexconf['width'].'px' : '1440px';
		$processedData['interval'] = $flexconf['interval'];
		$processedData['darkVariant'] = $flexconf['darkVariant'];
		$processedData['carouselFade'] = !empty($flexconf['carouselFade']) ? ' carousel-fade': '';
		$processedData['carouselFade'] .= !empty($flexconf['darkVariant']) ? ' carousel-dark': '';
		$processedData['thumbnails'] = !empty($flexconf['thumbnails']) ? true : false;

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statement = $queryBuilder
			->select('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
			)
			->execute()
			->fetchAll();

		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		foreach($statement as $element) {
			$file = $fileRepository->findByRelation('tt_content', 'assets', $element['uid']);
			if ( !empty($file) ) {
				if ($file[0]->getMimeType() == 'video/mp4' || $file[0]->getMimeType() == 'video/webm' || $file[0]->getMimeType() == 'video/wav'
				 || $file[0]->getMimeType() == 'video/ogg' || $file[0]->getMimeType() == 'video/flac' || $file[0]->getMimeType() == 'video/opus') {
					$processedData['containsVideo'] = TRUE;
				}
			}
			$carouselSlides[$element['uid']] = $file[0];
		}

		$processedData['carouselSlides'] = !empty($carouselSlides) ? $carouselSlides : '';

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 */
	public function getParallaxWrapper(array $processedData, array $flexconf, bool $webp=FALSE): array
	{
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];
		$processedData['file'] = $file;
		if ( $file ) {
			if ( $file->getType() === 4 ) {
				$processedData['video'] = TRUE;
				if ( $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube' ) {
				// youtube video
					$processedData['youtube'] = TRUE;
					$processedData['videoId'] = GeneralUtility::makeInstance(YouTubeRenderer::class)->render($file);
				} elseif ( $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo' ) {
				// vimeo video
					$processedData['vimeo'] = TRUE;
					$processedData['videoId'] = GeneralUtility::makeInstance(YouTubeRenderer::class)->render($file);
				} else {
				// local video
					$processedData['local'] = TRUE;
					if ( $file->getMimeType() == 'video/mp4' ) {
						$processedData['mimeType'] = 'mp4' ;
					}
					if ( $file->getMimeType() == 'video/webm' ) {
						$processedData['mimeType'] = 'webm' ;
					}
					if ( $file->getMimeType() == 'video/ogv' ) {
						$processedData['mimeType'] = 'ogv' ;
					}
					$processedData['file'] = $file;
				}
			} else {
				$bgImage = GeneralUtility::makeInstance(BackgroundImageUtility::class)
				 ->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE, [], FALSE, 0, $webp);
				$processedData['parallaxImage'] = $bgImage;
			}

			$processedData['width'] = $flexconf['width'];
			$processedData['speedFactor'] = $flexconf['speedFactor'] ?: 1;
			$processedData['addHeight'] = (int)$flexconf['addHeight'] ?: 0;
			$processedData['no-mobile'] = $flexconf['mobile'] ? '/iPad|iPhone|iPod|Android/' : '-';
		}

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 */
	public function getCollapsible(array $processedData, array $flexconf, array $parentflexconf): array
	{
		$fileRepository = GeneralUtility::makeInstance(FileRepository::class);

		$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid']);
		if (!empty($file)) {$file = $file[0];}
		$processedData['appearance'] = $parentflexconf['appearance'];
		$processedData['show'] = $flexconf['active'] ? ' show' : '';
		$processedData['collapsed'] = $flexconf['active'] ? '' : ' collapsed';
		$processedData['expanded'] = $flexconf['active'] ? 'true' : 'false';
		$processedData['alwaysOpen'] = $parentflexconf['alwaysOpen'] ? 'true' : 'false';
		$processedData['buttonstyle'] = !empty($flexconf['style']) ? $flexconf['style'] : 'primary';
		$processedData['collapsibleByPid'] = $flexconf['collapsibleByPid'] ?: '';
		$processedData['media'] = $file ? $file : '';

		return $processedData;
	}

}
