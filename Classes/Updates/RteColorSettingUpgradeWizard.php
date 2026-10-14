<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Renames the extension configuration "rteStyleColors" to "rteColor". A wizard and not a code
 * fallback, because an update writes every new ext_conf_template key with its default, so
 * "rteColor = 1" would sit next to the old value undecided. The old value wins until saved once.
 */
#[UpgradeWizard('t3sbootstrap_rteColorSettingUpgradeWizard')]
final class RteColorSettingUpgradeWizard implements UpgradeWizardInterface
{
    private const EXTENSION = 't3sbootstrap';
    private const LEGACY_KEY = 'rteStyleColors';
    private const NEW_KEY = 'rteColor';

    public function getTitle(): string
    {
        return 'EXT:t3sbootstrap: Rename the RTE setting "rteStyleColors" to "rteColor"';
    }

    public function getDescription(): string
    {
        return 'The text colours moved from the Styles dropdown into a toolbar dropdown of their own.'
            . ' This copies the stored value of "rteStyleColors" over to "rteColor" and removes the'
            . ' obsolete key, so only one setting is left. Without it the old value keeps being'
            . ' honoured until the extension configuration form is saved once.';
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return array_key_exists(self::LEGACY_KEY, $this->readConfiguration());
    }

    public function executeUpdate(): bool
    {
        $configuration = $this->readConfiguration();

        if (!array_key_exists(self::LEGACY_KEY, $configuration)) {
            return true;
        }

        $legacy = $configuration[self::LEGACY_KEY];
        unset($configuration[self::LEGACY_KEY]);

        // An empty legacy value means "never decided" - keep the new key's default
        // instead of overwriting it with ''.
        if ($legacy !== '') {
            $configuration[self::NEW_KEY] = $legacy;
        }

        GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->set(self::EXTENSION, $configuration);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function readConfiguration(): array
    {
        try {
            $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get(self::EXTENSION);
        } catch (\Throwable) {
            return [];
        }

        return is_array($configuration) ? $configuration : [];
    }
}
