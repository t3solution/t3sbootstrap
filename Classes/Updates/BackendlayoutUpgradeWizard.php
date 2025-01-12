<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[UpgradeWizard('t3sbootstrap_backendlayoutUpgradeWizard')]
final class BackendlayoutUpgradeWizard implements UpgradeWizardInterface
{  
    
	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate "Expanded Content" to backend layouts';
	}

	public function getDescription(): string
	{
		return 'Migrate "Expanded Content" (colPos 20 & 21) from EM config to backend layouts. This gives you a better overview and more options.';
	}

	public function executeUpdate(): bool
	{

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');

		$numberOfEC = $queryBuilder
			->count('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(20, Connection::PARAM_INT))
			)
			->orWhere(
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(21, Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchOne();
		
		
		if ( $numberOfEC > 0 ) {
		
			$fieldName = 'backend_layout';
			$fieldNameNext = 'backend_layout_next_level';
			$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
			$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
			$statements = $queryBuilder
				->select('uid', $fieldName, $fieldNameNext)
				->from('pages')
				->where($queryBuilder->expr()->neq($fieldName,'""'))
				->orWhere($queryBuilder->expr()->neq($fieldNameNext,'""'))
				->executeQuery()
				->fetchAllAssociative();
		
			foreach($statements as $key=>$statement) {
				$belayout = !empty($statement[$fieldName]) ? $statement[$fieldName].'_Extra' : '';
				$belayoutNext = !empty($statement[$fieldNameNext]) ? $statement[$fieldNameNext].'_Extra' : '';
		
				$queryBuilder
				->update('pages')
				->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($statement['uid'], Connection::PARAM_INT)))
				->set($fieldName, $belayout)
				->set($fieldNameNext, $belayoutNext)
				->executeStatement();


			}
		
		}
		
		return true;
	}


	public function updateNecessary(): bool
	{
		$updateNeeded = false;
		// Check if the database table even exists
		if ($this->checkIfWizardIsRequired()) {
			$updateNeeded = true;
		}

		return $updateNeeded;
	}


	public function getPrerequisites(): array
	{
		return [];
	}


	protected function checkIfWizardIsRequired(): bool
	{
		$required = false;
		
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');

		$numberOfEC = $queryBuilder
			->count('uid')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(20, Connection::PARAM_INT))
			)
			->orWhere(
				$queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter(21, Connection::PARAM_INT))
			)
			->executeQuery()
			->fetchOne();

		if ( $numberOfEC > 0 ) {
		
			$fieldName = 'backend_layout';
			$fieldNameNext = 'backend_layout_next_level';
			$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
			$statements = $queryBuilder
				->select('uid', $fieldName, $fieldNameNext)
				->from('pages')
				->where($queryBuilder->expr()->neq($fieldName,'""'))
				->orWhere($queryBuilder->expr()->neq($fieldNameNext,'""'))
				->executeQuery()
				->fetchAllAssociative();
		
			foreach($statements as $key=>$statement) {
				if (str_ends_with($statement[$fieldName], '_Extra')) {
					// the wizard is not required
				} elseif (str_ends_with($statement[$fieldNameNext], '_Extra')) {
					// the wizard is not required
				} else {
					$required = true;
					break;
				}
			}
		}

		return $required;
	}

}
