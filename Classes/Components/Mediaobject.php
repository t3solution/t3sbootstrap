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
class Mediaobject implements SingletonInterface
{

	/**
	 * Returns the $processedData
	 */
	public function getProcessedData(array $processedData, array $flexconf): array
	{

		$processedData['mediaobject']['order'] = $flexconf['order'] == 'right' ? 'right' : 'left';
		$processedData['mediaObjectBody'] = $flexconf['order'] == 'right' ? ' me-3 m-1' : ' ms-3 m-1';
		$processedData['addmedia']['figureclass'] = '';

		switch ( $processedData['data']['imageorient'] ) {
			 case 91:
			 	$processedData['addmedia']['figureclass'] .= ' align-self-center';
			break;
			 case 92:
			 	$processedData['addmedia']['figureclass'] .= ' align-self-start';
			break;
			 case 93:
			 	$processedData['addmedia']['figureclass'] .= ' align-self-end';
			break;
			 default:
			 	$processedData['addmedia']['figureclass'] .= '';
		}

		$processedData['class'] .= ' media';

		return $processedData;
	}

}
