<?php
namespace T3SBS\T3sbootstrap\Helper;

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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class DefaultHelper implements SingletonInterface
{

	/**
	 * Returns the Container Class
	 *
	 * @return string
	 */
	public function getContainerClass($data)
	{
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		if ( $extConf['container']
		 && $data['tx_t3sbootstrap_container']
		 && ($data['colPos'] === 0 || $data['colPos'] === 20 || $data['colPos'] === 21) ) {

			$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;

			if ( $pageContainer === FALSE ) {
				$container = $data['tx_t3sbootstrap_container'];
			} else {
				$container = FALSE;
			}

		} else {

			$container = FALSE;
		}


		return trim($container);
	}



	/**
	 * Returns the frontend controller
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}



}
