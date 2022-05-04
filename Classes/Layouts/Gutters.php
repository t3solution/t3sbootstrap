<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Layouts;

use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Gutters implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getGutters(array $processedData, array $flexconf): array
	{
		$horizontalGutters = !empty($flexconf['horizontalGutters']) ? trim((string)$flexconf['horizontalGutters']) : '';
		$verticalGutters = !empty($flexconf['verticalGutters']) ? trim((string)$flexconf['verticalGutters']) : '';

		$extraWrapperClass = '';
		if ( $verticalGutters ) {
			#  The vertical gutters can cause some overflow below the .row at the end of a page.
			# If this occurs, you add a wrapper around .row with the .overflow-hidden class
			$extraWrapperClass = 'overflow-hidden';
		}
		$processedData['gutters'] = $horizontalGutters || $verticalGutters ? ' '.$horizontalGutters.' '.$verticalGutters : '';
		$processedData['extraWrapperClass'] = $extraWrapperClass;

		return $processedData;
	}

}