<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class T3sbCardsUpdateWizard implements UpgradeWizardInterface
{
	/**
	 * Return the identifier for this wizard
	 * This should be the same string as used in the ext_localconf class registration
	 */
	public function getIdentifier(): string
	{
	  return 't3sbCardsUpdateWizard';
	}

	/**
	 * Return the speaking name of this wizard
	 */
	public function getTitle(): string
	{
	  return 'T3SBootstrap - Migrate Card text fields';
	}

	/**
	 * Return the description for this wizard
	 */
	public function getDescription(): string
	{
	  return 'Migrate Card text fields (Header,Text-Top,Text-Bottom,Footer-Text) from pi_flexform to tt_content';
	}

	/**
	 * Execute the update
	 *
	 * Called when a wizard reports that an update is necessary
	 */
	public function executeUpdate(): bool
	{
		$this->upgradeCards();

 		return true;
	}

	/**
	 * Migrate...
	 */
	protected function upgradeCards(): void
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statements = $queryBuilder
			->select('uid', 'pi_flexform')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('t3sbs_card'))
			)
			->executeQuery()
			->fetchAll();

		if (!empty($statements)) {
			$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
			foreach ($statements as $statement) {
				$recordId = (int)$statement['uid'];
				$flexconf = $flexFormService->convertFlexFormContentToArray($statement['pi_flexform']);
				$count = 0;
				// List Group inline
				if (!empty($flexconf['list'])) {
					$queryBuilder = $connectionPool->getQueryBuilderForTable('tx_t3sbootstrap_list_item_inline');
					$countBefore = $queryBuilder
						->count('parentid')
						->from('tx_t3sbootstrap_list_item_inline')
						->where(
							$queryBuilder->expr()->eq('parentid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
						)
						->executeQuery()
						->fetchOne();
	
					if ($countBefore === 0) {
						// insert only once
						if (!empty($flexconf['list']['container'])) {
							foreach ( $flexconf['list']['container'] as $listItem) {
								if (!empty($listItem['list']['group'])) {
									$queryBuilder
										->insert('tx_t3sbootstrap_list_item_inline')
										->values([
											'parentid' => $recordId,
											'parenttable' => 'tt_content',
											'listitem' => $listItem['list']['group']
										])
										->executeStatement();
								}
							}
						}
					}
	
					$count = $queryBuilder
						->count('parentid')
						->from('tx_t3sbootstrap_list_item_inline')
						->where(
							$queryBuilder->expr()->eq('parentid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
						)
						->executeQuery()
						->fetchOne();
				}
	
				if (!empty($flexconf)) {
					// update/migrate
					$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
					$queryBuilder
						->update('tt_content')
						->where(
							$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($recordId, \PDO::PARAM_INT))
						)
						->set('tx_t3sbootstrap_cardheader', $flexconf['header']['text'])
						->set('bodytext', $flexconf['text']['top'])
						->set('tx_t3sbootstrap_bodytext', $flexconf['text']['bottom'])
						->set('tx_t3sbootstrap_cardfooter', $flexconf['footer']['text'])
						->set('tx_t3sbootstrap_list_item', $count)
						->executeQuery();
				}

			}
		}
	}

	/**
	 * Is an update necessary?
	 *
	 * Is used to determine whether a wizard needs to be run.
	 * Check if data for migration exists.
	 */
	public function updateNecessary(): bool
	{
		$updateNeeded = false;
		// Check if the database table even exists
		if ( $this->checkIfWizardIsRequired() ) {
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
		return '';
	}


	/**
	 * Check for old Bootstrap Utility Classes
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function checkIfWizardIsRequired(): bool
	{
		$require = false;
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$statements = $queryBuilder
			->select('uid', 'pi_flexform')
			->from('tt_content')
			->where(
				$queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('t3sbs_card'))
			)
			->executeQuery()
			->fetchAll();

		if (!empty($statements)) {
			$flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
			foreach ($statements as $statement) {
				$recordId = (int)$statement['uid'];
				$flexconf = $flexFormService->convertFlexFormContentToArray($statement['pi_flexform']);
				if (!empty($flexconf)) {
					$require = true;
				}
			}
		}

		return $require;
	}

}
