<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use Psr\Http\Message\ServerRequestInterface;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class LastModifiedProcessor implements DataProcessorInterface
{
    
    protected $request;

    /**
     * Fetches records from the database as an array
     *
     * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     *
     * @return mixed processedData
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        /** @var ServerRequestInterface $request */
        $this->request = $cObj->getRequest();

        if (!empty($processorConfiguration['lastModifiedContentElement'])) {
            $processorConfiguration = [];
            $processorConfiguration['pidInList'] = self::getCurrentUid();
            $records = $cObj->getRecords('tt_content', $processorConfiguration);

            foreach ($records as $record) {
                $lmc[] = $record['tstamp'];
            }

            if (!empty($lmc)) {
                rsort($lmc, SORT_NUMERIC);
            } else {
                $lmc[0] = '';
            }

            $processedData['lastModifiedContentElement'] = $lmc[0];
        }

        if (!empty($processorConfiguration['recentlyUpdatedContentElements'])) {
            $setMaxResults = !empty($processorConfiguration['setMaxResults']) ? $processorConfiguration['setMaxResults'] : 10;
            if (self::isMenuRecentlyUpdatedOnPage()) {
                $processedData['recentlyUpdatedContentElements'] = self::getRecentlyUpdated((int) $setMaxResults);
            }
        }

        return $processedData;
    }


    /**
     * Returns true if is page w/ content.cType == menu_recently_updated
     *
     * @return bool
     */
    protected function isMenuRecentlyUpdatedOnPage(): bool
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $sysLanguageUid = $languageAspect->getContentId() ?: 0;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
             ->select('uid')
             ->from('tt_content')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)),
                 $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter(self::getCurrentUid(), Connection::PARAM_INT)),
                 $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('menu_recently_updated'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

        return empty($result) ? false : true;
    }


    /**
     * Returns $mdtm
     *
     * @param int $setMaxResults
     * @return array $mdtm
     */
    protected function getRecentlyUpdated($setMaxResults): array
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $sysLanguageUid = $languageAspect->getContentId() ?: 0;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
             ->select('uid', 'pid', 'header', 'tstamp')
             ->from('tt_content')
             ->orderBy('tstamp', 'DESC')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)),
                 $queryBuilder->expr()->neq('pid', $queryBuilder->createNamedParameter(self::getCurrentUid(), Connection::PARAM_INT))
             )
             ->setMaxResults($setMaxResults)
             ->executeQuery()
             ->fetchAllAssociative();

        $mdtm = [];

        if (!empty($result)) {
            foreach ($result as $ce) {
                $pageTitle = self::getPageTitle($ce['pid']);
                if ($pageTitle) {
                    $mdtm[$ce['uid']][$pageTitle] = $ce;
                }
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
    protected function getPageTitle($uid): string
    {
        $pageTitle = '';

        if ($uid) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $result = $queryBuilder
                 ->select('uid', 'title', 'nav_title')
                 ->from('pages')
                 ->where(
                     $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)),
                     $queryBuilder->expr()->eq('doktype', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT))
                 )
                 ->executeQuery()
                 ->fetch();

            $pageTitle = !empty($result['nav_title']) ? $result['nav_title'] : '';
        }

        return (string)$pageTitle;
    }


    /**
     * Returns $id int
     *
     * @return int
     */
    protected function getCurrentUid(): int
    {
        $pageArguments = $this->request->getAttribute('routing');
        return $pageArguments->getPageId();
    }
}
