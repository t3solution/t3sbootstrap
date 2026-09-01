<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\ContentElements;

use TYPO3\CMS\Core\SingletonInterface;

class Table implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{

		// Eine Tabelle, deren FlexForm nie geoeffnet wurde, hat gar keine Werte:
		// tx_t3sbootstrap_flexform ist NULL, $flexconf entsprechend leer. Ein
		// blosser Zugriff loest "Undefined array key" aus, und TYPO3s
		// Error-Handler macht daraus eine Exception - Frontend steht.
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
