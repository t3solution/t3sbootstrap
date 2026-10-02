<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Moves asset directories older versions wrote into EXT:t3sb_package/Resources/Public/ over
 * to typo3temp/assets/t3sbootstrap/. Runs once, guarded by a marker file; upgrade wizard and
 * frontend middleware both call it, so an installation is migrated with or without the install tool.
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
     * Until 5.3.49 the three @import lines in bootstrap-<uid>.scss pointed at
     * EXT:t3sb_package/Resources/Public/ and were copied unchanged, so a replaced or missing
     * sitepackage makes scssphp throw a CompilerException in the PageRenderer hook, outside any
     * try/catch, and takes the frontend down. They are rewritten relative, as Command\CustomScss does.
     *
     * @return string[] absolute paths of the files with stale imports
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
