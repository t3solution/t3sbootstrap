<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class MediaElementHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $extConf, $breakpoint, $parentflexconf): array
	{
		$cType = $processedData['data']['CType'];

		$processedData['addmedia']['imgclass'] = !empty($processedData['addmedia']['imgclass']) ? $processedData['addmedia']['imgclass'] : 'img-fluid';
		$processedData['addmedia']['imgclass'] .= $processedData['data']['imageborder'] ? ' border' :'';
		$processedData['addmedia']['imgclass'] .= $processedData['data']['tx_t3sbootstrap_bordercolor'] && $processedData['data']['imageborder']
		 ? ' border-'.$processedData['data']['tx_t3sbootstrap_bordercolor'] : '';

		// lazyload
		if ( $extConf['lazyLoad']) {
			if (!empty($parentflexconf) && isset($parentflexconf['card_wrapper']) && $parentflexconf['card_wrapper'] === 'slider' ) {
					$processedData['addmedia']['lazyload'] = 0;
					$processedData['lazyload'] = 0;
			} else {
				$processedData['addmedia']['imgclass'] .= $extConf['lazyLoad'] == 1 ? ' lazy' : '';
				$processedData['addmedia']['lazyload'] = $extConf['lazyLoad'];
				$processedData['lazyload'] = $extConf['lazyLoad'];
			}
		}

		$processedData['addmedia']['figureclass'] = !empty($processedData['addmedia']['figureclass']) ? ' '.trim($processedData['addmedia']['figureclass']) : '';
		$processedData['addmedia']['imagezoom'] = $processedData['data']['image_zoom'];
		$processedData['addmedia']['CType'] = $cType;
		$processedData['addmedia']['ratio'] = $processedData['data']['tx_t3sbootstrap_image_ratio'];

		if ( $cType === 'textmedia'
		 || $cType === 'textpic'
		 || $cType === 'image'
		 || $cType === 't3sbs_card' )
		{
			$imageorient = $processedData['data']['imageorient'];
			// hover effect
			$processedData['hoverEffect'] = FALSE;
			if (is_array($processedData['files'])) {
				foreach ($processedData['files'] as $file ) {
					if ($file->getProperties()['tx_t3sbootstrap_hover_effect'])	$processedData['hoverEffect'] = TRUE;
				}
			}
			$galleryUtility = GeneralUtility::makeInstance(GalleryHelper::class);
			// Gallery row with 25, 33, 50, 66, 75 or 100%
			$processedData = $galleryUtility->getGalleryRowWidth( $processedData );
			$processedData = $galleryUtility->getGalleryClasses( $processedData, $breakpoint );

		} else {
			$processedData['addmedia']['figureclass'] .= !empty($processedData['data']['image_zoom']) ? ' gallery' : '';
		}

		if ($cType !== 't3sbs_carousel') {
			$processedData['addmedia']['origImg'] = $processedData['data']['tx_t3sbootstrap_image_orig'];
		}

		return $processedData;
	}

}
