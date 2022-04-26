<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Components;

use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Button implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf, array $parentflexconf): array
	{
		$outline = !empty($flexconf['outline']) ? 'outline-':'';
		$style = !empty($flexconf['style']) ? $flexconf['style'] : '';
		$typolinkButtonClass = ' btn btn-'.$outline.$style;
		$typolinkButtonClass .= !empty($flexconf['btnsize']) && $flexconf['btnsize'] != 'default' ? ' '.$flexconf['btnsize']:'';
		if ( empty($parentflexconf) ) {
			$processedData['btn-block'] = false;
			if (!empty($flexconf['block'])) {
				$processedData['btn-block'] = true;
			}
		}
		$headerPosition = '';
		if ($processedData['data']['header_position']) {
			$headerPosition = $processedData['data']['header_position'];
			if ( $headerPosition == 'left' ) $headerPosition = '';
			if ( $headerPosition == 'center' ) $headerPosition = 'text-center';
			if ( $headerPosition == 'right' ) $headerPosition = 'd-md-flex justify-content-md-end';
		}

		$processedData['headerPosition'] = $headerPosition;

		if ( $flexconf['fixedPosition'] ) {
			$typolinkButtonClass .= ' d-none fixedPosition fixedPosition-'.$flexconf['fixedPosition'];
			$typolinkButtonClass .= !empty($flexconf['rotate']) ? ' rotateFixedPosition rotate-'.$flexconf['rotate'] : '';
			$processedData['fixedButton'] = $flexconf['fixedPosition'];
		}

		$processedData['linkTitle'] = $flexconf['linkTitle'];
		$processedData['slideInButton'] = FALSE;
		$processedData['slideInButtonFaIcon'] = FALSE;

		if ( !empty($parentflexconf['fixedPosition']) && $parentflexconf['fixedPosition'] == 'right'
		 && $parentflexconf['slideIn']
		 && $parentflexconf['visiblePart']
		 && $parentflexconf['vertical']
		) {
			// slide in button
			$processedData['slideInButton'] = TRUE;

			if ( $processedData['data']['tx_t3sbootstrap_header_fontawesome'] ) {
				$processedData['slideInButtonFaIcon'] = TRUE;
			} else {
				$processedData['data']['tx_t3sbootstrap_header_fontawesome'] = 'fas fa-ban text-danger';
				$processedData['slideInButtonFaIcon'] = TRUE;
			}
		}

		$processedData['class'] .= $typolinkButtonClass;

		return $processedData;
	}

}
