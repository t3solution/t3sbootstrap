<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Moves the asset directories that older versions wrote into
 * EXT:t3sb_package/Resources/Public/ over to typo3temp/assets/t3sbootstrap/.
 *
 * Only the downloaded files really need this. The TypoScript and the scss are
 * derived from the configuration record and are rewritten by
 * Backend\Hooks\OutsourcedFiles anyway - but copying them along costs nothing
 * and keeps the first request after an update free of surprises.
 *
 * Runs at most once, guarded by a marker file. Both the upgrade wizard and the
 * frontend middleware call it, so an installation is migrated whether the
 * integrator opens the install tool or not.
 */
final class LegacyAssetMigrationService implements SingletonInterface
{
    private const LEGACY_BASE = 'EXT:t3sb_package/Resources/Public/';

    private const DIRECTORIES = [
        'T3SB-CSS',
        'T3SB-JS',
        'T3SB-SCSS',
        'T3SB-Bootstrap',
    ];

    // -2, weil die erste Fassung die Include-Dateien zwar kopiert, ihre
    // @import-Zeilen aber nicht umgeschrieben hat. Installationen, die schon
    // migriert wurden, muessen deshalb noch einmal durchlaufen.
    private const MARKER = '.legacy-migrated-2';

    public function __construct(
        private readonly AssetPathService $assetPathService,
    ) {}

    /**
     * True when there is nothing left to do.
     */
    public function isDone(): bool
    {
        if (is_file($this->assetPathService->getPath() . self::MARKER)) {
            return true;
        }

        // Die Include-Datei muss auch dann repariert werden, wenn in
        // EXT:t3sb_package nichts mehr liegt - genau das ist der Normalfall,
        // nachdem ein composer update das Sitepackage ersetzt hat.
        return $this->countLegacyFiles() === 0 && $this->findStaleIncludeFiles() === [];
    }

    /**
     * Copies whatever is still sitting in the old location and writes the
     * marker. Never overwrites a file that already exists in the new place -
     * the new location always wins.
     *
     * @return int number of files copied
     */
    public function migrate(): int
    {
        $copied = 0;

        foreach (self::DIRECTORIES as $directory) {
            $source = GeneralUtility::getFileAbsFileName(self::LEGACY_BASE . $directory);

            if ($source === '' || !is_dir($source)) {
                continue;
            }

            $copied += $this->copyTree($source, $this->assetPathService->getPath($directory));
        }

        $copied += $this->repairIncludeFiles();

        GeneralUtility::writeFile(
            $this->assetPathService->getPath() . self::MARKER,
            "Assets migrated from EXT:t3sb_package. Safe to delete - it only prevents a repeated scan.\n",
            true
        );

        return $copied;
    }

    /**
     * bootstrap-<uid>.scss enthaelt nur drei @import-Zeilen, und bis 5.3.49
     * zeigten sie auf EXT:t3sb_package/Resources/Public/. Kopiert wurde die
     * Datei unveraendert - sie liegt also am neuen Ort und importiert vom
     * alten. Ersetzt ein composer update das Sitepackage, oder ist es gar nicht
     * geladen, bricht scssphp mit einer CompilerException ab und nimmt das
     * ganze Frontend mit (der Compile laeuft im PageRenderer-Hook, ausserhalb
     * jedes try/catch).
     *
     * Die Zeilen werden deshalb auf dieselbe relative Form gebracht, die
     * Command\CustomScss heute schreibt - relativ, damit kein absoluter
     * Serverpfad in der Datei einbetoniert wird.
     *
     * @return string[] absolute Pfade der Dateien mit veralteten Importen
     */
    private function findStaleIncludeFiles(): array
    {
        $path = $this->assetPathService->getPath('T3SB-SCSS/Bootstrap');
        $stale = [];

        foreach ((array)glob($path . 'bootstrap-*.scss') as $file) {
            if (!is_string($file) || !is_file($file)) {
                continue;
            }

            $content = (string)file_get_contents($file);

            if (str_contains($content, self::LEGACY_BASE)) {
                $stale[] = $file;
            }
        }

        return $stale;
    }

    /**
     * @return int Anzahl der reparierten Dateien
     */
    private function repairIncludeFiles(): int
    {
        $repaired = 0;

        foreach ($this->findStaleIncludeFiles() as $file) {
            // bootstrap-17.scss -> 17
            $rootPageId = (int)substr(pathinfo($file, PATHINFO_FILENAME), strlen('bootstrap-'));

            if ($rootPageId <= 0) {
                continue;
            }

            $content = '
@import "../custom-variables-' . $rootPageId . '";
@import "../../T3SB-Bootstrap/Bootstrap/scss/bootstrap";
@import "../custom-' . $rootPageId . '";
            ';

            if (GeneralUtility::writeFile($file, $content)) {
                $repaired++;
            }
        }

        return $repaired;
    }

    public function countLegacyFiles(): int
    {
        $count = 0;

        foreach (self::DIRECTORIES as $directory) {
            $source = GeneralUtility::getFileAbsFileName(self::LEGACY_BASE . $directory);

            if ($source === '' || !is_dir($source)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function copyTree(string $source, string $target): int
    {
        $copied = 0;
        $source = rtrim($source, '/');
        $target = rtrim($target, '/');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $destination = $target . '/' . $iterator->getSubPathname();

            if ($item->isDir()) {
                GeneralUtility::mkdir_deep($destination);
                continue;
            }

            if (is_file($destination)) {
                continue;
            }

            GeneralUtility::mkdir_deep(dirname($destination));

            if (copy($item->getPathname(), $destination)) {
                $copied++;
            }
        }

        return $copied;
    }
}
