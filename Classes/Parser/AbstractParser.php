<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Parser;

use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @param string $extension
     * @return bool
     */
    public function supports(string $extension): bool
    {
        return false;
    }

    /**
     * @param string $file
     * @param array $settings
     * @return string
     */
    public function compile(string $file, array $settings): string
    {
        return $file;
    }

    /**
     * @param string $file
     * @param array $settings
     * @return bool
     */
    protected function isCached(string $file, array $settings): bool
    {
        $cacheIdentifier = $this->getCacheIdentifier($file, $settings);
        $cacheFile = $this->getCacheFile($cacheIdentifier, $settings['cache']['tempDirectory']);
        $cacheFileMeta = $this->getCacheFileMeta($cacheFile);

        return file_exists($cacheFile) && file_exists($cacheFileMeta);
    }

    /**
     * @param string $cacheFile
     * @param string $cacheFileMeta
     * @param array $settings
     * @return bool
     */
    protected function needsCompile(string $cacheFile, string $cacheFileMeta, array $settings): bool
    {
        $needCompilation = false;
        $fileModificationTime = filemtime($cacheFile);
        $metadata = unserialize((string) @file_get_contents($cacheFileMeta), ['allowed_classes' => false]);

        if (!is_array($metadata) || !is_array($metadata['files'] ?? null)) {
            // unreadable or outdated metadata - compile again
            return true;
        }

        foreach ($metadata['files'] as $file) {
            // A recorded import can be gone: the file was renamed, the asset
            // directory was wiped or an update moved it elsewhere. filemtime()
            // would raise a warning that TYPO3's error handler turns into an
            // exception and takes the whole frontend down. A missing dependency
            // simply means the cached css is stale.
            if (!is_file($file)) {
                return true;
            }

            if (filemtime($file) > $fileModificationTime) {
                $needCompilation = true;
                break;
            }
        }

        if (!$needCompilation && $settings['variables'] !== $metadata['variables']) {
            $needCompilation = true;
        }

        if (!$needCompilation && $settings['options']['sourceMap'] !== $metadata['sourceMap']) {
            $needCompilation = true;
        }

        return $needCompilation;
    }

    /**
     * @param string $cacheIdentifier
     * @param string $tempDirectory
     * @return string
     */
    protected function getCacheFile(string $cacheIdentifier, string $tempDirectory): string
    {
        return $tempDirectory . $cacheIdentifier . '.css';
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getCacheFileMeta(string $filename)
    {
        return $filename . '.meta';
    }

    /**
     * @param string $file
     * @param array $settings
     * @return string
     */
    protected function getCacheIdentifier(string $file, array $settings): string
    {
        $filehash = md5($file);
        $hash = hash('sha256', $filehash . serialize($settings));
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return basename($file, '.' . $extension) . '-' . $hash;
    }

    /**
     * @return string
     */
    protected function getPathSite(): string
    {
        return Environment::getPublicPath() . '/';
    }

    /**
     * Clear all page caches
     * @throws NoSuchCacheGroupException
     */
    protected function clearPageCaches(): void
    {
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
    }
}
