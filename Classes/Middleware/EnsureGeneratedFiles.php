<?php
declare(strict_types=1);

namespace T3SBS\T3sbootstrap\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use T3SBS\T3sbootstrap\Backend\Hooks\OutsourcedFiles;
use T3SBS\T3sbootstrap\Service\LegacyAssetMigrationService;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * Rewrites the files derived from the configuration record when they are gone.
 *
 * The generated TypoScript and scss live below typo3temp/assets/, which TYPO3
 * treats as disposable: "Remove Temporary Assets" in the install tool empties
 * it, and most deployments do not carry it over. Since everything in there is
 * derived from tx_t3sbootstrap_domain_model_config, it can simply be written
 * again - no network, one database read.
 *
 * This has to happen before TypoScript is resolved: RequestHandler drops
 * includeCSS/includeJS entries whose file is missing without any error and
 * stores that result in the page cache, so healing later in the PageRenderer
 * would be too late and would bake a broken page into the cache. Hence a
 * middleware placed before typo3/cms-frontend/prepare-tsfe-rendering.
 *
 * What this deliberately does NOT do is fetch the downloaded assets: that needs
 * network access and belongs into t3sbootstrap:cdnToLocal, not into a request.
 */
final readonly class EnsureGeneratedFiles implements MiddlewareInterface
{
    public function __construct(
        private OutsourcedFiles $outsourcedFiles,
        private LegacyAssetMigrationService $legacyAssetMigration,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $site = $request->getAttribute('site');

        if ($site instanceof SiteInterface) {
            $rootPageId = $site->getRootPageId();

            if ($rootPageId > 0) {
                try {
                    // one-time move of files an older version wrote into
                    // EXT:t3sb_package - the downloads cannot be rebuilt from
                    // the database, so they have to be carried over
                    if (!$this->legacyAssetMigration->isDone()) {
                        $this->legacyAssetMigration->migrate();
                    }

                    $this->outsourcedFiles->ensureFilesExist($rootPageId);
                } catch (\Throwable) {
                    // never let a missing cache file break the frontend
                }
            }
        }

        return $handler->handle($request);
    }
}
