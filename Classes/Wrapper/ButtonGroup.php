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
class ButtonGroup implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{
		$processedData['class'] .= !empty($flexconf['vertical']) ? ' btn-group-vertical' : ' btn-group';
		$processedData['class'] .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] != 'default' ? ' '.$flexconf['btnsize']: '';
		$processedData['buttonGroupClass'] = !empty($flexconf['align']) ? $flexconf['align'] : '';

		$processedData['visiblePart'] = '';

		if ( !empty($flexconf['fixedPosition']) ) {
			$processedData['buttonGroupClass'] .= ' d-none fixedGroupButton fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
			$processedData['class'] .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			$processedData['class'] .= !empty($flexconf['vertical']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			$processedData['fixedButton'] = TRUE;
			if ( !empty($flexconf['slideIn']) && !empty($flexconf['vertical']) && $flexconf['fixedPosition'] == 'right' ) {
				$processedData['class'] .= ' slideInButton';
				$processedData['visiblePart'] = $flexconf['visiblePart'] ? (int)$flexconf['visiblePart'] : 33;
				$processedData['slideIn'] = $flexconf['slideIn'];
			}
		}

		return $processedData;
	}

}
