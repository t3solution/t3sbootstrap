<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileRepository;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class T3sbsElementHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 *
	 * @param array $processedData
	 * @param array	$flexconf
	 * @param array	$parentflexconf
	 * @param string $animateCss
	 *
	 */
	public function getProcessedData($processedData, $flexconf, $parentflexconf, $animateCss): array
	{

		$cType = $processedData['data']['CType'];

		/**
		 * Button
		 */
		if ( $cType == 't3sbs_button' ) {
			$outline = $flexconf['outline'] ? 'outline-':'';
			$typolinkButtonClass = ' btn btn-'.$outline.$flexconf['style'];
			$typolinkButtonClass .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] != 'default' ? ' '.$flexconf['btnsize']:'';
			if ( empty($parentflexconf) ) {
				$processedData['btn-block'] = false;
				if ($flexconf['block']) {
					$processedData['btn-block'] = true;
				}
			}
			if ($processedData['data']['header_position']) {
				$headerPosition = $processedData['data']['header_position'];
				if ( $headerPosition == 'left' ) $headerPosition = '';
				if ( $headerPosition == 'center' ) $headerPosition = 'text-center';
				if ( $headerPosition == 'right' ) $headerPosition = 'd-md-flex justify-content-md-end';
			}

			$processedData['headerPosition'] = $headerPosition;

			if ( $flexconf['fixedPosition'] ) {
				$typolinkButtonClass .= ' d-none fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
				$typolinkButtonClass .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
				$processedData['fixedButton'] = $flexconf['fixedPosition'];
			}

			$processedData['linkTitle'] = $flexconf['linkTitle'];
			$processedData['slideInButton'] = FALSE;
			$processedData['slideInButtonFaIcon'] = FALSE;

			if ( $parentflexconf['fixedPosition'] == 'right'
			 && $parentflexconf['slideIn'] 
			 && $parentflexconf['visiblePart'] 
			 && $parentflexconf['vertical'] 
			) {
				// slide in button
				$processedData['slideInButton'] = TRUE;

				if ( $processedData['data']['tx_t3sbootstrap_header_fontawesome'] ) {
					$processedData['slideInButtonFaIcon'] = TRUE;
				} else {
					$processedData['data']['tx_t3sbootstrap_header_fontawesome'] = 'fas fa-ban text-danger';
					$processedData['slideInButtonFaIcon'] = TRUE;
				}					
			}

			$processedData['class'] .= $typolinkButtonClass;
		}


		/**
		 * Cards
		 */
		if ( $cType == 't3sbs_card' ) {

			$backstyle = '';
			$backclass = '';

			if ( !empty($processedData['data']['imagewidth']) && !empty($flexconf['maxwidth'])) {
				$processedData['style'] .= ' max-width: '.$processedData['data']['imagewidth'].'px;';
			}

			// Flip Card
			if ( !empty($flexconf['flipcard']) ) {
				if ( $processedData['data']['tx_t3sbootstrap_textcolor'] ) {
					$backclass .= 'text-'.$processedData['data']['tx_t3sbootstrap_textcolor'];
				}
				if ( $processedData['data']['tx_t3sbootstrap_bgcolor'] ) {
					$backstyle .= $processedData['data']['tx_t3sbootstrap_bgcolor'];
				} else {
					if ( $processedData['data']['tx_t3sbootstrap_contextcolor'] ) {
						$backclass .= ' bg-'.$processedData['data']['tx_t3sbootstrap_contextcolor'];
					}
				}
				$processedData['backclass'] = trim((string)$backclass);
				$processedData['backstyle'] = $backstyle;
			}
		}


		/**
		 * Carousel
		 */
		if ( $cType == 't3sbs_carousel' ) {

			$innerCaptionStyle = '';
			$processedData['dimensions']['width'] = $parentflexconf['width'] ?: '';
			$processedData['carouselLink'] = $parentflexconf['link'] ?? '';
			$processedData['mobileNoRatio'] = $parentflexconf['mobileNoRatio'] ?? '';

			if (!empty($parentflexconf['link']) && $parentflexconf['link'] == 'button' && $processedData['data']['header_link']) {
				$processedData['data']['button_link'] = $processedData['data']['header_link'];
				$processedData['data']['header_link'] = '';
			}

			$flexconf['captionVAlign'] = $flexconf['captionVAlign'] ? $flexconf['captionVAlign'] : 'end';
			if ($flexconf['bgOverlay'] == 'caption') {
				$innerCaptionStyle = $processedData['style'].' padding:15px 0; z-index:1';
				$processedData['style'] = '';
			} elseif ($flexconf['bgOverlay'] == 'image') {
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

			$animateCss = (string)$processedData['data']['tx_t3sbootstrap_animateCss'];
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

			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			$file = $fileRepository->findByRelation('tt_content', 'assets', (int)$processedData['data']['uid'])[0];

			$processedData['localVideoPath'] = '';
			if ($file) {
				if ($file->getMimeType() == 'video/mp4' || $file->getMimeType() == 'video/webm' || $file->getMimeType() == 'video/wav'
				 || $file->getMimeType() == 'video/ogg' || $file->getMimeType() == 'video/flac' || $file->getMimeType() == 'video/opus') {
					$processedData['localVideoPath'] = '/'.$file->getStorage()->getConfiguration()['basePath'].substr($file->getIdentifier(), 1);
				}
			}
			if ($parentflexconf['ratio']) {
				$ratioArr = explode(':', $parentflexconf['ratio']);
				$processedData['ratioCalc'] = $ratioArr[1] / $ratioArr[0];
			} else {
				$processedData['ratioCalc'] = 1;
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
			if ($parentflexconf['ratio']) {
				$carouselRatioArr = explode(':', (string) $parentflexconf['ratio']);
				if ( !empty($carouselRatioArr[0]) ) {
					$processedData['ratio'] = $parentflexconf['ratio'];
				}
			}
			$processedData['shift'] = '';
			$processedData['videoShift'] = '';
			if ($flexconf['shift']){
				$processedData['shift'] = (int)$flexconf['shift'] / 100;
				$breakWidth = '576';
				$processedData['videoShift'] = '@media(min-width: '.$breakWidth.'px){.carousel-item-'.$processedData['data']['uid'].' figure{margin-top:'.(int)$flexconf['shift'].'% !important}}';

			}
			if ( !empty($parentflexconf['swiperJs']) ) {
				$processedData['swiper'] = TRUE;
			}
		}


		/**
		 * Media object
		 */
		if ( $cType == 't3sbs_mediaobject' ) {

			$processedData['mediaobject']['order'] = $flexconf['order'] == 'right' ? 'right' : 'left';
			$processedData['mediaObjectBody'] = $flexconf['order'] == 'right' ? ' me-3 m-1' : ' ms-3 m-1';

			$processedData['addmedia']['figureclass'] = '';

			switch ( $processedData['data']['imageorient'] ) {
				 case 91:
				 	$processedData['addmedia']['figureclass'] .= ' align-self-center';
				break;
				 case 92:
				 	$processedData['addmedia']['figureclass'] .= ' align-self-start';
				break;
				 case 93:
				 	$processedData['addmedia']['figureclass'] .= ' align-self-end';
				break;
				 default:
				 	$processedData['addmedia']['figureclass'] .= '';
			}

			$processedData['class'] .= ' media';

		}


		/**
		 * Toasts
		 */
		if ( $cType == 't3sbs_toast' ) {
			$processedData['animation'] = $flexconf['animation'] ? 'true' : 'false';
			$processedData['autohide'] = $flexconf['autohide'] ? 'true' : 'false';
			$processedData['delay'] = $flexconf['delay'];
			$processedData['style'] .= !empty($flexconf['toastwidth']) ? ' width:'.$flexconf['toastwidth'].'px;' : '';
			if ($flexconf['placement'] && str_starts_with($flexconf['placement'], 'top-0')) {
				$processedData['placement'] = ' '.str_replace('top-0', 'top-70', $flexconf['placement']);
			} else {
				$processedData['placement'] = ' '.$flexconf['placement'];
			}
			$processedData['cookie'] = $flexconf['cookie'];
			$processedData['expires'] = !empty($flexconf['expires']) ? $flexconf['expires'] : '';

			$processedData['style'] .= ' z-index:1;';
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
		if ($flexconf['bgOverlay'] == 'caption') {

			$captionStyle = ' top:0; left:15%; right:15%; bottom:0;';
			$captionStyle .= $flexconf['captionVAlign'] == 'top' ? ' bottom:inherit;' : '';
			$captionStyle .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';

		} elseif ($flexconf['bgOverlay'] == 'image') {

			$captionStyle = ' top:0; left:0; right:0; bottom:0;';
			$captionStyle .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';

		} else {

			$style .= $flexconf['captionVAlign'] == 'top' ? ' top:0;' : '';
			$style .= $flexconf['captionVAlign'] == 'center' ? ' bottom:0;' : '';
			#$style .= $flexconf['captionVAlign'] == 'end' ? ' padding-bottom:50px;' : '';
			$captionStyle = '';
		}
		if ($animate){
			$style .= $captionStyle;
		}

		return $style;
	}
}
