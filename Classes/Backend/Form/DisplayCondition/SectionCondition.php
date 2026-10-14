<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Backend\Form\DisplayCondition;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;

/**
 * Shows a "hide on this page" checkbox only where that part can appear at all.
 *
 * The switches live in the T3SB configuration record, not in the page - so a plain
 * FIELD condition cannot reach them. The record sits on the root page of the site,
 * which is why the rootline is walked upwards from the edited page.
 *
 * Which fields of the configuration record decide is handed over as condition
 * parameters, e.g.
 *   USER:...\SectionCondition->isEnabled:breadcrumb_enable:breadcrumb_bottom
 * Any one of them being set is enough - the breadcrumb may sit above OR below.
 *
 * A select ('' = off) counts as set when it is not empty, a checkbox when it is
 * not 0. !empty() covers both, which is why the fields need no type map here.
 */
final class SectionCondition
{
    private const TABLE = 'tx_t3sbootstrap_domain_model_config';

    public function isEnabled(array $parameters): bool
    {
        $fields = array_values(array_filter(
            (array)($parameters['conditionParameters'] ?? []),
            // Guards against a typo in the TCA turning into an SQL error.
            static fn($field): bool => is_string($field) && preg_match('/^[a-z_]+$/', $field) === 1
        ));

        if ($fields === []) {
            // Without fields there is nothing to check - showing the field is the
            // harmless answer.
            return true;
        }

        $record = $parameters['record'] ?? [];

        // A page being created has no uid yet, and its pid is the parent. Both are
        // fine here: the configuration record is looked up along the rootline.
        $pageUid = (int)($record['uid'] ?? 0);
        if ($pageUid <= 0) {
            $pageUid = (int)($record['pid'] ?? 0);
        }
        if ($pageUid <= 0) {
            // No page to work from - showing the field is the harmless answer,
            // hiding it would make the option unreachable.
            return true;
        }

        if ($this->isActive($pageUid, $fields)) {
            return true;
        }

        try {
            $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $pageUid)->get();
        } catch (\Throwable) {
            // A broken rootline must not make the field disappear.
            return true;
        }

        foreach ($rootline as $page) {
            if ($this->isActive((int)($page['uid'] ?? 0), $fields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True when a configuration record on this page has any of the given fields set.
     *
     * @param string[] $fields
     */
    private function isActive(int $pid, array $fields): bool
    {
        if ($pid <= 0) {
            return false;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);

        $row = $queryBuilder
            ->select(...$fields)
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            return false;
        }

        foreach ($fields as $field) {
            if (!empty($row[$field])) {
                return true;
            }
        }

        return false;
    }
}
