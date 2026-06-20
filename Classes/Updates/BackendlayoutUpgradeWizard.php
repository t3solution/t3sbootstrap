<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[UpgradeWizard('t3sbootstrap_backendlayoutUpgradeWizard')]
final class BackendlayoutUpgradeWizard implements UpgradeWizardInterface
{
	private const SUFFIX = '_Extra';

	private const FIELDS = ['backend_layout', 'backend_layout_next_level'];

	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate backend layout keys';
	}

	public function getDescription(): string
	{
		return 'Removes the "_Extra" suffix from "backend_layout" and "backend_layout_next_level" on pages.';
	}

	public function executeUpdate(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$connection = $connectionPool->getConnectionForTable('pages');

		foreach ($this->fetchRowsToMigrate() as $row) {
			$update = [];

			foreach (self::FIELDS as $field) {
				$value = (string)($row[$field] ?? '');
				$migrated = $this->removeSuffix($value);

				if ($migrated !== $value) {
					$update[$field] = $migrated;
				}
			}

			if ($update !== []) {
				$connection->update('pages', $update, ['uid' => (int)$row['uid']]);
			}
		}

		return true;
	}

	public function updateNecessary(): bool
	{
		foreach ($this->fetchRowsToMigrate() as $row) {
			foreach (self::FIELDS as $field) {
				if ($this->needsMigration((string)($row[$field] ?? ''))) {
					return true;
				}
			}
		}

		return false;
	}

	public function getPrerequisites(): array
	{
		return [];
	}

	/**
	 * Removes the “_Extra” suffix if a non-empty value contains it.
	 */
	private function removeSuffix(string $value): string
	{
		if (!$this->needsMigration($value)) {
			return $value;
		}

		return substr($value, 0, -strlen(self::SUFFIX));
	}

	private function needsMigration(string $value): bool
	{
		return $value !== '' && str_ends_with($value, self::SUFFIX);
	}

	/**
	 * Returns all pages where at least one of the two fields ends with “_Extra”.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function fetchRowsToMigrate(): array
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');

		return $queryBuilder
			->select('uid', 'backend_layout', 'backend_layout_next_level')
			->from('pages')
			->where(
				$queryBuilder->expr()->or(
					$queryBuilder->expr()->like(
						'backend_layout',
						$queryBuilder->createNamedParameter('%' . self::SUFFIX)
					),
					$queryBuilder->expr()->like(
						'backend_layout_next_level',
						$queryBuilder->createNamedParameter('%' . self::SUFFIX)
					)
				)
			)
			->executeQuery()
			->fetchAllAssociative();
	}
}
