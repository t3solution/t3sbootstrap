<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Updates;

use T3SBS\T3sbootstrap\Backend\Hooks\OutsourcedFiles;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Moves path, width, height and alt text of the brand logo from the site settings into the
 * configuration record; the site settings are only the fallback. Convenience, not a
 * prerequisite - only unset record values are taken over, unresolvable sites are skipped.
 */
#[UpgradeWizard('t3sbootstrap_navbarBrandImageUpgradeWizard')]
final class NavbarBrandImageUpgradeWizard implements UpgradeWizardInterface
{
    private const TABLE = 'tx_t3sbootstrap_domain_model_config';

    public function getTitle(): string
    {
        return 'EXT:t3sbootstrap: Move the navbar brand image settings into the configuration record';
    }

    public function getDescription(): string
    {
        return 'Copies settings.bootstrap.navbar.image.defaultPath/width/height/altText from each site'
            . ' configuration into the fields navbar_image, navbar_image_width, navbar_image_height and'
            . ' navbar_image_alt of the matching configuration record. Records that already carry a value'
            . ' are left alone. Without this wizard nothing breaks - the site settings keep working as a'
            . ' fallback.';
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return $this->collectUpdates() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE);
        $outsourcedFiles = GeneralUtility::makeInstance(OutsourcedFiles::class);
        $rootPageIds = [];

        foreach ($this->collectUpdates() as $uid => $update) {
            $connection->update(self::TABLE, $update['values'], ['uid' => $uid]);
            $rootPageIds[$update['pid']] = $update['pid'];
        }

        // The write bypasses DataHandler and middleware, so the derived TypoScript files
        // have to be rewritten by hand here. Otherwise the new values sit in the database
        // while the frontend stays unchanged and the wizard is silently ineffective.
        foreach ($rootPageIds as $rootPageId) {
            $outsourcedFiles->rewriteFiles($rootPageId);
        }

        return true;
    }

    /**
     * Ermittelt je Datensatz die Felder, die aus der Site uebernommen werden
     * muessen. Ein leeres Ergebnis heisst: nichts zu tun.
     *
     * @return array<int, array{pid: int, values: array<string, int|string>}>
     */
    private function collectUpdates(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $records = $queryBuilder
            ->select('uid', 'pid', 'navbar_image', 'navbar_image_width', 'navbar_image_height', 'navbar_image_alt')
            ->from(self::TABLE)
            ->executeQuery()
            ->fetchAllAssociative();

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $updates = [];

        foreach ($records as $record) {
            try {
                $site = $siteFinder->getSiteByPageId((int)$record['pid']);
            } catch (SiteNotFoundException) {
                // Ein Datensatz ohne aufloesbare Site kann nichts erben.
                continue;
            }

            $image = $site->getConfiguration()['settings']['bootstrap']['navbar']['image'] ?? [];
            $values = [];

            // Only take over what the site really carries and what is still unset in the record
            // - a wizard must not overwrite a decision the editor already made. The path comes
            // along too, so the setting stops being spread over record and site settings.
            if ((string)$record['navbar_image'] === '' && (string)($image['defaultPath'] ?? '') !== '') {
                $values['navbar_image'] = (string)$image['defaultPath'];
            }
            if ((int)$record['navbar_image_width'] === 0 && (int)($image['width'] ?? 0) > 0) {
                $values['navbar_image_width'] = (int)$image['width'];
            }
            if ((int)$record['navbar_image_height'] === 0 && (int)($image['height'] ?? 0) > 0) {
                $values['navbar_image_height'] = (int)$image['height'];
            }
            if ((string)$record['navbar_image_alt'] === '' && (string)($image['altText'] ?? '') !== '') {
                $values['navbar_image_alt'] = (string)$image['altText'];
            }

            if ($values !== []) {
                $updates[(int)$record['uid']] = [
                    'pid' => (int)$record['pid'],
                    'values' => $values,
                ];
            }
        }

        return $updates;
    }
}
