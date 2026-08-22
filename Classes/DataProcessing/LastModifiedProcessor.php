<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use Psr\Http\Message\ServerRequestInterface;

class LastModifiedProcessor implements DataProcessorInterface
{
    
    protected ServerRequestInterface $request;

    public function __construct(
        private readonly Context $context,
        private readonly ConnectionPool $connectionPool,
        private readonly SiteFinder $siteFinder,
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
            // must not overwrite $processorConfiguration: it also holds the config of the second block
            $recordsConfiguration = ['pidInList' => $this->getCurrentUid()];
            $records = $cObj->getRecords('tt_content', $recordsConfiguration);

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
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        $result = $queryBuilder
             ->select('uid', 'pid', 'header', 'tstamp')
             ->from('tt_content')
             ->orderBy('tstamp', 'DESC')
             ->where(
                 $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)),
                 $queryBuilder->expr()->neq('pid', $queryBuilder->createNamedParameter($this->getCurrentUid(), Connection::PARAM_INT))
             )
             // fetch a larger candidate set, because rows are filtered below
             ->setMaxResults(max($setMaxResults, 1) * 10)
             ->executeQuery()
             ->fetchAllAssociative();

        $mdtm = [];

        if (!empty($result)) {
            $site = $this->request->getAttribute('site');
            $rootPageId = $site instanceof SiteInterface ? $site->getRootPageId() : 0;
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class, $this->context);

            foreach ($result as $ce) {
                // resolves hidden, starttime/endtime, fe_group, workspace and the language overlay
                $page = $pageRepository->getPage((int)$ce['pid']);
                if (empty($page['title'])) {
                    continue;
                }
                // pages that cannot be linked
                if (in_array((int)($page['doktype'] ?? 0), [
                    PageRepository::DOKTYPE_BE_USER_SECTION,
                    PageRepository::DOKTYPE_SPACER,
                    PageRepository::DOKTYPE_SYSFOLDER,
                ], true)) {
                    continue;
                }
                // no content of foreign sites
                if ($rootPageId > 0) {
                    try {
                        if ($this->siteFinder->getSiteByPageId((int)$ce['pid'])->getRootPageId() !== $rootPageId) {
                            continue;
                        }
                    } catch (SiteNotFoundException) {
                        continue;
                    }
                }
                $mdtm[$ce['uid']][$page['title']] = $ce;
                if (count($mdtm) >= $setMaxResults) {
                    break;
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
