<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\Utility\YouTubeRenderer;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ParallaxWrapper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
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
				 ->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE, [], FALSE, 0);
				$processedData['parallaxImage'] = $bgImage;
			}

			$processedData['width'] = !empty($flexconf['width']) ? $flexconf['width'] : 'auto';
			$processedData['speedFactor'] = $flexconf['speedFactor'] ?: 1;
			$processedData['addHeight'] = !empty($flexconf['addHeight']) ? (int)$flexconf['addHeight'] : 0;
			$processedData['no-mobile'] = $flexconf['mobile'] ? '/iPad|iPhone|iPod|Android/' : '-';
		}

		return $processedData;
	}

}
