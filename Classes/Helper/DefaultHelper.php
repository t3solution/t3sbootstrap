<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use T3SBS\T3sbootstrap\DataProcessing\BootstrapProcessor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class DefaultHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getContainerClass(
		array $processedData, 
		string $extConfContainer, 
		array $containerConfig
	): array
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
		}

		return $processedData;
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
		string $parentCType,
		array $containerConfig = []
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
		#
		# colPos 0 is the main column, colPos > 199 are the columns of a container.
		# Both are content areas - the same test tt_content.stdWrap.prepend and the
		# section menu use. Limiting this to colPos 0 left every element inside a
		# container without the margin, so the automatic spacing stopped exactly
		# where layouts are built.
		#
		# Jumbotron (3), footer column (4) and the expanded content areas (20, 21)
		# fall out through their colPos. The footer PAGE does not: its elements sit
		# in colPos 0 like any other page, so it is tested separately.
		#
		# A wrapper is the exception: it brings its own padding, and the first element
		# in it would push a gap between the wrapper's edge and its content. Only the
		# column containers (two_columns and friends) keep the margin - there the
		# elements stand one below the other and need the rhythm.
		$colPos = (int)($processedData['data']['colPos'] ?? 0);
		$footerPid = (int)($containerConfig['footerPid'] ?? 0);
		$isFooterPage = $footerPid > 0 && (int)($processedData['data']['pid'] ?? 0) === $footerPid;
		$isInWrapper = $parentCType !== '' && in_array(
			$parentCType,
			GeneralUtility::trimExplode(',', BootstrapProcessor::TX_CONTAINER, true),
			true
		);
		$hasMarginTop = (bool)preg_match('/(^|\s)m[ty]?-/', (string)$processedData['class']);
		if ($contentMarginTop && !$isInWrapper && !$isFooterPage
			&& ($colPos === 0 || $colPos > 199) && $hasMarginTop === FALSE ) {
			// A few containers print their header BEFORE the element that carries
			// $processedData['class'] - the background wrapper is one. The margin
			// would open a gap between header and section instead of in front of
			// the whole block, so it goes on the <header> there. The flag is set by
			// the wrapper, which alone knows whether a header is printed above.
			if (!empty($processedData['marginTopOnHeader'])) {
				$processedData['header']['class'] = trim(
					($processedData['header']['class'] ?? '').' '.$contentMarginTop
				);
			} else {
				$processedData['class'] .= ' '.$contentMarginTop;
			}
		}

		return $processedData;
	}

}
