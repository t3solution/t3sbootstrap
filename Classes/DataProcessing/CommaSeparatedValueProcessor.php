<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\CsvUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;


class CommaSeparatedValueProcessor implements DataProcessorInterface
{
	/**
	 * Process CSV field data to split into a multi dimensional array
	 *
	 * @param ContentObjectRenderer $cObj The data of the content element or page
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 * @return array the processed data as key/value store
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
	{
		if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
			return $processedData;
		}

		// The field name to process
		$fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration);
		if (empty($fieldName)) {
			return $processedData;
		}

		$originalValue = $cObj->data[$fieldName];

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


##t3sb
$tableClass = '';
$croppedTable = [];

if (is_array($processedData['table'])) {
	foreach ($processedData['table'] as $key=>$table) {
		if ( \TYPO3\CMS\Core\Utility\GeneralUtility::isFirstPartOfStr($table[count($table)-1], 'ç') ) {
			$tableClass = TRUE;
			break;
		} else {
			$tableClass = FALSE;
		}
	}
	if ($tableClass) {
		foreach ($processedData['table'] as $tKey=>$table) {
			foreach ($table as $key=>$row) {
				if ( $key < count($table)-1 ) {
					$rowClass = trim(str_replace('ç', '', $table[count($table)-1]));
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
