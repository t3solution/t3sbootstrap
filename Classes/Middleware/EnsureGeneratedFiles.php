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
 * Rewrites the generated TypoScript and scss when they are gone; one database read is enough,
 * as everything below typo3temp/assets/ derives from the configuration record. Runs before
 * TypoScript is resolved, because RequestHandler silently drops includeCSS/includeJS entries
 * whose file is missing and caches that. Downloads are left out - they need network.
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
