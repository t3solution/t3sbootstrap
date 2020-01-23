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
use TYPO3\CMS\Core\Database\ConnectionPool;

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

		if ( $extConf['container'] && $data['tx_t3sbootstrap_container'] ) {

			$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;

			if ( $pageContainer === FALSE ) {
				$container = $data['tx_t3sbootstrap_container'];
			} else {
				$container = FALSE;
			}

		} else {

			if ( $data['colPos'] === 0 ) {
				$container = FALSE;
			} else {

				foreach ( self::getFrontendController()->rootLine as $page ) {	
					$t3sbconfig = self::getConfig($page['uid']);
					$jumbotronContainer = $t3sbconfig['jumbotron_container'];
					$footerContainer = $t3sbconfig['footer_container'];
					$expandedcontentTopContainer = $t3sbconfig['expandedcontent_containertop'];
					$expandedcontentBottomContainer = $t3sbconfig['expandedcontent_containerbottom'];
					if(!empty($t3sbconfig)) break;
				}
				if ( $data['colPos'] === 3 && !$jumbotronContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( $data['colPos'] === 4 && !$footerContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( $data['colPos'] === 20 && !$expandedcontentTopContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( $data['colPos'] === 21 && !$expandedcontentBottomContainer ) $container = $data['tx_t3sbootstrap_container'];

			}
		}

		return trim($container);
	}


	/**
	 * Returns the t3sb configuration
	 *
	 * @return configuration
	 */
	protected function getConfig($uid)
	{
		// Get a QueryBuilder, which should only be used a single time
		$query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
		$query->select('*')
		   ->from('tx_t3sbootstrap_domain_model_config')
		   ->where(
		      $query->expr()->eq('uid', $uid)
		   );
		return $query->execute()->fetchAll();
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
