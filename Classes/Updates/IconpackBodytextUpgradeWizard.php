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

#[UpgradeWizard('t3sbootstrap_iconpackBodytextUpgradeWizard')]
final class IconpackBodytextUpgradeWizard implements UpgradeWizardInterface
{
   
	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate FA7 free icons in tt_content:bodytext to use with EXT:iconpack & EXT:iconpack_fontawesome';
	}

	public function getDescription(): string
	{
		return 'Migrate fa-solid, fas, fa-brands, fab, fa-regular, far  and fixed width, size, transform, decoration';
	}

	public function executeUpdate(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->getRestrictions()->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class));

		// replace fa-icons in bodytext to use iconpack
		$fieldName = 'bodytext';
		$bodytextStatements = $queryBuilder
				->select('uid', $fieldName)
				->from('tt_content')
				->where($queryBuilder->expr()->neq($fieldName, $queryBuilder->createNamedParameter('')))
				->executeQuery()
				->fetchAllAssociative();

		foreach ($bodytextStatements as $statement) {
			$bodytext = (string)$statement[$fieldName];
			$migrated = $this->replaceFaIcons($bodytext);

			// Writing an unchanged value would mark the wizard as done although
			// nothing was migrated, so untouched records are skipped.
			if ($migrated === $bodytext) {
				continue;
			}

			$connectionPool->getConnectionForTable('tt_content')->update(
				'tt_content',
				[$fieldName => $migrated],
				['uid' => (int)$statement['uid']]
			);
		}

		return true;
	}


	public function updateNecessary(): bool
	{
		$updateNeeded = false;

		if ( ExtensionManagementUtility::isLoaded('iconpack') ) {
			// Check if the database table even exists
			if ($this->checkIfBodytextWizardIsRequired()) {
				return true;
			}
		}

		return $updateNeeded;
	}


	public function getPrerequisites(): array
	{
		return [];
	}


	protected function checkIfBodytextWizardIsRequired(): bool
	{
		$required = false;

		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
		$queryBuilder->getRestrictions()->removeAll()
			->add(GeneralUtility::makeInstance(DeletedRestriction::class));

		$fieldName = 'bodytext';
		$statements = $queryBuilder
				->select('uid', $fieldName)
				->from('tt_content')
				->where($queryBuilder->expr()->neq($fieldName, $queryBuilder->createNamedParameter('')))
				->executeQuery()
				->fetchAllAssociative();
		
		foreach ($statements as $statement) {
			$bodytext = (string)$statement[$fieldName];
			if ($this->replaceFaIcons($bodytext) !== $bodytext) {
				$required = true;
				break;
			}
		}

		return $required;
	}


	/**
	 * Turns every Font Awesome <i> tag into the iconpack <span>. Reads the class
	 * list itself: the previous version only matched tags carrying aria-hidden
	 * and took every second class for the size, whatever it was.
	 */
	public function replaceFaIcons(string $string): string
	{
		$replaced = preg_replace_callback(
			'#<i\s+class="([^"]*)"[^>]*>\s*</i>#i',
			static function (array $matches): string {
				$iconfig = IconpackClassParser::toIconfig($matches[1]);

				// no icon name in there (or not Font Awesome at all): keep the markup
				return $iconfig === '' ? $matches[0] : '<span data-iconfig="' . $iconfig . '"></span>';
			},
			$string
		);

		return $replaced ?? $string;
	}


}
