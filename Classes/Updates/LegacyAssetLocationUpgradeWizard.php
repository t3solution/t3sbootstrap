<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use T3SBS\T3sbootstrap\Service\LegacyAssetMigrationService;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[UpgradeWizard('t3sbootstrap_legacyAssetLocationUpgradeWizard')]
final class LegacyAssetLocationUpgradeWizard implements UpgradeWizardInterface
{
    public function getTitle(): string
    {
        return 'EXT:t3sbootstrap: Move generated assets out of EXT:t3sb_package';
    }

    public function getDescription(): string
    {
        $count = $this->getService()->countLegacyFiles();

        return sprintf(
            'Up to version 5.3.49 the downloaded CSS/JS, the bootstrap sources and the '
            . 'generated SCSS were written into EXT:t3sb_package/Resources/Public/, where a '
            . 'composer update replaces them. They now live in typo3temp/assets/t3sbootstrap/, '
            . 'which is rebuilt from the configuration record and from t3sbootstrap:cdnToLocal. '
            . 'This wizard copies the %d file(s) still present in the old location, so the '
            . 'frontend keeps working without running the command first. Nothing is deleted.',
            $count
        );
    }

    public function updateNecessary(): bool
    {
        return !$this->getService()->isDone();
    }

    public function executeUpdate(): bool
    {
        $this->getService()->migrate();

        return true;
    }

    /**
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }

    private function getService(): LegacyAssetMigrationService
    {
        return GeneralUtility::makeInstance(LegacyAssetMigrationService::class);
    }
}
