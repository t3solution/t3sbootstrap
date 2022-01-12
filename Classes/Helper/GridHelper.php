<?php
namespace T3SBS\T3sbootstrap\Helper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;


class GridHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 *
	 * @param array $data
	 * @param array	$flexconf
	 *
	 * @return array
	 */
	public function getGrid($processedData, $flexconf)
	{

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

		if ( $flexconf['equalWidth'] ) {

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

}
