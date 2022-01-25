<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use T3SBS\T3sbootstrap\Utility\BackgroundImageUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ContainerGridHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, bool $webp=FALSE): array
	{

		$cType = $processedData['data']['CType'];

		$horizontalGutters = !empty($flexconf['horizontalGutters']) ? trim((string)$flexconf['horizontalGutters']) : '';
		$verticalGutters = !empty($flexconf['verticalGutters']) ? trim((string)$flexconf['verticalGutters']) : '';
		$extraWrapperClass = '';
		if ( $verticalGutters ) {
			#  The vertical gutters can cause some overflow below the .row at the end of a page.
			# If this occurs, you add a wrapper around .row with the .overflow-hidden class
			$extraWrapperClass = 'overflow-hidden';
		}
		$processedData['gutters'] = $horizontalGutters || $verticalGutters ? ' '.$horizontalGutters.' '.$verticalGutters : '';
		$processedData['extraWrapperClass'] = $extraWrapperClass;

		$processedData = self::getGrid($processedData, $flexconf);

		if ( $cType == 'two_columns' ) {
			$processedData['style'] .= !empty($flexconf['colHeight']) ? ' min-height: '.$flexconf['colHeight'].'px;' : '';
			$processedData['verticalAlign'] = !empty($flexconf['colHeight']) && !empty($flexconf['verticalAlign']) ? ' d-flex align-items-' . $flexconf['verticalAlign'] : '';
			$processedData['bgimages'] = '';
			$processedData['bgimagePosition'] = '';
			if ( !empty($flexconf['bgimages']) ) {
				$bgimages = $this->getBackgroundImageUtility()
					->getBgImage($processedData['data']['uid'], 'tt_content', FALSE, FALSE,
					 $flexconf, FALSE, $processedData['data']['uid'], $webp,'2560,1920,1200,992,768,576', 2);
				if ($bgimages) {
					$processedData['bgimages'] = $bgimages;
					$processedData['bgimagePosition'] = $flexconf['bgimagePosition'];
					$processedData['class'] .= ' col-image';
				}
			}
		}

		$rowClass = [];
		if ( $cType == 'row_columns' ) {
			if ($flexconf['cols_extraClass'] ?? '') {
				foreach (explode(',',$flexconf['cols_extraClass']) as $key=>$cec ) {
					$colsClass[$key] = ' '.trim($cec);
				}
				$processedData['extraClassCols'] = $colsClass;
			}
			foreach (array_reverse($flexconf) as $key=>$grid) {
				if ( str_ends_with($key, 'rowclass') ) {
					$rowClass[$key] = $grid;
				}
			}
			$processedData['class'] .= ' '.trim(implode(' ',$rowClass));
		}

		return $processedData;
	}


	/**
	 * Returns the $processedData
	 */
	public function getGrid(array $processedData, array $flexconf): array
	{
		$columnOneExtraClass = '';
		$columnTwoExtraClass = '';
		$columnThreeExtraClass = '';
		$columnFourExtraClass = '';
		$columnFiveExtraClass = '';
		$columnSixExtraClass = '';

		foreach ($flexconf as $key=>$grid) {

			if ( $key == 'extraClass_one' || $key == 'extraClass_two' || $key == 'extraClass_three'
			 || $key == 'extraClass_four' || $key == 'extraClass_five' || $key == 'extraClass_six' ) {

				switch ( $key ) {
					 case 'extraClass_one':
					 	$columnOneExtraClass = ' '.$grid;
					break;
					 case 'extraClass_two':
					 	$columnTwoExtraClass = ' '.$grid;
					break;
					 case 'extraClass_three':
					 	$columnThreeExtraClass = ' '.$grid;
					break;
					 case 'extraClass_four':
					 	$columnFourExtraClass = ' '.$grid;
					break;
					 case 'extraClass_five':
					 	$columnFiveExtraClass = ' '.$grid;
					break;
					 case 'extraClass_six':
					 	$columnSixExtraClass = ' '.$grid;
					break;
				}
			}
		}

		if ( !empty($flexconf['equalWidth']) ) {

			$colOne = 'col';
			$colTwo = 'col';
			$colThree = 'col';
			$colFour = 'col';
			$colFive = 'col';
			$colSix = 'col';

		} else {

			$colOne = '';
			$colTwo = '';
			$colThree = '';
			$colFour = '';
			$colFive = '';
			$colSix = '';

			foreach ($flexconf as $key=>$grid) {

				if ( substr($key, 0, 2) != 'ex' ) {

					if ( $key != 'extraClass_one' || $key != 'extraClass_two' || $key != 'extraClass_three'
					 || $key != 'extraClass_four' || $key != 'extraClass_five' || $key != 'extraClass_six' ) {

						if ($grid) {

							if ( substr($key, 0, 2) == 'xs' ) {

								if ( substr($key, -3) == 'one' ) {
									$colOne .= ' col-'.$grid;
								}
								if ( substr($key, -3) == 'two' ) {
									$colTwo .= ' col-'.$grid;
								}
								if ( substr($key, -5) == 'three' ) {
									$colThree .= ' col-'.$grid;
								}
								if ( substr($key, -4) == 'four' ) {
									$colFour .= ' col-'.$grid;
								}

								if ( substr($key, -4) == 'five' ) {
									$colFive .= ' col-'.$grid;
								}
								if ( substr($key, -3) == 'six' ) {
									$colSix .= ' col-'.$grid;
								}

							} else {
								if ( substr($key, -3) == 'one' ) {
									$colOne .= ' col-'.substr($key, 0, -4).'-'.$grid;
								}
								if ( substr($key, -3) == 'two' ) {
									$colTwo .= ' col-'.substr($key, 0, -4).'-'.$grid;
								}
								if ( substr($key, -5) == 'three' ) {
									$colThree .= ' col-'.substr($key, 0, -6).'-'.$grid;
								}
								if ( substr($key, -4) == 'four' ) {
									$colFour .= ' col-'.substr($key, 0, -5).'-'.$grid;
								}

								if ( substr($key, -4) == 'five' ) {
									$colFive .= ' col-'.substr($key, 0, -5).'-'.$grid;
								}
								if ( substr($key, -3) == 'six' ) {
									$colSix .= ' col-'.substr($key, 0, -4).'-'.$grid;
								}
							}
						}
					}
				}
			}
		}

		$processedData['columnOne'] = trim($colOne.$columnOneExtraClass);
		$processedData['columnTwo'] = trim($colTwo.$columnTwoExtraClass);
		$processedData['columnThree'] = trim($colThree.$columnThreeExtraClass);
		$processedData['columnFour'] = trim($colFour.$columnFourExtraClass);
		$processedData['columnFive'] = trim($colFive.$columnFiveExtraClass);
		$processedData['columnSix'] = trim($colSix.$columnSixExtraClass);

		return $processedData;
	}


	/**
	 * Returns an instance of the background image utility
	 */
	protected function getBackgroundImageUtility(): BackgroundImageUtility
	{
		return GeneralUtility::makeInstance(BackgroundImageUtility::class);
	}

}
