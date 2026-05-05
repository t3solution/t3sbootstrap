<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Core\Utility\CsvUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class CommaSeparatedValueProcessor implements DataProcessorInterface
{
	public function process(
		ContentObjectRenderer $cObj, 
		array $contentObjectConfiguration, 
		array $processorConfiguration, 
		array $processedData
	): array
	{
		if (!empty($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		// The field name to process
		$fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration);
		if (empty($fieldName)) {
			return $processedData;
		}
		// @extensionScannerIgnoreLine
		$originalValue = $cObj->data[$fieldName];

		if (empty($originalValue)) {
			return $processedData;
		}

		// Set the target variable
		$targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, $fieldName);

		// Set the maximum amount of columns
		$maximumColumns = $cObj->stdWrapValue('maximumColumns', $processorConfiguration, 0);

		// Set the field delimiter which is "," by default
		$fieldDelimiter = $cObj->stdWrapValue('fieldDelimiter', $processorConfiguration, ',');

		// Set the field enclosure which is " by default
		$fieldEnclosure = $cObj->stdWrapValue('fieldEnclosure', $processorConfiguration, '"');

		$processedData[$targetVariableName] = CsvUtility::csvToArray(
			$originalValue,
			$fieldDelimiter,
			$fieldEnclosure,
			(int)$maximumColumns
		);

		$hasRowClass = false;
		$croppedTable = [];

		if (is_array($processedData['table'])) {
		
			// Prüfen ob Row-Klassen vorhanden
			foreach ($processedData['table'] as $table) {
				$lastIndex = count($table) - 1;
				if (str_starts_with($table[$lastIndex], 'ç')) {
					$hasRowClass = true;
					break;
				}
			}
		
			if ($hasRowClass) {
				foreach ($processedData['table'] as $tKey=>$table) {
					$lastIndex = count($table) - 1;
					foreach ($table as $key=>$row) {
						if ($key < $lastIndex) {
							$rowClass = trim(str_replace('ç', '', $table[$lastIndex]));
							$processedData['table-row-class'][$tKey] = $rowClass;
							$croppedTable[$tKey][$key] = $row;
						}
					}
				}
			}
		
			if (!empty($croppedTable)) {
				$processedData['table'] = $croppedTable;
			}
		}
		

		return $processedData;
	}
}
