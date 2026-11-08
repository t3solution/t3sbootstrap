<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * One place for everything this extension writes below its asset directory
 * (TypoScript, T3SB-SCSS, T3SB-CSS, T3SB-JS, T3SB-Bootstrap, css).
 *
 * The default is typo3temp/assets/t3sbootstrap/. Two kinds of files live there,
 * and they have opposite lifetimes: TypoScript, scss and the compiled css are
 * derived from the configuration record and come back on their own, while the
 * cdn assets and the Bootstrap sources are downloads and need network. Since
 * the core declares typo3temp/ disposable, a host that empties it on automatic
 * updates takes the downloads with it.
 *
 * The extension configuration "assetDir" therefore moves the whole directory
 * somewhere persistent, e.g. fileadmin/t3sb-assets/. Everything stays together,
 * so the relative @import paths between T3SB-SCSS and T3SB-Bootstrap keep working.
 */
final class AssetPathService implements SingletonInterface
{
    /**
     * Asset root relative to the public path, used when "assetDir" is unset or
     * does not hold a usable path.
     */
    public const BASE_REL = 'typo3temp/assets/t3sbootstrap/';

    /**
     * Resolved once per request - the setting cannot change in between.
     */
    private ?string $baseRel = null;

    /**
     * The configured asset root, relative to the public path, with a trailing slash.
     */
    public function getBaseRel(): string
    {
        if ($this->baseRel !== null) {
            return $this->baseRel;
        }

        return $this->baseRel = $this->resolveBaseRel();
    }

    /**
     * Absolute path of a directory below the asset root, with trailing slash.
     * Created on demand - the directory is regularly emptied, and a configured
     * one may not exist yet at all.
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

        return $this->getBaseRel() . ($subPath === '' ? '' : $subPath . '/');
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
     * Directory holding the compiled css, relative to the public path.
     */
    public function getCssRelPath(): string
    {
        return $this->getRelPath('css');
    }

    /**
     * How far the compiled css sits below the public directory, as "../" steps.
     * The scss compiler needs it to rewrite relative urls, and it changes with
     * the configured asset directory - so it is counted, not hard coded.
     */
    public function getCssDepthToRoot(): string
    {
        return str_repeat('../', substr_count(trim($this->getCssRelPath(), '/'), '/') + 1);
    }

    /**
     * Absolute path of a single file, or null when it does not exist.
     * Creates nothing.
     */
    public function resolveFile(string $subPath): ?string
    {
        $file = Environment::getPublicPath() . '/' . $this->getBaseRel() . ltrim($subPath, '/');

        return is_file($file) ? $file : null;
    }

    /**
     * Reads "assetDir" and keeps it only when it is a relative path below the
     * public directory. Anything else - absolute paths, a scheme, a step up
     * with ".." - falls back to the default instead of failing, because this
     * value decides where every asset of the extension is looked for.
     */
    private function resolveBaseRel(): string
    {
        try {
            $configured = (string)(GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('t3sbootstrap')['assetDir'] ?? '');
        } catch (\Throwable) {
            return self::BASE_REL;
        }

        $configured = trim(str_replace('\\', '/', $configured));
        $configured = trim($configured, '/');

        if ($configured === ''
            || str_contains($configured, '..')
            || str_contains($configured, ':')
            || str_starts_with($configured, '.')
        ) {
            return self::BASE_REL;
        }

        return $configured . '/';
    }
}
