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
   
	/**
	 * The icon name is the first "fa-*" token that is not a style or a modifier.
	 *
	 * Returns '' when there is nothing recognisable in the value - such a record
	 * must neither be migrated (that produced a bare "fa7:") nor be reported as
	 * pending by updateNecessary() forever.
	 */
	private static function resolveIconName(string $value): string
	{
		foreach (preg_split('/\s+/', trim($value)) ?: [] as $token) {
			if (!str_starts_with($token, 'fa-')) {
				continue;
			}
			if (in_array($token, ['fa-solid', 'fa-brands', 'fa-regular', 'fa-light', 'fa-thin', 'fa-duotone', 'fa-sharp', 'fa-fw', 'fa-border', 'fa-spin', 'fa-pulse', 'fa-inverse'], true)) {
				continue;
			}
			if (preg_match('/^fa-(xs|sm|lg|\d+x)$/', $token)) {
				continue;
			}
			return substr($token, 3);
		}

		return '';
	}


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

				// pad, so the " fa-xx " probes below also match a modifier that
				// sits at the very start or the very end of the value
				$string = ' '.trim((string)$string).' ';
				$erg = 	'fa7:';
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


				$iconName = self::resolveIconName($string);

				if ($iconName === '') {
					// nothing recognisable in there - leave the record alone rather
					// than writing a bare "fa7:" and clearing the source field
					continue;
				}

				$erg .= $iconName;

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
			if (self::resolveIconName((string)$row) !== '') {
				$required = true;
				break;
			}
		}

		return $required;
	}


}