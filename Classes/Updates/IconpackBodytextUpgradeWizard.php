<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

#[UpgradeWizard('t3sbootstrap_iconpackBodytextUpgradeWizard')]
final class IconpackBodytextUpgradeWizard implements UpgradeWizardInterface
{
   
	public function getTitle(): string
	{
		return 'EXT:t3sbootstrap: Migrate FA6 free icons in tt_content:bodytext to use with EXT:iconpack & EXT:iconpack_fontawesome';
	}

	public function getDescription(): string
	{
		return 'Migrate fa-solid, fas, fa-brands, fab, fa-regular, far  and fixed width, size, transform, decoration';
	}

	public function executeUpdate(): bool
	{
		$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
		$queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');

		// replace fa-icons in bodytext to use iconpack
		$fieldName = 'bodytext';
		$bodytextStatements = $queryBuilder
				->select('uid', $fieldName)
				->from('tt_content')
				->where($queryBuilder->expr()->neq($fieldName,'""'))
				->executeQuery()
				->fetchAllAssociative();

		if (count($bodytextStatements)) {		
			foreach($bodytextStatements as $statement) {
			
				if (str_contains($statement[$fieldName], '<i class="fa')) {
					$queryBuilder
					    ->update('tt_content')
					    ->where(
					        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($statement['uid'], Connection::PARAM_INT))
					    )
					    ->set($fieldName, self::replaceFaIcons($statement[$fieldName]))
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

		$fieldName = 'bodytext';
		$statements = $queryBuilder
				->select('uid', $fieldName)
				->from('tt_content')
				->where($queryBuilder->expr()->neq($fieldName,'""'))
				->executeQuery()
				->fetchAllAssociative();
		
		foreach($statements as $statement) {	
			if (str_contains($statement[$fieldName], '<i class="fa')) {
				$required = true;
				break;
			}
		}

		return $required;
	}


	function replaceFaIcons(string $string): string
	{
		$stringreplace = str_replace(' aria-hidden="true"', '', $string);
		
		$contentRegularArr = explode('<i class="fa-regular ', $stringreplace);
		$contentSolidArr = explode('<i class="fa-solid ', $stringreplace);
		$contentBrandArr = explode('<i class="fa-brands ', $stringreplace);

		$contentRArr = explode('<i class="far ', $stringreplace);
		$contentSArr = explode('<i class="fas ', $stringreplace);
		$contentBArr = explode('<i class="fab ', $stringreplace);

		$faArr['regular'] = self::replaceArr($contentRegularArr, 'regular', 'fa-regular');
		$faArr['solid'] = self::replaceArr($contentSolidArr, 'solid', 'fa-solid');
		$faArr['brands'] = self::replaceArr($contentBrandArr, 'brands', 'fa-brands');

		$faArr['far'] = self::replaceArr($contentRArr, 'regular', 'far');
		$faArr['fas'] = self::replaceArr($contentSArr, 'solid', 'fas');
		$faArr['fab'] = self::replaceArr($contentBArr, 'brands', 'fab');

		foreach ( $faArr as $type=>$replaceArr ) {
			foreach ( $replaceArr as $replace ) {
				// replace
				$string = str_replace($replace[1], $replace[0], $string);	
			}
		}
			
	    return $string;
	}


	function replaceArr(array $iArr, string $type, string $toReplace): array
	{
		$icons = [];
		foreach ($iArr as $key=>$content) {
			if (str_starts_with($content, 'fa')) {
				$icons[$key][0] = '<span data-iconfig="fa6:'.$type.',';
				$icons[$key][1] = '<i class="'.$toReplace.' fa-';
				$brand = substr(explode('"', $content)[0], 3);
				if (str_contains($brand, ' ')) {
					foreach ( explode(' ', $brand) as $k=>$s ) {
						if ($k === 0) {
							$icons[$key][0] .= $s;			
							$icons[$key][1] .= $s;
						}
						if ($k === 1) {
							$icons[$key][0] .= ',size:'.substr($s, 3);			
							$icons[$key][1] .= ' fa-'.substr($s, 3);
						}
						if ($k === 2) {
							$icons[$key][0] .= ',fixed:true';			
							$icons[$key][1] .= ' '.$s;
						}
						if ($k === 3) {
							$icons[$key][0] .= ',decoration:border';			
							$icons[$key][1] .= ' fa-border';
						}
						if ($k === 4) {
							$icons[$key][0] .= ',transform:spin';			
							$icons[$key][1] .= ' fa-spin';
						}
					}
				} else {
					$icons[$key][0] .= $brand;
					$icons[$key][1] .= $brand;
				}
				$icons[$key][0] .= '"></span>';
				$icons[$key][1] .= '" aria-hidden="true"></i>';
			}
		}
		return $icons;
	}


}
