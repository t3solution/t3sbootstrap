<?php
namespace T3SBS\T3sbootstrap\Domain\Repository;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * The repository for Configs
 */
class ConfigRepository extends Repository
{
	public function initializeObject() {
		 $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
		 $querySettings->setRespectStoragePage(false);
		 $this->setDefaultQuerySettings($querySettings);
	}
}
