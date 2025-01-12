<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class GalleryHelper implements SingletonInterface
{
	/**
	 * Returns row width
	 */
	public function getGalleryRowWidth(array $processedData): array
	{
		// Gallery row with 25, 33, 50, 66, 75 or 100%
		if ( $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'] == 'auto' ) {

			if ($processedData['data']['bodytext']) {

				if ( !empty($processedData['gallery']['position']['vertical']) && $processedData['gallery']['position']['vertical'] == 'intext' ) {
					if ( $processedData['gallery']['count']['columns'] == 1) {
						$processedData['rowwidth'] = ' w-33';
						$processedData['restrowwidth'] = ' w-66';
					} else {
						$processedData['rowwidth'] = ' w-50';
						$processedData['restrowwidth'] = ' w-50';
					}
				} else {
					// above or below
					if ( $processedData['data']['imageorient'] <= 10 ) {
						$processedData['rowwidth'] = '';
						$processedData['restrowwidth'] = '';

					} else {
						$processedData['rowwidth'] = ' w-66';
						$processedData['restrowwidth'] = ' w-33';
					}
				}

			} else {
				// image only
				$processedData['gallery']['position']['vertical'] = [];
				if ( $processedData['gallery']['position']['vertical'] === 'intext' ) {
					$processedData['rowwidth'] = ' w-100';
					$processedData['restrowwidth'] = '';
				}

			}

		} elseif ( $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'] == 'none' ) {

				$processedData['rowwidth'] = '';
				$processedData['restrowwidth'] = '';

		} else {
			$processedData['rowwidth'] = ' '.$processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'];

			switch ( $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'] ) {
				 case 'w-25':
				 	$processedData['restrowwidth'] = ' w-75';
				break;
				 case 'w-33':
				 	$processedData['restrowwidth'] = ' w-66';
				break;
				 case 'w-50':
				 	$processedData['restrowwidth'] = ' w-50';
				break;
				 case 'w-66':
				 	$processedData['restrowwidth'] = ' w-33';
				break;
				 case 'w-75':
				 	$processedData['restrowwidth'] = ' w-25';
				break;
				 case 'w-100':
				 	$processedData['restrowwidth'] = '';
				break;
				default:
					$processedData['restrowwidth'] = '';
			}
		}

		return $processedData;
	}


	/**
	 * Returns gallery classes
	 */
	public function getGalleryClasses(array $processedData, string $breakpoint): array
	{
		$galleryClass = 'gallery imageorient-'.$processedData['data']['imageorient'];
		$galleryRowClass = '';
		$imageorient = $processedData['data']['imageorient'];
		$rowwidth = empty($processedData['rowwidth']) ? '' : $processedData['rowwidth'];
		// Above or below (0,1,2,8,9,10)
		if ( $imageorient < 11 ) {
			if ( $imageorient == 0 || $imageorient == 8 ) {
				// center
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $rowwidth.' mx-auto';
				$processedData['addmedia']['zoomOverlay'] = ' d-flex mx-auto';
				$processedData['addmedia']['figureclass'] .= ' mx-auto';
			}
			if ( $imageorient == 1 || $imageorient == 9 ) {
				// right
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $rowwidth.' float-md-end';
				$processedData['addmedia']['zoomOverlay'] = ' zoom-right';
			}
			if ( $imageorient == 2 || $imageorient == 10 ) {
				// left
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $rowwidth.' float-md-start';
			}
		}
		// In Text right or left (17,18)
		if ( $imageorient == 17 || $imageorient == 18 ) {
			$inTextImgColumns = empty($processedData['data']['tx_t3sbootstrap_inTextImgColumns']) ? 4 : $processedData['data']['tx_t3sbootstrap_inTextImgColumns'];
			$galleryClass .= $imageorient == 17 ? ' col-md-'.$inTextImgColumns.' float-md-end ms-md-3'
			 : ' col-md-'.$inTextImgColumns.' float-md-start me-md-3';
			$galleryClass .= ' '.$rowwidth;
		}
		// Beside Text right or left (nowrap) (25,26)
		if ( $imageorient == 25 || $imageorient == 26 ) {
			$galleryClass .= ' mx-auto';
		}
		// Beside Text right or left (align-items-center) (66,77)
		if ( $imageorient == 66 || $imageorient == 77 ) {
			$processedData['addmedia']['figureclass'] .= $imageorient == 66 ? ' float-md-end' : ' float-md-start' ;
		}
		// gallery class
		$processedData['gallery']['class'] = trim($galleryClass);
		$processedData['gallery']['rowClass'] = trim($galleryRowClass);
		$header_position = empty($processedData['data']['tx_t3sbootstrap_header_position']) ? 'above' : $processedData['data']['tx_t3sbootstrap_header_position'];
		$processedData['gallery']['headerBeside'] = $header_position == 'beside' ? TRUE : FALSE;

		return $processedData;
	}

}
