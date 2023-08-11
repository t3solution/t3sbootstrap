<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;
use T3SBS\T3sbootstrap\Layouts\Grid;
use T3SBS\T3sbootstrap\Layouts\Gutters;

use TYPO3\CMS\Core\Service\FlexFormService;
/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class TwoColumns implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, string $bgMediaQueries='2560,1920,1200,992,768,576'): array
	{
		$processedData = GeneralUtility::makeInstance(Gutters::class)->getGutters($processedData, $flexconf);
		$processedData = GeneralUtility::makeInstance(Grid::class)->getGrid($processedData, $flexconf);

		$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		foreach ($processedData as $key=>$data) {
			if ($key == 'col_221' || $key == 'col_222') {
				if (is_array($data) && !empty($data[0]['tx_t3sbootstrap_flexform'])) {
					$flexconf = $flexFormService->convertFlexFormContentToArray($data[0]['tx_t3sbootstrap_flexform']);
					if (!empty($flexconf['tiling']['enable'])) {
						// cards with tiling enabled inside
						$processedData['gutters'] = ' gx-0 gy-0';
						break;
					}
				}	
			}
		}

		$processedData['style'] .= !empty($flexconf['colHeight']) ? ' min-height: '.$flexconf['colHeight'].'px;' : '';
		$processedData['verticalAlign'] = !empty($flexconf['colHeight'])
		 && !empty($flexconf['verticalAlign']) ? ' d-flex align-items-' . $flexconf['verticalAlign'] : '';
		$processedData['bgimages'] = '';
		$processedData['bgimagePosition'] = '';
		if ( !empty($flexconf['bgimages']) ) {
			$bgimages = GeneralUtility::makeInstance(BackgroundImageUtility::class)
				->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE,
				 $flexconf, FALSE, $processedData['data']['uid'], $bgMediaQueries);
			if ($bgimages) {
				$processedData['bgimages'] = $bgimages;
				$processedData['bgimagePosition'] = $flexconf['bgimagePosition'];
				$processedData['class'] .= ' col-image';
			}
		}
		$processedData['equalHeight'] = !empty($flexconf['equalHeight']) ? ' d-flex align-items-stretch' : '';

		return $processedData;
	}

}
