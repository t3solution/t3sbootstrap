<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Context\Context;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use T3SBS\T3sbootstrap\Utility\YouTubeRenderer;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class BackgroundWrapper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, string $bgMediaQueries='2560,1920,1200,992,768,576'): array
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
					$processedData['vimeo'] = FALSE;
					$processedData['isVideo'] = TRUE;
					$processedData['contentPosition'] = !empty($flexconf['contentPosition']) ? $flexconf['contentPosition'] : 'align-self-center';
					$processedData['ytVideo']['bgHeight'] = !empty($flexconf['bgHeight']) ? $flexconf['bgHeight'] : '';
					$processedData['ytVideo']['ytshift'] = !empty($flexconf['ytshift']) ? $flexconf['ytshift'] : '';
					$processedData['videoAutoPlay'] = $file->getProperties()['autoplay'];
					$youTubeRenderer = GeneralUtility::makeInstance(YouTubeRenderer::class);
					$processedData['videoId'] = $youTubeRenderer->render($file);
				} else {
					if ( $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo' ) {
					// vimeo video
						$processedData['vimeo'] = TRUE;
						$processedData['youtube'] = FALSE;
						$processedData['isVideo'] = TRUE;
						$processedData['contentPosition'] = !empty($flexconf['contentPosition']) ? $flexconf['contentPosition'] : 'align-self-center';
						$processedData['ytVideo']['bgHeight'] = !empty($flexconf['bgHeight']) ? $flexconf['bgHeight'] : '';
						$processedData['ytVideo']['ytshift'] = !empty($flexconf['ytshift']) ? $flexconf['ytshift'] : '';
						$processedData['videoAutoPlay'] = $file->getProperties()['autoplay'];
						$youTubeRenderer = GeneralUtility::makeInstance(YouTubeRenderer::class);
						$processedData['videoId'] = $youTubeRenderer->render($file);
					} else {
					// local video
						$processedData['file'] = $file;
						// align content items
						$processedData['alignItem'] = $flexconf['alignVideoItem'] != 'none' ? ' '.$flexconf['alignVideoItem'] :'';
						// video shift
						$processedData['shift'] = $flexconf['shift'];
						$processedData['horizontalShift'] = !empty($flexconf['horizontalShift']) ? $flexconf['horizontalShift'] : 0;
						// overlay child
						$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
						$overlayChild = $queryBuilder
							 ->count('uid')
							 ->from('tt_content')
							 ->where(
									$queryBuilder->expr()->eq('sys_language_uid', $processedData['data']['sys_language_uid']),
									$queryBuilder->expr()->eq('tx_container_parent', $queryBuilder->createNamedParameter($processedData['data']['uid'], \PDO::PARAM_INT))
								)
							 ->executeQuery()
							 ->fetchOne();

						$autoplay = $file->getProperties()['autoplay'];
						$loop = $flexconf['loop'];
						$mute = $autoplay ? true : $flexconf['mute'];
						$processedData['localVideo']['inlineCSS'] = '';
						$mobileHeight = $flexconf['mobileHeight'] != 'none' ? (int) trim($flexconf['mobileHeight']) :'';
						$mobileWidth = $flexconf['mobileWidth'] != 'none' ? (int) trim($flexconf['mobileWidth']) :'';
						// max-width:575px
						$processedData['localVideo']['inlineCSS'] = '@media (max-width:768px){#s-'.$processedData['data']['uid'].
						' figure.video{width:'.$mobileWidth.'%; max-height:'.$mobileHeight.'px; margin-left:'.$processedData['horizontalShift'].'%}}';
						$ratio = end(explode('/', $flexconf['aspectRatio'])).'x9';
						$ratioArr = explode('x', $ratio);
						$x = $ratio;
						$y = $ratioArr[1].' / '.$ratioArr[0].' * 100%';
						$processedData['ratioCalcCss'] = '.ratio-'.$x.'{--bs-aspect-ratio:calc('.$y.');}';
						$processedData['localVideo']['class'] = ' ratio ratio-'.$ratio;
						$processedData['localVideo']['overlayChild'] = $overlayChild;
						$processedData['localVideo']['autoplay'] = $autoplay;
						$processedData['localVideo']['loop'] = $loop;
						$processedData['localVideo']['mute'] = $mute;
						$processedData['localVideo']['controls'] = $flexconf['localControls'] ?: 0;
					}
				}

			} elseif ( $file->getType() === 2 ) {
			// IMAGE
				// orig. image option in flexform
				if (!empty($flexconf['origImage'])) {
					$processedData['file'] = $file;
					$processedData['ingWidth'] = $flexconf['width'] ? $flexconf['width'] : 1296;
				} else {
					$bgImage = GeneralUtility::makeInstance(BackgroundImageUtility::class)
						->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, TRUE, $flexconf, FALSE, 0, $bgMediaQueries);
					$processedData['bgImage'] = $bgImage;
					if (!empty($flexconf['paddingTopBottom'])) {
						$processedData['style'] .= ' padding: '.$flexconf['paddingTopBottom'].'rem 0;';
					}
				}
				// align content items
				$processedData['alignItem'] = !empty($flexconf['alignItem']) ? ' '.$flexconf['alignItem'] :'';

				// image raster
				$processedData['imageRaster'] = !empty($flexconf['imageRaster']) ? 'multiple-' : '';

				// Text color - overlay (
				if ( $processedData['data']['tx_t3sbootstrap_textcolor'] ) {
					$processedData['overlayClass'] = ' text-'.$processedData['data']['tx_t3sbootstrap_textcolor'];
				}

				$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);
				$processedData['bgColorOverlay'] = $styleHelper->getBgColor($processedData['data'], FALSE);

				$filter = !empty($flexconf['imgGrayscale']) ? ' grayscale('.$flexconf['imgGrayscale'].'%) ' : '';
				$filter .= !empty($flexconf['imgSepia']) ? ' sepia('.$flexconf['imgSepia'].'%) ' : '';
				$filter .= !empty($flexconf['imgOpacity']) && $flexconf['imgOpacity'] != 100 ? ' opacity('.$flexconf['imgOpacity'].'%) ' : '';
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

		$vMute = !empty($flexconf['videoMute']) ? $flexconf['videoMute'] : 0;
		$mute = !empty($processedData['videoAutoPlay']) ? 1 : $vMute;
		if (!empty($flexconf['videoControls'])) {
			$processedData['controlStyle'] = '';
		} elseif ( empty($processedData['videoAutoPlay']) ) {
			$processedData['controlStyle'] = '';
		} else {
			$processedData['controlStyle'] = ' pointer-events:none;';
		}
		if ( !empty($processedData['videoId']) && $processedData['youtube'] ) {
			$params = '?autoplay='.$processedData['videoAutoPlay'].'&loop='.$flexconf['videoLoop'].'&playlist='.
			$processedData['videoId'].'&mute='.$mute.'&rel=0&showinfo=0&controls='.$flexconf['videoControls'].'&modestbranding='.$flexconf['videoControls'];
			$processedData['youtubeParams'] = $params;
		}
		if ( !empty($processedData['videoId']) && $processedData['vimeo'] ) {
			$background = !empty($processedData['videoAutoPlay']) ? '&background=1' : '';
			$processedData['vimeoParams'] = $background.'&autoplay='.$processedData['videoAutoPlay'].'&loop='.$flexconf['videoLoop'].'&mute='.$mute;
			$processedData['startButton'] = $processedData['videoAutoPlay'] ? 0 : 1;
		}

		return $processedData;
	}

}
