<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;

/*
* This file is part of the TYPO3 extension t3sbootstrap.
*
* For the full copyright and license information, please read the
* LICENSE file that was distributed with this source code.
*/
class Carousel implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf, string $animateCss): array
	{
		$innerCaptionStyle = '';
		$processedData['dimensions']['width'] = !empty($parentflexconf['width']) ? $parentflexconf['width'] : '';
		$processedData['carouselLink'] = $parentflexconf['link'] ?? '';
		$processedData['mobileNoRatio'] = $parentflexconf['mobileNoRatio'] ?? '';

		if (!empty($parentflexconf['link']) && $parentflexconf['link'] == 'button' && $processedData['data']['header_link']) {
			$processedData['data']['button_link'] = $processedData['data']['header_link'];
			$processedData['data']['header_link'] = '';
		}

		$flexconf['captionVAlign'] = !empty($flexconf['captionVAlign']) ? $flexconf['captionVAlign'] : 'end';
		if (!empty($flexconf['bgOverlay']) && $flexconf['bgOverlay'] == 'caption') {
			$innerCaptionStyle = $processedData['style'].' padding:15px 0; z-index:1';
			$processedData['style'] = '';
		} elseif (!empty($flexconf['bgOverlay']) && $flexconf['bgOverlay'] == 'image') {
			$innerCaptionStyle = 'z-index:1';
		} else {
			$processedData['style'] = '';
		}

		$processedData['origImage'] = $parentflexconf['origImage'] ?? '';
		$processedData['addmedia']['origImg'] = $parentflexconf['origImage'] ?? '';
		$processedData['maxWidth'] = !empty($parentflexconf['width']) ? $parentflexconf['width'].'px' : '1440px';

		if (!empty($parentflexconf['buttontext'])) {
			if ( !empty( explode('|', (string) $parentflexconf['buttontext'])[$processedData['data']['sys_language_uid']] ) ) {
				$processedData['buttontext'] = trim( explode('|', (string) $parentflexconf['buttontext'])[$processedData['data']['sys_language_uid']] );
			}
		}

		$animateCss = $animateCss ? (string)$processedData['data']['tx_t3sbootstrap_animateCss'] : false;
		$parentAnimateCss = !empty($parentflexconf['animate']) ? (string)$parentflexconf['animate'] : '';

		if ($animateCss) {
			$parentAnimateCss = $animateCss;
		}
		$height = '';

		$processedData['innerStyle'] = $innerCaptionStyle;

		if ( $parentAnimateCss ) {
			$processedData['animate'] = $parentAnimateCss ?
			 ' caption-animated animated align-items-'.$flexconf['captionVAlign'].' '.$parentAnimateCss : '';
		} elseif ($processedData['data']['tx_t3sbootstrap_bgcolor']) {
			$height = $flexconf['captionVAlign'] == 'top' ? '' : 'h-100';
			$processedData['animate'] = ' '.$height.' d-flex align-items-'.$flexconf['captionVAlign'];
		} else {
			$height = $flexconf['captionVAlign'] == 'end' ? '' : 'h-100';
			$processedData['animate'] = ' '.$height.' d-flex align-items-'.$flexconf['captionVAlign'];
			$processedData['innerStyle'] = '';
		}
		if ( $processedData['animate'] ) {
			$processedData['dataAnimate'] = $animateCss ? $animateCss : '';
			$processedData['animateCssRepeat'] = $processedData['data']['tx_t3sbootstrap_animateCssRepeat'];
		}
		$animate = ($animateCss && $parentAnimateCss) || $processedData['data']['tx_t3sbootstrap_bgcolor'] ? TRUE : FALSE;
		$processedData['style'] .= self::getCarouselCaptionStyle( $flexconf, $animate );

		if (!empty($processedData['files'])) {
			$file = $processedData['files'][0];
		} else {
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			if (!empty($fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid']))) {
				$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];
			}
		}
		$processedData['localVideoPath'] = '';
		if (!empty($file)) {
			if ($file->getMimeType() == 'video/mp4' || $file->getMimeType() == 'video/webm' || $file->getMimeType() == 'video/wav'
			 || $file->getMimeType() == 'video/ogg' || $file->getMimeType() == 'video/flac' || $file->getMimeType() == 'video/opus') {
				$processedData['localVideoPath'] = '/'.$file->getStorage()->getConfiguration()['basePath'].substr($file->getIdentifier(), 1);
			}
			$processedData['autoplay'] = $file->getProperties()['autoplay'];
			$processedData['loop'] = !empty($flexconf['loop']) ? $flexconf['loop'] : FALSE;
			$muted = !empty($flexconf['muted']) ? $flexconf['muted'] : FALSE;
			$processedData['muted'] = !empty($file->getProperties()['autoplay']) ? TRUE : $muted;
			$processedData['playsinline'] = !empty($flexconf['playsinline']) ? TRUE : FALSE;
			$btnlink = !empty($parentflexconf['link']) && $parentflexconf['link'] == 'button' ? TRUE : FALSE;
			if ($processedData['data']['header'] || $processedData['data']['bodytext']
			 || ( $processedData['data']['header_link'] && $btnlink) ) {
				$processedData['controls'] = 0;
			} else {
				$processedData['controls'] = !empty($flexconf['controls']) ? $flexconf['controls'] : 0;
			}
		}


		$processedData['ratioCalc'] = '';

		if (!empty($parentflexconf['ratio'])) {
			$ratioArr = explode(':', $parentflexconf['ratio']);
			$x = str_replace(':', 'x', $parentflexconf['ratio']);
			$y = $ratioArr[1].' / '.$ratioArr[0].' * 100%';	
			$processedData['ratioCalc'] .= '.ratio-'.$x.'{--bs-aspect-ratio:calc('.$y.');}';
			$processedData['videoRatio'] = str_replace(':', 'x', $parentflexconf['ratio']);
			$processedData['videoStyle'] = '';
			if ( $parentflexconf['ratio'] !== '16:9') {
				$processedData['videoStyle'] .= 'object-fit: cover;';
			}
		} else {
			$processedData['ratioCalc'] = 1;
			$processedData['videRatio'] = '';
			$processedData['videoStyle'] = '';
		}

		if ( empty($processedData['files']) && !$processedData['localVideoPath'] ) {
			$ratio = $parentflexconf['ratio'] ? $parentflexconf['ratio'] : '16:9';
			$noImgHeight = explode(':', (string) $ratio);
			$noImgHeight = (int) round($parentflexconf['width'] / $noImgHeight[0] * $noImgHeight[1]);
			$processedData['animate'] .= ' position-static';
			$processedData['style'] .= ' min-height:'.$noImgHeight.'px;';
			$processedData['style'] .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';
		}
		$processedData['zoom'] = $parentflexconf['zoom'] ?? '';
		$processedData['ratio'] = '';
		if (!empty($parentflexconf['ratio'])) {
			$carouselRatioArr = explode(':', (string) $parentflexconf['ratio']);
			if ( !empty($carouselRatioArr[0]) ) {
				$processedData['ratio'] = $parentflexconf['ratio'];
			}
		}
		$processedData['shift'] = '';
		$processedData['videoShift'] = '';

		if (!empty($flexconf['shift'])){
			$processedData['videoStyle'] .= 'transform: translateY('.(int)$flexconf['shift'].'%);';
			$negShift = (int)$flexconf['shift'] * -1;
			$processedData['videoStyle'] .= 'top: '.$negShift.'% !important;';
			$processedData['shift'] = (int)$flexconf['shift'] / 100;
			$breakWidth = '576';
		}

		$videoStyle = $processedData['videoStyle'];
		$processedData['videoStyle'] = ' style="'.$videoStyle.'"';

		if ( !empty($parentflexconf['swiperJs']) ) {
			$processedData['swiper'] = TRUE;
		}

		return $processedData;
	}


	/**
	 * Returns carousel caption style
	 *
	 * @param array	$flexconf
	 * @param bool	$animate
	 *
	 */
	public function getCarouselCaptionStyle( $flexconf, $animate ): string
	{
		$style = '';
		if (!empty($flexconf['bgOverlay']) && $flexconf['bgOverlay'] == 'caption') {
			$captionStyle = ' top:0; left:15%; right:15%; bottom:0;';
			$captionStyle .= $flexconf['captionVAlign'] == 'top' ? ' bottom:inherit;' : '';
			$captionStyle .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';

		} elseif (!empty($flexconf['bgOverlay']) && $flexconf['bgOverlay'] == 'image') {
			$captionStyle = ' top:0; left:0; right:0; bottom:0;';
			$captionStyle .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';
		} else {
			$style .= $flexconf['captionVAlign'] == 'top' ? ' top:0;' : '';
			$style .= $flexconf['captionVAlign'] == 'center' ? ' bottom:0;' : '';
			$style .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';
			$captionStyle = '';
		}
		if ($animate){
			$style .= $captionStyle;
		}

		return $style;
	}

}