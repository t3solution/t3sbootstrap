<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class DefaultHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getContainerClass(array $processedData, string $extConfContainer, array $containerConfig): array
	{
		$container = '';

		if (!empty($extConfContainer) && $processedData['data']['tx_t3sbootstrap_container']) {
			if ( $processedData['data']['tx_container_parent'] === 0 ) {
				if ( (int)$containerConfig['footerPid'] === (int)$processedData['data']['pid'] ) {
					if ( $containerConfig['footerContainer'] === 'none' && $processedData['data']['colPos'] === 0 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
				} else {
					if ( $containerConfig['pageContainer'] === FALSE && $processedData['data']['colPos'] === 0 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $containerConfig['jumbotronContainer'] === 'none' && $processedData['data']['colPos'] === 3 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $containerConfig['expandedcontentContainertop'] === 'none' && $processedData['data']['colPos'] === 20 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $containerConfig['expandedcontentContainerbottom'] === 'none' && $processedData['data']['colPos'] === 21 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
					if ( $containerConfig['footerContainer'] === 'none' && $processedData['data']['colPos'] === 4 ) {
						$container = $processedData['data']['tx_t3sbootstrap_container'];
					}
				}
			}
		}

		if (!empty($container)) {
			$processedData['containerPre'] = '<div class="'.trim($container).'">';
			$processedData['containerPost'] = '</div>';
			$processedData['container'] = trim($container);
		} else {
			$processedData['containerError'] = FALSE;
			if ( ($processedData['be_layout'] === 'OneCol' || $processedData['be_layout'] === 'OneCol_Extra') && !empty($containerConfig['containerError']) ) {
				$processedData['containerError'] = $this->getContainerError($processedData['data'], $containerConfig);
			}
		}

		return $processedData;
	}


	/**
	 * Returns the Container Error
	 */
	public function getContainerError(array $data, array $containerConfig): bool
	{
		$error = FALSE;
		if ( $data['tx_container_parent'] === 0 ) {
			if ( $containerConfig['footerPid'] === $data['pid'] ) {
				if ( $containerConfig['footerContainer'] === 'none' && $data['colPos'] === 0 ) {
					$error = TRUE;
				}
				if ( $containerConfig['pageContainer'] === FALSE && $data['colPos'] === 0 ) {
					$error = TRUE;
				}
				if ( $containerConfig['jumbotronContainer'] === 'none' && $data['colPos'] === 3 ) {
					$error = TRUE;
				}
				if ( $containerConfig['expandedcontentContainertop'] === 'none' && $data['colPos'] === 20 ) {
					$error = TRUE;
				}
				if ( $containerConfig['expandedcontentContainerbottom'] === 'none' && $data['colPos'] === 21 ) {
					$error = TRUE;
				}
				if ( $containerConfig['footerContainer'] === 'none' && $data['colPos'] === 4 ) {
					$error = TRUE;
				}
			}
		}

		return $error;
	}



	/**
	 * Returns the $processedData
	 */
	public function getDefaults(
		array $processedData,
		array $flexconf,
		int $defaultHeaderType,
		string $contentMarginTop,
		string $animateCss,
		string $parentCType
	): array
	{
		$cType = $processedData['data']['CType'];

		// default header type
		switch ( $cType ) {
			case 't3sbs_card':
				$processedData['header']['default'] = 4;
				break;
			case 't3sbs_mediaobject':
				$processedData['header']['default'] = 5;
				break;
			default:
				$processedData['header']['default'] = $defaultHeaderType;
		}

		// content element link
		$flexconf['bgwlink'] = !empty($flexconf['bgwlink']) ? $flexconf['bgwlink'] : '';

		if ( ($processedData['data']['tx_t3sbootstrap_header_celink'] && $processedData['data']['header_link'])
			|| (!empty($flexconf['bgwlink']) && $processedData['data']['header_link']) ) {
			if ( $cType === 't3sbs_card' ) {
				if (!empty($flexconf['button']['enable'])) {
					$processedData['card']['button']['link'] = $processedData['data']['header_link'];
				}
			}
			if ( $parentCType !== 'listGroup_wrapper' ) {
				$processedData['class'] .= ' ce-link-content';
			}
			$processedData['celink'] = $processedData['data']['header_link'];
			$processedData['data']['header_link'] = '';
			// no image zoom if ce-link (did not work)
			$processedData['data']['image_zoom'] = '';
			$processedData['addmedia']['imagezoom'] = '';
		}

		// animate css for all CEs exept t3sbs_carousel & collapsible_accordion
		if ($animateCss && (!empty($processedData['data']['tx_t3sbootstrap_animateCss']) || !empty($flexconf['animate']))
		 && $cType !== 't3sbs_carousel' && $cType !== 'collapsible_accordion')
		{
			$processedData['isAnimateCss'] = TRUE;
			if ( !empty($processedData['data']['tx_t3sbootstrap_animateCss']) ) {
				$processedData['class'] .= ' animated '.$processedData['data']['tx_t3sbootstrap_animateCss'];
				if( $processedData['data']['tx_t3sbootstrap_animateCssRepeat'] ) {
					$processedData['dataAnimate'] = $processedData['data']['tx_t3sbootstrap_animateCss'];
					$processedData['class'] .= ' bt_hidden';
					$processedData['animateCssRepeat'] = TRUE;
				}
				$cssDelay = substr($processedData['data']['tx_t3sbootstrap_animateCssDelay'], 0, -1);
				$cssDuration = substr($processedData['data']['tx_t3sbootstrap_animateCssDuration'], 0, -1);
				if (!empty($cssDuration) && $cssDuration !== '0.0' ) {
					$processedData['style'] .= ' animation-duration: '.$cssDuration.'s;';
				}
				if (!empty($cssDelay) && $cssDelay !== '0.0' ) {
					$processedData['style'] .= ' animation-delay: '.$cssDelay.'s;';
				}
			}
		}

		// extend flexforms with custom fields
		$flexconf['ffExtra'] = $flexconf['ffExtra'] ?? '';
		if ( is_array($flexconf['ffExtra']) ) {
			$processedData['ffExtra'] = $flexconf['ffExtra'];
		}

		# default margin-top for each content-element if no margin-top
		$hasMarginTop = strpos($processedData['class'], 'mt-') || strpos($processedData['class'], 'my-') || strpos($processedData['class'], 'm-');



		if ($contentMarginTop && $processedData['data']['colPos'] == 0 && $hasMarginTop == FALSE ) {
			$processedData['class'] .= ' '.$contentMarginTop;
		}

		return $processedData;
	}

}
