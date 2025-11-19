<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
                # pages
                if (str_starts_with($dropdownItem['list']['group'], 't3:')) {
                    $btnDropdownItem[$key]['link'] = $dropdownItem['list']['group'];
                    if (ExtensionManagementUtility::isLoaded('iconpack')) {
                        $pid = (int) explode('=', $dropdownItem['list']['group'])[1];
                        $btnDropdownItem[$key]['page_icon'] = BackendUtility::getRecord('pages', (int)$pid, 'page_icon')['page_icon'];
                    }
                    $tile = '';
                    if (!empty($dropdownItem['list']['title'])) {
                        $tile = $dropdownItem['list']['title'];
                    } else {
                        if (str_contains($dropdownItem['list']['group'], '"')) {
                            $tile = explode('"', $dropdownItem['list']['group'])[1];
                        } else {
                            $tile = end(explode(' ', $dropdownItem['list']['group']));
                        }
                    }
                    $btnDropdownItem[$key]['title'] = !empty($tile) ? $tile : '* no title assigned *';
                }
                # mail
                if (str_starts_with($dropdownItem['list']['group'], 'mailto:')) {
                    $groupArr = explode('?', $dropdownItem['list']['group']);
                    $emailAddress = $groupArr[0];
                    if (str_starts_with($groupArr[1], 'subject=')) {
                        $subjectArr = explode('&', $groupArr[1])[0];
                        $subject = explode('=', $subjectArr);
                        $subject = str_replace('%20', ' ', $subject[1]);
                        $btnDropdownItem[$key]['subject'] = !empty($subject) ? $subject : '';
                    }
                    $btnDropdownItem[$key]['emailAddress'] = $emailAddress;
                    $btnDropdownItem[$key]['title'] = !empty($dropdownItem['list']['title']) ? $dropdownItem['list']['title'] : '* no title assigned *';
                }
                $btnDropdownItem[$key]['target'] = '_self';
            }
        }

        $processedData['dropdownItems'] = $btnDropdownItem;
        $outline = !empty($flexconf['outline']) ? 'outline-' : '';
        $style = !empty($flexconf['style']) ? $flexconf['style'] : '';
        $typolinkButtonClass = ' btn btn-'.$outline.$style;
        $typolinkButtonClass .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] !== 'default' ? ' '.$flexconf['btnsize'] : '';
        if (empty($parentflexconf)) {
            $processedData['btn-block'] = false;
            if (!empty($flexconf['block'])) {
                $processedData['btn-block'] = true;
            }
        }
        $headerPosition = '';
        if ($processedData['data']['header_position']) {
            $headerPosition = $processedData['data']['header_position'];
            if ($headerPosition === 'left') {
                $headerPosition = '';
            }
            if ($headerPosition === 'center') {
                $headerPosition = 'text-center';
            }
            if ($headerPosition === 'right') {
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
         && $parentflexconf['fixedPosition'] === 'right'
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
