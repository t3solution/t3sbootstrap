<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

class Mediaobject implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{

		// An element whose FlexForm was never opened has no values at all: tx_t3sbootstrap_flexform
		// is NULL and $flexconf empty. The key is normalised once, because a direct read raises
		// "Undefined array key" on PHP 8 and TYPO3's error handler turns that into an exception.
		$order = (string)($flexconf['order'] ?? '');

		$processedData['mediaobject']['order'] = $order === 'right' ? 'right' : 'left';
		$processedData['mediaObjectBody'] = $order === 'right' ? ' me-3 m-1' : ' ms-3 m-1';
		$processedData['addmedia']['figureclass'] = '';

		if (!empty($flexconf['borderradius'])) {
			$processedData['addmedia']['imgclass'] = match($order) {
				'right' => ' rounded-end',
				default => ' rounded-start',
			};
		}

		return $processedData;
	}

}
