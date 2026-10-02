<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use T3SBS\T3sbootstrap\Service\AnchorService;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Fills the "Speaking ID" wherever it is empty. Does the same thing as opening every
 * element and saving it - the slug is generated on save when the field is empty - only
 * without going through hundreds of forms.
 *
 * Anchors that already carry a value stay untouched: whether one was typed by hand or
 * dragged along by a copy cannot be told apart from the data. The command
 * "t3sbootstrap:anchor --mode=rebuild" rebuilds those too, after a dry run.
 */
#[UpgradeWizard('t3sbootstrap_anchorUpgradeWizard')]
final class AnchorUpgradeWizard implements UpgradeWizardInterface
{
    public function __construct(private readonly AnchorService $anchorService) {}

    public function getTitle(): string
    {
        return 'EXT:t3sbootstrap: Generate the missing "Speaking ID" of content elements';
    }

    public function getDescription(): string
    {
        if (!$this->isEnabled()) {
            return 'The extension option "Speaking ID" is off - nothing to generate.';
        }

        $count = count($this->anchorService->collect(AnchorService::MODE_EMPTY));

        return 'The anchor of a content element (tx_t3sbootstrap_anchor) is generated from the'
            . ' header when the field is left empty on save. Elements that were never opened'
            . ' since the field appeared therefore have none, and the section menu links to'
            . ' nothing. This generates them - ' . $count . ' element(s) affected. Existing'
            . ' anchors are kept, so no link that works today breaks.';
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return $this->isEnabled() && $this->anchorService->collect(AnchorService::MODE_EMPTY) !== [];
    }

    public function executeUpdate(): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $this->anchorService->apply($this->anchorService->collect(AnchorService::MODE_EMPTY));

        return true;
    }

    /**
     * Without the option "Speaking ID" the wizard must stay quiet. It used to fill the
     * anchor of every element regardless, and the frontend renders a span for each one -
     * that is how anchors turn up on an installation where the option is off.
     */
    private function isEnabled(): bool
    {
        try {
            $extconf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('t3sbootstrap');
        } catch (\Throwable) {
            return false;
        }

        return !empty($extconf['speakingID']);
    }
}
