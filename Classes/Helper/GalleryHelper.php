<?php
namespace T3SBS\T3sbootstrap\Helper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\SingletonInterface;

class GalleryHelper implements SingletonInterface
{

	/**
	 * Returns row width
	 *
	 * @param array $processedData
	 * @return array
	 */
	public function getGalleryRowWidth( $processedData )
	{
		// Gallery row with 25, 33, 50, 66, 75 or 100%
		if ( $processedData['data']['tx_t3sbootstrap_inTextImgRowWidth'] == 'auto' ) {

			if ($processedData['data']['bodytext']) {

				if ( $processedData['gallery']['position']['vertical'] == 'intext' ) {

					if ( $processedData['gallery']['count']['columns'] == 1) {

						$processedData['rowwidth'] = ' w-33';
						$processedData['restrowwidth'] = ' w-66';

					} else {

						$processedData['rowwidth'] = ' w-50';
						$processedData['restrowwidth'] = ' w-50';
					}

				} else {
					// above or below
					if ( $processedData['data']['imageorient'] === 0 || $processedData['data']['imageorient'] === 8 ) {
						$processedData['rowwidth'] = ' w-100';
						$processedData['restrowwidth'] = '';

					} else {

						$processedData['rowwidth'] = ' w-66';
						$processedData['restrowwidth'] = ' w-33';
					}
				}

			} else {
				// image only
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
	 *
	 * @param array $processedData
	 * @return array
	 */
	public function getGalleryClasses( $processedData )
	{
		$galleryClass = 'gallery imageorient-'.$processedData['data']['imageorient'];
		$galleryRowClass = '';
		$imageorient = $processedData['data']['imageorient'];

		// Above or below (0,1,2,8,9,10)
		if ( $imageorient < 11 ) {
			if ( $imageorient == 0 || $imageorient == 8 ) {
				// center
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $processedData['rowwidth'].' text-center mx-auto';
				$processedData['addmedia']['zoomOverlay'] = ' d-flex mx-auto';
			}
			if ( $imageorient == 1 || $imageorient == 9 ) {
				// right
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $processedData['rowwidth'].' float-md-right';
				$processedData['addmedia']['zoomOverlay'] = ' zoom-right';
			}
			if ( $imageorient == 2 || $imageorient == 10 ) {
				// left
				$galleryClass .= ' clearfix';
				$galleryRowClass .= $processedData['rowwidth'].' float-md-left';
			}
		}
		// In Text right or left (17,18)
		if ( $imageorient == 17 || $imageorient == 18 ) {
			$galleryClass .= $imageorient == 17 ? ' float-md-right ml-md-3' : ' float-md-left mr-md-3';
			$galleryClass .= ' '.$processedData['rowwidth'];
		}
		// Beside Text right or left (nowrap) (25,26)
		if ( $imageorient == 25 || $imageorient == 26 ) {
			$galleryClass .= ' mx-auto';
		}
		// Beside Text right or left (align-items-center) (66,77)
		if ( $imageorient == 66 || $imageorient == 77 ) {
			$processedData['addmedia']['figureclass'] .= $imageorient == 66 ? ' float-md-right' : ' float-md-left' ;
		}
		// gallery class
		$processedData['gallery']['class'] = trim($galleryClass);
		$processedData['gallery']['rowClass'] = trim($galleryRowClass);
		$processedData['gallery']['headerBeside'] = $processedData['data']['tx_t3sbootstrap_header_position'] == 'beside' ? TRUE : FALSE;

		return $processedData;
	}

}
