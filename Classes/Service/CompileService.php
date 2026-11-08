<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Service;

use T3SBS\T3sbootstrap\Parser\ParserInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;

class CompileService
{
    public function __construct(
        private readonly PackageManager $packageManager,
        private readonly AssetPathService $assetPathService,
    ) {}

    /**
     * @throws \Exception
     */
    public function getCompiledFile(ServerRequestInterface $request, string $file): ?string
    {
        $absoluteFile = $this->resolveAbsolutePath($file);

        // The css lives below the configured asset directory, which may have been
        // moved out of typo3temp/ - getPath() creates it when it is not there yet.
        $this->assetPathService->getPath('css');

        // Settings
        $settings = [
            'file' => [
                'absolute' => $absoluteFile,
                'relative' => $file,
                'info' => pathinfo($absoluteFile)
            ],
            'cache' => [
                'tempDirectory' => $this->assetPathService->getCssRelPath(),
                'tempDirectoryRelativeToRoot' => $this->assetPathService->getCssDepthToRoot(),
            ],
            'options' => [
                'override' => false,
                'sourceMap' => false,
                'compress' => true
            ],
            'variables' => []
        ];

        // Parser
        // @extensionScannerIgnoreLine
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser']) && is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'])
        ) {
            // @extensionScannerIgnoreLine
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/t3sbootstrap/css']['parser'] as $className) {
                $parser = GeneralUtility::makeInstance($className);
                if ($parser instanceof ParserInterface
                    && !empty($settings['file']['info']['extension'])
                    && $parser->supports($settings['file']['info']['extension'])
                ) {
                    // A compile error is passed on to the caller, which keeps the page
                    // alive - see Hooks\PageRenderer\PreProcessHook::execute().
                    return $parser->compile($file, $settings);
                }
            }
        }

        return null;
    }


    /**
     * Resolves a PageRenderer file reference to an absolute file system path. Since TYPO3 v14 the
     * cssFiles/cssLibs arrays are keyed by system resource identifiers, which getFileAbsFileName()
     * no longer accepts: "PKG:vendor/package:path" for a path below the public directory, and
     * "FAL:<storage>:<identifier>" once the path is inside a file storage - which is the case as
     * soon as the asset directory is configured below fileadmin/. Both are resolved here.
     */
    protected function resolveAbsolutePath(string $file): string
    {
        if (str_starts_with($file, 'FAL:')) {
            return $this->resolveFalPath($file);
        }

        if (!str_starts_with($file, 'PKG:')) {
            return GeneralUtility::getFileAbsFileName($file);
        }

        $parts = explode(':', $file, 3);
        if (count($parts) !== 3 || $parts[1] === '' || $parts[2] === '') {
            return '';
        }
        [, $composerName, $relativePath] = $parts;

        // The root package ("typo3/app" by convention) is not necessarily
        // registered under its composer name, so resolve it directly.
        if ($composerName === 'typo3/app') {
            return rtrim(Environment::getProjectPath(), '/') . '/' . ltrim($relativePath, '/');
        }

        try {
            return $this->packageManager->getPackage($composerName)->getPackagePath() . $relativePath;
        } catch (\Throwable) {
            // Last resort: let the core resolve it. This still emits the v14
            // deprecation notice, but keeps the file reference working.
            return GeneralUtility::getFileAbsFileName($file);
        }
    }


    /**
     * "FAL:<storage>:<identifier>" to an absolute path. getForLocalProcessing() returns the file
     * itself for a local driver; a remote driver copies it to a temporary file, which is still
     * good enough for the compiler.
     */
    protected function resolveFalPath(string $file): string
    {
        $parts = explode(':', $file, 3);
        if (count($parts) !== 3 || (int)$parts[1] <= 0 || $parts[2] === '') {
            return '';
        }

        try {
            $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid((int)$parts[1]);

            return $storage?->getFile($parts[2])?->getForLocalProcessing(false) ?? '';
        } catch (\Throwable) {
            // A missing storage or file must not end the request - the caller drops the
            // reference and logs it, see Hooks\PageRenderer\PreProcessHook::execute().
            return '';
        }
    }

}
