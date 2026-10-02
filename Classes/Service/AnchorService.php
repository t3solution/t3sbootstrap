<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Rebuilds the "Speaking ID" (tx_t3sbootstrap_anchor) from the header, with the same
 * generator TYPO3 uses when the field is empty on save. Saves opening every element by
 * hand after a round of copying.
 */
final class AnchorService
{
    public const TABLE = 'tt_content';
    public const FIELD = 'tx_t3sbootstrap_anchor';

    /** Only elements without an anchor. A value that is there was put there by somebody. */
    public const MODE_EMPTY = 'empty';

    /** Every anchor that no longer matches its own header - manually set ones included. */
    public const MODE_REBUILD = 'rebuild';

    /** Empties every anchor. Links pointing at them stop working. */
    public const MODE_CLEAR = 'clear';

    /**
     * The configuration of the TCA column. Repeated here because the column is not
     * registered while the option "Speaking ID" is off - the command still has to run.
     */
    private const SLUG_CONFIG = [
        'type' => 'slug',
        'generatorOptions' => [
            'fields' => ['header'],
            'prefixParentPageSlug' => false,
        ],
        'fallbackCharacter' => '-',
        'eval' => 'uniqueInPid',
    ];

    /**
     * Plans the changes without writing them.
     *
     * @return list<array{uid:int,pid:int,lang:int,header:string,old:string,new:string}>
     */
    public function collect(string $mode = self::MODE_EMPTY, int $pid = 0, int $uid = 0): array
    {
        $records = $this->fetchRecords($pid, $uid);

        if ($mode === self::MODE_CLEAR) {
            return $this->collectClear($records);
        }

        $slugHelper = $this->getSlugHelper();

        // Uniqueness per page and language, exactly as eval=uniqueInPid checks it. Seeded
        // with what is in the database and kept up to date while planning, so a batch of
        // elements with the same header does not end up with the same anchor.
        $taken = [];
        foreach ($records as $record) {
            $anchor = (string)$record[self::FIELD];
            if ($anchor !== '') {
                $taken[$this->scope($record)][$anchor] = (int)$record['uid'];
            }
        }

        $changes = [];

        foreach ($records as $record) {
            $header = trim((string)$record['header']);
            $old = (string)$record[self::FIELD];

            // Nothing to derive an anchor from.
            if ($header === '') {
                continue;
            }

            if ($mode === self::MODE_EMPTY && $old !== '') {
                continue;
            }

            $scope = $this->scope($record);
            $base = $slugHelper->generate($record, (int)$record['pid']);

            if ($base === '') {
                continue;
            }

            $new = $this->makeUnique($base, $taken[$scope] ?? [], (int)$record['uid']);

            if ($new === $old) {
                continue;
            }

            if ($old !== '') {
                unset($taken[$scope][$old]);
            }
            $taken[$scope][$new] = (int)$record['uid'];

            $changes[] = [
                'uid' => (int)$record['uid'],
                'pid' => (int)$record['pid'],
                'lang' => (int)$record['sys_language_uid'],
                'header' => $header,
                'old' => $old,
                'new' => $new,
            ];
        }

        return $changes;
    }

    /**
     * Every element that carries an anchor, planned back to an empty value.
     *
     * @param list<array<string, mixed>> $records
     * @return list<array{uid:int,pid:int,lang:int,header:string,old:string,new:string}>
     */
    private function collectClear(array $records): array
    {
        $changes = [];

        foreach ($records as $record) {
            $old = (string)$record[self::FIELD];

            if ($old === '') {
                continue;
            }

            $changes[] = [
                'uid' => (int)$record['uid'],
                'pid' => (int)$record['pid'],
                'lang' => (int)$record['sys_language_uid'],
                'header' => trim((string)$record['header']),
                'old' => $old,
                'new' => '',
            ];
        }

        return $changes;
    }

    /**
     * @param list<array{uid:int,new:string}> $changes
     */
    public function apply(array $changes): int
    {
        if ($changes === []) {
            return 0;
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE);

        $written = 0;
        foreach ($changes as $change) {
            // Deliberately without DataHandler and without touching tstamp: the field is a
            // plain string, no relation - the reference index does not know it, and the
            // editorial "last changed" of a few hundred elements should not jump because
            // of a technical field.
            $written += $connection->update(
                self::TABLE,
                [self::FIELD => $change['new']],
                ['uid' => $change['uid']],
                [Connection::PARAM_STR]
            );
        }

        return $written;
    }

    /**
     * Live records only. A workspace version carries its own row; rewriting it behind the
     * editor's back would show up as a change nobody made.
     *
     * @return list<array<string, mixed>>
     */
    private function fetchRecords(int $pid, int $uid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder
            ->select('uid', 'pid', 'header', 'sys_language_uid', self::FIELD)
            ->from(self::TABLE)
            ->where($queryBuilder->expr()->eq('t3ver_wsid', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)))
            ->orderBy('pid')
            ->addOrderBy('sorting')
            ->addOrderBy('uid');

        if ($pid > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
            );
        }

        if ($uid > 0) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
            );
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    private function getSlugHelper(): SlugHelper
    {
        $configuration = $GLOBALS['TCA'][self::TABLE]['columns'][self::FIELD]['config'] ?? self::SLUG_CONFIG;

        return GeneralUtility::makeInstance(SlugHelper::class, self::TABLE, self::FIELD, $configuration);
    }

    /**
     * @param array<string, int> $taken anchor => uid
     */
    private function makeUnique(string $base, array $taken, int $uid): string
    {
        if (!isset($taken[$base]) || $taken[$base] === $uid) {
            return $base;
        }

        // Same suffix scheme as SlugHelper::buildSlugForUniqueInPid().
        for ($i = 1; $i < 1000; $i++) {
            $candidate = $base . '-' . $i;
            if (!isset($taken[$candidate]) || $taken[$candidate] === $uid) {
                return $candidate;
            }
        }

        return $base . '-' . $uid;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function scope(array $record): string
    {
        return $record['pid'] . '-' . $record['sys_language_uid'];
    }
}
