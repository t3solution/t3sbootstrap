<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\SingletonInterface;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Modal implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$processedData['modal']['animation'] = $flexconf['animation'];
		$processedData['modal']['size'] = $flexconf['size'];
		$processedData['modal']['button'] = $flexconf['button'];
		$processedData['modal']['style'] = $flexconf['style'];
		if ( $flexconf['buttonText'] ) {
			$processedData['modal']['buttonText'] = $flexconf['buttonText'];
		} elseif ( $processedData['data']['header'] ) {
			$processedData['modal']['buttonText'] = $processedData['data']['header'];
		} else {
			$processedData['modal']['buttonText'] = $processedData['modal']['button'] ? 'Modal-Button' :'Modal-Link';
		}
		if ( $flexconf['fixedPosition'] ) {
			$processedData['modal']['fixedClass'] = 'fixedModalButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
			$processedData['class'] .= $flexconf['rotate'] ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			$processedData['modal']['fixedButton'] = TRUE;
		}
		if ($processedData['data']['header_position']) {
			$headerPosition = $processedData['data']['header_position'];
			if ( $headerPosition == 'left' ) $headerPosition = 'start';
			if ( $headerPosition == 'right' ) $headerPosition = 'end';
			$processedData['class'] .= ' text-'.$headerPosition;
		}

		return $processedData;
	}

}
