<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Psr\Http\Message\ServerRequestInterface;

class LastModifiedProcessor implements DataProcessorInterface
{
    
    protected ServerRequestInterface $request;

    public function __construct(
        private readonly Context $context,
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function process(
        ContentObjectRenderer $cObj, 
        array $contentObjectConfiguration, 
        array $processorConfiguration, 
        array $processedData
    ): array
    {
        /** @var ServerRequestInterface $request */
        $this->request = $cObj->getRequest();

        if (!empty($processorConfiguration['lastModifiedContentElement'])) {
            $processorConfiguration = [];
            $processorConfiguration['pidInList'] = $this->getCurrentUid();
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
            $setMaxResults = $processorConfiguration['setMaxResults'] ?? 10;
            if ($this->isMenuRecentlyUpdatedOnPage()) {
                $processedData['recentlyUpdatedContentElements'] = $this->getRecentlyUpdated((int) $setMaxResults);
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
        $languageAspect = $this->context->getAspect('language');
        $sysLanguageUid = $languageAspect->getContentId() ?: 0;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
             ->select('uid')
             ->from('tt_content')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)),
                 $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($this->getCurrentUid(), Connection::PARAM_INT)),
                 $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('menu_recently_updated'))
             )
             ->executeQuery()
             ->fetchAllAssociative();

        return !empty($result);
    }


    /**
     * Returns $mdtm
     *
     * @param int $setMaxResults
     * @return array $mdtm
     */
    protected function getRecentlyUpdated(int $setMaxResults): array
    {
        $languageAspect = $this->context->getAspect('language');
        $sysLanguageUid = $languageAspect->getContentId() ?: 0;
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
             ->select('uid', 'pid', 'header', 'tstamp')
             ->from('tt_content')
             ->orderBy('tstamp', 'DESC')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)),
                 $queryBuilder->expr()->neq('pid', $queryBuilder->createNamedParameter($this->getCurrentUid(), Connection::PARAM_INT))
             )
             ->setMaxResults($setMaxResults)
             ->executeQuery()
             ->fetchAllAssociative();

        $mdtm = [];

        if (!empty($result)) {
            foreach ($result as $ce) {
                $pageTitle = BackendUtility::getRecord('pages', $ce['pid'], 'title')['title'];
                if (!empty($pageTitle)) {
                    $mdtm[$ce['uid']][$pageTitle] = $ce;
                }
            }
        }

        return $mdtm;
    }


    /**
     * Returns $id int
     *
     * @return int
     */
    protected function getCurrentUid(): int
    {
        return $this->request->getAttribute('routing')->getPageId();
    }
}
