<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FourColumns implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData = GeneralUtility::makeInstance(Gutters::class)->getGutters($processedData, $flexconf);
		$processedData = GeneralUtility::makeInstance(Grid::class)->getGrid($processedData, $flexconf);
		$processedData['equalHeight'] = !empty($flexconf['equalHeight']) ? ' d-flex align-items-stretch' : '';

		return $processedData;
	}

}
