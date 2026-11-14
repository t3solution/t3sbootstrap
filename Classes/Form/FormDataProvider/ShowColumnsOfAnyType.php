<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Form\FormDataProvider;

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

use Psr\Http\Message\ServerRequestInterface;
use T3SBS\T3sbootstrap\Service\ConfigTransferService;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

/**
 * Lets a columnsOnly request reach any column of the configuration record.
 *
 * There is one record per root page. Its record types are a view switch on that one record,
 * not kinds of record - each type shows a part of the same columns. FormEngine resolves
 * columnsOnly against the showitem of the current type only, so a field belonging to another
 * type would open an empty form. That is what the constants table in the backend module runs
 * into: the constants are generated from every column of the table.
 *
 * The missing columns are therefore appended to the showitem - but only for a columnsOnly
 * request, which renders that one field and nothing else. A full edit form passes no
 * columnsOnly and keeps its type untouched.
 *
 * Only genuinely missing ones: a column the current type already shows must not be appended.
 * ListOfFieldsContainer stops at the first match for a plain field, but not for one found
 * inside a palette - there it keeps scanning and would match the appended copy as well,
 * which renders the field twice.
 */
final class ShowColumnsOfAnyType implements FormDataProviderInterface
{
    public function addData(array $result): array
    {
        if (($result['tableName'] ?? '') !== ConfigTransferService::TABLE) {
            return $result;
        }

        $type = (string)($result['recordTypeValue'] ?? '');
        if (!isset($result['processedTca']['types'][$type]['showitem'])) {
            return $result;
        }

        $typeField = (string)($result['processedTca']['ctrl']['type'] ?? '');
        $present = $this->resolveFieldNames(
            (string)$result['processedTca']['types'][$type]['showitem'],
            (array)($result['processedTca']['palettes'] ?? [])
        );
        $columns = [];

        foreach ($this->getRequestedColumns() as $column) {
            // only real columns the type does not show already, and never the type field -
            // switching the kind of the record through a form that shows nothing else is a trap
            if ($column !== ''
                && $column !== $typeField
                && isset($result['processedTca']['columns'][$column])
                && !in_array($column, $present, true)
            ) {
                $columns[] = $column;
            }
        }

        if ($columns === []) {
            return $result;
        }

        $result['processedTca']['types'][$type]['showitem'] .= ',' . implode(',', array_unique($columns));

        return $result;
    }

    /**
     * Field names of a showitem string, palettes resolved. Dividers, line breaks and the
     * labels after a semicolon fall away.
     *
     * @param array<string, mixed> $palettes
     * @return string[]
     */
    private function resolveFieldNames(string $showitem, array $palettes, int $depth = 0): array
    {
        if ($depth > 1) {
            return [];
        }

        $fields = [];

        foreach (explode(',', $showitem) as $item) {
            $parts = array_map(trim(...), explode(';', $item));
            $name = $parts[0] ?? '';

            if ($name === '' || $name === '--div--' || $name === '--linebreak--') {
                continue;
            }

            if ($name === '--palette--') {
                $palette = (string)($palettes[$parts[2] ?? '']['showitem'] ?? '');
                foreach ($this->resolveFieldNames($palette, $palettes, $depth + 1) as $field) {
                    $fields[] = $field;
                }
                continue;
            }

            $fields[] = $name;
        }

        return $fields;
    }

    /**
     * @return string[]
     */
    private function getRequestedColumns(): array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return [];
        }

        $body = $request->getParsedBody();
        $columnsOnly = (is_array($body) ? ($body['columnsOnly'] ?? null) : null)
            ?? $request->getQueryParams()['columnsOnly']
            ?? null;

        if (!is_array($columnsOnly)) {
            return [];
        }

        $forTable = $columnsOnly[ConfigTransferService::TABLE] ?? null;

        return is_array($forTable) ? array_values(array_filter($forTable, 'is_string')) : [];
    }
}
