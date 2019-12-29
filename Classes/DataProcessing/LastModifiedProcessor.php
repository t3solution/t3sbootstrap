<?php
namespace T3SBS\T3sbootstrap\DataProcessing;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class LastModifiedProcessor implements DataProcessorInterface
{

	/**
	 * Fetches records from the database as an array
	 *
	 * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 *
	 * @return string timestamp
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration,	 array $processedData)
	{
		$processorConfiguration = [];
		// the table to query
		$tableName = 'tt_content';
		$processorConfiguration['pidInList'] = self::getFrontendController()->id;
		// Execute a SQL statement to fetch the records from current page
		$records = $cObj->getRecords($tableName, $processorConfiguration);

		foreach ( $records as $record ) {
			$lmc[] = $record['tstamp'];
		}

		if (!empty($lmc)) {
			rsort($lmc,SORT_NUMERIC);
		} else {
			$lmc[0] = '';
		}

		$processedData['lastModifiedContentElement'] = $lmc[0];

		return $processedData;
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}


}
