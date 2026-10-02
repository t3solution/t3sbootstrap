<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Wrapper;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MasonryWrapper implements SingletonInterface
{
    /** The column EXT:container fills for this wrapper, see TCA/Overrides/tt_content_container.php */
    private const COLPOS = 283;

    public function getProcessedData(array $processedData, array $flexconf): array
    {
        $processedData['masonryClass'] = $flexconf['colclass'] ?? '';
        $processedData['shuffle'] = false;
        $processedData['shuffleNotice'] = '';

        if (empty($flexconf['shuffle'])) {
            return $processedData;
        }

        // From here on the filter was asked for. Every way out says why, otherwise the
        // option looks broken instead of unconfigured.
        $uid = (int)($processedData['data']['uid'] ?? 0);
        $selected = GeneralUtility::intExplode(',', (string)($flexconf['shuffleCategories'] ?? ''), true);

        if ($selected === []) {
            $processedData['shuffleNotice'] = 'No category selected for the filter.';

            return $processedData;
        }

        $children = $uid > 0 ? $this->childUids($uid) : [];
        if ($children === []) {
            $processedData['shuffleNotice'] = 'The wrapper has no content elements.';

            return $processedData;
        }

        $assigned = $this->assignedCategories($children);
        $titles = $this->categoryTitles($selected);

        // The order of the buttons is the order of the field. A category without a single
        // element in this wrapper only takes up space, so it is dropped.
        $filters = [];
        foreach ($selected as $category) {
            if (empty($assigned[$category]) || !isset($titles[$category])) {
                continue;
            }
            $filters[] = ['key' => 'c-' . $category, 'title' => $titles[$category]];
        }

        if ($filters === []) {
            $processedData['shuffleNotice'] = 'None of the selected categories is assigned to'
                . ' a content element of this wrapper (' . count($children) . ' elements checked).'
                . ' The category is set on the element itself, tab "Categories".';

            return $processedData;
        }

        // Every child gets the attribute, one without a category an empty one - it is
        // then only visible under "all".
        $groups = array_fill_keys($children, []);
        foreach ($assigned as $category => $contentUids) {
            if (!in_array($category, $selected, true)) {
                continue;
            }
            foreach ($contentUids as $contentUid) {
                $groups[$contentUid][] = 'c-' . $category;
            }
        }

        $processedData['shuffle'] = true;
        $processedData['shuffleFilters'] = $filters;
        $processedData['shuffleLabel'] = trim((string)($flexconf['shuffleLabel'] ?? '')) ?: 'Filtern';
        $processedData['shuffleAllLabel'] = trim((string)($flexconf['shuffleAllLabel'] ?? '')) ?: 'Alle';
        $processedData['shuffleGroups'] = array_map(
            static fn(array $keys): string => implode(',', array_unique($keys)),
            $groups
        );

        return $processedData;
    }

    /**
     * The content elements of this wrapper, in their own sorting.
     *
     * @return list<int>
     */
    private function childUids(int $wrapperUid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $rows = $queryBuilder
            ->select('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'tx_container_parent',
                    $queryBuilder->createNamedParameter($wrapperUid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'colPos',
                    $queryBuilder->createNamedParameter(self::COLPOS, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map('intval', $rows);
    }

    /**
     * @param list<int> $contentUids
     * @return array<int, list<int>> category uid => content element uids
     */
    private function assignedCategories(array $contentUids): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->getRestrictions()->removeAll();

        $rows = $queryBuilder
            ->select('uid_local', 'uid_foreign')
            ->from('sys_category_record_mm')
            ->where(
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter('tt_content')
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter('categories')
                ),
                $queryBuilder->expr()->in(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($contentUids, Connection::PARAM_INT_ARRAY)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $assigned = [];
        foreach ($rows as $row) {
            $assigned[(int)$row['uid_local']][] = (int)$row['uid_foreign'];
        }

        return $assigned;
    }

    /**
     * @param list<int> $categoryUids
     * @return array<int, string>
     */
    private function categoryTitles(array $categoryUids): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');

        $rows = $queryBuilder
            ->select('uid', 'title')
            ->from('sys_category')
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($categoryUids, Connection::PARAM_INT_ARRAY)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $titles = [];
        foreach ($rows as $row) {
            $titles[(int)$row['uid']] = (string)$row['title'];
        }

        return $titles;
    }
}
