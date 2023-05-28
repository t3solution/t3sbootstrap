<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ClassHelper implements SingletonInterface
{

	/**
	 * Returns the CSS-class for default elements
	 */
	public function getDefaultClass(array $data, array $flexconf, string $cTypeClass, string $sectionMenuClass): string
	{
	 	// class
		if ( $cTypeClass ) {
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
				if ( $data['tx_t3sbootstrap_padding_sides'] == 'l' ) {
					$paddingSide = 's';
				} elseif ( $data['tx_t3sbootstrap_padding_sides'] == 'r' ) {
					$paddingSide = 'e';
				} else {
					$paddingSide = $data['tx_t3sbootstrap_padding_sides'];
				}
				$class .= ' p'.$paddingSide.'-'.$data['tx_t3sbootstrap_padding_size'];
			}
		}
		// Spacing: margin
		if ( $data['tx_t3sbootstrap_margin_sides'] ) {
			// on all 4 sides of the element
			if ( $data['tx_t3sbootstrap_margin_sides'] == 'blank' ) {
				$class .= ' m-'.$data['tx_t3sbootstrap_margin_size'];
			} else {

				if ( $data['tx_t3sbootstrap_margin_sides'] == 'l' ) {
					$marginSide = 's';
				} elseif ( $data['tx_t3sbootstrap_margin_sides'] == 'r' ) {
					$marginSide = 'e';
				} else {
					$marginSide = $data['tx_t3sbootstrap_margin_sides'];
				}
				$class .= ' m'.$marginSide.'-'.$data['tx_t3sbootstrap_margin_size'];
			}
		}
		// Layout
		if ($data['layout']) {
			$pagesTSconfig = self::getFrontendController()->getPagesTSconfig();
			$layout = $data['layout'];
			$layoutAddItems = '';
			$layoutClasses = '';
			$layoutAltLabels = '';

			if (!empty($pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['addItems.'])) {
				$layoutAddItems = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['addItems.'];
			}
			if (!empty($pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['classes.'])) {
				$layoutClasses = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['classes.'];
			}
			if (!empty($pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['altLabels.'])) {
				$layoutAltLabels = $pagesTSconfig['TCEFORM.']['tt_content.']['layout.']['altLabels.'];
			}

			if (isset($layoutAddItems) && isset($layoutAddItems) === $layout) {
				$class .= ' layout-'.$layout;
			} elseif (isset($layoutAltLabels) && !empty($layoutAltLabels[$layout])) {
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
		if (!empty($flexconf['responsiveVariations']) && !empty($flexconf['alignSelf'])) {
			$class .= $flexconf['alignSelf'] ? ' align-self-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignSelf'] : '';
		} else {
			$class .= !empty($flexconf['alignSelf']) ? ' align-self-'.$flexconf['alignSelf'] : '';
		}

		//Flip Card
		if ( $data['CType'] == 't3sbs_card' && !empty($flexconf['flipcard']) ) {
			if ( $data['tx_t3sbootstrap_contextcolor'] ) {
				$data['tx_t3sbootstrap_contextcolor'] = '';
			}
			if ( $data['tx_t3sbootstrap_textcolor'] ) {
				$data['tx_t3sbootstrap_textcolor'] = '';
			}
		}
		$class .= $data['tx_t3sbootstrap_textcolor'] ? ' text-'.$data['tx_t3sbootstrap_textcolor'] : '';
		$class .= $data['tx_t3sbootstrap_contextcolor'] ? ' bg-'.$data['tx_t3sbootstrap_contextcolor'] : '';

		// Extra class
		$class .= $data['tx_t3sbootstrap_extra_class'] ? ' '.$data['tx_t3sbootstrap_extra_class'] : '';
		// Border
		if ( !empty($flexconf['border']) ) {
			if ( $flexconf['border'] == 'border' ) {
				$border = 'border';
			} else {
				$border = 'border '.$flexconf['border'];
			}
			if ( !empty($flexconf['borderstyle']) ) {
				$class .= ' '.$border.' border-'.$flexconf['borderstyle'];
			} else {
				$class .= ' '.$border;
			}
			$class .= !empty($flexconf['borderradius']) ? ' '.$flexconf['borderradius'] : '';
		}

		// Hiding / Display Elements
		$class .= !empty($flexconf['hidden']) ? ' '.$flexconf['hidden'] : '';
		// Float
		$class .= !empty($flexconf['float']) ? ' '.$flexconf['float'] : '';
		// if media
		if ($data['assets'] || $data['image'] || $data['media']) {
			$imageorient = $data['imageorient'];
			if ( $imageorient == 10 || $imageorient == 17 || $imageorient == 18 ) {
				$class .= ' clearfix';
			}
		}

		if ($sectionMenuClass) {
			$class .= ' section-index';	
		}

		return ' '.trim($class);
	}


	/**
	 * Returns the CSS-class for tx_container
	 */
	public function getTxContainerClass(array $data, array $flexconf, bool $isVideo): string
	{
		$class = '';

		/**
		 * Background Wrapper
		 */
		if ( $data['CType'] == 'background_wrapper' && $isVideo == FALSE ) {
			$class .= !empty($flexconf['bgAttachmentFixed']) ? ' background-fixed' : '';
			if ((!$data['assets'] && !empty($flexconf['imageRaster'])) || (!empty($flexconf['origImage']) && !empty($flexconf['imageRaster']))) {
				$class .= ' bg-raster';
			}
		}

		/**
		 * Auto-layout row/column
		 */
		if ( $data['CType'] == 'autoLayout_row' ) {

			if ( !empty($flexconf['horizontalGutters']) && $flexconf['horizontalGutters'] != 'gx-4') {
				$class .= $flexconf['horizontalGutters'] ? ' '.$flexconf['horizontalGutters'] : '';
			}

			if (!empty($flexconf['verticalGutters'])) {

				if ($flexconf['verticalGutters'] == 'gy-0') $flexconf['verticalGutters'] = 0;


				$class .= !empty($flexconf['verticalGutters']) ? ' '.$flexconf['verticalGutters'] : '';
			}

			if (!empty($flexconf['responsiveVariations'])) {
				$class .= !empty($flexconf['justify']) ? ' justify-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['justify'] : '';
				$class .= !empty($flexconf['alignItem']) ? ' align-items-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignItem'] : '';
			} else {
				$class .= !empty($flexconf['alignItem']) ? ' align-items-'.$flexconf['alignItem'] : '';
				$class .= !empty($flexconf['justify']) ? ' justify-content-'.$flexconf['justify'] : '';
			}
		}

		/**
		 * Container
		 */
		if ( $data['CType'] == 'container' ) {
			if (!empty($flexconf['flexContainer'])) {
				if (!empty($flexconf['responsiveVariations'])) {
					$class .= !empty($flexconf['flexContainer']) ? ' d-'.$flexconf['responsiveVariations'].'-'.$flexconf['flexContainer'] : '';
					$class .= !empty($flexconf['direction']) ? ' flex-'.$flexconf['responsiveVariations'].'-'.$flexconf['direction'] : '';
					$class .= !empty($flexconf['justify']) ? ' justify-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['justify'] : '';
					$class .= !empty($flexconf['alignItem']) ? ' align-items-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignItem'] : '';
					$class .= !empty($flexconf['wrap']) ? ' flex-'.$flexconf['responsiveVariations'].'-'.$flexconf['wrap'] : '';
					$class .= !empty($flexconf['alignContent']) ? ' align-content-'.$flexconf['responsiveVariations'].'-'.$flexconf['alignContent'] : '';
				} else {
					$class .= !empty($flexconf['flexContainer']) ? ' d-'.$flexconf['flexContainer'] : '';
					$class .= !empty($flexconf['direction']) ? ' flex-'.$flexconf['direction'] : '';
					$class .= !empty($flexconf['justify']) ? ' justify-content-'.$flexconf['justify'] : '';
					$class .= !empty($flexconf['alignItem']) ? ' align-items-'.$flexconf['alignItem'] : '';
					$class .= !empty($flexconf['wrap']) ? ' flex-'.$flexconf['wrap'] : '';
					$class .= !empty($flexconf['alignContent']) ? ' align-content-'.$flexconf['alignContent'] : '';
				}
			}
		}

		return trim($class);
	}


	/**
	 * Returns processedData header class
	 */
	public function getHeaderClass(array $data): array
	{
		$headerPosition = $data['header_position'];
		if ( $headerPosition == 'left' ) $headerPosition = 'start';
		if ( $headerPosition == 'right' ) $headerPosition = 'end';
		$header['class'] = $headerPosition ? 'text-'.$headerPosition : '';
		$header['hClass'] = '';
		$header['hColorVar'] = '';
		$header['hLine'] = '';

		if ( $data['tx_t3sbootstrap_header_class'] ) {
			if (str_contains($data['tx_t3sbootstrap_header_class'], 'h-line-1')) {
				$header['hLine'] = 'h-line-1';
			}
			if (str_contains($data['tx_t3sbootstrap_header_class'], 'h-line-2')) {
				$header['hLine'] = 'h-line-2';
			}
			$textColors = explode(',','text-primary,text-secondary,text-danger,text-success,text-warning,
			text-info,text-light,text-dark,text-body,text-muted,text-white');
			foreach ($textColors as $textColor) {
				if (str_contains($data['tx_t3sbootstrap_header_class'], $textColor)) {
					$header['hClass'] .= $textColor;
					$header['hColorVar'] = 'var(--bs-'.substr($textColor, 5).')';
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
			$header['hFa'] = '<i class="me-1 '.trim($data['tx_t3sbootstrap_header_fontawesome']).'"></i> ';
		}

		return $header;
	}


	/**
	 * Returns processedData if parent auto layout
	 */
	public function getAutoLayoutClass(array $flexconf): string
	{
		$class = '';

		if ( !empty($flexconf['gridSystem']) ) {
			switch ( $flexconf['gridSystem'] ) {
				 case 'equal':
					$class .= ' col';
				break;
				 case 'column':
					$class .= !empty($flexconf['xsColumns']) ? ' col-'.$flexconf['xsColumns'] : '';
				break;
				 case 'variable':

				 if ( $flexconf['xsColumns'] == 'equal'
					|| $flexconf['smColumns'] == 'equal'
					|| $flexconf['mdColumns'] == 'equal'
					|| $flexconf['lgColumns'] == 'equal'
					|| $flexconf['xlColumns'] == 'equal' ) {

					$class .= !empty($flexconf['xsColumns']) ? ' col-xs' : '';
					$class .= !empty($flexconf['smColumns']) ? ' col-sm' : '';
					$class .= !empty($flexconf['mdColumns']) ? ' col-md' : '';
					$class .= !empty($flexconf['lgColumns']) ? ' col-lg' : '';
					$class .= !empty($flexconf['xlColumns']) ? ' col-xl': '';
					$class .= !empty($flexconf['xxlColumns']) ? ' col-xxl': '';

				} else {

					$class .= !empty($flexconf['xsColumns']) ? ' col-'.$flexconf['xsColumns'] : '';
					$class .= !empty($flexconf['smColumns']) ? ' col-sm-'.$flexconf['smColumns'] : '';
					$class .= !empty($flexconf['mdColumns']) ? ' col-md-'.$flexconf['mdColumns'] : '';
					$class .= !empty($flexconf['lgColumns']) ? ' col-lg-'.$flexconf['lgColumns'] : '';
					$class .= !empty($flexconf['xlColumns']) ? ' col-xl-'.$flexconf['xlColumns'] : '';
					$class .= !empty($flexconf['xxlColumns']) ? ' col-xxl-'.$flexconf['xxlColumns'] : '';
				}
				break;
			}
		}

		return $class;

	}

	/**
	 * Returns processedData if parent container
	 */
	public function getContainerClass(array $parentflexconf, array $flexconf): string
	{
		$class = '';

		if ( !empty($parentflexconf['flexContainer']) && !empty($parentflexconf['responsiveVariations']) ) {
			if (!empty($flexconf['responsiveVariations'])) {
				$class .= !empty($flexconf['alignSelf']) ? ' align-self-'.$flexconf['responsiveVariations'].'-'.$flexconf['flexContainer'] : '';
			} else {
				$class .= !empty($flexconf['alignSelf']) ? ' align-self-'.$flexconf['alignSelf'] : '';
			}

			$class .= !empty($flexconf['autoMargins']) ? ' '.$flexconf['autoMargins'].'-auto' : '';
			$class .= !empty($flexconf['order']) ? ' order-'.$flexconf['order'] : '';
		}

		return $class;
	}


	/**
	 * Returns the frontend controller
	 */
	protected function getFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}

}
