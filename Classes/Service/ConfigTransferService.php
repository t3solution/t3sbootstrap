<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Portable export/import of the t3sbootstrap configuration. The export holds no uids and no
 * pid, only field values and FAL references as combined identifiers ("1:/user_upload/logo.svg").
 * The import goes through the DataHandler, so RefIndex, hooks and workspaces are served.
 */
final class ConfigTransferService
{
    public const TABLE = 'tx_t3sbootstrap_domain_model_config';
    public const FORMAT_VERSION = 1;

    /**
     * Systemfelder, die nie exportiert werden (installationsabhaengig).
     */
    private const SKIP_FIELDS = [
        'uid', 'pid', 'tstamp', 'crdate', 'cruser_id', 'deleted', 'sorting',
        't3ver_oid', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_count',
        't3ver_tstamp', 't3ver_move_id', 't3_origuid', 'l10n_diffsource',
        'l10n_parent', 'l10n_source', 'l18n_parent', 'l18n_diffsource',
    ];

    private const REFERENCE_META = ['title', 'alternative', 'description', 'link', 'crop'];

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly ResourceFactory $resourceFactory,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function export(?int $pid = null): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $queryBuilder->select('*')->from(self::TABLE);
        if ($pid !== null) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT))
            );
        }

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();
        $fileFields = $this->getFileFields();
        $records = [];

        foreach ($rows as $row) {
            $fields = [];
            foreach ($row as $field => $value) {
                if (in_array($field, self::SKIP_FIELDS, true) || in_array($field, $fileFields, true)) {
                    continue;
                }
                $fields[$field] = $value;
            }

            $files = [];
            foreach ($fileFields as $fileField) {
                $references = $this->getFileReferences((int)$row['uid'], $fileField);
                if ($references !== []) {
                    $files[$fileField] = $references;
                }
            }

            $records[] = [
                'sourcePage' => $this->getPageInfo((int)$row['pid']),
                'fields' => $fields,
                'files' => $files,
            ];
        }

        return [
            'meta' => [
                'formatVersion' => self::FORMAT_VERSION,
                'table' => self::TABLE,
                'exportedAt' => date('c'),
                'source' => (string)($_SERVER['HTTP_HOST'] ?? ''),
                'typo3Version' => (new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion(),
                'count' => count($records),
            ],
            'records' => $records,
        ];
    }

    /**
     * Update the existing record, keep its uid. Fields missing from the import
     * are reset to their TCA default, so the result matches the export exactly.
     */
    public const MODE_UPDATE = 'update';

    /**
     * Delete the existing records and create new ones. Changes the uid and
     * leaves soft-deleted rows behind.
     */
    public const MODE_REPLACE = 'replace';

    /**
     * Only import when the target page has no configuration yet.
     */
    public const MODE_ADD = 'add';

    /**
     * @param array<string, mixed> $payload
     * @param string $mode one of the MODE_* constants
     * @return array{imported: int, messages: string[], errors: string[]}
     */
    public function import(array $payload, int $pid, string $mode = self::MODE_UPDATE): array
    {
        $messages = [];
        $errors = [];

        if (($payload['meta']['table'] ?? '') !== self::TABLE) {
            throw new \RuntimeException('Die Datei enthaelt keinen t3sbootstrap-Config-Export.', 1754500001);
        }
        $records = $payload['records'] ?? [];
        if (!is_array($records) || $records === []) {
            throw new \RuntimeException('Der Export enthaelt keine Datensaetze.', 1754500002);
        }
        if ($pid <= 0) {
            throw new \RuntimeException('Es wurde keine gueltige Zielseite gewaehlt.', 1754500003);
        }

        $availableColumns = array_keys($GLOBALS['TCA'][self::TABLE]['columns'] ?? []);
        $fileFields = $this->getFileFields();

        $data = [];
        $commands = [];
        $existingUids = $this->getExistingUids($pid);

        // uid of the record that gets updated in place, null when creating
        $updateUid = null;

        switch ($mode) {
            case self::MODE_ADD:
                if ($existingUids !== []) {
                    throw new \RuntimeException(
                        'Auf der Zielseite existiert bereits eine Konfiguration. '
                        . 'Waehle "Aktualisieren" oder "Ersetzen".',
                        1754500004
                    );
                }
                break;

            case self::MODE_REPLACE:
                foreach ($existingUids as $uid) {
                    $commands[self::TABLE][$uid]['delete'] = 1;
                }
                break;

            case self::MODE_UPDATE:
            default:
                if ($existingUids !== []) {
                    // the lowest uid is the one findOneBy() returns, so that is
                    // the record that is actually in effect
                    $updateUid = (int)min($existingUids);

                    $surplus = array_diff($existingUids, [$updateUid]);
                    foreach ($surplus as $uid) {
                        $commands[self::TABLE][$uid]['delete'] = 1;
                    }
                    if ($surplus !== []) {
                        $messages[] = sprintf(
                            '%d ueberzaehlige Konfiguration(en) auf der Zielseite geloescht - '
                            . 'nur der Datensatz mit uid %d ist wirksam.',
                            count($surplus),
                            $updateUid
                        );
                    }
                }
                break;
        }

        $recordIndex = 0;
        $referenceIndex = 0;

        foreach ($records as $record) {
            // first payload record updates the existing one, further records are new
            $isUpdate = $updateUid !== null && $recordIndex === 0;
            $newRecordId = $isUpdate ? (string)$updateUid : 'NEW_t3sb_' . ($recordIndex + 1);
            $recordIndex++;

            $importedFields = array_intersect_key((array)($record['fields'] ?? []), array_flip($availableColumns));

            // On update every column is written, values missing from the export falling
            // back to their TCA default. Otherwise the target keeps leftovers of its previous
            // configuration and the result is a mixture of both, file references included.
            $fields = $isUpdate
                ? array_merge($this->getBlankFields($availableColumns), $importedFields)
                : $importedFields;

            $unknown = array_diff(array_keys((array)($record['fields'] ?? [])), $availableColumns);
            if ($unknown !== []) {
                $messages[] = 'Uebersprungene Felder (in dieser Installation nicht vorhanden): ' . implode(', ', $unknown);
            }

            foreach ((array)($record['files'] ?? []) as $fileField => $references) {
                if (!in_array($fileField, $fileFields, true)) {
                    continue;
                }
                $referenceIds = [];
                foreach ((array)$references as $reference) {
                    $identifier = (string)($reference['identifier'] ?? '');
                    try {
                        $file = $this->resourceFactory->getFileObjectFromCombinedIdentifier($identifier);
                    } catch (\Throwable $e) {
                        $errors[] = 'Datei nicht gefunden, Referenz uebersprungen: ' . $identifier;
                        continue;
                    }
                    $newReferenceId = 'NEW_t3sbref_' . ++$referenceIndex;
                    $referenceData = [
                        'table_local' => 'sys_file',
                        'uid_local' => $file->getUid(),
                        'tablenames' => self::TABLE,
                        'fieldname' => $fileField,
                        'uid_foreign' => $newRecordId,
                        'pid' => $pid,
                    ];
                    foreach (self::REFERENCE_META as $metaField) {
                        if (isset($reference[$metaField]) && $reference[$metaField] !== null) {
                            $referenceData[$metaField] = $reference[$metaField];
                        }
                    }
                    $data['sys_file_reference'][$newReferenceId] = $referenceData;
                    $referenceIds[] = $newReferenceId;
                }
                // an empty list removes the existing references on update
                $fields[$fileField] = implode(',', $referenceIds);
            }

            $fields['pid'] = $pid;
            $data[self::TABLE][$newRecordId] = $fields;
        }

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($data, $commands);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        foreach ($dataHandler->errorLog as $error) {
            $errors[] = (string)$error;
        }

        return [
            'imported' => $recordIndex,
            'messages' => $messages,
            'errors' => $errors,
        ];
    }

    /**
     * Rootseiten fuer die Auswahl im Modul.
     *
     * @return array<int, array{uid: int, title: string}>
     */
    public function getSiteRootPages(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $rows = $queryBuilder
            ->select('uid', 'title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
            )
            ->orderBy('title')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            static fn(array $row): array => ['uid' => (int)$row['uid'], 'title' => (string)$row['title']],
            $rows
        );
    }

    /**
     * @return string[]
     */
    private function getFileFields(): array
    {
        $fields = [];
        foreach ($GLOBALS['TCA'][self::TABLE]['columns'] ?? [] as $name => $configuration) {
            $type = $configuration['config']['type'] ?? '';
            if ($type === 'file') {
                $fields[] = $name;
                continue;
            }
            if ($type === 'inline' && ($configuration['config']['foreign_table'] ?? '') === 'sys_file_reference') {
                $fields[] = $name;
            }
        }

        return $fields;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getFileReferences(int $uidForeign, string $fieldName): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $rows = $queryBuilder
            ->select('ref.title', 'ref.alternative', 'ref.description', 'ref.link', 'ref.crop', 'file.identifier', 'file.storage')
            ->from('sys_file_reference', 'ref')
            ->join('ref', 'sys_file', 'file', $queryBuilder->expr()->eq('ref.uid_local', $queryBuilder->quoteIdentifier('file.uid')))
            ->where(
                $queryBuilder->expr()->eq('ref.tablenames', $queryBuilder->createNamedParameter(self::TABLE)),
                $queryBuilder->expr()->eq('ref.fieldname', $queryBuilder->createNamedParameter($fieldName)),
                $queryBuilder->expr()->eq('ref.uid_foreign', $queryBuilder->createNamedParameter($uidForeign, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('ref.table_local', $queryBuilder->createNamedParameter('sys_file'))
            )
            ->orderBy('ref.sorting_foreign')
            ->executeQuery()
            ->fetchAllAssociative();

        $references = [];
        foreach ($rows as $row) {
            $reference = ['identifier' => $row['storage'] . ':' . $row['identifier']];
            foreach (self::REFERENCE_META as $metaField) {
                if (!empty($row[$metaField])) {
                    $reference[$metaField] = $row[$metaField];
                }
            }
            $references[] = $reference;
        }

        return $references;
    }

    /**
     * @return int[]
     */
    private function getExistingUids(int $pid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $rows = $queryBuilder
            ->select('uid')
            ->from(self::TABLE)
            ->where($queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)))
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(static fn(array $row): int => (int)$row['uid'], $rows);
    }

    /**
     * @return array{uid: int, title: string}
     */
    private function getPageInfo(int $pid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();
        $title = $queryBuilder
            ->select('title')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)))
            ->executeQuery()
            ->fetchOne();

        return ['uid' => $pid, 'title' => (string)($title ?: '')];
    }

    /**
     * All columns of the table set to their TCA default.
     *
     * @param string[] $availableColumns
     * @return array<string, mixed>
     */
    private function getBlankFields(array $availableColumns): array
    {
        $blank = [];

        foreach ($availableColumns as $column) {
            if (in_array($column, self::SKIP_FIELDS, true)) {
                continue;
            }

            $config = $GLOBALS['TCA'][self::TABLE]['columns'][$column]['config'] ?? [];

            if (array_key_exists('default', $config)) {
                $blank[$column] = $config['default'];
                continue;
            }

            $blank[$column] = match ($config['type'] ?? 'input') {
                'check', 'number', 'inline', 'language', 'datetime' => 0,
                default => '',
            };
        }

        return $blank;
    }
}
