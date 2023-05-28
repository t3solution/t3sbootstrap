<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Table implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$tableClassArr = explode(',', (string) $flexconf['tableClass']);
		if ( count($tableClassArr) > 1 ) {
			$tableclass = 'table';
			foreach ($tableClassArr as $tc) {
				if ( strlen($tc) > 5 ) {
					$tableclass .= substr($tc, 5);
				}
			}
		} else {
			$tableclass = $flexconf['tableClass'] ? ' '.$flexconf['tableClass']:'';
		}
		$tableclass .= $flexconf['tableInverse'] ? ' table-dark' : '';
		$tableclass .= $processedData['data']['tx_t3sbootstrap_extra_class'] ? ' '.$processedData['data']['tx_t3sbootstrap_extra_class'] : '';
		$processedData['tableclass'] = trim($tableclass);
		$processedData['theadclass'] = !empty($flexconf['theadClass']) ? $flexconf['theadClass'] : '';
		$processedData['tableResponsive'] = $flexconf['tableResponsive'] ? TRUE : FALSE;
		$processedData['tableResponsiveVariant'] = !empty($flexconf['tableResponsiveVariant']) ? TRUE : FALSE;

		return $processedData;
	}

}
