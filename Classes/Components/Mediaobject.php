<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

class Mediaobject implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$processedData['mediaobject']['order'] = $flexconf['order'] === 'right' ? 'right' : 'left';
		$processedData['mediaObjectBody'] = $flexconf['order'] === 'right' ? ' me-3 m-1' : ' ms-3 m-1';
		$processedData['addmedia']['figureclass'] = '';

		if (!empty($flexconf['borderradius'])) {
			$processedData['addmedia']['imgclass'] = match($flexconf['order']) {
				'right' => ' rounded-end',
				default => ' rounded-start',
			};
		}

		return $processedData;
	}

}
