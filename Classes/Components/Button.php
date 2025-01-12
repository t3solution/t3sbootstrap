<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Button implements SingletonInterface
{
    /**
     * Returns the $processedData
     */
    public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf): array
    {
        $btnDropdownItem = [];
        if (!empty($flexconf['dropdownItems']) && is_array($flexconf['dropdownItems'])) {
            $processedData['dropdowndirection'] = !empty($flexconf['direction']) ? ' '.$flexconf['direction'] : '';

            foreach ($flexconf['dropdownItems'] as $key=>$dropdownItem) {
				$pid = (int) substr(explode(' ', $dropdownItem['list']['group'])[0], -1);
                $btnDropdownItem[$key]['pid'] = $pid;
                $btnDropdownItem[$key]['page_icon'] = BackendUtility::getRecord('pages', intval($pid), 'page_icon')['page_icon'];
                $btnDropdownItem[$key]['link'] = $dropdownItem['list']['group'];
                $btnDropdownItem[$key]['target'] = explode('=', $dropdownItem['list']['group'])[1];
            }
        }
        $processedData['dropdownItems'] = !empty($btnDropdownItem) ? $btnDropdownItem : [];
        $outline = !empty($flexconf['outline']) ? 'outline-' : '';
        $style = !empty($flexconf['style']) ? $flexconf['style'] : '';
        $typolinkButtonClass = ' btn btn-'.$outline.$style;
        $typolinkButtonClass .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] != 'default' ? ' '.$flexconf['btnsize'] : '';
        if (empty($parentflexconf)) {
            $processedData['btn-block'] = false;
            if (!empty($flexconf['block'])) {
                $processedData['btn-block'] = true;
            }
        }
        $headerPosition = '';
        if ($processedData['data']['header_position']) {
            $headerPosition = $processedData['data']['header_position'];
            if ($headerPosition == 'left') {
                $headerPosition = '';
            }
            if ($headerPosition == 'center') {
                $headerPosition = 'text-center';
            }
            if ($headerPosition == 'right') {
                $headerPosition = 'd-md-flex justify-content-md-end';
            }
        }

        $processedData['headerPosition'] = $headerPosition;

        if (!empty($flexconf['fixedPosition'])) {
            $typolinkButtonClass .= ' d-none fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
            $typolinkButtonClass .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
            $processedData['fixedButton'] = $flexconf['fixedPosition'];
        }

        $processedData['linkTitle'] = !empty($flexconf['linkTitle']) ? $flexconf['linkTitle'] : '';
        $processedData['slideInButton'] = false;
        $processedData['slideInButtonFaIcon'] = false;

        if (!empty($parentflexconf['fixedPosition'])
         && $parentflexconf['fixedPosition'] == 'right'
         && $parentflexconf['slideIn']
         && $parentflexconf['visiblePart']
         && $parentflexconf['vertical']
        ) {
            // slide in button
            $processedData['slideInButton'] = true;
            $processedData['slideInButtonFaIcon'] = true;
            if ( empty($processedData['data']['header_icon']) ) {
                $processedData['data']['header_icon'] = 'fa6:solid,ban';                
            }
        }

        $processedData['class'] .= $typolinkButtonClass;

        return $processedData;
    }

}
