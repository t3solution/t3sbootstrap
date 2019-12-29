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

class StyleHelper implements SingletonInterface
{

	/**
	 * Returns background color
	 *
	 * @param boolean $hexdec
	 * @return string
	 */
	public function getBgColor( $data, $hexdec=TRUE )
	{
		$color = '';

		if ( $data['tx_t3sbootstrap_bgcolor']
		 && !$data['tx_t3sbootstrap_contextcolor'] ) {

			if ( $data['tx_t3sbootstrap_bgopacity'] && $data['tx_t3sbootstrap_bgopacity'] != 100 ) {
				// if opacity
				$opacity = (int)$data['tx_t3sbootstrap_bgopacity'] / 100;
				$rgba = self::hex2RGB($data['tx_t3sbootstrap_bgcolor'], true).','.$opacity;
				$color = 'background-color: rgba('.$rgba.');';
			} elseif ( $hexdec ) {
				$color = 'background-color: '.$data['tx_t3sbootstrap_bgcolor'].';';
			}
		}

		return $color;
	}


	/**
	* Convert a hexa decimal color code to its RGB equivalent
	*
	* @param string $hexStr (hexadecimal color value)
	* @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	* @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	* @return array or string (depending on second parameter. Returns False if invalid hex color value)
	*/
	function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}


}
