<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class ConfigRepository extends Repository
{
	public function initializeObject(): void {
		/** @var Typo3QuerySettings $querySettings */
		$querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
		$querySettings->setRespectStoragePage(false);
		$this->setDefaultQuerySettings($querySettings);
	}
}
