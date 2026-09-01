<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;

class StyleHelper implements SingletonInterface
{
    /**
     * Returns background color
     */
    public function getBgColor(array $data, bool $hexdec=true): string
    {
        $color = '';

        if ($data['tx_t3sbootstrap_bgcolor']
         && !$data['tx_t3sbootstrap_contextcolor']) {
            if ($data['tx_t3sbootstrap_bgopacity'] && $data['tx_t3sbootstrap_bgopacity'] != 1) {
                // if opacity
                $rgb = $this->hex2RGB($data['tx_t3sbootstrap_bgcolor']);
                // An unparsable value used to produce "rgba(,0.5)" - keep the plain
                // colour in that case instead of emitting a broken declaration.
                $color = $rgb !== ''
                    ? 'background-color: rgba('.$rgb.','.$data['tx_t3sbootstrap_bgopacity'].');'
                    : 'background-color: '.$data['tx_t3sbootstrap_bgcolor'].';';
            } elseif ($hexdec) {
                $color = 'background-color: '.$data['tx_t3sbootstrap_bgcolor'].';';
            }
        } elseif (!empty($data['tx_t3sbootstrap_contextcolor'])) {
            if ($data['tx_t3sbootstrap_bgopacity'] < 1 && $data['tx_t3sbootstrap_bgopacity'] > 0) {
                $color = '--bs-bg-opacity: '.$data['tx_t3sbootstrap_bgopacity'].';';
            }
        }

        return $color;
    }


    /**
    * Convert a hexa decimal color code to its RGB equivalent
    */
    public function hex2RGB(string $hexStr, string $seperator = ','): string
    {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        // The colour picker of tx_t3sbootstrap_bgcolor has "opacity => true", so an
        // editor using its alpha slider stores #RRGGBBAA (or #RGBA). The alpha is
        // dropped here - the opacity comes from tx_t3sbootstrap_bgopacity.
        if (strlen($hexStr) == 8) {
            $hexStr = substr($hexStr, 0, 6);
        } elseif (strlen($hexStr) == 4) {
            $hexStr = substr($hexStr, 0, 3);
        }
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat($hexStr[0], 2));
            $rgbArray['green'] = hexdec(str_repeat($hexStr[1], 2));
            $rgbArray['blue'] = hexdec(str_repeat($hexStr[2], 2));
        } else {
            return ''; //Invalid hex color code
        }
        return implode($seperator, $rgbArray);
    }
}
