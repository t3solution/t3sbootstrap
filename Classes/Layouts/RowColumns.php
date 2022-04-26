<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use T3SBS\T3sbootstrap\Layouts\Grid;
use T3SBS\T3sbootstrap\Layouts\Gutters;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class RowColumns implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData = GeneralUtility::makeInstance(Gutters::class)->getGutters($processedData, $flexconf);
		$processedData = GeneralUtility::makeInstance(Grid::class)->getGrid($processedData, $flexconf);

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
