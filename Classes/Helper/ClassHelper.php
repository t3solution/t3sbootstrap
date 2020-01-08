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

class ClassHelper implements SingletonInterface
{

	/**
	 * Returns the CSS-class for all elements
	 *
	 * @param array $data
	 * @param array	$flexconf
	 * @param array	$extConf
	 *
	 * @return string
	 */
	public function getAllClass($data, $flexconf, $extConf)
	{
	 	// class
		if ( $extConf['cTypeClass'] && $data['CType'] != 'gridelements_pi1') {
			$class = 'fsc-default ce-'. $data['CType'];
		} else {
			$class = '';
		}

		// Spacing: padding
		if ( $data['tx_t3sbootstrap_padding_sides'] ) {
			// on all 4 sides of the element
			if ( $data['tx_t3sbootstrap_padding_sides'] == 'blank' ) {
				$class .= ' p-'.$data['tx_t3sbootstrap_padding_size'];
			} else {
				$class .= ' p'.$data['tx_t3sbootstrap_padding_sides'].'-'.$data['tx_t3sbootstrap_padding_size'];
			}
		}
		// Spacing: margin
		if ( $data['tx_t3sbootstrap_margin_sides'] ) {
			// on all 4 sides of the element
			if ( $data['tx_t3sbootstrap_margin_sides'] == 'blank' ) {
				$class .= ' m-'.$data['tx_t3sbootstrap_margin_size'];
			} else {
				$class .= ' m'.$data['tx_t3sbootstrap_margin_sides'].'-'.$data['tx_t3sbootstrap_margin_size'];
			}
		}
		// Layout
		if ($data['layout']) {
			$pagesTSconfig = self::getFrontendController()->getPagesTSconfig();
			$layout = $data['layout'];
			$layoutAddItems = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['addItems.'];
			$layoutClasses = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['classes.'];
			$layoutAltLabels = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['altLabels.'];

			if (isset($layoutAddItems) && key($layoutAddItems) === $layout) {
				$class .= ' layout-'.$layout;
			} elseif (isset($layoutAltLabels) && $layoutAltLabels[$layout]) {
				if (isset($layoutClasses) && $layoutClasses[$layout]) {
					$class .= ' '.strtolower($layoutClasses[$layout]);
				} else {
					$class .= ' layout-'.str_replace(' ', '-', strtolower($layoutAltLabels[$layout]));
				}
			} else {
				$class .= ' layout-'.$layout;
			}
		}
		// Frame class
		if ($data['frame_class'] != 'default') {
			$class .= ' frame-'.$data['frame_class'];
		}

		// Align self
		if ($flexconf['responsiveVariations']) {
			$class .= $flexconf['alignSelf'] ? ' align-self-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignSelf'] : '';
		} else {
			$class .= $flexconf['alignSelf'] ? ' align-self-'.$flexconf['alignSelf'] : '';
		}

		$class .= $data['tx_t3sbootstrap_textcolor'] ? ' text-'.$data['tx_t3sbootstrap_textcolor'] : '';
		$class .= $data['tx_t3sbootstrap_contextcolor'] ? ' bg-'.$data['tx_t3sbootstrap_contextcolor'] : '';

		// Extra class
		$class .= $data['tx_t3sbootstrap_extra_class'] ? ' '.$data['tx_t3sbootstrap_extra_class'] : '';
		// Border
		if ( $flexconf['border'] && $data['CType'] != 't3sbs_card' ) {
			if ( $flexconf['border'] == 'border' ) {
				$border = 'border';
			} else {
				$border = 'border '.$flexconf['border'];
			}
			if ( $flexconf['borderstyle'] ) {
				$class .= ' '.$border.' border-'.$flexconf['borderstyle'];
			} else {
				$class .= ' '.$border;
			}
			$class .= $flexconf['borderradius'] ? ' '.$flexconf['borderradius'] : '';
		}

		// Hiding / Display Elements
		$class .= $flexconf['hidden'] ? ' '.$flexconf['hidden'] : '';

		// Float
		$class .= $flexconf['float'] ? ' '.$flexconf['float'] : '';

		// if media
		if ($data['assets'] || $data['image'] || $data['media']) {
			$imageorient = $data['imageorient'];
			if ( $imageorient == 10 || $imageorient == 17 || $imageorient == 18 ) {
				$class .= ' clearfix';
			}
		}

		return ' '.trim($class);
	}


	/**
	 * Returns the CSS-class for gridelements
	 *
	 * @param array $data
	 * @param array	$flexconf
	 * @param boolean $isVideo
	 * @param array	$extConf
	 *
	 * @return string
	 */
	public function getGeClass($data, $flexconf, $isVideo, $extConf)
	{
		/**
		 * CType: Gridelements
		 */
		if ( $extConf['cTypeClass'] ) {
			$class .= 'ge ge_'. $data['tx_gridelements_backend_layout'];
		}

		/**
		 * Background Wrapper
		 */
		if ( $data['tx_gridelements_backend_layout'] == 'background_wrapper' && $isVideo == FALSE ) {
			$class .= $flexconf['bgAttachmentFixed'] ? ' background-fixed' : '';
			if ((!$data['assets'] && $flexconf['imageRaster']) || ($flexconf['origImage'] && $flexconf['imageRaster'])) {
				$class .= ' bg-raster';
			}
		}

		/**
		 * Button group
		 */
		if ( $data['tx_gridelements_backend_layout'] == 'button_group' ) {
			$class .= $flexconf['size'] ? ' '.$flexconf['size'] : '';
			if ( $flexconf['fixedPosition'] ) {
				$class .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			} else {
				$class .= $flexconf['vertical'] ? ' btn-group-vertical' : ' btn-group';
			}
		}

		/**
		 * Auto-layout row/column
		 */
		if ( $data['tx_gridelements_backend_layout'] == 'autoLayout_row' ) {
			$class .= $flexconf['noGutters'] ? ' no-gutters' : '';
			if ($flexconf['responsiveVariations']) {
				$class .= $flexconf['justify'] ? ' justify-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['justify'] : '';
				$class .= $flexconf['alignItem'] ? ' align-items-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignItem'] : '';
			} else {
				$class .= $flexconf['alignItem'] ? ' align-items-'.$flexconf['alignItem'] : '';
				$class .= $flexconf['justify'] ? ' justify-content-'.$flexconf['justify'] : '';
			}
		}

		/**
		 * Container
		 */
		if ( $data['tx_gridelements_backend_layout'] == 'container' ) {
			if ($flexconf['flexContainer']) {
				if ($flexconf['responsiveVariations']) {
					$class .= $flexconf['flexContainer'] ? ' d-'.$flexconf['responsiveVariations'].'-'.$flexconf['flexContainer'] : '';
					$class .= $flexconf['direction'] ? ' flex-'.$flexconf['responsiveVariations'].'-'.$flexconf['direction'] : '';
					$class .= $flexconf['justify'] ? ' justify-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['justify'] : '';
					$class .= $flexconf['alignItem'] ? ' align-items-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignItem'] : '';
					$class .= $flexconf['wrap'] ? ' flex-'.$flexconf['responsiveVariations'].'-'.$flexconf['wrap'] : '';
					$class .= $flexconf['alignContent'] ? ' align-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignContent'] : '';
				} else {
					$class .= $flexconf['flexContainer'] ? ' d-'.$flexconf['flexContainer'] : '';
					$class .= $flexconf['direction'] ? ' flex-'.$flexconf['direction'] : '';
					$class .= $flexconf['justify'] ? ' justify-content-'.$flexconf['justify'] : '';
					$class .= $flexconf['alignItem'] ? ' align-items-'.$flexconf['alignItem'] : '';
					$class .= $flexconf['wrap'] ? ' flex-'.$flexconf['wrap'] : '';
					$class .= $flexconf['alignContent'] ? ' align-content-'.$flexconf['alignContent'] : '';
				}
			}
		}

		/**
		 * Grid
		 */
		if ( $data['tx_gridelements_backend_layout'] == 'two_columns'
		 || $data['tx_gridelements_backend_layout'] == 'three_columns'
		 || $data['tx_gridelements_backend_layout'] == 'four_columns'
		 || $data['tx_gridelements_backend_layout'] == 'six_columns' ) {
			$class .= $flexconf['noGutters'] ? ' no-gutters' : '';
			$class .= $flexconf['equalHeight'] ? ' row-eq-height' : '';
		}

		return trim($class);
	}


	/**
	 * Returns processedData header class
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function getHeaderClass($data)
	{
		$header['class'] = $data['header_position'] ? ' text-'.$data['header_position'] : '';

		$hClass = $data['tx_t3sbootstrap_header_class'];
		$hClass .= ' '.$data['tx_t3sbootstrap_header_display'] ?: '';
		$header['hClass'] = trim($hClass);

		$header['hLinkClass'] = trim($header['hLinkClass']);

		if ( $data['CType'] == 't3sbs_mediaobject' ) {
			$header['hClass'] .= $header['hClass'] ? $header['hClass'].' mt-0' : 'mt-0';
		}

		if ( $data['tx_t3sbootstrap_header_fontawesome'] ) {
			$header['hFa'] = '<i class="mr-1 '.trim($data['tx_t3sbootstrap_header_fontawesome']).'"></i> ';
		}

		return $header;

	}



	/**
	 * Returns the frontend controller
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}



}
