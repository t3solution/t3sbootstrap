<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use T3SBS\T3sbootstrap\Helper\FlexformHelper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Card implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf): array
	{
		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexformHelper = GeneralUtility::makeInstance(FlexformHelper::class);
		$pi_flexform = $flexformService->convertFlexFormContentToArray($processedData['data']['pi_flexform']);
		$pi_flexform = $flexformHelper->addMissingElements($pi_flexform, 't3sbs_card', TRUE);
		$flexconf = array_merge ($pi_flexform, $flexconf);
		$parentflexconf = [];
		$parentUid = $processedData['data']['tx_container_parent'];
		if ($parentUid) {
			$parentFlexformData = BackendUtility::getRecord('tt_content', $parentUid, 'CType, tx_t3sbootstrap_flexform');
			if ($parentFlexformData['tx_t3sbootstrap_flexform']) {
				$parentflexconf = $flexformService->convertFlexFormContentToArray($parentFlexformData['tx_t3sbootstrap_flexform']);
				$parentflexconf =$flexformHelper->addMissingElements($parentflexconf, $parentFlexformData['CType'], TRUE);
			}
		}
		$cardData = $flexconf;
		// crop max characters
		$cardData['cropMaxCharacters'] = !empty($parentflexconf['cropMaxCharacters']) ? $parentflexconf['cropMaxCharacters'] : '';
		// image position
		if ( (int)$processedData['data']['imageorient'] == 8 ) {
			$processedData['data']['imageorient'] = 'bottom';
		} else {
			$processedData['data']['imageorient'] = 'top';
		}
		// title position
		if ( !empty($cardData['title']['onTop']) && $processedData['data']['imageorient'] == 'top' && !$cardData['image']['overlay'] ) {
			$cardData['title']['position'] = 'top';
		} else {
			$cardData['title']['position'] = 'default';
		}
		// button
		if ( !empty($cardData['text']['top']) && $cardData['text']['bottom'] ) {
			$cardData['button']['position'] = 'bottom';
		} elseif ( !empty($cardData['text']['top']) ) {
			$cardData['button']['position'] = 'top';
		} else {
			$cardData['button']['position'] = 'bottom';
		}
		if (!empty($flexconf['button']['enable'])) {
			$cardData['button']['link'] = $processedData['data']['header_link'];
			$processedData['data']['header_link'] = '';
		}
		$cardData['button']['linkClass'] = !empty($flexconf['button']['outline']) ? '-outline': '';
		$cardData['button']['linkClass'] .= !empty($flexconf['button']['style']) ? '-'.$flexconf['button']['style'] : '';
		$cardData['button']['linkClass'] .= !empty($flexconf['button']['block']) ? ' btn-block' : '';
		$cardData['button']['linkClass'] .= !empty($flexconf['button']['stretchedLink']) ? ' stretched-link' : '';
		// dimensions
		$cardData['dimensions']['width'] = $processedData['data']['imagewidth'];
		$cardData['dimensions']['height'] = $processedData['data']['imageheight'];
		// class
		$cardClass = 'card'.$processedData['class'];
		$cardClass .= !empty($flexconf['button']['stretchedLink']) ? ' ce-link-content' : '';
		// image
		if ( !empty($cardData['image']['overlay']) ) {
			$cardClass .= ' overflow-hidden';
			$cardData['image']['class'] = 'img-fluid';
			$cardData['image']['overlay'] = 'card-img-overlay d-flex';
			$cardData['mobile']['overlay'] = FALSE;
		} else {
			if ( !empty($cardData['mobile']['overlay']) ) {
				# card-img-overlay for mobile < 576 by JS and class overlay
				$cardData['mobile']['overlay'] = 'img-overlay';
			}
			if ( $processedData['data']['imageorient'] == 'top' ) {
				if ( !empty($cardData['title']['onTop']) || !empty($cardData['header']['text']) ) {
					$cardData['image']['class'] = 'img-fluid';
				} else {
					$cardData['image']['class'] = 'card-img-top img-fluid';
				}
			} else {
				if ( $cardData['footer']['text'] ) {
					$cardData['image']['class'] = 'img-fluid';
				} else {
					$cardData['image']['class'] = 'card-img-bottom img-fluid';
				}
			}
		}
		// block
		if ( empty($cardData['text']['top']) && empty($cardData['text']['bottom']) && empty($processedData['data']['header']) && empty($processedData['data']['subheader']) ) {
			$cardData['block']['enable'] = FALSE;
		} else {
			$cardData['block']['enable'] = TRUE;
		}
		// flip card
		if ( !empty($flexconf['flipcard']) ) {
			$backstyle = '';
			$backclass = '';
			$cardClass .= ' flip-card border-0 bg-transparent';
			$cardData['flipcard'] = TRUE;
			$cardData['rotateY'] = $flexconf['rotateY'];
			if ( $processedData['data']['tx_t3sbootstrap_textcolor'] ) {
				$backclass .= 'text-'.$processedData['data']['tx_t3sbootstrap_textcolor'];
			}
			if ( $processedData['data']['tx_t3sbootstrap_bgcolor'] ) {
				$backstyle .= $processedData['data']['tx_t3sbootstrap_bgcolor'];
			} else {
				if ( $processedData['data']['tx_t3sbootstrap_contextcolor'] ) {
					$backclass .= ' bg-'.$processedData['data']['tx_t3sbootstrap_contextcolor'];
				}
			}
			$processedData['backclass'] = trim((string)$backclass);
			$processedData['backstyle'] = $backstyle;
		} else {
			$cardClass .= $processedData['data']['tx_t3sbootstrap_header_position'] ? ' '.$processedData['data']['tx_t3sbootstrap_header_position']:'';
		}
		// list group
		if ( !empty($flexconf['list']['container']) && is_array($flexconf['list']['container']) ) {
			foreach( $flexconf['list'] as $container ) {
				foreach ($container as $list)
					$listGroup[] = $list['list']['group'];
			}
			$cardData['list'] = $listGroup;
		}

		// profile card
		$cardData['multiImage']['enable'] = FALSE;
		if ( !empty($flexconf['multiImage']['enable']) ) {
			$cardData['multiImage']['enable'] = TRUE;
			$cardData['multiImage']['percent'] = '0.'.$flexconf['multiImage']['percent'];
			$cardData['multiImage']['style'] = 'top: -' . $flexconf['multiImage']['percent']/2 .'px';
			$borderColor = $flexconf['multiImage']['borderColor'] ? ' border-'.$flexconf['multiImage']['borderColor'] : '';
			$shadow = $flexconf['multiImage']['shadow'] ? ' circularshadow' : '';
			$cardData['multiImage']['shadow'] = $flexconf['multiImage']['shadow'] ? TRUE : FALSE;
			$cardData['multiImage']['border'] = $flexconf['multiImage']['borderWidth'].$borderColor.$shadow;
			$cardData['multiImage']['slope'] =	$flexconf['multiImage']['diagonal'] ? $flexconf['multiImage']['slope'] : 0;
			$cardData['multiImage']['socialmedia']['enable'] = $flexconf['multiImage']['socialmedia']['enable'];
			$cardData['multiImage']['socialmedia']['footer'] = $flexconf['multiImage']['socialmedia']['footer'];
			if (!empty($flexconf['multiImage']['socialmedia']['enable'])) {
				foreach ($flexconf['multiImage']['socialmedia'] as $key=>$socialmedia) {
					if ($key != 'enable' && $key != 'footer' &&  !empty($socialmedia)) {
						$cardData['multiImage']['socialmediaLinks'][$key] = $socialmedia;
					}
				}
			}
		}
		// header position
		if ( $processedData['data']['header_position'] ) {
			$headerPosition = $processedData['data']['header_position'];
			if ( $headerPosition == 'left' ) $headerPosition = 'start';
			if ( $headerPosition == 'right' ) $headerPosition = 'end';
			$cardClass .= ' text-'.$headerPosition;
		}
		// effect
		if ( !empty($cardData['effect']) ) {
			$cardClass .= ' card-effect-'.$cardData['effect'];
		}
		// custom border class
		if ( !empty($cardData['cardborder']) ) {
			$cardClass .= ' border-'.$cardData['cardborderstyle'];
		}
		// parent equal Height
		if ( !empty($parentflexconf['equalHeight']) ) {
			$cardClass .= ' h-100';
		}

		$processedData['class'] = trim($cardClass);

		// addmedia
		$processedData['addmedia']['imgclass'] = $cardData['image']['class'];
		$processedData['addmedia']['imgclass'] .= !empty($flexconf['horizontal']) ? ' rounded-start' : '';
		$processedData['addmedia']['figureclass'] = ' text-center';
		$processedData['addmedia']['figureclass'] .= !empty($flexconf['horizontal']) ? ' d-block' : '';

		$processedData['card'] = $cardData;

		if ( !empty($processedData['data']['imagewidth']) && !empty($flexconf['maxwidth'])) {
			$processedData['style'] .= ' max-width: '.$processedData['data']['imagewidth'].'px;';
		}

		return $processedData;
	}

}
