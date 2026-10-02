<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;

class Table implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{

		// A table whose FlexForm was never opened has no values at all:
		// tx_t3sbootstrap_flexform is NULL and $flexconf accordingly empty. A plain read
		// raises "Undefined array key", which TYPO3's error handler turns into an exception.
		$tableClass = (string)($flexconf['tableClass'] ?? '');

		$tableClassArr = explode(',', $tableClass);

		if ( count($tableClassArr) > 1 ) {
			$tableclass = 'table';
			foreach ($tableClassArr as $tc) {
				if ( strlen($tc) > 5 ) {
					$tableclass .= substr($tc, 5);
				}
			}
		} else {
			$tableclass = $tableClass ? ' '.$tableClass : '';
		}
		$tableclass .= !empty($flexconf['tableInverse']) ? ' table-dark' : '';
		$tableclass .= !empty($processedData['data']['tx_t3sbootstrap_extra_class'])
			? ' '.$processedData['data']['tx_t3sbootstrap_extra_class'] : '';
		$processedData['tableclass'] = trim($tableclass);
		$processedData['theadclass'] = $flexconf['theadClass'] ?? '';
		$processedData['tableResponsive'] = !empty($flexconf['tableResponsive']);
		$processedData['tableResponsiveVariant'] = !empty($flexconf['tableResponsiveVariant']);

		return $processedData;
	}

}
