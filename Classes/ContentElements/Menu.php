<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Menu implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, string $cType): array
	{
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
