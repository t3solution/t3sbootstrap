<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

class Mediaobject implements SingletonInterface
{

	public function getProcessedData(array $processedData, array $flexconf): array
	{

		// Ein Element, dessen FlexForm nie geoeffnet wurde, hat gar keine Werte:
		// tx_t3sbootstrap_flexform ist NULL, $flexconf entsprechend leer. Der
		// Schluessel wird deshalb einmal normalisiert, statt ihn an drei Stellen
		// direkt zu lesen - sonst warnt PHP 8 mit "Undefined array key", und
		// TYPO3s Error-Handler macht daraus eine Exception.
		$order = (string)($flexconf['order'] ?? '');

		$processedData['mediaobject']['order'] = $order === 'right' ? 'right' : 'left';
		$processedData['mediaObjectBody'] = $order === 'right' ? ' me-3 m-1' : ' ms-3 m-1';
		$processedData['addmedia']['figureclass'] = '';

		if (!empty($flexconf['borderradius'])) {
			$processedData['addmedia']['imgclass'] = match($order) {
				'right' => ' rounded-end',
				default => ' rounded-start',
			};
		}

		return $processedData;
	}

}
