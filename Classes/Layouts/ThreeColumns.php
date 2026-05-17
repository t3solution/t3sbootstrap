<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ThreeColumns implements SingletonInterface
{

	public function __construct(
		private readonly Gutters $gutters,
		private readonly Grid $grid,
	) {}
	
	
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData = $this->gutters->getGutters($processedData, $flexconf);
		$processedData = $this->grid->getGrid($processedData, $flexconf);

		$processedData['equalHeight'] = !empty($flexconf['equalHeight']) ? ' d-flex align-items-stretch' : '';

		return $processedData;
	}
}
