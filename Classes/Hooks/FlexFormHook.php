<?php
namespace T3SBS\T3sbootstrap\Hooks;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;

class FlexFormHook
{

	/**
	* @param array $dataStructure
	* @param array $identifier
	* @return array
	*/
	public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
	{

		$extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		$ffPath = $extconf['flexformPath'] ?: 'fileadmin/T3SB/FlexForms/';

		foreach ( $GLOBALS['TCA']['tt_content']['columns']['tx_t3sbootstrap_flexform']['config']['ds'] as $key=>$flexForm ) {

			$flexForms[$key] = substr($flexForm, 46, -4);
		}

		if ( array_key_exists($identifier['dataStructureKey'],$flexForms) ) {

			if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content'
			&& $identifier['fieldName'] === 'tx_t3sbootstrap_flexform' && $identifier['dataStructureKey']) {

				$file = Environment::getPublicPath() . $ffPath.$flexForms[$identifier['dataStructureKey']].'.xml';

				$content = file_exists($file) ? file_get_contents($file) : '';

				if ($content) {
					$dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
				}
			}
		}

		return $dataStructure;
	}
}