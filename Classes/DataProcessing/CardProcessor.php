<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\ConnectionPool;

class CardProcessor implements DataProcessorInterface {

	/**
	 * Process data for a card, CType "t3sbs_card"
	 *
	 * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration,	 array $processedData)
	{

		$flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$pi_flexform = $flexformService->convertFlexFormContentToArray($processedData['data']['pi_flexform']);
		$tx_t3sbootstrap_flexform = $flexformService->convertFlexFormContentToArray($processedData['data']['tx_t3sbootstrap_flexform']);
		$parentUid = $processedData['data']['tx_container_parent'];
		$parentflexconf = [];

		if ($processedData['data']['tx_container_parent']) {

			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
			$queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
			$parent = $queryBuilder
				->select('tx_t3sbootstrap_flexform')
				->from('tt_content')
				->where(
					$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($processedData['data']['tx_container_parent'], \PDO::PARAM_INT))
				)
				->execute()
				->fetch();

			if ($parent['tx_t3sbootstrap_flexform'])
			$parentflexconf = $flexformService->convertFlexFormContentToArray($parent['tx_t3sbootstrap_flexform']);
		}

		$flexconf = array_merge ($pi_flexform, $tx_t3sbootstrap_flexform);

		// List-group if available
		foreach($flexconf as $key=>$ff) {
			if ($key == 'list') {
				if ( is_array($ff['container']) ) {
					foreach($ff['container'] as $container) {
						if ($container['list']['group']) {
							$cardData[$key][] = $container['list']['group'];
						}
					}
				}
			} else {
				$cardData[$key] = $ff;
			}
		}

		// crop max characters
		$cardData['cropMaxCharacters'] = $parentflexconf['cropMaxCharacters'];

		// image position
		if ( (int)$processedData['data']['imageorient'] == 8 ) {
			$processedData['data']['imageorient'] = 'bottom';
		} else {
			$processedData['data']['imageorient'] = 'top';
		}

		// title position
		if ( $cardData['title']['onTop'] && $processedData['data']['imageorient'] == 'top' && !$cardData['image']['overlay'] ) {
			$cardData['title']['position'] = 'top';
		} else {
			$cardData['title']['position'] = 'default';
		}

		// text top
		if ( $cardData['text']['top'] && $cardData['text']['bottom'] ) {
			$cardData['button']['position'] = 'bottom';
		} elseif ( $cardData['text']['top'] ) {
			if ( $cardData['list'] ) {
				$cardData['button']['position'] = 'list';
			} else {
				$cardData['button']['position'] = 'top';
			}
		} else {
			$cardData['button']['position'] = 'bottom';
		}

		if ($flexconf['button']['enable']) {
			$cardData['button']['link'] = $processedData['data']['header_link'];
			$processedData['data']['header_link'] = '';
		}

		$cardData['button']['linkClass'] = $flexconf['button']['outline'] ? '-outline': '';
		$cardData['button']['linkClass'] .= $flexconf['button']['style'] ?: '';
		$cardData['button']['linkClass'] .= $flexconf['button']['block'] ? ' btn-block': '';
		$cardData['button']['linkClass'] .= $flexconf['button']['stretchedLink'] ? ' stretched-link': '';

		$cardData['dimensions']['width'] = $processedData['data']['imagewidth'];
		$cardData['dimensions']['height'] = $processedData['data']['imageheight'];

		// image
		if ( $cardData['image']['overlay'] ) {
			if ( $cardData['header']['text'] && $cardData['footer']['text'] ) {
				$cardData['image']['class'] = 'img-fluid';
			} elseif ( $cardData['header']['text'] ) {
				$cardData['image']['class'] = 'card-img-bottom img-fluid';
			} elseif ( $cardData['footer']['text'] ) {
				$cardData['image']['class'] = 'card-img-top img-fluid';
			} else {
				$cardData['image']['class'] = 'img-fluid';
			}

			$cardData['image']['overlay'] = 'card-img-overlay d-flex align-items-end';

			$cardData['mobile']['overlay'] = FALSE;
		} else {
			if ( $cardData['mobile']['overlay'] ) {
				# card-img-overlay for mobile < 576 by JS and class overlay
				$cardData['mobile']['overlay'] = 'img-overlay';
			}
			if ( $processedData['data']['imageorient'] == 'top' ) {
				if ( $cardData['title']['onTop'] || $cardData['header']['text'] ) {
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

		if ( !$cardData['text']['top'] && !$cardData['text']['bottom'] && !$processedData['data']['header'] && !$processedData['data']['subheader'] ) {
			$cardData['block']['enable'] = FALSE;
		} else {
			$cardData['block']['enable'] = TRUE;
		}

		// class
		$cardClass = 'card';
		$cardClass .= $parentflexconf['equalHeight'] ? ' h-100' : '';
		$cardClass .= $processedData['data']['tx_t3sbootstrap_header_position'] ? ' '.$processedData['data']['tx_t3sbootstrap_header_position']:'';
		if ( $processedData['data']['header_position'] ) {
			$headerPosition = $processedData['data']['header_position'];
			if ( $headerPosition == 'left' ) $headerPosition = 'start';
			if ( $headerPosition == 'right' ) $headerPosition = 'end';
			$cardClass .= ' text-'.$headerPosition;
		}
		// effect
		if ( $cardData['effect'] ) {
			$cardClass .= ' card-effect-'.$cardData['effect'];
		}
		// custom border class
		if ( $cardData['cardborder'] ) {
			$cardClass .= ' border-'.$cardData['cardborderstyle'];
		}
		$processedData['class'] = trim($cardClass);

		// addmedia
		$processedData['addmedia']['imgclass'] = $cardData['image']['class'];
		$processedData['addmedia']['imgclass'] .= $tx_t3sbootstrap_flexform['horizontal'] ? ' rounded-start' : '';
		$processedData['addmedia']['figureclass'] = ' text-center';
		$processedData['addmedia']['figureclass'] .= $tx_t3sbootstrap_flexform['horizontal'] ? ' d-block' : '';

		$processedData['card'] = $cardData;

		return $processedData;
	}

}
