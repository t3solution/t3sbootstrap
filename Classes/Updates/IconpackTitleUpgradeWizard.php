<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[UpgradeWizard('t3sbootstrap_iconpackTitleUpgradeWizard')]
final class IconpackTitleUpgradeWizard implements UpgradeWizardInterface
{
   
	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate FA6 free icons in pages:tx_t3sbootstrap_fontawesome_icon to use with EXT:iconpack & EXT:iconpack_fontawesome';
	}

	public function getDescription(): string
	{
		return 'Migrate fa-solid, fas, fa-brands, fab, fa-regular, far and fixed width, size, transform, decoration';
	}

	public function executeUpdate(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');

		// replace page_icon to use iconpack
		$fieldName = 'tx_t3sbootstrap_fontawesome_icon';
		$statements = $queryBuilder
				 ->select('uid', $fieldName)
				 ->from('pages')
				 ->where($queryBuilder->expr()->neq($fieldName,'""'))
				 ->executeQuery()
				 ->fetchAllAssociative();

		if (count($statements)) {
			foreach($statements as $key=>$statement) {
				
				$string = $statement[$fieldName];

				if (!empty($string)) {
					$erg = 	'fa6:';
				if ( str_contains($string, 'fa-solid') ) {
					$erg .= 'solid,';
				}
				if ( str_contains($string, 'fas') ) {
					$erg .= 'solid,';
				}
				if ( str_contains($string, 'fa-brands') ) {
					$erg .= 'brands,';
				}
				if ( str_contains($string, 'fab') ) {
					$erg .= 'brands,';
				}
				if ( str_contains($string, 'fa-regular') ) {
					$erg .= 'regular,';
				}
				if ( str_contains($string, 'far') ) {
					$erg .= 'regular,';
				}
				$fafw  = false;
				if ( str_contains($string, ' fa-fw ') ) {
					$string = str_replace(' fa-fw ', ' ', $string);
					$fafw  = true;	
				}
				$border  = false;
				if ( str_contains($string, ' fa-border ') ) {
					$string = str_replace(' fa-border ', ' ', $string);
					$border  = true;
				}
				$spin  = false;
				if ( str_contains($string, ' fa-spin ') ) {
					$string = str_replace(' fa-spin ', ' ', $string);
					$spin  = true;
				}
				$size = '';
				if ( str_contains($string, ' fa-xs ') ) {
					$string = str_replace(' fa-xs ', ' ', $string);
					$size  = 'xs';
				}
				if ( str_contains($string, ' fa-sm ') ) {
					$string = str_replace(' fa-sm ', ' ', $string);
					$size  = 'sm';
				}
				if ( str_contains($string, ' fa-lg ') ) {
					$string = str_replace(' fa-lg ', ' ', $string);
					$size  = 'lg';
				}
				if ( str_contains($string, ' fa-2x ') ) {
					$string = str_replace(' fa-2x ', ' ', $string);
					$size  = '2x';
				}
				if ( str_contains($string, ' fa-3x ') ) {
					$string = str_replace(' fa-3x ', ' ', $string);
					$size  = '3x';
				}
				if ( str_contains($string, ' fa-5x ') ) {
					$string = str_replace(' fa-5x ', ' ', $string);
					$size  = '5x';
				}
				if ( str_contains($string, ' fa-7x ') ) {
					$string = str_replace(' fa-7x ', ' ', $string);
					$size  = '7x';
				}
				if ( str_contains($string, ' fa-10x ') ) {
					$string = str_replace(' fa-10x ', ' ', $string);
					$size  = '10x';
				}


				if ( str_contains($string, ' fa-') ) {
					$erg .= substr(explode(' ',$string)[1], 3);
				}

				if ($size) {
					$erg .= ',size:'.$size;
				}

				if ($border) {
					$erg .= ',decoration:border';
				}

				if ($spin) {
					$erg .= ',transform:spin';
				}

				if ($fafw) {
					$erg .= ',fixed:true';
				}
					
				$queryBuilder
					->update('pages')
					->where(
						$queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($statement['uid'], Connection::PARAM_INT))
					)
					->set('page_icon', $erg)
					->set($fieldName, '')
					->executeStatement();
				}
			}
		}

		return true;
	}


	public function updateNecessary(): bool
	{
		$updateNeeded = false;

		if ( ExtensionManagementUtility::isLoaded('iconpack') ) {
			// Check if the database table even exists
			if ($this->checkIfTitleWizardIsRequired()) {
				return true;
			}
		}

		return $updateNeeded;
	}


	public function getPrerequisites(): array
	{
		return [];
	}


	protected function checkIfTitleWizardIsRequired(): bool
	{
		$required = false;
	
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('pages');

		$fieldName = 'tx_t3sbootstrap_fontawesome_icon';
		$numberOfPageicons = $queryBuilder
			 ->count('uid')
			 ->from('pages')
			 ->where($queryBuilder->expr()->neq($fieldName,'""'))
			 ->executeQuery()
			 ->fetchOne();

		if (!empty($numberOfPageicons)) {
			$required = true;
		}

		return $required;
	}

}
