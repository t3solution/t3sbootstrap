<?php
	
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class FlexformHelper implements SingletonInterface
{
	/**
	 * Returns the flexform with missing elements
	 */
	public function addMissingElements(array $flexconf, string $cType, bool $t3sbsElement): array
	{
		$flexconfEl = [];
		$dataStructure = [];
		$file = '';
		$flexfomrsFolderPath = 'EXT:t3sbootstrap/Configuration/FlexForms/';

		if ( $t3sbsElement === FALSE ) {
			$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Bootstrap.xml';
		} else {
			if ($cType == 't3sbs_button') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Button.xml';
			}
			if ($cType == 't3sbs_card') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'CardSetting.xml';
			}
			if ($cType == 't3sbs_carousel') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Carousel.xml';
			}
			if ($cType == 't3sbs_mediaobject') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Mediaobject.xml';
			}
			if ($cType == 'table') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Table.xml';
			}
			if ($cType == 't3sbs_toast') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'ToastSetting.xml';
			}
			if ($cType == 't3sbs_gallery') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Bootstrap.xml';
			}
			# container
			if ($cType == 'carousel_container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/CarouselContainer.xml';
			}
			if ($cType == 'toast_container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/ToastContainer.xml';
			}
			if ($cType == 'button_group') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/Buttongroup.xml';
			}
			if ($cType == 'card_wrapper') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/CardWrapper.xml';
			}
			if ($cType == 'background_wrapper') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/BackgroundWrapper.xml';
			}
			if ($cType == 'parallax_wrapper') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/ParallaxWrapper.xml';
			}
			if ($cType == 'autoLayout_row') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/AutoLayoutRow.xml';
			}
			if ($cType == 'container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/Container.xml';
			}
			if ($cType == 'collapsible_container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/CollapseContainer.xml';
			}
			if ($cType == 'collapsible_accordion') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/Collapse.xml';
			}
			if ($cType == 'modal') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/Modal.xml';
			}
			if ($cType == 'tabs_container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/Tabs.xml';
			}
			if ($cType == 'tabs_tab') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/TabsTab.xml';
			}
			if ($cType == 'listGroup_wrapper') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Bootstrap.xml';
			}
			if ($cType == 'masonry_wrapper') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/MasonryWrapper.xml';
			}
			if ($cType == 'swiper_container') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/SwiperContainer.xml';
			}
			if ($cType == 'two_columns') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/TwoColumns.xml';
			}
			if ($cType == 'three_columns') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/ThreeColumns.xml';
			}
			if ($cType == 'four_columns') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/FourColumns.xml';
			}
			if ($cType == 'six_columns') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/SixColumns.xml';
			}
			if ($cType == 'row_columns') {
				$file = Environment::getPublicPath().$flexfomrsFolderPath . 'Container/RowColumns.xml';
			}
		}

		if ( !empty($file) ) {
			$content = @file_get_contents($file);
			if ($content) {
				$dataStructure = GeneralUtility::xml2array($content);
			}

			if ( !empty($dataStructure) && count($dataStructure['sheets']) ) {
				foreach ( $dataStructure['sheets'] as $tab=>$sheet ) {
					if ( is_array($dataStructure['sheets'][$tab]['ROOT']['el']) ) {
						foreach($dataStructure['sheets'][$tab]['ROOT']['el'] as $key=>$el) {
							if ( str_contains($key, '.') ) {
								$ak = explode('.', $key);
								$flexconfEl[$ak[0]][$ak[1]] = '';
							} else {
								$flexconfEl[$key] = '';
							}
						}
					}
				}
			}
			$flexconf = array_merge($flexconfEl, $flexconf);
		}

		return $flexconf;
	}


}
