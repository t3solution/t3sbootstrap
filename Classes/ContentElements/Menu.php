<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;

class Menu implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf, string $cType): array
	{
			$processedData['menudirection'] = !empty($flexconf['menudirection']) ? ' '.$flexconf['menudirection'] : null;
			$processedData['menupills'] = !empty($flexconf['menupills']) ? ' nav-pills' :'';
			if ($processedData['menudirection'] === ' flex-row') {
				$processedData['menuHorizontalAlignment'] = !empty($flexconf['menuHorizontalAlignment'])
				 ? ' '.$flexconf['menuHorizontalAlignment'] : ' justify-content-end';
			}
			if ( $cType === 'menu_section' ) {
				$processedData['pageLink'] = false;
				// if more than 1 page for section-menu
				if (count(explode( ',' , (string) $processedData['data']['pages'])) > 1) {
					$processedData['pageLink'] = true;
				} else {
					// if current page is selected
			        $request = $GLOBALS['TYPO3_REQUEST'];
					$pageInformation = $request->getAttribute('frontend.page.information');
					if ( $pageInformation->getId() === $processedData['data']['pid'] ) {
						$processedData['onlyCurrentPageSelected'] = true;
					} else {
						$processedData['pageLink'] = true;
					}
				}
			}
			if (!empty($flexconf['menuHorizontalAlignment']) && $flexconf['menuHorizontalAlignment'] === 'nav-fill variant') {
				$processedData['menupills'] = '';
			}

		return $processedData;
	}

}
