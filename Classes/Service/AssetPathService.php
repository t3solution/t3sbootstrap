<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * One place for everything this extension writes.
 *
 *   typo3temp/assets/t3sbootstrap/
 *       TypoScript/   t3sbconstants.typoscript, t3sbsetup.typoscript
 *       T3SB-SCSS/    custom-variables-<uid>.scss, custom-<uid>.scss
 *       T3SB-CSS/     downloaded css
 *       T3SB-JS/      downloaded js
 *       T3SB-Bootstrap/ bootstrap sources from the release zip
 *       css/          compiled css (CompileService)
 *
 * Everything below this directory is a cache, not a source:
 *
 * - the TypoScript and the scss are derived from the configuration record in
 *   tx_t3sbootstrap_domain_model_config (fields custom_scss,
 *   custom_variables_scss and the rest of the model). The database is the single
 *   source of truth, the files exist only because the scss compiler and the
 *   TypoScript parser read files. GeneratedFilesService rewrites them from the
 *   record whenever they are missing - no network needed.
 * - the compiled css is derived from those files by CompileService.
 * - the downloaded assets are reproducible with t3sbootstrap:cdnToLocal.
 *
 * That is why typo3temp/assets/ is the correct location and why the extension
 * needs neither a site package it can write into, nor a directory in the
 * editorial file storage. Anything TYPO3 wipes here comes back on its own,
 * except the downloads, which need one command run.
 */
final class AssetPathService implements SingletonInterface
{
    /**
     * Asset root relative to the public path.
     */
    public const BASE_REL = 'typo3temp/assets/t3sbootstrap/';

    /**
     * Absolute path of a directory below the asset root, with trailing slash.
     * Created on demand - typo3temp/ is regularly emptied.
     */
    public function getPath(string $subPath = ''): string
    {
        $path = Environment::getPublicPath() . '/' . $this->getRelPath($subPath);
        GeneralUtility::mkdir_deep($path);

        return $path;
    }

    /**
     * Same location relative to the public path, with trailing slash. This is
     * what belongs into TypoScript and into Fluid asset references.
     */
    public function getRelPath(string $subPath = ''): string
    {
        $subPath = trim($subPath, '/');

        return self::BASE_REL . ($subPath === '' ? '' : $subPath . '/');
    }

    /**
     * Directory holding the generated TypoScript.
     */
    public function getTypoScriptPath(): string
    {
        return $this->getPath('TypoScript');
    }

    /**
     * Directory holding the scss written from the configuration record.
     */
    public function getScssPath(): string
    {
        return $this->getPath('T3SB-SCSS');
    }

    /**
     * Absolute path of a single file, or null when it does not exist.
     * Creates nothing.
     */
    public function resolveFile(string $subPath): ?string
    {
        $file = Environment::getPublicPath() . '/' . self::BASE_REL . ltrim($subPath, '/');

        return is_file($file) ? $file : null;
    }
}
