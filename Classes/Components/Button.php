<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Button implements SingletonInterface
{

    public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf): array
    {
        $btnDropdownItem = [];

        if (!empty($flexconf['dropdownItems']) && is_array($flexconf['dropdownItems'])) {
            $processedData['dropdowndirection'] = !empty($flexconf['direction']) ? ' '.$flexconf['direction'] : '';
            foreach ($flexconf['dropdownItems'] as $key=>$dropdownItem) {
                // pages
                if (str_starts_with($dropdownItem['list']['group'], 't3:')) {
                    $btnDropdownItem[$key]['link'] = $dropdownItem['list']['group'];
                    if (ExtensionManagementUtility::isLoaded('iconpack')) {
                        $pid = (int) explode('=', $dropdownItem['list']['group'])[1];
                        $btnDropdownItem[$key]['page_icon'] = BackendUtility::getRecord('pages', $pid, 'page_icon')['page_icon'];
                    }
                    $tile = '';
                    if (!empty($dropdownItem['list']['title'])) {
                        $tile = $dropdownItem['list']['title'];
                    } else {
                        if (str_contains($dropdownItem['list']['group'], '"')) {
                            $tile = explode('"', $dropdownItem['list']['group'])[1];
                        } else {
                            $array = explode(' ', $dropdownItem['list']['group']);
                            $tile = end($array);
                        }
                    }
                    $btnDropdownItem[$key]['title'] = !empty($tile) ? $tile : '* no title assigned *';
                }
                // mail
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
        $style = $flexconf['style'] ?? '';
        $typolinkButtonClass = ' btn btn-'.$outline.$style;
        $typolinkButtonClass .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] !== 'default' ? ' '.$flexconf['btnsize'] : '';
        if (empty($parentflexconf)) {
            $processedData['btn-block'] = !empty($flexconf['block']);
        }
        $headerPosition = '';
        $headerPosition = match($processedData['data']['header_position'] ?? '') {
            'center' => 'text-center',
            'right'  => 'd-md-flex justify-content-md-end',
            default  => '',
        };

        $processedData['headerPosition'] = $headerPosition;

        if (!empty($flexconf['fixedPosition'])) {
            $typolinkButtonClass .= ' d-none fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
            if (!empty($flexconf['rotate']) && $flexconf['rotate'] === 'vertical') {
                $typolinkButtonClass .= ' vertical-lr';
            } else {
                $typolinkButtonClass .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
            }
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
                $processedData['data']['header_icon'] = 'fa7:solid,ban';
            }
        }

        $processedData['class'] .= $typolinkButtonClass;

        return $processedData;
    }

}
