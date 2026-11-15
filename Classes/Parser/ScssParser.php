<?php

declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Parser;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\Version;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class ScssParser extends AbstractParser
{
    /**
     * The bundled compiler this parser is written against. A 2.x compiler has a
     * different API - registerFunction() takes three arguments there, and custom
     * functions receive value objects instead of the old array form.
     */
    private const MAX_COMPILER_VERSION = '2.0.0';

    /**
     * Whether the loaded scssphp is one this parser can drive.
     */
    private bool $compilerIsUsable = true;

    /**
     * Logged once per request, not once per file.
     */
    private static bool $warned = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!class_exists('ScssPhp\ScssPhp\Version')) {
            require_once ExtensionManagementUtility::extPath('t3sbootstrap') . '/Contrib/scssphp/scss.inc.php';
        }

        // Another extension may have loaded its own scssphp through the Composer
        // autoloader before us. Only one copy of the class can exist in a process,
        // so the require_once above is then skipped and we would be driving a
        // foreign compiler with an API this parser does not speak.
        $this->compilerIsUsable = version_compare(Version::VERSION, self::MAX_COMPILER_VERSION, '<');
    }

    /**
     * @param string $extension
     * @return bool
     */
    public function supports(string $extension): bool
    {
        if ($extension !== 'scss') {
            return false;
        }

        if (!$this->compilerIsUsable) {
            $this->warnAboutForeignCompiler();
            return false;
        }

        return true;
    }

    /**
     * Stepping aside is the whole point: whoever brought that compiler along also
     * brought a parser for it, and that one keeps working.
     */
    private function warnAboutForeignCompiler(): void
    {
        if (self::$warned) {
            return;
        }

        self::$warned = true;
        GeneralUtility::makeInstance(LogManager::class)
            ->getLogger(__CLASS__)
            ->warning(
                'scssphp ' . Version::VERSION . ' was loaded by another extension. This parser needs '
                . 'a 1.x compiler and leaves scss compilation to that extension. Custom SCSS of '
                . 't3sbootstrap is unavailable while this is the case; the page falls back to the '
                . 'local bootstrap.min.css.'
            );
    }

    /**
     * @param string $file
     * @param array $settings
     * @return string
     */
    public function compile(string $file, array $settings): string
    {
        $cacheIdentifier = $this->getCacheIdentifier($file, $settings);
        $cacheFile = $this->getCacheFile($cacheIdentifier, $settings['cache']['tempDirectory']);
        $cacheFileMeta = $this->getCacheFileMeta($cacheFile);
        $compile = false;

        if (!$this->isCached($file, $settings)
            || $this->needsCompile($cacheFile, $cacheFileMeta, $settings)) {
            $compile = true;
        }

        if ($compile) {
            $result = $this->parseFile($file, $settings);
            // The path is handed to the PageRenderer in the same request, so a browser
            // could otherwise fetch a half written file. The meta file goes first:
            // it is what needsCompile() looks at.
            $this->writeAtomically(GeneralUtility::getFileAbsFileName($cacheFileMeta), serialize($result['cache']));
            $this->writeAtomically(GeneralUtility::getFileAbsFileName($cacheFile), $result['css']);
            $this->clearPageCaches();
        }

        return $cacheFile;
    }

    /**
     * Temporary file plus rename, so no request sees a partially written file.
     */
    protected function writeAtomically(string $fullPath, string $content): void
    {
        $temporaryPath = $fullPath . '.' . getmypid() . '.tmp';

        if (GeneralUtility::writeFile($temporaryPath, $content, true) === false) {
            return;
        }

        if (!@rename($temporaryPath, $fullPath)) {
            @unlink($temporaryPath);
        }
    }

    /**
     * @param string $file
     * @param array $settings
     * @return array
     */
    protected function parseFile(string $file, array $settings): array
    {
        $scss = new Compiler();
        $scss->setOutputStyle(OutputStyle::COMPRESSED);
        $scss->addVariables($settings['variables']);
        if ($settings['options']['sourceMap']) {
            $scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
            $scss->setSourceMapOptions([
                'sourceMapRootpath' => $settings['cache']['tempDirectoryRelativeToRoot'],
                'sourceMapBasepath' => '<PATH DOES NOT EXIST BUT SUPRESSES WARNINGS>'
            ]);
        }
        $absoluteFilename = $settings['file']['absolute'];
        // Adds the visual directory path of the initial file as import path. Needed when
        // packages are symlinked through Composer's `path` repository, because the PHP SCSS
        // parser works on resolved real paths and would lose the symlinked context.
        $visualImportPath = dirname($absoluteFilename);
        $scss->addImportPath(function ($url) use ($visualImportPath): ?string {
            // Resolve potential back paths manually using PathUtility::getCanonicalPath,
            // but make sure we do not break out of TYPO3 application path using GeneralUtility::getFileAbsFileName
            // Also resolve EXT: paths if given
            $isTypo3Absolute = (strpos($url, 'EXT:') === 0) || PathUtility::isAbsolutePath($url);
            $fileName = $isTypo3Absolute ? $url : $visualImportPath . '/' . $url;
            $full = GeneralUtility::getFileAbsFileName(PathUtility::getCanonicalPath($fileName));
            // The API forces us to check the existence of files paths, with or without url.
            // We must only return a string if the file to be imported actually exists.
            $hasExtension = (bool) preg_match('/[.]s?css$/', $url);
            if (
                is_file($file = $full . '.scss') ||
                ($hasExtension && is_file($file = $full))
            ) {
                // We could trigger a deprecation message here at some point
                return $file;
            }

            return null;
        });
        // Add extensions path to import paths, so that we can use paths relative to this directory to resolve imports
        $scss->addImportPath(Environment::getExtensionsPath());

        // Make paths in url() statements relative to site root
        $absoluteFilePath = dirname($absoluteFilename);
        $relativeFilePath = PathUtility::getAbsoluteWebPath($absoluteFilePath);

        $scss->registerFunction(
            'url',
            function ($args) use (
                $scss,
                $absoluteFilePath,
                $relativeFilePath
            ): string {
                $marker = $args[0][1];
                $args[0][1] = '';
                $result = $scss->compileValue($args[0]);
                if (substr_compare($result, 'data:', 0, 5, true) !== 0) {
                    // EXT: is resolved for @import but was not for url(), so fonts and
                    // images of another extension could not be reached from scss at all.
                    // In Composer installations this becomes an /_assets/<hash>/ path.
                    if (str_starts_with($result, 'EXT:')) {
                        $absoluteTarget = GeneralUtility::getFileAbsFileName($result);
                        if ($absoluteTarget !== '' && is_file($absoluteTarget)) {
                            $result = PathUtility::getAbsoluteWebPath($absoluteTarget);
                        }
                    } elseif (is_file(PathUtility::getCanonicalPath($absoluteFilePath . '/' . $result))) {
                        $result = PathUtility::getCanonicalPath($relativeFilePath . '/' . $result);
                    }
                    $result = str_starts_with($result, '/') ? substr($result, 1) : $result;
                }
                return 'url(' . $marker . $result . $marker . ')';
            }
        );

        // Compile file
        $compilationResult = $scss->compileString('@import "' . $absoluteFilename . '"');
        $css = $compilationResult->getCss();

        // Fix paths in url() statements to be relative to temp directory
        $relativeTempPath = $settings['cache']['tempDirectoryRelativeToRoot'];
        $search = '%url\s*\(\s*[\\\'"]?(?!(((?:https?:)?\/\/)|(?:data:?:)))([^\\\'")]+)[\\\'"]?\s*\)%';
        $replace = 'url("' . $relativeTempPath . '$3")';
        $css = (string) preg_replace($search, $replace, $css);

        return [
            'css' => $css,
            'cache' => [
                'version' => Version::VERSION,
                'date' => date('r'),
                'css' => $css,
                'etag' => md5($css),
                'files' => $compilationResult->getIncludedFiles(),
                'variables' => $settings['variables'],
                'sourceMap' => $settings['options']['sourceMap']
            ]
        ];
    }
}
