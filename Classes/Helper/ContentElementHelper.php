<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use T3SBS\T3sbootstrap\Helper\StyleHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ContentElementHelper implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$cType = $processedData['data']['CType'];
		$styleHelper = GeneralUtility::makeInstance(StyleHelper::class);


		/**
		 * Menu
		 */
		if ( substr($cType, 0, 4) == 'menu' ) {
			$processedData['menudirection'] = ' '.$flexconf['menudirection'];
			$processedData['menupills'] = $flexconf['menupills'] ? ' nav-pills' :'';
			$processedData['menuHorizontalAlignment'] = !empty($flexconf['menudirection']) && $flexconf['menudirection'] == 'flex-row'
			 ? ' '.$flexconf['menuHorizontalAlignment'] :'';
			if ( $cType == 'menu_section' ) {
				$processedData['pageLink'] = FALSE;
				# if more than 1 page for section-menu
				if (count(explode( ',' , (string) $processedData['data']['pages'])) > 1) {
					$processedData['pageLink'] = TRUE;
				} else {
					// if current page is selected
					$frontendController = self::getFrontendController();
					if ( $frontendController->id == $processedData['data']['pid'] ) {
						$processedData['onlyCurrentPageSelected'] = TRUE;
					} else {
						$processedData['pageLink'] = TRUE;
					}
				}
			}
			if ($flexconf['menuHorizontalAlignment'] == 'nav-fill variant') {
				$processedData['menupills'] = '';
			}
		}

		/**
		 * Table
		 */
		if ( $cType == 'table' ) {
			$tableClassArr = explode(',', (string) $flexconf['tableClass']);
			if ( count($tableClassArr) > 1 ) {
				$tableclass = 'table';
				foreach ($tableClassArr as $tc) {
					if ( strlen($tc) > 5 ) {
						$tableclass .= substr($tc, 5);
					}
				}
			} else {
				$tableclass = $flexconf['tableClass'] ? ' '.$flexconf['tableClass']:'';
			}
			$tableclass .= $flexconf['tableInverse'] ? ' table-dark' : '';
			$tableclass .= $processedData['data']['tx_t3sbootstrap_extra_class'] ? ' '.$processedData['data']['tx_t3sbootstrap_extra_class'] : '';
			$processedData['tableclass'] = trim($tableclass);
			$processedData['theadclass'] = $flexconf['theadClass'];
			$processedData['tableResponsive'] = $flexconf['tableResponsive'] ? TRUE : FALSE;
		}

		return $processedData;
	}

	/**
	 * Returns the frontend controller
	 */
	protected function getFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}

}
