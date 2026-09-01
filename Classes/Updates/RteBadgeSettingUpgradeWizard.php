<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Benennt die Extension-Konfiguration "rteStyleBadges" in "rteBadge" um.
 *
 * Badges waren 16 Eintraege im Styles-Dropdown und wurden ueber rteStyleBadges
 * ein- und ausgeschaltet. Seit dieser Version sind sie ein eigenes
 * Toolbar-Dropdown, die Einstellung heisst rteBadge.
 *
 * Warum das ein Wizard ist und nicht einfach ein Rueckfall im Code: TYPO3 traegt
 * beim Update jeden neuen Schluessel aus ext_conf_template mit seinem
 * Vorgabewert in die gespeicherte Konfiguration ein. "rteBadge = 1" steht danach
 * also da, ohne dass es jemand entschieden haette, und der alte Wert liegt
 * daneben. FeatureToggles loest das, indem der alte Schluessel gewinnt, solange
 * er existiert - und dieser Wizard raeumt genau das auf, sodass am Ende nur noch
 * ein Schluessel uebrig ist.
 *
 * Der Wizard ist nicht zwingend: ohne ihn gilt weiter der alte Wert, bis das
 * Formular der Extension-Konfiguration einmal gespeichert wird.
 */
#[UpgradeWizard('t3sbootstrap_rteBadgeSettingUpgradeWizard')]
final class RteBadgeSettingUpgradeWizard implements UpgradeWizardInterface
{
    private const EXTENSION = 't3sbootstrap';
    private const LEGACY_KEY = 'rteStyleBadges';
    private const NEW_KEY = 'rteBadge';

    public function getTitle(): string
    {
        return 'EXT:t3sbootstrap: Rename the RTE setting "rteStyleBadges" to "rteBadge"';
    }

    public function getDescription(): string
    {
        return 'Badges moved from the Styles dropdown into a toolbar dropdown of their own.'
            . ' This copies the stored value of "rteStyleBadges" over to "rteBadge" and removes the'
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

        // Ein leerer Altwert heisst "nie entschieden" - dann bleibt der Vorgabewert
        // des neuen Schluessels stehen, statt ihn mit '' zu ueberschreiben.
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
