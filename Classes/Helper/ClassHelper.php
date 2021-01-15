<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
	public function getAllClass($data, $flexconf, $extConf): string
	{
	 	// class
		if ( $extConf['cTypeClass']) {
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
	 * Returns the CSS-class for tx_container
	 *
	 * @param array $data
	 * @param array	$flexconf
	 * @param boolean $isVideo
	 * @param array	$extConf
	 *
	 * @return string
	 */
	public function getTxContainerClass($data, $flexconf, $isVideo, $extConf): string
	{

		$class = '';

		/**
		 * Background Wrapper
		 */
		if ( $data['CType'] == 'background_wrapper' && $isVideo == FALSE ) {
			$class .= $flexconf['bgAttachmentFixed'] ? ' background-fixed' : '';
			if ((!$data['assets'] && $flexconf['imageRaster']) || ($flexconf['origImage'] && $flexconf['imageRaster'])) {
				$class .= ' bg-raster';
			}
		}

		/**
		 * Button group
		 */
		if ( $data['CType'] == 'button_group' ) {
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
		if ( $data['CType'] == 'autoLayout_row' ) {
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
		if ( $data['CType'] == 'container' ) {
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
		if ( $data['CType'] == 'two_columns'
		 || $data['CType'] == 'three_columns'
		 || $data['CType'] == 'four_columns'
		 || $data['CType'] == 'six_columns' ) {
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
	public function getHeaderClass($data): array
	{
		$header['class'] = $data['header_position'] ? ' text-'.$data['header_position'] : '';
		$header['hClass'] = '';

		if ( $data['tx_t3sbootstrap_header_class'] ) {
			$textColors = explode(',','text-primary,text-secondary,text-danger,text-success,text-warning,
			text-info,text-light,text-dark,text-body,text-muted,text-white');
			foreach ($textColors as $textColor) {
				if (strpos($data['tx_t3sbootstrap_header_class'], $textColor) !== false) {
					$header['hClass'] .= $textColor;
					$data['tx_t3sbootstrap_header_class'] = trim(str_replace($textColor, '', $data['tx_t3sbootstrap_header_class']));
					break;
				}
			}
		}

		$header['class'] .= $data['tx_t3sbootstrap_header_class'] ? ' '.$data['tx_t3sbootstrap_header_class'] : '';
		$header['hClass'] .= $data['tx_t3sbootstrap_header_display'] ? ' '.$data['tx_t3sbootstrap_header_display'] : '';

		if ( $data['CType'] == 't3sbs_mediaobject' ) {
			$header['hClass'] .= ' mt-0';
		}

		if ($data['header_link'] && !$data['tx_t3sbootstrap_header_celink']) {
			$header['hLinkClass'] = trim($header['hClass']);
			$header['hClass'] = '';
		}

		if ( $data['tx_t3sbootstrap_header_fontawesome'] ) {
			$header['hFa'] = '<i class="mr-1 '.trim($data['tx_t3sbootstrap_header_fontawesome']).'"></i> ';
		}

		return $header;

	}

	/**
	 * Returns processedData if parent auto layout
	 *
	 * @param array $flexconf
	 *
	 * @return string
	 */
	public function getAutoLayoutClass($flexconf): string
	{
		$class = '';

		if ( $flexconf['gridSystem'] ) {
			switch ( $flexconf['gridSystem'] ) {
				 case 'equal':
					$class .= ' col';
				break;
				 case 'column':
					$class .= $flexconf['xsColumns'] ? ' col-'.$flexconf['xsColumns'] : '';
				break;
				 case 'variable':

				 if ( $flexconf['xsColumns'] == 'equal'
					|| $flexconf['smColumns'] == 'equal'
					|| $flexconf['mdColumns'] == 'equal'
					|| $flexconf['lgColumns'] == 'equal'
					|| $flexconf['xlColumns'] == 'equal' ) {

					$class .= $flexconf['xsColumns'] ? ' col-xs' : '';
					$class .= $flexconf['smColumns'] ? ' col-sm' : '';
					$class .= $flexconf['mdColumns'] ? ' col-md' : '';
					$class .= $flexconf['lgColumns'] ? ' col-lg' : '';
					$class .= $flexconf['xlColumns'] ? ' col-xl': '';

				} else {

					$class .= $flexconf['xsColumns'] ? ' col-'.$flexconf['xsColumns'] : '';
					$class .= $flexconf['smColumns'] ? ' col-sm-'.$flexconf['smColumns'] : '';
					$class .= $flexconf['mdColumns'] ? ' col-md-'.$flexconf['mdColumns'] : '';
					$class .= $flexconf['lgColumns'] ? ' col-lg-'.$flexconf['lgColumns'] : '';
					$class .= $flexconf['xlColumns'] ? ' col-xl-'.$flexconf['xlColumns'] : '';
				}
				break;
			}
		}

		return $class;

	}

	/**
	 * Returns processedData if parent container
	 *
	 * @param array $parentflexconf
	 * @param array $flexconf
	 *
	 * @return string
	 */
	public function getContainerClass($parentflexconf, $flexconf): string
	{
		$class = '';

		if ( $parentflexconf['flexContainer'] ) {
			if ($flexconf['responsiveVariations']) {
				$class .= $flexconf['alignSelf'] ? ' align-self-'.$flexconf['responsiveVariations'].'-'.$flexconf['flexContainer'] : '';
			} else {
				$class .= $flexconf['alignSelf'] ? ' align-self-'.$flexconf['alignSelf'] : '';
			}

			$class .= $flexconf['autoMargins'] ? ' '.$flexconf['autoMargins'].'-auto' : '';
			$class .= $flexconf['order'] ? ' order-'.$flexconf['order'] : '';
		}

		return $class;
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}



}
