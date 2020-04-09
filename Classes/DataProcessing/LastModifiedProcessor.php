<?php
namespace T3SBS\T3sbootstrap\DataProcessing;

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

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;


class LastModifiedProcessor implements DataProcessorInterface
{

	/**
	 * Fetches records from the database as an array
	 *
	 * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
	 * @param array $contentObjectConfiguration The configuration of Content Object
	 * @param array $processorConfiguration The configuration of this processor
	 * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
	 *
	 * @return array processedData
	 */
	public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration,	 array $processedData)
	{

		if ($processorConfiguration['lastModifiedContentElement']) {

			$processorConfiguration = [];
			$processorConfiguration['pidInList'] = self::getFrontendController()->id;
			$records = $cObj->getRecords('tt_content', $processorConfiguration);

			foreach ( $records as $record ) {
				$lmc[] = $record['tstamp'];
			}

			if (!empty($lmc)) {
				rsort($lmc,SORT_NUMERIC);
			} else {
				$lmc[0] = '';
			}

			$processedData['lastModifiedContentElement'] = $lmc[0];
		}

		if ($processorConfiguration['recentlyUpdatedContentElements']) {

			$setMaxResults = $processorConfiguration['setMaxResults'] ?: 10;

			if (self::isMenuRecentlyUpdatedOnPage()) {
				$processedData['recentlyUpdatedContentElements'] = self::getRecentlyUpdated((int) $setMaxResults);
			}
		}

		return $processedData;
	}


	/**
	 * Returns true if is page w/ content.cType == menu_recently_updated
	 *
	 * @return $mdtm
	 */
	protected function isMenuRecentlyUpdatedOnPage()
	{

		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
			 ->select('uid')
			 ->from('tt_content')
			 ->where(
				$queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter(self::getFrontendController()->id, \PDO::PARAM_INT)),
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('menu_recently_updated'))
			 )
			 ->execute()
			 ->fetchAll();

		return empty($result) ? FALSE : TRUE;
	}

	/**
	 * Returns $mdtm
	 *
	 * @param int $setMaxResults
	 * @return array $mdtm
	 */
	protected function getRecentlyUpdated($setMaxResults)
	{
		$sysLanguageUid = self::getFrontendController()->sys_language_uid ?: 0;
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
		$result = $queryBuilder
			 ->select('*')
			 ->from('tt_content')
			 ->orderBy('tstamp', 'DESC')
			 ->where(
				$queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, \PDO::PARAM_INT)),
				$queryBuilder->expr()->notIn('pid', $queryBuilder->createNamedParameter(self::getFrontendController()->id, \PDO::PARAM_INT))
			 )
			 ->setMaxResults($setMaxResults)
			 ->execute()
			 ->fetchAll();

		$mdtm = [];

		if (!empty($result)) {

			foreach ( $result as $ce ) {
				$mdtm[$ce['uid']][self::getPageTitle($ce['pid'])] = $ce;
			}
		}

		return $mdtm;
	}


	/**
	 * Returns $pageTitle
	 *
	 * @param int $uid
	 * @return string page title
	 */
	protected function getPageTitle($uid)
	{
		$pageTitle = '';

		if ($uid) {
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
			$result = $queryBuilder
				 ->select('title', 'nav_title')
				 ->from('pages')
				 ->where(
					$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)),
					$queryBuilder->expr()->eq('doktype', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
				 )
				 ->execute()
				 ->fetchAll();

			$pageTitle = $result[0]['nav_title'] ?: $result[0]['title'];
		}

		return $pageTitle;
	}


	/**
	 * Returns $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 *
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontendController()
	{
		return $GLOBALS['TSFE'];
	}


}
