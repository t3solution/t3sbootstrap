<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;

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
			$processedData['menudirection'] = !empty($flexconf['menudirection']) ? ' '.$flexconf['menudirection'] : null;
			$processedData['menupills'] = !empty($flexconf['menupills']) ? ' nav-pills' :'';
			if ($processedData['menudirection'] === ' flex-row') {
				$processedData['menuHorizontalAlignment'] = !empty($flexconf['menuHorizontalAlignment'])
				 ? ' '.$flexconf['menuHorizontalAlignment'] : ' justify-content-end';
			}
			if ( $cType === 'menu_section' ) {
				$processedData['pageLink'] = FALSE;
				# if more than 1 page for section-menu
				if (count(explode( ',' , (string) $processedData['data']['pages'])) > 1) {
					$processedData['pageLink'] = TRUE;
				} else {
					// if current page is selected
			        $request = $GLOBALS['TYPO3_REQUEST'];
			        $frontendController = $request->getAttribute('frontend.controller');
					if ( $frontendController->id == $processedData['data']['pid'] ) {
						$processedData['onlyCurrentPageSelected'] = TRUE;
					} else {
						$processedData['pageLink'] = TRUE;
					}
				}
			}
			if (!empty($flexconf['menuHorizontalAlignment']) && $flexconf['menuHorizontalAlignment'] === 'nav-fill variant') {
				$processedData['menupills'] = '';
			}

		return $processedData;
	}

}
