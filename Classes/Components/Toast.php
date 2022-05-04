<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Toast implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['animation'] = $flexconf['animation'] ? 'true' : 'false';
		$processedData['autohide'] = $flexconf['autohide'] ? 'true' : 'false';
		$processedData['delay'] = $flexconf['delay'];
		$processedData['style'] .= !empty($flexconf['toastwidth']) ? ' width:'.$flexconf['toastwidth'].'px;' : '';
		if ($flexconf['placement'] && str_starts_with($flexconf['placement'], 'top-0')) {
			$processedData['placement'] = ' '.str_replace('top-0', 'top-70', $flexconf['placement']);
		} else {
			$processedData['placement'] = ' '.$flexconf['placement'];
		}
		$processedData['cookie'] = $flexconf['cookie'];
		$processedData['expires'] = !empty($flexconf['expires']) ? $flexconf['expires'] : '';

		$processedData['style'] .= ' z-index:1;';

		return $processedData;
	}

}
