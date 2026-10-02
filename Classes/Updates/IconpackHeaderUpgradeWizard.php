<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[UpgradeWizard('t3sbootstrap_iconpackHeaderUpgradeWizard')]
final class IconpackHeaderUpgradeWizard implements UpgradeWizardInterface
{
   
	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate FA7 free icons in tt_content:tx_t3sbootstrap_header_fontawesome to use with EXT:iconpack & EXT:iconpack_fontawesome';
	}

	public function getDescription(): string
	{
		return 'Migrate fa-solid, fas, fa-brands, fab, fa-regular, far and fixed width, size, transform, decoration';
	}

	public function executeUpdate(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->getRestrictions()->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class));

		// replace header_icon to use iconpack
		$fieldName = 'tx_t3sbootstrap_header_fontawesome';
		$statements = $queryBuilder
			 ->select('uid', $fieldName)
			 ->from('tt_content')
			 ->where(
			 	$queryBuilder->expr()->neq($fieldName, $queryBuilder->createNamedParameter('')),
			 	// an icon that is already set in the target field wins
			 	$queryBuilder->expr()->eq('header_icon', $queryBuilder->createNamedParameter(''))
			 )
			 ->executeQuery()
			 ->fetchAllAssociative();

		foreach($statements as $key=>$statement) {

			$string = $statement[$fieldName];

			if (!empty($string)) {

				$erg = IconpackClassParser::toIconfig((string)$string);

				if ($erg === '') {
					// nothing recognisable in there - leave the record alone rather
					// than writing a bare "fa7:" and clearing the source field
					continue;
				}

				$connectionPool->getConnectionForTable('tt_content')->update(
					'tt_content',
					[
						'header_icon' => $erg,
						$fieldName => '',
					],
					['uid' => (int)$statement['uid']]
				);
			}
		}

		return true;
	}


	public function updateNecessary(): bool
	{
		$updateNeeded = false;

		if ( ExtensionManagementUtility::isLoaded('iconpack') ) {
			// Check if the database table even exists
			if ($this->checkIfHeaderWizardIsRequired()) {
				return true;
			}
		}

		return $updateNeeded;
	}


	public function getPrerequisites(): array
	{
		return [];
	}


	protected function checkIfHeaderWizardIsRequired(): bool
	{
		$required = false;

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->getRestrictions()->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class));
		
		$fieldName = 'tx_t3sbootstrap_header_fontawesome';
		$rows = $queryBuilder
				 ->select('tx_t3sbootstrap_header_fontawesome')
				 ->from('tt_content')
				 ->where(
				 	$queryBuilder->expr()->neq($fieldName, $queryBuilder->createNamedParameter('')),
				 	// an icon that is already set in the target field wins
				 	$queryBuilder->expr()->eq('header_icon', $queryBuilder->createNamedParameter(''))
				 )
				 ->executeQuery()
				 ->fetchFirstColumn();

		// Values without a recognisable icon name are skipped by executeUpdate(),
		// so they must not keep the wizard pending either.
		foreach ($rows as $row) {
			if (IconpackClassParser::toIconfig((string)$row) !== '') {
				$required = true;
				break;
			}
		}

		return $required;
	}


}