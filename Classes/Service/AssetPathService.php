<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * One place for everything this extension writes below typo3temp/assets/t3sbootstrap/
 * (TypoScript, T3SB-SCSS, T3SB-CSS, T3SB-JS, T3SB-Bootstrap, css). All of it is cache and
 * comes back on its own when TYPO3 wipes it - only the downloads need t3sbootstrap:cdnToLocal.
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
