<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class TabsContainer implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		if ($processedData['data']['CType'] == 'tabs_container') {
			if ( !empty($flexconf['display_type']) && $flexconf['display_type'] == 'verticalpills') {
				$processedData['pill']['asideWidth'] = (int)$flexconf['aside_width'];
				$processedData['pill']['mainWidth'] = $flexconf['aside_width'] ? 12 - (int)$flexconf['aside_width'] : 9;
			}
			$processedData['tab']['displayType'] = !empty($flexconf['display_type']) ? $flexconf['display_type'] : '';
			$processedData['tab']['switchEffect'] =	!empty($parentflexconf['switch_effect']) ? $parentflexconf['switch_effect'] : '';
			$processedData['tab']['fill'] =	 !empty($flexconf['fill']) ? ' '.$flexconf['fill']: '';
		} else {
			$processedData['tab']['contentByPid'] =	!empty($flexconf['contentByPid']) ? $flexconf['contentByPid'] : 0;
		}

		return $processedData;
	}

}
