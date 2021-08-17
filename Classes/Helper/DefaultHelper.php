<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Helper;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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
	public function getContainerClass($data): string
	{
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');

		if ( $extConf['container'] && $data['tx_t3sbootstrap_container'] ) {

			$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;

			if ( $pageContainer === FALSE && ($data['colPos'] === 0 || $data['tx_container_parent'] === 0) ) {
				$container = $data['tx_t3sbootstrap_container'];
			} else {

				$t3sbconfig = self::getConfig($data['configuid']);

				$jumbotronContainer = $t3sbconfig['jumbotron_container'];
				$footerContainer = $t3sbconfig['footer_container'];
				$expandedcontentTopContainer = $t3sbconfig['expandedcontent_containertop'];
				$expandedcontentBottomContainer = $t3sbconfig['expandedcontent_containerbottom'];

				$footerByPid = $t3sbconfig['footer_pid'];

				if ( ($data['colPos'] === 3 || $data['tx_container_parent'] === 3)
				 && !$jumbotronContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( ($data['colPos'] === 4 || $data['tx_container_parent'] === 4)
				 && !$footerContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( ($data['colPos'] === 20 || $data['tx_container_parent'] === 20)
				 && !$expandedcontentTopContainer ) $container = $data['tx_t3sbootstrap_container'];
				if ( ($data['colPos'] === 21 || $data['tx_container_parent'] === 21)
				 && !$expandedcontentBottomContainer ) $container = $data['tx_t3sbootstrap_container'];

				if ($footerByPid && ($data['colPos'] === 0 || $data['tx_container_parent'] === 0)) {
					$container = $data['tx_t3sbootstrap_container'];
				}
			}

		} else {

			$container = '';

			$t3sbconfig = self::getConfig($data['configuid']);

			$jumbotronContainer = $t3sbconfig['jumbotron_container'];
			$footerContainer = $t3sbconfig['footer_container'];
			$expandedcontentTopContainer = $t3sbconfig['expandedcontent_containertop'];
			$expandedcontentBottomContainer = $t3sbconfig['expandedcontent_containerbottom'];

			$footerByPid = $t3sbconfig['footer_pid'];

			if ( ($data['colPos'] === 3 || $data['tx_container_parent'] === 3) && $jumbotronContainer ) $container = 'colPosContainer';
			if ( ($data['colPos'] === 4 || $data['tx_container_parent'] === 4) && $footerContainer ) $container = 'colPosContainer';
			if ( ($data['colPos'] === 20 || $data['tx_container_parent'] === 20) && $expandedcontentTopContainer ) $container = 'colPosContainer';
			if ( ($data['colPos'] === 21 || $data['tx_container_parent'] === 21) && $expandedcontentBottomContainer ) $container = 'colPosContainer';

			if ($footerByPid && ($data['colPos'] === 0 || $data['tx_container_parent'] === 0)) {
				$container = 'colPosContainer';
			}
		}

		return trim((string)$container);
	}


	/**
	 * Returns the t3sb configuration
	 *
	 * @return array
	 */
	protected function getConfig($uid): array
	{

		$query = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_t3sbootstrap_domain_model_config');
		$query->select('*')
			 ->from('tx_t3sbootstrap_domain_model_config')
			 ->where(
				$query->expr()->eq('uid', $query->createNamedParameter($uid, \PDO::PARAM_INT))
			 );
		return $query->execute()->fetch();
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}

}
