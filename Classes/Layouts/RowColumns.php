<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use T3SBS\T3sbootstrap\Layouts\Grid;
use T3SBS\T3sbootstrap\Layouts\Gutters;

class RowColumns implements SingletonInterface
{

	public function __construct(
		private readonly Gutters $gutters,
		private readonly Grid $grid,
	) {}
	
	
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData = $this->gutters->getGutters($processedData, $flexconf);
		$processedData = $this->grid->getGrid($processedData, $flexconf);

		$rowClass = [];
		if ($flexconf['cols_extraClass'] ?? '') {
			foreach (explode(',',$flexconf['cols_extraClass']) as $key=>$cec ) {
				$colsClass[$key] = ' '.trim($cec);
			}
			$processedData['extraClassCols'] = $colsClass;
		}
		foreach (array_reverse($flexconf) as $key=>$grid) {
			if ( str_ends_with($key, 'rowclass') ) {
				$rowClass[$key] = $grid;
			}
		}
		$processedData['class'] .= ' '.trim(implode(' ',$rowClass));

		return $processedData;
	}

}
