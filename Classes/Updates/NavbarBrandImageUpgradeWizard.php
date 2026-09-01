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
 * Holt Pfad, Breite, Hoehe und Alt-Text des Brand-Logos aus den Site-Settings in
 * den Konfigurations-Datensatz.
 *
 * Bisher lagen bootstrap.navbar.image.width/height/altText ausschliesslich in
 * der Site-Konfiguration. Damit galt pro Site genau ein Mass - auch dann, wenn
 * ein Siteroot ueber navbar_image ein ganz anderes Logo gesetzt hatte. Seit
 * dieser Version stehen die drei Werte im Datensatz neben dem Pfad, die
 * Site-Settings sind nur noch der Rueckfall.
 *
 * Der Pfad wandert mit, obwohl es navbar_image schon vorher gab: solange er nur
 * in der Site steht, bleibt das Logo ueber zwei Orte verteilt und die
 * Site-Settings lassen sich nicht abraeumen.
 *
 * Der Wizard ist deshalb reine Bequemlichkeit, keine Voraussetzung: ohne ihn
 * bleiben die Datensatzfelder leer und der ConfigProcessor greift weiterhin auf
 * die Site-Settings zurueck - die Ausgabe aendert sich nicht.
 *
 * Uebernommen wird nur, was der Editor noch nicht selbst gesetzt hat, und nur
 * dort, wo die Site tatsaechlich einen Wert traegt. Ein Datensatz, dessen Site
 * nicht aufloesbar ist, wird uebersprungen statt zu scheitern.
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

        // Der Schreibvorgang laeuft an DataHandler und Middleware vorbei, also
        // muessen die abgeleiteten TypoScript-Dateien hier von Hand neu
        // geschrieben werden. Sonst stuenden die neuen Werte zwar in der
        // Datenbank, im Frontend passierte aber nichts - der Wizard waere still
        // wirkungslos.
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

            // Nur uebernehmen, was die Site wirklich traegt und was im Datensatz
            // noch nicht gesetzt ist - eine bereits getroffene Entscheidung des
            // Editors darf ein Wizard nicht ueberschreiben.
            //
            // Der Pfad ist mitgenommen, obwohl navbar_image das Feld schon vorher
            // gab: solange der Wert nur in der Site steht, bleibt die Einstellung
            // ueber zwei Orte verteilt und die Site-Settings lassen sich nie
            // abraeumen. Die Rangfolge ist ohnehin dieselbe, die Ausgabe aendert
            // sich durch das Umziehen also nicht.
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
