<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;

final class BgOpacityUpgradeWizard implements UpgradeWizardInterface
{
    /**
     * Return the speaking name of this wizard
     */
    public function getTitle(): string
    {
        return 'Migrate background opacity';
    }

    /**
     * Return the description for this wizard
     */
    public function getDescription(): string
    {
        return 'EXT:t3sbootstrap: Change the background opacity settings (tx_t3sbootstrap_bgopacity) from 100 to 1 etc.';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     */
    public function executeUpdate(): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $statements = $queryBuilder
                 ->select('uid', 'tx_t3sbootstrap_bgopacity')
                 ->from('tt_content')
                 ->executeQuery()
                 ->fetchAllAssociative();

        $erg = '';
        foreach ($statements as $key=>$statement) {
            $recordId = (int)$statement['uid'];
            if ($statement['tx_t3sbootstrap_bgopacity'] > '1' && $statement['tx_t3sbootstrap_bgopacity'] != '') {
                $arrA[$key]['uid'] = $statement['uid'];
                $arrA[$key]['tx_t3sbootstrap_bgopacity'] = $statement['tx_t3sbootstrap_bgopacity'] / 100;
                $erg = $statement['tx_t3sbootstrap_bgopacity'] / 100;
                $queryBuilder
                        ->update('tt_content')
                        ->where(
                            $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, Connection::PARAM_INT))
                        )
                     ->set('tx_t3sbootstrap_bgopacity', $erg)
                     ->executeStatement();
            }
        }

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        $updateNeeded = false;
        // Check if the database table even exists
        if ($this->checkIfWizardIsRequired()) {
            $updateNeeded = true;
        }

        return $updateNeeded;
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * Check if there are record within database table with an empty "compress" field.
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function checkIfWizardIsRequired(): bool
    {
        $required = false;
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
        $numberOfEntries = $queryBuilder
             ->count('uid')
             ->from('tt_content')
             ->where(
                 $queryBuilder->expr()->gt('tx_t3sbootstrap_bgopacity', '1')
             )
            ->executeQuery()
            ->fetchOne();

        if (!empty($numberOfEntries)) {
            $required = true;
        }

        return $required;
    }
}
