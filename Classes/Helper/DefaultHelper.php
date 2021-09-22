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
		$container = '';
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		if ($extConf['container'] && $data['tx_t3sbootstrap_container']) {
			if ( $data['tx_container_parent'] === 0 ) {
				$t3sbconfig = self::getConfig($data['configuid']);
				if ( $t3sbconfig['footer_pid'] === $data['pid'] ) {
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 0 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
				} else {
					$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
					if ( $pageContainer === FALSE && $data['colPos'] === 0 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['jumbotron_container'] === 'none' && $data['colPos'] === 3 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['expandedcontent_containertop'] === 'none' && $data['colPos'] === 20 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['expandedcontent_containerbottom'] === 'none' && $data['colPos'] === 21 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 4 ) {
						$container = $data['tx_t3sbootstrap_container'];
					}
				}
			}
		}

		return trim((string)$container);
	}


	/**
	 * Returns the Container Error
	 *
	 * @return boolean
	 */
	public function getContainerError($data): bool
	{
		$error = FALSE;

		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
		if ($extConf['container']) {
			if ( $data['tx_container_parent'] === 0 ) {
				$t3sbconfig = self::getConfig($data['configuid']);
				if ( $t3sbconfig['footer_pid'] === $data['pid'] ) {
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 0 ) {
						$error = TRUE;
					}
				} else {
					$pageContainer = self::getFrontendController()->page['tx_t3sbootstrap_container'] ? TRUE : FALSE;
					if ( $pageContainer === FALSE && $data['colPos'] === 0 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['jumbotron_container'] === 'none' && $data['colPos'] === 3 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['expandedcontent_containertop'] === 'none' && $data['colPos'] === 20 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['expandedcontent_containerbottom'] === 'none' && $data['colPos'] === 21 ) {
						$error = TRUE;
					}
					if ( $t3sbconfig['footer_container'] === 'none' && $data['colPos'] === 4 ) {
						$error = TRUE;
					}
				}
			}
		}

		return $error;
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
